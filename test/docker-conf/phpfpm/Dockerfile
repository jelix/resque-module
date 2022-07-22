FROM 3liz/liz-php-fpm:8.1

RUN apt-get -y install \
    supervisor \
    ; apt-get clean

COPY profile.start /etc/profile.d/start
COPY launch.sh /bin/entrypoint.d/
COPY appctl.sh /bin/
COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisord_workers.conf /etc/supervisor/conf.d/supervisord_workers.conf

RUN chmod 755 /bin/entrypoint.d/launch.sh /bin/appctl.sh;

CMD ["/usr/bin/supervisord","-c","/etc/supervisor/supervisord.conf"]

