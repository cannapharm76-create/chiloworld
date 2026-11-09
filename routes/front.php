<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [HomeController::class, 'products'])->name('products');
Route::get('/brands', [HomeController::class, 'brands'])->name('brands');
Route::get('/reviews', [HomeController::class, 'reviews'])->name('reviews');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/leaderboard', [HomeController::class, 'leaderboard'])->name('leaderboard');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');
Route::get('/strain-index', [HomeController::class, 'strainIndex'])->name('strain-index');
Route::redirect('/clones', '/strain-index')->name('clones');
Route::get('/cannabis-map', [\App\Http\Controllers\GisController::class, 'page'])->name('cannabis-map');

// Google login (Laravel Socialite)
Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('google.callback');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// account pages (GET shows the page, POST handles the form)
Route::match(['get', 'post'], '/login', [HomeController::class, 'login'])->name('login');
Route::match(['get', 'post'], '/signup', [HomeController::class, 'signup'])->name('signup');
Route::match(['get', 'post'], '/forgot-password', [HomeController::class, 'forgotPassword'])->name('forgot-password');
Route::match(['get', 'post'], '/forgot-username', [HomeController::class, 'forgotUsername'])->name('forgot-username');
Route::match(['get', 'post'], '/redeem', [HomeController::class, 'redeem'])->name('redeem');

// strain detail: /strains/{brand}/{strain}
Route::get('/strains/{brand}/{strain}', [HomeController::class, 'strain'])->name('strain');

// brand landing pages: /superdope, /tedsbudz, /omakase ...
Route::get('/{brand}', [HomeController::class, 'brand'])
    ->whereIn('brand', HomeController::BRANDS)
    ->name('brand');
