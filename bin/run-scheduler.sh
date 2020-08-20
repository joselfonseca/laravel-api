#!/usr/bin/env bash

while [ true ]
do
  php /var/www/html/artisan schedule:run --quiet --no-interaction &
  sleep 60
done
