# Produção — estende o ambiente de desenvolvimento com opcache e hardening.
FROM php:8.2-apache

# mod_rewrite (roteamento CI4) e headers (headers de segurança do Apache)
RUN a2enmod rewrite headers

RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        unzip \
        git \
    && docker-php-ext-install pdo_mysql mysqli intl zip mbstring curl opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer (para `composer install` dentro do container)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurações de produção do PHP (opcache + erros ocultos)
COPY docker/php/prod-php.ini /usr/local/etc/php/conf.d/zz-prod.ini

# Hardening do Apache (ServerTokens/Signature/Trace)
COPY docker/php/prod-security.conf /etc/apache2/conf-available/zz-prod-security.conf
RUN a2enconf zz-prod-security

# DocumentRoot em public/ e vhost de produção (sem listagem de diretório)
COPY docker/php/prod-000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
