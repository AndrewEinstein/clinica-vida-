FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        ca-certificates \
        git \
        unzip \
        libpq-dev \
        libzip-dev \
        libonig-dev \
        libxml2-dev \
        libcurl4-openssl-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install pdo_pgsql pgsql zip mbstring bcmath curl \
    && a2enmod rewrite headers env \
    && rm -rf /var/lib/apt/lists/*

# Configure Apache to serve Laravel from /public
COPY docker/000-default.conf /etc/apache2/sites-available/000-default.conf

# Install Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm -f composer-setup.php

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader

COPY . .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

COPY docker/start.sh /usr/local/bin/start
RUN chmod +x /usr/local/bin/start

EXPOSE 80
CMD ["start"]
