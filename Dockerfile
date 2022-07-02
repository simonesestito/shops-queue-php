# Add mysqli to PHP instance
FROM php:7.4-fpm-alpine3.16
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
