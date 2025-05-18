FROM php:7.4-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    sqlite3 \
    libsqlite3-dev \
    unzip \
    && docker-php-ext-install pdo pdo_sqlite

ENV APACHE_DOCUMENT_ROOT /var/www/html

COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
