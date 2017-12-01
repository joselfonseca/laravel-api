FROM joselfonsecadt/nginx-php7.0:latest

MAINTAINER Jose Fonseca <jose@ditecnologia.com>

COPY docker/default /etc/nginx/sites-available/default

COPY docker/php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf

COPY docker/www.conf /etc/php/7.0/fpm/pool.d/www.conf

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html/

COPY composer.json ./

COPY composer.lock ./

RUN composer install --no-scripts --no-autoloader

COPY . /var/www/html/

RUN composer dump-autoload --optimize && \
	php artisan optimize

RUN cp .env.example .env

EXPOSE 80

CMD ["/usr/bin/supervisord"]