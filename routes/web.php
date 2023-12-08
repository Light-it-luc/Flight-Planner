<?php

use App\Http\Controllers\AirlineController;
use App\Http\Controllers\CityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group([
    'prefix' => 'cities',
    'controller' => CityController::class
], function () {
    Route::get('/get', 'index');
    Route::get('/', 'indexView');
    Route::post('/', 'store');
    Route::put('/{city}', 'update');
    Route::delete('/{city}', 'destroy');
});

Route::group([
    'prefix' => 'airlines',
    'controller' => AirlineController::class
], function () {
    Route::get('/get', 'index');
    Route::get('/', 'indexView');
    Route::post('/', 'store');
    Route::put('/{airline}', 'update');
    Route::delete('/{airline}', 'destroy');
});
