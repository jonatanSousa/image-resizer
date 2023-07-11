FROM php:8.1-apache

RUN apt-get update \
  && apt-get install -y libzip-dev git wget libjpeg-dev libpng-dev --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure gd --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd

RUN wget https://getcomposer.org/download/2.0.9/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer

COPY docker/apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY docker/entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

CMD ["apache2-foreground"]
ENTRYPOINT ["/entrypoint.sh"]
COPY . /var/www
WORKDIR /var/www

CMD ["apache2-foreground"]

RUN a2enmod rewrite
RUN service apache2 restart

EXPOSE 80