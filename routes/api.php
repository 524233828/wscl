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

Route::post("/login", "IndexController@login");
Route::get("/counties", "IndexController@getCounty");
Route::get("/companies", "IndexController@getCompanies");
Route::get("/company_info", "IndexController@getCompanyInfo");
Route::post("/build_info", "IndexController@updateBuildInfo");