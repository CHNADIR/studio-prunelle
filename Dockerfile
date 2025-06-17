# Stage 1: Build Node.js assets
FROM node:18-alpine as node_builder

WORKDIR /app_node

COPY package.json ./
COPY package-lock.json ./

RUN npm install

COPY webpack.config.js ./
COPY assets ./assets

RUN npm run build

# Stage 2: PHP Application
FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Arguments pour l'utilisateur et le groupe
ARG UID=1000
ARG GID=1000

# Installer les dépendances système et les extensions PHP
RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    mysql-client \
    git \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    intl \
    zip \
    gd \
    opcache \
    mbstring \
    xml

# Configurer OPcache
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Utiliser un compte système avec des droits limités
RUN addgroup -g $GID -S appgroup && \
    adduser -u $UID -S appuser -G appgroup

# Définir les permissions sur le répertoire de travail
RUN chown -R appuser:appgroup /var/www/html

# Copier les fichiers de composer pour exploitation du cache Docker
COPY --chown=appuser:appgroup composer.json composer.lock symfony.lock ./

# Installer les dépendances PHP
USER appuser
RUN composer install --prefer-dist --no-scripts --no-progress --no-interaction

# Copier le reste de l'application
COPY --chown=appuser:appgroup . .

# Copier les assets compilés depuis l'étape node_builder
COPY --from=node_builder /app_node/public/build /var/www/html/public/build

# Créer les répertoires nécessaires
RUN mkdir -p var/cache var/log var/uploads public/uploads

# Ajouter cette ligne dans votre Dockerfile après la création des répertoires
RUN chmod -R 775 var/uploads public/uploads

# Retourner à l'utilisateur root pour le démarrage de PHP-FPM
USER root

# Exposer le port pour PHP-FPM
EXPOSE 9000

# Démarrer PHP-FPM
CMD ["php-fpm"]