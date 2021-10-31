#!/bin/bash

if ! [ -x "$(command -v docker)" ]; then
  echo 'Error: docker is not installed.' >&2
  exit 1
fi

runtimePath="./runtimes/8.0"
/bin/bash "$runtimePath/bin/init-dummy-certs.sh"

if [ ! -f "$runtimePath/conf/php.ini" ]; then
    cp "$runtimePath/conf/php.ini.example" "$runtimePath/conf/php.ini"
fi

if [ ! -f "$runtimePath/conf/supervisord.conf" ]; then
    cp "$runtimePath/conf/supervisord.conf.example" "$runtimePath/conf/supervisord.conf"
fi

if [ ! -f "$runtimePath/conf/cron" ]; then
    cp "$runtimePath/conf/cron.example" "$runtimePath/conf/cron"
fi

if [ ! -f "$runtimePath/conf/app.conf" ]; then
    cp "$runtimePath/conf/app.conf.example" "$runtimePath/conf/app.conf"
fi

cp .env.example .env

docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest composer install --ignore-platform-reqs
docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest php artisan key:generate

./vendor/bin/sail up -d
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

