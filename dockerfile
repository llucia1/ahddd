FROM ghcr.io/gridcp/docker-php-8.5.2:main

WORKDIR /var/www

# Primero copia app
COPY ./app .

RUN composer install --no-interaction --prefer-dist --optimize-autoloader \
        && chmod -R 777 var/ && chmod -R 777 public/


EXPOSE 80