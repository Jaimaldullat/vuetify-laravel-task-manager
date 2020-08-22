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

Route::get('tasks',  ['uses' => 'TaskController@getTasks']);
Route::get('tasks/{id}', ['uses' => 'TaskController@getTask']);
Route::post('tasks', ['uses' => 'TaskController@store']);
Route::delete('tasks/{id}', ['uses' => 'TaskController@destroy']);
Route::put('tasks/{id}', ['uses' => 'TaskController@update']);

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact support'], 404);
});

