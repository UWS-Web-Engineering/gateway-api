FROM php:fpm
LABEL maintainer="Yashar Zolmajdi"

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update \
  && apt-get install --no-install-recommends -y libpq-dev \
  && docker-php-ext-install pdo_pgsql pgsql \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/*

COPY . /usr/src/gateway
WORKDIR /usr/src/gateway

RUN apt-get update && \
  apt-get upgrade -y && \
  apt-get install -y git && \
  apt-get install -y zip && \
  apt-get install -y unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction --prefer-dist
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN php -d output_buffering=4098

CMD sleep 5 && php artisan migrate --force && php -S 0.0.0.0:8000 -t public

EXPOSE 8000