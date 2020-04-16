# 2019 Novel Coronavirus COVID-19 (2019-nCoV) Live Cases Data PHP REST API

This is a PHP-based user-authenticated REST API repository for the 2019 Novel Coronavirus COVID-19 (2019-nCoV) Live Cases Data 
developed by [S. M. Sakil Imran](mailto:sakilcse7@gmail.com) from [WorldoMeters](https://www.worldometers.info/coronavirus/) data.
This project is developed by extending the following project: 

* [Slim Eloquent REST Boilerplate Application](https://github.com/sakilimran/slim-eloquent-rest-boilerplate)

**Data Source:**
* [WorldoMeters](https://www.worldometers.info/coronavirus/)

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

Endpoint | Parameters | Description | Sample Output
--- | --- | --- | ---
`GET /all-cases` | *n/a* | lists total cases worldwide | `{"success":true,"data":{"title":"World","cases":2164963,"todayCases":78532,"deaths":144313,"todayDeaths":5694,"recovered":546227,"activeCases":1474423,"critical":57062}}`
`GET /country-cases` | *n/a* | lists all cases by country | `{"success":true,"data":[{"country":"USA","cases":667572,"todayCases":19424,"deaths":33903,"todayDeaths":1315,"recovered":57189,"activeCases":576480,"critical":13369,"emoji":"🇺🇸"},{"country":"Spain","cases":182816,"todayCases":2157,"deaths":19130,"todayDeaths":318,"recovered":74797,"activeCases":88889,"critical":7371,"emoji":"🇪🇸"},".............."]}`
`GET /country-cases/{name}` | *n/a* | lists all cases for any specific country | `{"success":true,"data":{"country":"Bangladesh","cases":1572,"todayCases":341,"deaths":60,"todayDeaths":10,"recovered":49,"activeCases":1463,"critical":1,"emoji":"\ud83c\udde7\ud83c\udde9"}}`