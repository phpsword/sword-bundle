FROM nginx:alpine

COPY --chown=www-data:www-data . /var/www/html/

RUN cp -R /var/www/html/docker/nginx/* /etc/nginx/ \
    && rm -rf /var/www/html/docker

WORKDIR /var/www/html/public
