<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// php -S localhost:8000 -t public

// 00 Success
// 01 Failed
// 02 Not Found Data

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

$router->post('api/login', 'AuthController@login');

$router->group(['middleware' => 'authmiddleware'], function () use ($router) {
    $router->post('api/change-password', 'AuthController@changePassword');

    $router->get('api/list-user', 'UserController@GetListUser');
    $router->post('api/save-user', 'UserController@SaveUser');
    $router->post('api/edit-user', 'UserController@EditUser');
    $router->post('api/delete-user', 'UserController@DeleteUser');
});
