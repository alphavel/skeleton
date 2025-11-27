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
    echo "Running composer install inside container..."
    cd /var/www
    composer install \
        --no-interaction \
        --no-dev \
        --optimize-autoloader \
        --classmap-authoritative \
        --no-progress
    echo "✅ Composer install completed"
fi

# Check if bootstrap/app.php exists after composer install
if [ ! -f "/var/www/bootstrap/app.php" ]; then
    echo "❌ ERROR: bootstrap/app.php still not found after composer install!"
    echo ""
    echo "This means the skeleton files were not properly installed."
    echo ""
    echo "Solution:"
    echo "  The container will copy the file from the image to your volume."
    
    # If we're in the image (not a volume mount), bootstrap should exist in the image
    if [ -f "/tmp/bootstrap/app.php" ]; then
        echo "  Copying bootstrap/app.php from image..."
        cp /tmp/bootstrap/app.php /var/www/bootstrap/app.php
    else
        echo "  Please ensure you created the project with:"
        echo "  composer create-project alphavel/skeleton your-project --ignore-platform-reqs"
    fi
fi

echo "✅ All critical files found. Starting server..."

# Execute the main command
exec "$@"
