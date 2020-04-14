# 2019 Novel Coronavirus COVID-19 (2019-nCoV) Live Cases Data PHP REST API

This is a PHP-based user-authenticated REST API repository for the 2019 Novel Coronavirus COVID-19 (2019-nCoV) Live Cases Data 
developed by [S. M. Sakil Imran](mailto:sakilcse7@gmail.com) from a data [repository](https://github.com/CSSEGISandData/COVID-19) provided by the Johns Hopkins University Center for Systems Science and Engineering (JHU CSSE).
This project is developed by extending the following project: 

* [Slim Eloquent REST Boilerplate Application](https://github.com/sakilimran/slim-eloquent-rest-boilerplate)

**Data Provided By:**
* [Johns Hopkins University Center for Systems Science and Engineering (JHU CSSE)](https://github.com/CSSEGISandData/COVID-19)

<b>Data Sources:</b><br>
* World Health Organization (WHO): https://www.who.int/ <br>
* DXY.cn. Pneumonia. 2020. http://3g.dxy.cn/newh5/view/pneumonia.  <br>
* BNO News: https://bnonews.com/index.php/2020/02/the-latest-coronavirus-cases/  <br>
* National Health Commission of the People’s Republic of China (NHC): <br>
 http://www.nhc.gov.cn/xcs/yqtb/list_gzbd.shtml <br>
* China CDC (CCDC): http://weekly.chinacdc.cn/news/TrackingtheEpidemic.htm <br>
* Hong Kong Department of Health: https://www.chp.gov.hk/en/features/102465.html <br>
* Macau Government: https://www.ssm.gov.mo/portal/ <br>
* Taiwan CDC: https://sites.google.com/cdc.gov.tw/2019ncov/taiwan?authuser=0 <br>
* US CDC: https://www.cdc.gov/coronavirus/2019-ncov/index.html <br>
* Government of Canada: https://www.canada.ca/en/public-health/services/diseases/coronavirus.html <br>
* Australia Government Department of Health: https://www.health.gov.au/news/coronavirus-update-at-a-glance <br>
* European Centre for Disease Prevention and Control (ECDC): https://www.ecdc.europa.eu/en/geographical-distribution-2019-ncov-cases 
* Ministry of Health Singapore (MOH): https://www.moh.gov.sg/covid-19
* Italy Ministry of Health: http://www.salute.gov.it/nuovocoronavirus
* 1Point3Arces: https://coronavirus.1point3acres.com/en
* WorldoMeters: https://www.worldometers.info/coronavirus/

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
