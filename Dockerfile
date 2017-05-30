FROM joselfonsecadt/php7.0:1.0.0

MAINTAINER Jose Fonseca <jose@ditecnologia.com>

COPY docker/default /etc/nginx/sites-available/default

COPY docker/php-fpm.conf /etc/php/7.0/fpm/php-fpm.conf

COPY docker/www.conf /etc/php/7.0/fpm/pool.d/www.conf

COPY . /var/www/html/

EXPOSE 80

COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html/

RUN cp .env.example .env

CMD ["/usr/bin/supervisord"]