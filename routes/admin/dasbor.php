<?php

use App\Http\Controllers\Admin\Dasbor\DasborController;
use Illuminate\Support\Facades\Route;

Route::get('/', DasborController::class)->name('dasbor');
