FROM php:7.4.1-apache-buster

ENV VANILLA_VERSION 3.3

# Install system dependencies
RUN apt-get update && apt-get install -y \
  unzip \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  libicu-dev

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
  && a2enmod rewrite

# Enable php mysql extension
# TODO: Try using docker-php-ext-enable instead to save space if already installed
RUN docker-php-ext-install gd mysqli pdo_mysql intl

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
