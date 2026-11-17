<?php

use Illuminate\Support\Facades\Route;


// Route::middleware(['auth', 'verified'])->group(function () {
    //  Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('/', 'home')->name('home');
    Route::view('mis-perfiles', 'pages.mis-perfiles')->name('mis-perfiles');

// });

require __DIR__.'/settings.php';
