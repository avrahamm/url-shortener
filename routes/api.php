<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\LinkController;

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

Route::group(['middleware' => ['api_key'], 'as' => 'api.'], function () {
    Route::post('links', [LinkController::class, 'store'])
        ->name('links.store');

    Route::get('/links/{slug}/stats', [LinkController::class, 'stats'])
    ->name('links.stats');

});
