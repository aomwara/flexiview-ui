FROM php:7.4-fpm-alpine

ADD ./php/www.conf /usr/local/etc/php-fpm.d/www.conf
ADD ./php/php.ini /usr/local/etc/php/php.ini

RUN addgroup -g 1000 flexiview && adduser -G flexiview -g flexiview -s /bin/sh -D flexiview

RUN mkdir -p /var/www/html

RUN chown flexiview:flexiview /var/www/html

WORKDIR /var/www/html

RUN docker-php-ext-install pdo pdo_mysql