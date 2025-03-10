#!/bin/bash

# Set a default MySQL root password if one doesn't exist.
MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD:-root} # Use "root" as default

# Check if database is initialized
if [ ! -f /var/lib/mysql/mysql/user.frm ]; then
    echo "Initializing MySQL with password '$MYSQL_ROOT_PASSWORD'..."
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED BY '$MYSQL_ROOT_PASSWORD';"
    mysql -u root -e "ALTER USER 'root'@'%' IDENTIFIED BY '$MYSQL_ROOT_PASSWORD';" # Allow root access from any host in container
    mysql -u root -e "FLUSH PRIVILEGES;"
    echo "MySQL initialized."
fi

# Check if and create the database
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS fleektest01;"

#Grant root user access
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "GRANT ALL PRIVILEGES ON fleektest01.* TO 'root'@'localhost' IDENTIFIED BY '$MYSQL_ROOT_PASSWORD';"
mysql -u root -p"$MYSQL_ROOT_PASSWORD" -e "FLUSH PRIVILEGES;"

#Wait add some sleep (Important)
sleep 10

# Wait for MySQL to be ready
max_tries=60 # Double the number of tries
counter=0
echo "Waiting for MySQL to be READY..."
while ! mysqladmin ping -h"localhost" -uroot -p"$MYSQL_ROOT_PASSWORD" --silent; do
    sleep 1
    counter=$((counter + 1))
    if [ $counter -gt $max_tries ]; then
        echo "MySQL did not become ready in time. Exiting."
        exit 1
    fi
    echo "Waiting for MySQL... ($counter/$max_tries)"
done

echo "MySQL is ready. Running Laravel commands..."

# Run Laravel commands
cd /var/www/html
php artisan migrate --force
php artisan db:seed --class=CategorySeeder --force
php artisan db:seed --class=UserSeeder --force

echo "Laravel setup complete!"
