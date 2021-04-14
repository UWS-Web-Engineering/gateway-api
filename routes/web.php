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

// Public routes
$router->post('login', 'AuthController@login');
$router->group(['prefix' => 'setup'], function () use ($router) {
    $router->get('status', 'SetupController@status');
    $router->post('admin', 'SetupController@createAdmin');
});

// Private routes
$router->group(['middleware' => ['auth']], function () use ($router) {
    $router->group(['middleware' => ['role:admin']], function () use ($router) {
        $router->post('register', 'AuthController@register');
        $router->get('user/{id}', 'UserController@singleUser');
        $router->delete('user/{id}', 'UserController@removeUser');
        $router->get('users', 'UserController@allUsers');
    });

    $router->get('profile', 'UserController@profile');
    $router->get('logout', 'AuthController@logout');

    $router->get('/services', 'ServiceController@index');
    $router->get('/services/healths', 'ServiceController@healths');
    $router->post('/service', 'ServiceController@create');
    $router->get('/service/{id}', 'ServiceController@show');
    $router->put('/service/{id}', 'ServiceController@update');
    $router->delete('/service/{id}', 'ServiceController@destroy');
    $router->get('/service/{serviceId}/paths', 'PathController@pathsForService');

    $router->get('/logs', 'LogController@index');
    $router->get('/logs/{serviceId}', 'LogController@logsForService');

    $router->get('/health/requests[/{id}]', 'HealthController@requestCount');
    $router->get('/health/success[/{id}]', 'HealthController@successRate');
    $router->get('/health/responseTime[/{id}]', 'HealthController@avgResponseTime');
    $router->get('/health/chart[/{id}]', 'HealthController@getChartData');
});

$router->get('/{any:.*}', "GatewayController@index");
$router->post('/{any:.*}', "GatewayController@index");
$router->put('/{any:.*}', "GatewayController@index");
$router->patch('/{any:.*}', "GatewayController@index");
$router->delete('/{any:.*}', "GatewayController@index");
$router->options('/{any:.*}', "GatewayController@index");
