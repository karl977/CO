#!/bin/sh

cd /var/www

npm install
npm run build

php artisan migrate
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan start-sync

/usr/bin/supervisord -c /etc/supervisord.conf
