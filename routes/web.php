<?php

use Illuminate\Support\Facades\Route;

// Públicas: no piden cuenta.
Route::view('/', 'home')->name('home');
Route::view('crear', 'crear')->name('crear');
