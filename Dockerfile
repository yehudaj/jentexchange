FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libonig-dev sqlite3 libsqlite3-dev libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-install pdo pdo_mysql pdo_sqlite zip gd && \
    a2enmod rewrite ssl proxy_fcgi setenvif && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80 443

CMD ["apache2-foreground"]
FROM php:8.2-apache

# Install system deps and PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git libonig-dev sqlite3 libsqlite3-dev libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-install pdo pdo_mysql pdo_sqlite zip gd && \
    a2enmod rewrite ssl proxy_fcgi setenvif && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY app/ /var/www/html/

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true

EXPOSE 80 443

CMD ["apache2-foreground"]
