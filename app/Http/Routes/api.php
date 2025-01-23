<?php

/**
 * @var $router ReWork\Framework\Http\Router\Router
 */

$router->get('/welcome', 'WelcomeController@index');

$router->get('/posts', 'PostController@get');
$router->get('/posts/{id}', 'PostController@find');
$router->post('/posts', 'PostController@store');
$router->put('/posts/{id}', 'PostController@update');
$router->delete('/posts/{id}', 'PostController@delete');
