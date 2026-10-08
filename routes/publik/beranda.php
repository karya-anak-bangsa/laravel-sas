<?php

use Illuminate\Support\Facades\Route;

// Sementara diarahkan ke halaman masuk sampai company profile (Tahap 2) diputuskan.
Route::get('/', fn () => redirect()->route('admin.masuk'))->name('beranda');
