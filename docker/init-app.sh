#!/bin/sh

TARGET="/var/www/html/"
SETUP="${TARGET}/composer-setup.php"
PHAR="${TARGET}/composer.phar"

EXPECTED_CHECKSUM="$(curl -s -o - https://composer.github.io/installer.sig)"
curl -s -o ${SETUP} https://getcomposer.org/installer;
ACTUAL_CHECKSUM="$(openssl dgst -sha384 ${SETUP} | awk '{print $2}')"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm ${SETUP}
    exit 1
fi

php ${SETUP} --quiet --install-dir="${TARGET}"
RESULT=$?
if [ $RESULT -ne 0 ]; then
  rm ${SETUP}
  exit $RESULT
fi

if [ ! -f "${PHAR}" ]; then
  >&2 echo 'Unable to locate composer.phar'
  exit 1
fi

if [ ! -d "/var/www/html/vendor" ]; then
  if [ "${ENVIRONMENT}" = "dev" ]; then
    php ${PHAR} \
      --working-dir=${TARGET} \
      --no-progress \
      --optimize-autoloader \
      --no-interaction \
      --no-cache \
      install
  else
    php ${PHAR} \
      --working-dir=${TARGET} \
      --no-dev \
      --no-progress \
      --optimize-autoloader \
      --no-interaction \
      --no-cache \
      install
  fi;

  RESULT=$?
  if [ $RESULT -ne 0 ]; then
    >&2 echo 'ERROR: Composer install failed'
    rm ${PHAR}
    exit 1
  fi
fi;

echo 'All done';
