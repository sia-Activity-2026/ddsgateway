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

$router->post('/oauth/token', 'AccessTokenController@issueToken');

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['middleware' => 'client.credentials'], function () use ($router) {

    // Api gateway routes for site1 users
    $router->get('/users1', 'User1Controller@index');
    $router->post('/users1', 'User1Controller@add');
    $router->get('/users1/{id}', 'User1Controller@show');
    $router->put('/users1/{id}', 'User1Controller@update');
    $router->delete('/users1/{id}', 'User1Controller@delete');


    // Api gateway routes for site2 users
    $router->get('/users2', 'User2Controller@index');
    $router->post('/users2', 'User2Controller@add');
    $router->get('/users2/{id}', 'User2Controller@show');
    $router->put('/users2/{id}', 'User2Controller@update');
    $router->delete('/users2/{id}', 'User2Controller@delete');

    // Api gateway routes for products (handled by gateway local ProductController)
    $router->get('/products', 'ProductController@index');
    $router->post('/products', 'ProductController@store');
    $router->get('/products/{id}', 'ProductController@show');
    $router->put('/products/{id}', 'ProductController@update');
    $router->delete('/products/{id}', 'ProductController@destroy');
});