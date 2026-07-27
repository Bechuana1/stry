<?php

use Illuminate\Support\Facades\Route;

// Route::inertia('/', 'Welcome')->name('home');
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');