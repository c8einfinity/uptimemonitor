#!/bin/bash

# Define the path to your PHP script
SCRIPT_FILENAME=/home/uptimemonitor/public_html/bin/tina4service

# Define the PHP-FPM Unix socket path
PHP_FPM_SOCKET=/run/php/php8.3-fpm.sock

# Execute the PHP script via FastCGI
REQUEST_METHOD=GET \
SCRIPT_FILENAME=$SCRIPT_FILENAME \
cgi-fcgi -bind -connect $PHP_FPM_SOCKET
