<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('/user')->group(function () {
    Route::post('/login', 'LoginController@login');
});

Route::post('login', ['as' => 'login', 'uses' => 'LoginController@login']);

Route::middleware('auth:api')->get('index', 'UnitController@indexApiWialon');
Route::middleware('auth:api')->get('{name}/name', 'UnitController@name');
