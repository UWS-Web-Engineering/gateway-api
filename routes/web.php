<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

// gateway routes
$router->group(['prefix' => 'gateway'], function () use ($router) {
    $router->get('{route:.*}/', "GatewayController@index");
    $router->post('{route:.*}/', "GatewayController@index");
    $router->put('{route:.*}/', "GatewayController@index");
    $router->patch('{route:.*}/', "GatewayController@index");
    $router->delete('{route:.*}/', "GatewayController@index");
    $router->options('{route:.*}/', "GatewayController@index");
});

// Public routes
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('login', 'AuthController@login');

    $router->group(['prefix' => 'setup'], function () use ($router) {
        $router->get('status', 'SetupController@status');
        $router->post('admin', 'SetupController@createAdmin');
    });
});

// Private routes
$router->group(['prefix' => 'api', 'middleware' => ['auth']], function () use ($router) {
    $router->group(['middleware' => ['role:admin']], function () use ($router) {
        $router->post('register', 'AuthController@register');
        $router->get('user/{id}', 'UserController@singleUser');
        $router->delete('user/{id}', 'UserController@removeUser');
        $router->get('users', 'UserController@allUsers');
    });

    $router->get('profile', 'UserController@profile');
    $router->get('logout', 'AuthController@logout');

    $router->get('/services', 'ServiceController@index');
    $router->post('/service', 'ServiceController@create');
    $router->get('/service/{id}', 'ServiceController@show');
    $router->delete('/service/{id}', 'ServiceController@destroy');

    $router->get('/logs', 'LogController@index');
    $router->get('/logs/{serviceId}', 'LogController@logsForService');
});
