ARG ALPINE_VERSION=3.18
ARG PHP_VERSION=8.2

FROM php:${PHP_VERSION}-fpm-alpine${ALPINE_VERSION} as base

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/