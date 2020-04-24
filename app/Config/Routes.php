<?php
namespace App\Config;

class Routes {
    function __construct($app) {
        /* Basic routing for users */
        $app->post('/users', '\App\Controllers\UserController:create');
        $app->post('/users/login', '\App\Controllers\UserController:login');

        /* routing for covid-19 cases */
        $app->get('/all', '\App\Controllers\CountryController:all');
        $app->get('/countries', '\App\Controllers\CountryController:countries');
        $app->get('/countries/{name}', '\App\Controllers\CountryController:find');
        $app->get('/timeline', '\App\Controllers\CountryController:timeline');
        $app->get('/timeline/{country_code}', '\App\Controllers\CountryController:country_timeline');
    }
}