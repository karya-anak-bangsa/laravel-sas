<?php

use App\Http\Controllers\Admin\MasterData\BidangKeahlianController;
use App\Http\Controllers\Admin\MasterData\KonsentrasiKeahlianController;
use App\Http\Controllers\Admin\MasterData\ProgramKeahlianController;
use App\Http\Controllers\Admin\MasterData\RombelController;
use App\Http\Controllers\Admin\MasterData\SatuanPendidikanController;
use App\Http\Controllers\Admin\MasterData\SemesterAktifController;
use App\Http\Controllers\Admin\MasterData\TahunAjaranController;
use Illuminate\Support\Facades\Route;

Route::prefix('master-data')->name('master-data.')->group(function () {
    Route::resource('satuan-pendidikan', SatuanPendidikanController::class)
        ->except('show')
        ->parameters(['satuan-pendidikan' => 'satuanPendidikan']);

    Route::resource('bidang-keahlian', BidangKeahlianController::class)
        ->except('show')
        ->parameters(['bidang-keahlian' => 'bidangKeahlian']);

    Route::resource('program-keahlian', ProgramKeahlianController::class)
        ->except('show')
        ->parameters(['program-keahlian' => 'programKeahlian']);

    Route::resource('konsentrasi-keahlian', KonsentrasiKeahlianController::class)
        ->except('show')
        ->parameters(['konsentrasi-keahlian' => 'konsentrasiKeahlian']);

    Route::resource('tahun-ajaran', TahunAjaranController::class)
        ->except('show')
        ->parameters(['tahun-ajaran' => 'tahunAjaran']);

    Route::put('semester-aktif/{semester}', [SemesterAktifController::class, 'update'])
        ->name('semester-aktif.update');

    Route::resource('rombel', RombelController::class)->except('show');
});
