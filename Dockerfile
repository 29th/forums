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

# Use modified default config file
RUN cp conf/config-defaults.php conf/config.php \
  && sed -i conf/config.php \
    -e "s,\$Configuration\['Database'\]\['Host'\] = '.*',\$Configuration\['Database'\]\['Host'\] = getenv('DB_HOSTNAME')," \
    -e "s,\$Configuration\['Database'\]\['Name'\] = '.*',\$Configuration\['Database'\]\['Name'\] = getenv('DB_DATABASE')," \
    -e "s,\$Configuration\['Database'\]\['User'\] = '.*',\$Configuration\['Database'\]\['User'\] = getenv('DB_USERNAME')," \
    -e "s,\$Configuration\['Database'\]\['Password'\] *= '.*',\$Configuration\['Database'\]\['Password'\] = getenv('DB_PASSWORD')," \
    -e "s,\$Configuration\['Garden'\]\['Debug'\] = false,\$Configuration\['Debug'\] = getenv('ENVIRONMENT') == 'development'," \
    -e "s,\$Configuration\['Garden'\]\['Cookie'\]\['Salt'\] = '.*',\$Configuration\['Garden'\]\['Cookie'\]\['Salt'\] = getenv('VANILLA_COOKIE_SALT')," \
    -e "s,\$Configuration\['Garden'\]\['Installed'\] = false,\$Configuration\['Garden'\]\['Installed'\] = true,"

# Set directories writable
RUN chmod -R 777 conf cache uploads
