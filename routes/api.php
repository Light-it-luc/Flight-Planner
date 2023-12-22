<?php

use App\Http\Controllers\Api\AirlineController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\FlightController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'prefix' => 'v1/cities',
    'controller' => CityController::class
], static function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/{city}', 'update');
    Route::delete('/{city}', 'destroy');
});

Route::group([
    'prefix' => 'v1/airlines',
    'controller' => AirlineController::class
], static function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::put('/{airline}', 'update');
    Route::delete('/{airline}', 'destroy');
});

Route::group([
    'prefix' => 'v1/flights',
    'controller' => FlightController::class
], static function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::delete('/{flight}', 'destroy');
});
