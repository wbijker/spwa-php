FROM php:8.2-apache

# install mysql pdo extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# install and configure xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# enabled apace url rewrite module
RUN a2enmod rewrite && service apache2 restart

RUN mkdir -p /tmp && chmod 777 /tmp

# install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#CMD composer update
# run composer install on startup
#CMD composer install --no-interaction

WORKDIR /var/www/html
