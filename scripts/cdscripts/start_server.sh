#!/bin/bash
service nginx start
service php-fpm start

if [ -f "/etc/supervisord.conf" ]; then
    service supervisord start
fi