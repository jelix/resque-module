FROM debian:buster-slim

ARG composer_version=2.1.3

ARG app_user_id
ARG app_group_id

ENV APP_USER_ID=${app_user_id}
ENV APP_GROUP_ID=${app_group_id}
ENV PHP_VERSION=7.4

RUN set -eux; \
	apt-get update; \
    apt-get -y install apt-transport-https lsb-release ca-certificates curl wget git; \
    wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg;\
    sh -c 'echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list';\
    apt-get update;

RUN apt-get -y install \
    php${PHP_VERSION}-bcmath \
    php${PHP_VERSION}-bz2 \
    php${PHP_VERSION}-cli \
    php${PHP_VERSION}-fpm \
    php${PHP_VERSION}-curl \
    php${PHP_VERSION}-dba \
    php${PHP_VERSION}-gd \
    php${PHP_VERSION}-intl \
    php${PHP_VERSION}-json \
    php${PHP_VERSION}-ldap \
    php${PHP_VERSION}-mbstring \
    php${PHP_VERSION}-pgsql \
    php${PHP_VERSION}-sqlite3 \
    php${PHP_VERSION}-xml \
    php${PHP_VERSION}-zip \
    php-redis \
    supervisor \
    ; apt-get clean

RUN set -eux; \
    echo "APP_USER_ID" $APP_USER_ID; \
    echo "APP_GROUP_ID" $APP_GROUP_ID; \
    sed -i "/^display_errors =/c\display_errors = On" /etc/php/${PHP_VERSION}/cli/php.ini; \
    sed -i "/^display_errors =/c\display_errors = On" /etc/php/${PHP_VERSION}/fpm/php.ini; \
    ln -s /usr/sbin/php-fpm${PHP_VERSION} /usr/sbin/php-fpm; \
    addgroup --gid $APP_GROUP_ID grouptest; \
    useradd --uid $APP_USER_ID --gid $APP_GROUP_ID -m usertest ; \
    mkdir -p /run/php/


## Install Composer
RUN wget -O /bin/composer https://getcomposer.org/download/$composer_version/composer.phar \
    && chmod +x /bin/composer && /bin/composer self-update

COPY profile.start /etc/profile.d/start
COPY entrypoint.sh /bin/
COPY appctl.sh /bin/
COPY www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf
COPY supervisord.conf /etc/supervisor/supervisord.conf
COPY supervisord_workers.conf /etc/supervisor/conf.d/supervisord_workers.conf

RUN chmod 755 /bin/entrypoint.sh /bin/appctl.sh;


WORKDIR /jelixapp/
ENTRYPOINT ["/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord","-c","/etc/supervisor/supervisord.conf"]

