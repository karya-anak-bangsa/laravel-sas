<?php

use App\Http\Controllers\Admin\MasterData\AnggotaRombelController;
use App\Http\Controllers\Admin\MasterData\BidangKeahlianController;
use App\Http\Controllers\Admin\MasterData\KonsentrasiKeahlianController;
use App\Http\Controllers\Admin\MasterData\MuridController;
use App\Http\Controllers\Admin\MasterData\MuridOrangTuaController;
use App\Http\Controllers\Admin\MasterData\OrangTuaWaliController;
use App\Http\Controllers\Admin\MasterData\PenugasanController;
use App\Http\Controllers\Admin\MasterData\ProgramKeahlianController;
use App\Http\Controllers\Admin\MasterData\RombelController;
use App\Http\Controllers\Admin\MasterData\SatuanPendidikanController;
use App\Http\Controllers\Admin\MasterData\SemesterAktifController;
use App\Http\Controllers\Admin\MasterData\TahunAjaranController;
use App\Http\Controllers\Admin\MasterData\TenagaKependidikanController;
use App\Http\Controllers\Admin\MasterData\TenagaPendidikController;
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

    Route::get('rombel/{rombel}/anggota', [AnggotaRombelController::class, 'index'])->name('rombel.anggota.index');
    Route::post('rombel/{rombel}/anggota', [AnggotaRombelController::class, 'store'])->name('rombel.anggota.store');
    Route::delete('rombel/{rombel}/anggota/{murid}', [AnggotaRombelController::class, 'destroy'])->name('rombel.anggota.destroy');

    Route::resource('tenaga-pendidik', TenagaPendidikController::class)
        ->except('show')
        ->parameters(['tenaga-pendidik' => 'tenagaPendidik']);

    Route::resource('penugasan', PenugasanController::class)->except('show');

    Route::resource('tenaga-kependidikan', TenagaKependidikanController::class)
        ->except('show')
        ->parameters(['tenaga-kependidikan' => 'tenagaKependidikan']);

    Route::resource('murid', MuridController::class)->except('show');

    Route::resource('orang-tua-wali', OrangTuaWaliController::class)
        ->except('show')
        ->parameters(['orang-tua-wali' => 'orangTuaWali']);

    Route::post('murid/{murid}/orang-tua-wali', [MuridOrangTuaController::class, 'store'])
        ->name('murid.orang-tua-wali.store');
    Route::delete('murid/{murid}/orang-tua-wali/{orangTuaWali}', [MuridOrangTuaController::class, 'destroy'])
        ->name('murid.orang-tua-wali.destroy');
});
