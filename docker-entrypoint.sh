#!/bin/sh
set -e

# Ensure directories exist
mkdir -p /var/www/storage/framework /var/www/storage/logs /var/www/storage/cache /var/www/bootstrap/cache

# Set proper permissions for storage directories
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Execute the main command
exec "$@"
