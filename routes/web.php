<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
header( "Access-Control-Allow-Origin:*" );
header( "Access-Control-Allow-Methods:POST,GET" );
$local = env('LOCAL_HOST');
$api = app('Dingo\Api\Routing\Router');
$abc = env('LOCAL_HOST');
$api->version('v1', function ($api) {
    //用户认证
    $api->post('register', 'App\Http\Controllers\LoginController@register');
    $api->post('regcode', 'App\Http\Controllers\LoginController@regcode');
    $api->post('login', 'App\Http\Controllers\LoginController@login');
    //吊唁模块
    $api->group(['prefix' => 'condolence'], function ($api) {
        require __DIR__."/condolence.php";
    });
});