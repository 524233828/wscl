<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
//
//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
//
//Route::name("login")->get("/login", function(Request $request){
//    return "welcome";
//});

//Route::get("/chapter","ChapterController@addChapter");
Route::get("/get_card","IndexController@getCard");
Route::get("/wechat/access_token","WechatController@getAccessToken");
Route::post("/wechat/index","WechatController@index");
Route::get("/wechat/index","WechatController@index");
Route::get("/admin/reply","AdminController@reply");

Route::get("/index", "IndexController@index");
