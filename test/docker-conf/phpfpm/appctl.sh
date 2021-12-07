#!/bin/bash
APPDIR="/app/test/testapp"
APP_USER=userphp
APP_GROUP=groupphp

COMMAND="$1"

if [ "$COMMAND" == "" ]; then
    echo "Error: command is missing"
    exit 1;
fi


function cleanTmp() {
    if [ ! -d $APPDIR/var/log ]; then
        mkdir $APPDIR/var/log
        chown $APP_USER:$APP_GROUP $APPDIR/var/log
    fi

    if [ ! -d $APPDIR/temp/ ]; then
        mkdir $APPDIR/temp/
        chown $APP_USER:$APP_GROUP $APPDIR/temp
    else
        rm -rf $APPDIR/temp/*
    fi
    touch $APPDIR/temp/.dummy
    chown $APP_USER:$APP_GROUP $APPDIR/temp/.dummy
}


function resetApp() {
    if [ -f $APPDIR/var/config/CLOSED ]; then
        rm -f $APPDIR/var/config/CLOSED
    fi

    if [ ! -d $APPDIR/var/log ]; then
        mkdir $APPDIR/var/log
        chown $APP_USER:$APP_GROUP $APPDIR/var/log
    fi

    if [ -f $APPDIR/var/config/profiles.ini.php.dist ]; then
        cp $APPDIR/var/config/profiles.ini.php.dist $APPDIR/var/config/profiles.ini.php
    fi
    if [ -f $APPDIR/var/config/localconfig.ini.php.dist ]; then
        cp $APPDIR/var/config/localconfig.ini.php.dist $APPDIR/var/config/localconfig.ini.php
    fi
    chown -R $APP_USER:$APP_GROUP $APPDIR/var/config/profiles.ini.php $APPDIR/var/config/localconfig.ini.php

    if [ -f $APPDIR/var/config/installer.ini.php ]; then
        rm -f $APPDIR/var/config/installer.ini.php
    fi
    if [ -f $APPDIR/var/config/liveconfig.ini.php ]; then
        rm -f $APPDIR/var/config/liveconfig.ini.php
    fi
    rm -rf $APPDIR/var/log/*
    rm -rf $APPDIR/var/db/*
    rm -rf $APPDIR/var/mails/*
    rm -rf $APPDIR/var/uploads/*
    touch $APPDIR/var/log/.dummy && chown $APP_USER:$APP_GROUP $APPDIR/var/log/.dummy
    touch $APPDIR/var/db/.dummy && chown $APP_USER:$APP_GROUP $APPDIR/var/db/.dummy
    touch $APPDIR/var/mails/.dummy && chown $APP_USER:$APP_GROUP $APPDIR/var/mails/.dummy
    touch $APPDIR/var/uploads/.dummy && chown $APP_USER:$APP_GROUP $APPDIR/var/uploads/.dummy

    cleanTmp
    setRights
    launchInstaller
}


function launchInstaller() {
    su $APP_USER -c "php $APPDIR/install/installer.php --verbose"
}

function setRights() {
    USER="$1"
    GROUP="$2"

    if [ "$USER" = "" ]; then
        USER="$APP_USER"
    fi

    if [ "$GROUP" = "" ]; then
        GROUP="$APP_GROUP"
    fi

    DIRS="$APPDIR/var/config $APPDIR/var/db $APPDIR/var/log $APPDIR/var/mails $APPDIR/temp/"

    chown -R $USER:$GROUP $DIRS
    chmod -R ug+w $DIRS
    chmod -R o-w $DIRS

}

function launch() {
    if [ ! -f $APPDIR/var/config/profiles.ini.php ]; then
        cp $APPDIR/var/config/profiles.ini.php.dist $APPDIR/var/config/profiles.ini.php
    fi
    if [ -f $APPDIR/var/config/localconfig.ini.php ]; then
        cp $APPDIR/var/config/localconfig.ini.php.dist $APPDIR/var/config/localconfig.ini.php
    fi
    chown -R $APP_USER:$APP_GROUP $APPDIR/var/config/profiles.ini.php $APPDIR/var/config/localconfig.ini.php

    if [ ! -d $APPDIR/vendor ]; then
      /bin/helpers.sh composer-install $APPDIR
    fi

    launchInstaller
    setRights
    cleanTmp
}

case $COMMAND in
    clean_tmp)
        cleanTmp;;
    reset)
        resetApp;;
    launch)
        launch;;
    install)
        launchInstaller;;
    rights)
        setRights;;
    composer_install)
        /bin/helpers.sh composer-install $APPDIR;;
    composer_update)
        /bin/helpers.sh composer-update $APPDIR;;
    *)
        echo "wrong command"
        exit 2
        ;;
esac

