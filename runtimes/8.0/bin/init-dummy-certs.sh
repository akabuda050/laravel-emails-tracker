#!/bin/bash

if ! [ -x "$(command -v docker-compose)" ]; then
  echo 'Error: docker-compose is not installed.' >&2
  exit 1
fi

echo "### Creating dummy certificate for $domains ..."

domains=(localhost)
path="/etc/letsencrypt/live/$domains"
rsa_key_size=4096

docker-compose run --rm --entrypoint "mkdir -p $path" certbot
docker-compose run --rm --entrypoint "wget https://raw.githubusercontent.com/certbot/certbot/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf -O /etc/letsencrypt/options-ssl-nginx.conf" certbot
docker-compose run --rm --entrypoint "wget https://raw.githubusercontent.com/certbot/certbot/master/certbot/certbot/ssl-dhparams.pem -O /etc/letsencrypt/ssl-dhparams.pem"  certbot
docker-compose run --rm --entrypoint "openssl req -x509 -nodes -newkey rsa:$rsa_key_size -days 1\
    -keyout '$path/privkey.pem' \
    -out '$path/fullchain.pem' \
    -subj '/CN=$domains'" certbot
echo

