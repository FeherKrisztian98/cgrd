#!/bin/bash

# Check if vendor directory is missing and composer.json exists
if [ ! -d "/var/www/html/vendor" ] && [ -f "/var/www/html/composer.json" ]; then
    # Install dependencies
    composer install --no-interaction --optimize-autoloader
fi


if [ "$DEVELOPMENT" = "true" ]; then
  # Enable xdebug
  cp /var/www/html/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
  # Rebuild the autoloader in dev mode
  composer dump-autoload --optimize --dev
else
  # Disable xdebug
  rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
  # Rebuild the autoloader in production mode
  composer dump-autoload --optimize --no-dev
fi

# Run Apache in the foreground
apache2-foreground

