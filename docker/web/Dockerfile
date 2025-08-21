FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite headers
COPY docker/web/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Copy composer files first for better Docker layer caching

# Copy the rest of the application (.dockerignore excludes vendor/)
COPY . /var/www/html/

# Fix git ownership and reinstall dependencies to ensure vendor directory exists
RUN git config --global --add safe.directory /var/www/html || true


# Create required directories
RUN mkdir -p /var/www/html/public /var/www/html/logs /var/www/html/public/uploads

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod -R 777 /var/www/html/logs \
    && chmod -R 777 /var/www/html/public/uploads

EXPOSE 80
CMD ["apache2-foreground"]
