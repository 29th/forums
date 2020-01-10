#!/usr/bin/env bash

# https://github.com/docker-library/wordpress/issues/293
sed -i "s/Listen 80/Listen ${PORT:-80}/g" /etc/apache2/ports.conf
/usr/local/bin/docker-php-entrypoint apache2-foreground
