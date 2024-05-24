FROM php:8.2-apache

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update -y && apt-get install -y \
  git \
  zip unzip \
  curl \
  openssl 

RUN a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV NODE_VERSION 18
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.5/install.sh | bash \
  && . ~/.nvm/nvm.sh \
  && nvm install $NODE_VERSION \
  && nvm alias default $NODE_VERSION \
  && nvm use default

RUN docker-php-ext-install pdo
RUN docker-php-ext-install pdo_mysql

#upload
RUN echo "file_uploads = On\n" \
  "memory_limit = 500M\n" \
  "upload_max_filesize = 500M\n" \
  "post_max_size = 500M\n" \
  "max_execution_time = 600\n" \
  > /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html
COPY . /var/www/html/

RUN composer install --optimize-autoloader --no-dev
RUN . ~/.nvm/nvm.sh && npm install
RUN . ~/.nvm/nvm.sh && npm run build

RUN php artisan view:cache

RUN chown -R www-data:www-data /var/www/html
RUN chmod 755 /var/www/html
RUN chmod -R 755 /var/www/html/storage

COPY docker/default.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]