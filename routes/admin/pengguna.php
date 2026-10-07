<?php

use App\Http\Controllers\Admin\Pengguna\PenggunaController;
use Illuminate\Support\Facades\Route;

Route::resource('pengguna', PenggunaController::class)->except('show');
