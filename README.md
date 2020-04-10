# Covit-19 Coronavirus Live Cases Data PHP REST API

A PHP-based user-authenticated REST API by [S. M. Sakil Imran], extending the following project:
* [Slim Eloquent REST Boilerplate Application](https://github.com/sakilimran/slim-eloquent-rest-boilerplate)

**Data Source:**
* [Johns Hopkins University Center for Systems Science and Engineering (JHU CSSE)](https://github.com/CSSEGISandData/COVID-19)

## Installation
* `git clone https://github.com/sakilimran/covit19api.git` clone git repo
* `cd covit19api` change working directory to root project folder
* `composer install` install dependencies
* create `Config.php` file from this: `app/Config/Config-default.php` with your configurations
* create `phinx.yml` file from this: `phinx-default.yml` with your configurations
* `vendor/bin/phinx migrate` run initial database migration

## Run
* `cd public` change working directory to public folder and run `php -S localhost:8000` via command line
* or you can use Apache, set virtual host to *public* folder

## Tests
Execute unit tests via PHPUnit by running `./vendor/bin/phpunit ./tests/`.  You can debug tests via XDebug by running `./phpunit-debug ./tests/` (use Git Bash if on Windows).
This boilerplate's test suite features 100% code coverage out-of-the-box (see report in *./test/coverage/*).  To regenerate code coverage HTML report, run `./vendor/bin/phpunit --coverage-html ./tests/coverage/ --whitelist ./app/ ./tests/`

## API Documentation
### HTTP Codes
* `200` API request successful
* `400` API request returned an error
* `401` Unauthorized (access token missing/invalid/expired)
* `404` API endpoint not found
### Authentication
Endpoint | Parameters | Description
--- | --- | ---
`POST /users` | `username` *string* required<br>`password` *string* required | creates a user
`POST /users/login` | `username` *string* required<br>`password` *string* required | generates user access token
### Endpoints
All RESTful API endpoints below require a `Authorization: Bearer xxxx` header set on the HTTP request, *xxxx* is replaced with token generated from the Authentication API above.
