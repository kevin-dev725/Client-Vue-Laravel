#!/bin/sh

chown -R clientDomain.clientDomain /usr/share/nginx/html
find /usr/share/nginx/html -type d -exec chmod 755 {} \;
find /usr/share/nginx/html -type f -exec chmod 644 {} \;
