FROM php:7.4.33-apache

# install mysql pdo extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# enabled apace url rewrite module
RUN a2enmod rewrite && service apache2 restart

# install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

