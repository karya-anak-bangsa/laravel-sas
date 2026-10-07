<?php

use App\Http\Controllers\Admin\MasterData\SatuanPendidikanController;
use Illuminate\Support\Facades\Route;

Route::prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('satuan-pendidikan', SatuanPendidikanController::class)
        ->except('show')
        ->parameters(['satuan-pendidikan' => 'satuanPendidikan']);
});
