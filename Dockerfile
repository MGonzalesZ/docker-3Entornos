FROM php:8.1-fpm

# Instala dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# Limpia caché
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala extensiones de PHP
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Modifica la zona horaria
ENV TZ=America/La_Paz

# Instala composer
COPY --from=composer:2.7.4 /usr/bin/composer /usr/bin/composer

# Crea usuario del sistema para ejecutar composer y los comandos de artisan
RUN useradd -G www-data,root -u 1000 -d /home/newspaper newspaper
RUN mkdir -p /home/newspaper/.composer && \
    chown -R newspaper:newspaper /home/newspaper

ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/
COPY . /var/www/
RUN composer install

USER newspaper