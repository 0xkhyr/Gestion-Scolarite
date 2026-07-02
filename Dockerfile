# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1 — PHP dependencies (Composer), dist only (no git clones)
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-scripts --no-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# ---------------------------------------------------------------------------
# Stage 2 — Frontend assets (Vite / Tailwind v4 Filament theme)
# vendor is copied in so the theme's @source globs over vendor/filament resolve.
# ---------------------------------------------------------------------------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 3 — Final runtime image (PHP + Apache). No Node, Composer or git.
# ---------------------------------------------------------------------------
FROM php:8.4-apache
WORKDIR /var/www/html

# Runtime system libraries + PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    libzip-dev \
    netcat-openbsd \
    libpq-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl calendar \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

# Application code, then vendor + built assets from the build stages
COPY --chown=www-data:www-data . /var/www/html
COPY --from=vendor --chown=www-data:www-data /app/vendor /var/www/html/vendor
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Cache the package manifest (no DB needed); composer isn't present at runtime.
RUN php artisan package:discover --ansi || true

# mysql client: skip SSL verification in container (avoids self-signed cert errors)
RUN printf '[client]\nssl=0\n' > /root/.my.cnf || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Serve Laravel's public/ directory; listen on 8000 (Koyeb/Render)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN sed -i 's/Listen 80/Listen 8000/g' /etc/apache2/ports.conf
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

EXPOSE 8000

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
