#clientDomain
 
##Installation
1. clone repo
2. composer install
3. npm install
4. cp .env.example .env
5. fill .env variables
6. php artisan key:generate
7. php artisan migrate
8. php artisan passport:install

##Env Variables
1. API_CLIENT_ID: password grant oauth_client id in oauth_clients table
1. API_CLIENT_SECRET: password grant oauth_client secret in oauth_clients table

## Stripe
### Creating Stripe Plan
1. go to Billing/Products
2. create a product
3. add a pricing plan
4. use the ID of that pricing plan for ```STRIPE_PLAN_ID``` .env variable

## Quickbooks
### API Gets
1. Go to "Intuit Developer"
2. "My Apps"
3. Create an application to get ClientId and clientSecret

## Google OAuth
1. Go to https://console.cloud.google.com/apis/credentials and create an "OAuth client ID"
2. Add "https://xxx.com/social/google/callback" in "Authorised redirect URIs" Section
3. Use the generated client id and secret for `GOOGLE_ID` and `GOOGLE_SECRET` .env variables.

##Lien Files
Upload the lien files into `lien/files` folder in s3 bucket.
