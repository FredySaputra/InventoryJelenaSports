<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});
Route::view('/login', 'auth.login')->name('login');
Route::view('/dashboard', 'admin.dashboard');
Route::view('/pelanggans', 'admin.pelanggan');


