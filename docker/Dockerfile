FROM php:7.4.1-fpm-alpine as req_php

RUN apk add --no-cache --virtual .deps icu-libs zlib libintl libzip libxml2 libxslt freetype libpng libwebp libjpeg-turbo
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS icu-dev libzip-dev libxml2-dev libxslt-dev libgcrypt-dev freetype-dev libpng-dev libwebp-dev libjpeg-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd intl zip pdo_mysql xml xsl \
    && pecl install redis \
    && apk del .build-deps \
    && docker-php-ext-enable --ini-name 05-opcache.ini opcache \
    && docker-php-ext-enable --ini-name 20-redis.ini redis \
    && docker-php-source delete \
    && rm -rf /tmp/*

RUN mkdir /app && chown -R www-data:www-data /app
WORKDIR /app
USER www-data

########################################
FROM req_php as composer_deps

COPY --from=composer:1.9.1 /usr/bin/composer /usr/local/bin/composer

# prod|dev
ARG ENV=prod

COPY --chown=www-data:www-data src src
COPY --chown=www-data:www-data .env* composer.json composer.lock symfony.lock ./

ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_MEMORY_LIMIT -1
ENV APP_ENV $ENV

RUN composer global require "hirak/prestissimo:^0.3" --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative --no-interaction; \
    if [ $ENV = 'prod' ]; then \
        composer install --no-scripts --no-suggest --ignore-platform-reqs --optimize-autoloader --classmap-authoritative --no-dev --prefer-dist --no-progress --no-interaction; \
        rm -rf vendor/*/*/docs; \
        find vendor -name *.md -exec rm -rf {} \; ; \
        find vendor -name LICENSE -exec rm -rf {} \; ; \
    else \
        composer install --no-scripts --no-suggest --optimize-autoloader --classmap-authoritative --prefer-dist --no-progress --no-interaction; \
    fi; \
    composer dump-env $ENV

################################
FROM node:12-alpine as node_deps

WORKDIR /app
COPY package.json yarn.lock ./
RUN yarn install

COPY assets assets
COPY webpack.config.js postcss.config.js ./
RUN mkdir public && yarn encore production

####################################
FROM req_php as build_php

# prod|dev
ARG ENV=prod
ARG DUMP_ENV=0

USER root
COPY --from=bugoman/mozjpeg /opt/mozjpeg /opt/mozjpeg
RUN apk add --no-cache pngquant

COPY docker/php/php.prod.ini /usr/local/etc/php/php.ini
COPY docker/php/php-fpm.prod.conf /usr/local/etc/php-fpm.conf
COPY docker/php/www.prod.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

USER www-data
COPY --chown=www-data:www-data assets assets
COPY --chown=www-data:www-data bin/console bin/
COPY --chown=www-data:www-data config config
COPY --chown=www-data:www-data public/index.php public/
COPY --chown=www-data:www-data src src
COPY --chown=www-data:www-data templates templates
COPY --chown=www-data:www-data translations translations
COPY --chown=www-data:www-data .env* Makefile composer.json composer.lock ./
COPY --chown=www-data:www-data --from=node_deps /app/public/build/entrypoints.json public/build/entrypoints.json
COPY --chown=www-data:www-data --from=node_deps /app/public/build/manifest.json public/build/manifest.json
COPY --chown=www-data:www-data --from=composer_deps /app/vendor vendor
COPY --chown=www-data:www-data --from=composer_deps /app/.env.local.php .env.local.php

RUN if [ ${DUMP_ENV} -eq 0 ]; then echo "<?php return ['APP_ENV'=>'${ENV}','APP_SECRET'=>''];" > .env.local.php; fi; \
    chown www-data .env*; \
    sed -i "s/^APP_ENV=.*$/APP_ENV=${ENV}/" .env; \
    if [ -e .env.local ]; then sed -i "s/^APP_ENV=.*$/APP_ENV=${ENV}/" .env.local; fi
RUN mkdir -p public/bundles \
    && bin/console assets:install -v \
    && rm -rf var/cache/* \
    && bin/console cache:warmup -v \
    && mkdir -p var/log public/uploads

ENV BUILD_ENV prod
ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]

##############################
FROM httpd:2.4-alpine as build_apache

WORKDIR /app
COPY public/favicon.ico public/robots.txt public/
COPY public/icons public/icons
COPY --from=node_deps /app/public/build public/build
COPY --from=build_php /app/public/bundles public/bundles
RUN touch public/index.php

COPY docker/apache/httpd.conf /usr/local/apache2/conf/httpd.conf

##############################
FROM build_apache as build_apache_test

COPY docker/apache/httpd_test.conf /usr/local/apache2/conf/httpd.conf
#RUN touch public/index_test.php

###############################
FROM build_php as build_php_dev

#COPY public/index_test.php public/

USER root
COPY --from=composer:1.9.1 /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_MEMORY_LIMIT -1
ENV COMPOSER_ALLOW_SUPERUSER 1

# Xdebug install
ARG XDEBUG_VERSION=2.9.0
RUN set -eux; \
	apk add --no-cache --virtual .build-deps $PHPIZE_DEPS; \
	pecl install xdebug-$XDEBUG_VERSION; \
	apk del .build-deps; \
	rm -fr /tmp/*

RUN apk add --no-cache \
        chromium \
        chromium-chromedriver
ENV PANTHER_CHROME_DRIVER_BINARY /usr/lib/chromium/chromedriver
ENV PANTHER_NO_SANDBOX 1

COPY docker/php/php.dev.ini /usr/local/etc/php/php.ini
COPY docker/php/php-fpm.dev.conf /usr/local/etc/php-fpm.conf
COPY docker/php/www.dev.conf /usr/local/etc/php-fpm.d/www.conf

ENV BUILD_ENV dev

USER www-data
