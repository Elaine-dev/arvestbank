#!/bin/bash

# scripts/local-copy-files.sh
# This script will rsync the secret.settings.php file from the dev environment to a local environment.
# This script and the files are to be used to keep API variables outside of the github repository.
set -ev

target_env="$1"

export PATH=${COMPOSER_BIN}:$PATH

# Arvest Bank secrets file
if [[ ! -e /var/www/arvestbank/tmp/secrets.settings.php ]]; then
    mkdir -p /var/www/arvestbank/tmp
    touch /var/www/arvestbank/tmp/secrets.settings.php
fi

drush rsync @arvestbank.prod:/mnt/files/arvestbank.prod/secrets.settings.php /var/www/arvestbank/tmp/secrets.settings.php -y
  echo "Creating the secrets file in a tmp directory."
set +v
