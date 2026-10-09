# SRP: this image is only the Laravel runtime (PHP-FPM + Composer + HTTP on 8000).
# DIP: database and Redis hosts are injected at run time, never written into the image.
FROM php:8.3-fpm-bookworm

# phpredis is compiled from a pinned tarball. The PECL latest release currently has no REST metadata.
ARG PHPREDIS_VERSION=6.2.0

RUN apt-get update && apt-get install -y --no-install-recommends \
        $PHPIZE_DEPS \
        git \
        unzip \
        libzip-dev \
        nginx \
    && curl -fsSL "https://github.com/phpredis/phpredis/archive/${PHPREDIS_VERSION}.tar.gz" -o /tmp/redis.tar.gz \
    && mkdir -p /usr/src/php/ext/redis \
    && tar -xzf /tmp/redis.tar.gz -C /usr/src/php/ext/redis --strip-components=1 \
    && rm /tmp/redis.tar.gz \
    && docker-php-ext-install pdo_mysql zip bcmath opcache redis \
    && apt-get purge -y --auto-remove $PHPIZE_DEPS \
    && rm -rf /var/lib/apt/lists/* /usr/src/php/ext/redis \
    && rm -f /etc/nginx/sites-enabled/default

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/nginx.conf /etc/nginx/conf.d/fairwayflow.conf
COPY docker/zz-fairwayflow.conf /usr/local/etc/php-fpm.d/zz-fairwayflow.conf
COPY docker/fairwayflow.ini /usr/local/etc/php/conf.d/fairwayflow.ini
COPY docker/entrypoint.sh /usr/local/bin/fairwayflow-entrypoint

COPY . /var/www/html

RUN composer install --no-interaction --prefer-dist --no-scripts \
    && chmod +x /usr/local/bin/fairwayflow-entrypoint \
    && chown -R www-data:www-data /var/www/html

EXPOSE 8000

ENTRYPOINT ["fairwayflow-entrypoint"]
