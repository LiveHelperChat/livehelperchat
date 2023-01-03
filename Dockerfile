FROM php:7.4-apache

# Install dependencies
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libpng-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install bcmath
RUN a2enmod headers
RUN service apache2 restart

# Copy the lhc_web folder to /var/www/html
COPY ./lhc_web /var/www/html
WORKDIR /var/www/html

EXPOSE 80