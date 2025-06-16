# Stage 1: Build Node.js assets
FROM node:18-alpine as node_builder

WORKDIR /app_node

COPY package.json ./
COPY package-lock.json ./ 
# Décommentez et assurez-vous que package-lock.json est versionné

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

# Configurer OPcache pour la production
RUN { \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=2'; \
    echo 'opcache.fast_shutdown=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Configurer le php.ini pour la production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Utiliser un compte système avec des droits limités
RUN addgroup -g $GID -S appgroup && \
    adduser -u $UID -S appuser -G appgroup

# Définir les permissions sur le répertoire de travail
RUN chown -R appuser:appgroup /var/www/html

# Copier les fichiers de composer d'abord pour exploiter le cache Docker
COPY --chown=appuser:appgroup composer.json composer.lock symfony.lock ./

# Installer les dépendances PHP en mode non-root
USER appuser
RUN composer install --prefer-dist --no-scripts --no-progress --no-interaction

# Copier le reste de l'application
COPY --chown=appuser:appgroup . .

# Créer les répertoires nécessaires et définir les permissions
RUN mkdir -p var/cache var/log var/uploads public/uploads

# Retourner à l'utilisateur root pour le démarrage de PHP-FPM
USER root

# Exposer le port pour PHP-FPM
EXPOSE 9000

# Démarrer PHP-FPM
CMD ["php-fpm"]