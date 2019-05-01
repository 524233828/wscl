<?php

use Illuminate\Routing\Router;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\Route;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('/users', 'UserController');
    $router->resource('/table', 'TableController');
//    $router->resource('/demo', 'DemoController');
//    $router->resource('/card', 'CardController');
//    $router->resource('/chapter', 'ChapterController');
//    $router->resource('/user_cards', 'UserCardController');
//    $router->resource('/callable_function_type', 'CallableFunctionTypeController');
//    $router->resource('/callable_function', 'CallableFunctionController');
//    $router->resource('/field_type', 'FieldTypeController');
//    $router->resource('/admin_dashboard', 'AdminDashboardController');
    $router->resource('/dash', 'DashBoardController');
    $router->post('/dash/{key}', 'DashBoardController@create');
    $router->get('/dash/{key}/edit', 'DashBoardController@edit');

    $router->resource('/apps', 'AppController');

    $router->resource('/menus', 'MenuController');



});
