FROM php:8.3-fpm-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

WORKDIR /var/www/html

COPY main.php .
COPY styles.css .
COPY script.js .

EXPOSE 9000

CMD ["php-fpm"]
