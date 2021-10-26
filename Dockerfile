FROM php:7.4.23-apache

WORKDIR /var/www/

RUN apt-get update

# active the extension what i nedd
RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN a2enmod rewrite

EXPOSE 80
