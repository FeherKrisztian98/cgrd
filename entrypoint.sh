#!/bin/bash

# Check if vendor directory is missing and composer.json exists
if [ ! -d "/var/www/html/vendor" ] && [ -f "/var/www/html/composer.json" ]; then
    # Install dependencies
    composer install --no-interaction --optimize-autoloader
fi

# Enable xdebug conditionally
if [ "$XDEBUG_ENABLED" = "true" ]; then
  cp /var/www/html/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
else
  rm -f /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
fi

# Rebuild the autoloader to ensure it's updated
composer dump-autoload --optimize

# Run Apache in the foreground
apache2-foreground

