<?php

use App\Http\Controllers\Admin\Autentikasi\KeluarController;
use App\Http\Controllers\Admin\Autentikasi\MasukController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('masuk', [MasukController::class, 'create'])->name('masuk');
    Route::post('masuk', [MasukController::class, 'store']);
});

Route::post('keluar', KeluarController::class)->middleware('auth')->name('keluar');
