FROM php:8.2-alpine
LABEL maintainer="Jonas Marklén <txc@txc.se>"
LABEL image="PHP Base Image"
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN apk add --no-cache tzdata musl-locales musl-locales-lang \
    && install-php-extensions pdo_mysql pdo_sqlite gettext
