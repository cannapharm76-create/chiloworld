<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Medical cannabis GIS map (same endpoints as the original dashboard)
Route::prefix('geo')
    ->withoutMiddleware('throttle:api')
    ->middleware('throttle:600,1')
    ->controller(\App\Http\Controllers\GisController::class)
    ->group(function () {
        Route::get('markers', 'markers');
        Route::get('health-regions', 'healthRegions');
        Route::get('provinces', 'provinces');
        Route::get('summary', 'summary');
        Route::get('autocomplete', 'autocomplete');
        Route::post('track-dashboard-view', 'trackView');
    });
