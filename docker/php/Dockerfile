FROM php:8.2-apache

# Habilita mod_rewrite (necessário para o roteamento do CodeIgniter 4)
RUN a2enmod rewrite

# Dependências e extensões PHP necessárias ao CI4
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libicu-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        unzip \
        git \
    && docker-php-ext-install pdo_mysql mysqli intl zip mbstring curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer (para executar comandos dentro do container)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# DocumentRoot apontando para a pasta public/ do CI4
COPY docker/php/000-default.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
