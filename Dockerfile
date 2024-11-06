# Install latest stable PHP version
FROM php:8.3-apache

# Install necessary dependencies, and remove package list directories
RUN apt-get update && apt-get install -y \
    curl \
    git \
    && rm -rf /var/lib/apt/lists/*

# Install necessary PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Twig using Composer
RUN composer require "twig/twig:^3.0"

# Set the working directory and permissions
WORKDIR /var/www/html
RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Apache
EXPOSE 80

