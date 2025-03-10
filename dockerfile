# Use PHP 8.4.4 with Apache
FROM php:8.4.4-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    default-mysql-server \
    default-mysql-client \
    supervisor \
    nodejs \
    npm \
    libzip-dev

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Configure Apache
RUN a2enmod rewrite
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Copy application code
COPY . .

# Copy .env file
COPY .env .env

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Install Composer dependencies
RUN composer install --no-interaction --no-dev --optimize-autoloader

# Install npm dependencies and build assets
RUN npm install && npm run build

# Configure and start MySQL
RUN mkdir -p /var/run/mysqld \
    && chown -R mysql:mysql /var/run/mysqld \
    && chown -R mysql:mysql /var/lib/mysql

# Copy startup script
COPY start.sh /usr/local/bin/start.sh
RUN chmod +x /usr/local/bin/start.sh

# Copy supervisor configuration
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose ports
EXPOSE 80 3306
ENV MYSQL_ROOT_PASSWORD=root

# Start services with supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
