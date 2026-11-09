<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| front  -> routes/front.php   (public website)
|
*/

Route::prefix('/')->group(base_path('routes/front.php'));

Route::fallback(function () {
    return response()->view('404', [], 404);
});
