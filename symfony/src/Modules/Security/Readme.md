# Security module

Generate client: php bin/console trikoder:oauth:create-client --redirect-uri=http://localhost:3000/silent.html --redirect-uri=http://localhost:3000/signin.html --grant-type=authorization_code --scope=public.profile --scope=email --scope=openid --public --grant-type=refresh_token

Generate RSA private and public keys

openssl genrsa -out var/oauth/private.key 2048
openssl rsa -in var/oauth/private.key -pubout -out var/oauth/public.key

Assign read permissions to var/oauth

Login workflow (local)

1. login
2. request GET to /authorize
3. request POST to /token
4. (refresh) request POST to /token with grant refresh_token, client id, and refresh token
