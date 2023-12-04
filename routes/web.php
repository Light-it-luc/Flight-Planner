<?php

use App\Http\Controllers\AirlineController;
use App\Http\Controllers\CityController;
use App\Models\City;
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
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::patch('/{city}', 'update');
    Route::delete('/{city}', 'destroy');
});

Route::group([
    'prefix' => 'airlines',
    'controller' => AirlineController::class
], function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::delete('/{airline}', 'destroy');
    Route::patch('/{airline}', 'update');
});
