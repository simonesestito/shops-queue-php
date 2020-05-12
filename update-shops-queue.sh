#!/bin/bash

#
# This script must run on the server
# First, copy it using SCP
# Then, execute it via SSH
#

set -e

APP_DIR=/var/www/shopsqueue/html
TEMP_DIR=`mktemp -d`
REPO_URL=https://github.com/simonesestito/shops-queue-php

rm -rf $APP_DIR/*
git clone --depth 1 $REPO_URL $TEMP_DIR
mv $TEMP_DIR/src/* $APP_DIR
rm -rf $TEMP_DIR
