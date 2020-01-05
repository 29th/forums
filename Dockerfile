FROM php:7.4.1-apache-buster

ENV VANILLA_VERSION 2.8.4

# Install system dependencies
RUN apt-get update && apt-get install -y \
  unzip \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Configure apache
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf \
  && a2enmod rewrite

# Enable php mysql extension
RUN docker-php-ext-install gd mysqli pdo pdo_mysql

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

# Set directories writable
RUN chmod -R 777 conf cache uploads
