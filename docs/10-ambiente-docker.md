# 10 — Ambiente de Desenvolvimento Local (Docker)

Objetivo: subir o projeto CI4 + banco localmente, de forma reproduzível, sem
instalar PHP/Apache/MySQL na máquina do desenvolvedor.

## Componentes sugeridos

- **app/web**: PHP 8.2/8.3 + Apache (imagem `php:8.2-apache` com `mod_rewrite`
  e extensões) OU PHP-FPM + Nginx. Para CI4, Apache com `.htaccess` padrão é o
  caminho mais simples.
- **db**: MariaDB (ou MySQL) 10.x/8.x.
- **adminer/phpmyadmin** (opcional): interface web para inspecionar o banco.

## Estrutura de arquivos esperada

```
projeto/
├── app/            (código CI4)
├── public/         (webroot CI4)
├── writable/       (logs/cache CI4)
├── docker/
│   ├── php/Dockerfile
│   └── apache/vhost.conf (opcional)
├── docker-compose.yml
└── .env            (CI4 + variáveis de ambiente)
```

## Dockerfile (exemplo conceitual — `docker/php/Dockerfile`)

```dockerfile
FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    libzip-dev libicu-dev libpng-dev libxml2-dev unzip git \
    && docker-php-ext-install pdo_mysql intl zip gd mysqli \
    && apt-get clean

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# DocumentRoot apontando para public/
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
```

## docker-compose.yml (exemplo conceitual)

```yaml
services:
  web:
    build: ./docker/php
    ports:
      - "8080:80"
    volumes:
      - ./:/var/www/html
    environment:
      CI_ENVIRONMENT: development
    depends_on:
      - db

  db:
    image: mariadb:10.11
    restart: unless-stopped
    environment:
      MARIADB_DATABASE: aassc
      MARIADB_USER: aassc
      MARIADB_PASSWORD: aassc
      MARIADB_ROOT_PASSWORD: root
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql

  adminer:
    image: adminer
    restart: unless-stopped
    ports:
      - "8081:8080"

volumes:
  db_data:
```

## Passos de uso (local)

1. `composer create-project codeigniter4/appstarter .` (ou instalar via Composer).
2. Configurar `.env` com `CI_ENVIRONMENT = development` e banco apontando para
   `db` (host) / `aassc` (db) / `aassc` (user/senha).
3. `docker compose up -d --build`.
4. Acessar `http://localhost:8080`.
5. Importar schema: `docker compose exec -T db mysql -uaassc -paassc aassc < docs/_referencia/aassc.sql`
   (após criar o projeto; o dump está em `docs/_referencia/`).

## Extensões PHP necessárias (CI4)
- `intl`, `mbstring`, `pdo_mysql`/`mysqli`, `curl`, `json` (nativo), `openssl`.
- Opcional: `gd`/`imagick` se houver manipulação de imagens (não há hoje).

## Convenções
- Aplicar `.env` fora do controle de versão (`.gitignore`); versionar
  `.env.example`.
- Usar `writable/` para logs/cache (CI4) e garantir permissões no container.
