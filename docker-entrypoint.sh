#!/bin/sh
set -e

echo "🚀 Starting Alphavel Framework..."

# Ensure directories exist
mkdir -p /var/www/storage/framework /var/www/storage/logs /var/www/storage/cache /var/www/bootstrap/cache

# Set proper permissions for storage directories
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Verify critical files exist
if [ ! -f "/var/www/bootstrap/app.php" ]; then
    echo "❌ ERROR: bootstrap/app.php not found!"
    echo "This file is required to start the application."
    echo ""
    echo "Possible causes:"
    echo "  1. Incomplete composer create-project"
    echo "  2. Volume mounting overwrote skeleton files"
    echo "  3. Git clone without proper checkout"
    echo ""
    echo "Solution:"
    echo "  1. If using volumes, ensure you're not mounting over /var/www"
    echo "  2. Or run: composer create-project alphavel/skeleton . --no-cache"
    echo "  3. Or copy bootstrap/app.php from the skeleton repository"
    exit 1
fi

if [ ! -f "/var/www/vendor/autoload.php" ]; then
    echo "⚠️  WARNING: vendor/autoload.php not found!"
    echo "Running composer install..."
    composer install --no-interaction --no-dev --optimize-autoloader
fi

echo "✅ All critical files found. Starting server..."

# Execute the main command
exec "$@"
