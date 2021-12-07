#!/bin/bash

set -e
set -x

if [ "$1" == "/usr/bin/supervisord" ]; then
  /bin/appctl.sh launch
fi
