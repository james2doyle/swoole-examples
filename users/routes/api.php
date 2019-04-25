<?php

use Illuminate\Http\Request;
use App\User;

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

Route::get('/users', function (Request $request) {
    return User::all()->toArray();
});

Route::get('/big-json', function (Request $request) {
    return json_decode(file_get_contents(resource_path('data/MOCK_DATA.json')));
});
