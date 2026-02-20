FROM php:8.2-apache

# install extension yang umum + pdo_pgsql untuk Postgres
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip unzip \
  && docker-php-ext-install pdo_pgsql pgsql zip \
  && a2enmod rewrite

WORKDIR /var/www/html

# untuk Apache vhost + alias / redirect
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf
