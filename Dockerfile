FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    imagemagick \
    libmagickwand-dev \
    ghostscript \
    ffmpeg \
    unzip \
    zip \
    git \
    --no-install-recommends \
    && sed -i 's/rights="none" pattern="PDF"/rights="read|write" pattern="PDF"/' /etc/ImageMagick-6/policy.xml \
    && docker-php-ext-configure pgsql --with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/