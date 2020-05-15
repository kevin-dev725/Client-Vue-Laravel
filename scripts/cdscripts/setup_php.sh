#!/bin/bash
#
# Copyright 2014 Amazon.com, Inc. or its affiliates. All Rights Reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License").
# You may not use this file except in compliance with the License.
# A copy of the License is located at
#
#  http://aws.amazon.com/apache2.0
#
# or in the "license" file accompanying this file. This file is distributed
# on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
# express or implied. See the License for the specific language governing
# permissions and limitations under the License.

. $(dirname $0)/common_functions.sh

# If not production
msg "Checking for production stack"
get_stack_type

STACKTYPE=$?
if [ $STACKTYPE == 1 ]; then
    # Copy uat into position, env file and private/public keys
    aws s3 cp s3://clientDomain-codedeploy/config/uat.env /usr/share/nginx/html/.env
    chown clientDomain.clientDomain /usr/share/nginx/html/.env

    aws s3 cp s3://clientDomain-codedeploy/config/uat/oauth-private.key /usr/share/nginx/html/storage/oauth-private.key
    chown clientDomain.clientDomain /usr/share/nginx/html/storage/oauth-private.key

    aws s3 cp s3://clientDomain-codedeploy/config/uat/oauth-public.key /usr/share/nginx/html/storage/oauth-public.key
    chown clientDomain.clientDomain /usr/share/nginx/html/storage/oauth-public.key
elif [ $STACKTYPE == 0 ]; then
    # Copy prod into position
    aws s3 cp s3://clientDomain-codedeploy/config/prod.env /usr/share/nginx/html/.env
    chown clientDomain.clientDomain /usr/share/nginx/html/.env

    aws s3 cp s3://clientDomain-codedeploy/config/prod/oauth-private.key /usr/share/nginx/html/storage/oauth-private.key
    chown clientDomain.clientDomain /usr/share/nginx/html/storage/oauth-private.key

    aws s3 cp s3://clientDomain-codedeploy/config/prod/oauth-public.key /usr/share/nginx/html/storage/oauth-public.key
    chown clientDomain.clientDomain /usr/share/nginx/html/storage/oauth-public.key
fi

rm -fr /usr/share/nginx/html/storage/framework
mkdir -p /usr/share/nginx/html/storage/framework/{cache,sessions,testing,views}
chown clientDomain.clientDomain /usr/share/nginx/html/storage/framework/{cache,sessions,testing,views}

cd /usr/share/nginx/html
php artisan down --message="Updating clientDomain!" --retry=15
php artisan migrate --no-interaction --force
php artisan clear-compiled
php artisan cache:clear
php artisan view:cache
php artisan route:cache
php artisan config:cache
php artisan optimize
php artisan up