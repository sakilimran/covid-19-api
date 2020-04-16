<?php
namespace App\Config;

class Routes {
    function __construct($app) {
        /* Basic routing for users */
        $app->post('/users', '\App\Controllers\UserController:create');
        $app->post('/users/login', '\App\Controllers\UserController:login');

        /* routing for covit-19 cases */
        $app->get('/all-cases', '\App\Controllers\CountryController:all');
        $app->get('/country-cases', '\App\Controllers\CountryController:countries');
        $app->get('/country-cases/{name}', '\App\Controllers\CountryController:find');

    }
}