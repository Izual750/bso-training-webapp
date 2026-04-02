FROM php:8.3-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

WORKDIR /var/www/html

COPY main.php .
COPY helpers.php .
COPY styles.css .
COPY script.js .
COPY templates/ ./templates/

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]
