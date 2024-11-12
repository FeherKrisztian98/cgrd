# Install latest stable PHP version
FROM php:8.3-apache

# Install necessary dependencies, and remove package list directories
RUN apt-get update && apt-get install -y \
    curl \
    git \
    unzip \
    zip \
    && rm -rf /var/lib/apt/lists/*

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Xdebug via PECL
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Setup Apache configuration
COPY ./apache/cgrd.conf /etc/apache2/sites-available/cgrd.conf
RUN a2ensite cgrd.conf
RUN a2enmod rewrite

# Setup initial database schema and data
COPY mysql/migrations/migration_0.sql /docker-entrypoint-initdb.d/

# Copy Composer files
COPY composer.json composer.lock* ./

# Copy the entrypoint script
COPY entrypoint.sh /usr/local/bin/entrypoint.sh

# Make the entrypoint script executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Set the entrypoint to the script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Expose port 80 for Apache
EXPOSE 80

