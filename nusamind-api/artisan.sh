#!/bin/bash
PHP_INI_SCAN_DIR=/etc/php/8.3/cli/conf.d:/home/daffarizky/php_conf php artisan "$@"
