FROM php:8.3-fpm AS base

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Create system user
RUN useradd -G www-data,root -u 1000 -d /home/www www
RUN mkdir -p /home/www/.composer && \
    chown -R www:www /home/www

# Production stage
FROM base AS production

# Copy application files
COPY --chown=www:www . /var/www/html

# Install dependencies with production optimizations
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-plugins --no-scripts

# Optimize Laravel for production
RUN php artisan config:cache || true
RUN php artisan route:cache || true
RUN php artisan view:cache || true

# Set proper permissions
RUN chown -R www:www /var/www/html
RUN chmod -R 755 /var/www/html/storage
RUN chmod -R 755 /var/www/html/bootstrap/cache

# Copy nginx configuration
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create log directories
RUN mkdir -p /var/log/supervisor
RUN mkdir -p /var/www/html/storage/logs

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]