#!/bin/bash

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install

# Run database migrations
echo "Running migrations..."
php artisan migrate --force

# Seed the database
echo "Seeding categories..."
php artisan db:seed --class=CategorySeeder --force

echo "Seeding users..."
php artisan db:seed --class=UserSeeder --force

# Install JavaScript dependencies
echo "Installing NPM dependencies and building the webapp..."
npm install && npm run build

echo "Setup completed successfully! 🎉 (optionally you can run npm run dev)"
