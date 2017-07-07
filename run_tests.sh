#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
REQUIRED_PHP_VERSION="7.0"
PHP_BIN=""

if [ ! -f $DIR/vendor/bin/phpunit ]; then
    echo "PHPUnit is not installed in vendor directory.";
    echo "You may need to run a composer install or update";
    exit 1
fi

if [ -f /usr/bin/php7.0 ]; then
    PHP_BIN="/usr/bin/php7.0"
elif [ -f /usr/bin/php7.1 ]; then
    PHP_BIN="/usr/bin/php7.1"
else
    PHP_VERSION=$(php -v | head -n 1 | grep --only-matching -i --perl-regexp "PHP\s+\d\.\\d+" | grep --only-matching -i --perl-regexp "\d\.\\d+");
    if [ $(echo " $PHP_VERSION >= $REQUIRED_PHP_VERSION" | bc) -eq 1 ]; then
        PHP_BIN="php"
    else
        echo "PHP Required Version is at least 7.0 to run these tests";
        exit 1
    fi
fi

$PHP_BIN $DIR/vendor/bin/phpunit --coverage-text=tests/coverage/report.txt --coverage-html tests/coverage/html -c $DIR/tests/phpunit.xml
