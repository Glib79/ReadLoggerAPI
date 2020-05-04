# Read Logger API

API for Read Logger project based on Symfony 5 using: doctrine (for connection), authentication JWT token (lexik/jwt-authentication-bundle), phpunit tests, dto + validation, uuid (ramsey/uuid-doctrine), CORS controll (nelmio/cors-bundle)

## Instalation guide:

### Step 1 - prepare project
- git clone project from https://github.com/Glib79/ReadLoggerAPI
- rename `.env.example` file to `.env`
- edit `.env` and setup database credentials (MYSQL_ROOT_PASSWORD, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD), it's for database which will be created in docker container.
- rename `app/.env.example` to `app/.env`
- copy `app/.env` to `app/.env.local`
- edit `app/.env.local` and update line: `DATABASE_URL=mysql://db_user:db_password@mysql:3306/db_name?serverVersion=8` - replace `db_user`, `db_password` and `db_name` with your credentials (should be the same you set up in `.env` file earlier).

### Step 2 - start docker container
- docker-compose build
- docker-compose up -d
- docker-compose exec php composer install
- docker-compose exec php bin/console doctrine:migrations:migrate

Migration create database structure.

### Step 3 - add ssh keys
Application needs ssh keys:

Create directory for ssh keys:
- mkdir app/config/jwt

Generate ssh keys:
- docker-compose exec php openssl genrsa -out config/jwt/private.pem -aes256 4096
- docker-compose exec php openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem

Do not forget to put passphrase you typed during ssh key generation to `app/.env.local` JWT_PASSPHRASE like:
`JWT_PASSPHRASE=your_passphrase`

## Unit tests

To run unit tests use command:
- docker-compose exec php bin/phpunit

## User guide:

**Register**: POST `/auth/register` with data:

    {
	    "email": "test@test.com",
	    "password": "test123"
    }

**Login**: POST `/auth/login_check` with data:

    {
        "username": "test@test.com",
        "password": "test123"
    }

You should receive Bearer token - **use this token to authenticate yourself** in further requests.

**Add Category**: POST `/api/category` with data:

	{
		"name": "Category Name"
	}

**Update category**: PUT `/api/category/{id}` (replace {id} with id category to update) with data:

	{
		"name": "New Category Name"
	}
	
**Categories list**: GET `/api/categories`

**Single category**: GET `/api/category/{id}`  (replace {id} with id category to show)

**Delete category**: DELETE `/api/category/{id}`  (replace {id} with id category to delete)

**Example** curl request (replace: `localhost` in `--url http://localhost/api/category` with your domain and  `your_token` in `--header 'authorization: Bearer your_token'` with token received from `/auth/login_check`):

    curl --request POST \
      --url http://localhost/api/category \
      --header 'authorization: Bearer your_token' \
      --header 'content-type: application/json' \
      --data '{
	    "name": "Category name"
      }'
