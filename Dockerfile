FROM php:7.4.1-apache-buster

ENV VANILLA_VERSION 3.3

# Install system dependencies and php extensions
RUN apt-get update \
  && apt-get install -y \
    # for composer
    unzip \
    # for intl extension
    libicu-dev \
    # for gd
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install \
    gd \
    intl \
    mysqli \
    pdo_mysql \
  # cleanup
  && rm -rf \
      /var/lib/apt/lists/* \
      /usr/src/php/ext/* \
      /tmp/*

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
  && a2enmod rewrite

# Install vanilla
RUN curl --silent --show-error --location \
  --output vanilla.zip \
  "https://github.com/vanilla/vanilla/releases/download/Vanilla_${VANILLA_VERSION}/vanilla-${VANILLA_VERSION}.zip" \
  && unzip -q vanilla.zip \
  && rm vanilla.zip \
  && find package ! -path package -prune -exec mv {} . \; \
  && rmdir package

# Use default .htaccess file
RUN cp .htaccess.dist .htaccess

# Use custom config file from version control
COPY config.php conf/config.php
COPY bootstrap.early.php conf/bootstrap.early.php

# Set directories writable
RUN chmod -R 777 conf cache uploads

# Add theme
RUN curl --silent --show-error --location \
  --output bootstrap.zip \
  "https://github.com/29th/vanilla-bootstrap/archive/master.zip" \
  && unzip -q bootstrap.zip \
  && rm bootstrap.zip \
  && mv vanilla-bootstrap-master themes/bootstrap

# Add plugins
COPY plugins plugins

# Entrypoint script to support heroku $PORT env var at runtime
COPY run-apache2.sh /usr/local/bin
CMD [ "run-apache2.sh" ]
