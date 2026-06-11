# Use official PHP image with Apache
FROM php:8.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
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
    && rm -rf /var/lib/apt/lists/*

# Verify mysql client is available in PATH so schema load works during runtime
RUN command -v mysql >/dev/null 2>&1 || (echo 'mysql client not found after install' >&2 && exit 1)

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Copy existing application directory contents
COPY . /var/www/html

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www/html

# Install PHP dependencies
# Use --prefer-source to avoid corrupted zip dist issues during image builds
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-source || composer install --no-dev --optimize-autoloader --no-interaction

# Ensure mysql client config disables SSL verification in container (avoids self-signed cert errors)
RUN printf '[client]\nssl=0\n' > /root/.my.cnf || true

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configure Apache DocumentRoot to point to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Configure Apache to listen on port 8000 (for Koyeb)
RUN sed -i 's/Listen 80/Listen 8000/g' /etc/apache2/ports.conf

# Copy Apache virtual host configuration (already configured for port 8000)
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Set ServerName globally to suppress Apache warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Expose port 8000
EXPOSE 8000

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Start Apache
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
