#!/bin/bash

if ! [ -x "$(command -v docker)" ]; then
  echo 'Error: docker is not installed.' >&2
  exit 1
fi
/bin/bash ./runtimes/8.0/bin/init-dummy-certs.sh

cp .env.example .env
docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest composer install --ignore-platform-reqs
docker run --rm -u "$(id -u):$(id -g)" -v $(pwd):/opt -w /opt laravelsail/php80-composer:latest php artisan key:generate

./vendor/bin/sail up -d
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

