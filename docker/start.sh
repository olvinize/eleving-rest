#!/bin/sh

if [ "${ENV}" = "PROD" ]
then
  composer install --no-dev --optimize-autoloader
else
  composer install
fi


php-fpm