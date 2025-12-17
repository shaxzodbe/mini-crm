FROM php:8.4-fpm-alpine as builder

RUN apk add --no-cache $PHPIZE_DEPS \
    imagemagick-dev icu-dev zlib-dev jpeg-dev libpng-dev libzip-dev postgresql-dev libgomp linux-headers

RUN docker-php-ext-configure gd --with-jpeg
RUN docker-php-ext-install intl pcntl gd exif zip mysqli pgsql pdo pdo_mysql pdo_pgsql bcmath opcache

RUN pecl install imagick; \
    docker-php-ext-enable imagick;

RUN pecl install xdebug; \
    docker-php-ext-enable xdebug; \
    echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;

RUN apk del $PHPIZE_DEPS imagemagick-dev icu-dev zlib-dev jpeg-dev libpng-dev libzip-dev postgresql-dev libgomp

FROM php:8.4-fpm-alpine

COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/etc/php/conf.d /usr/local/etc/php/conf.d

RUN apk add --no-cache libpng libpq zip jpeg libzip imagemagick \
    git curl sqlite nodejs npm nano ncdu openssh-client

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www
RUN chown -R www:www /var/www/html

WORKDIR /var/www/html

USER www:www

EXPOSE 9000

CMD ["php-fpm"]
