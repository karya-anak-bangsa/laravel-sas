<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Route Web
|--------------------------------------------------------------------------
|
| Route setiap modul ditulis di routes/publik/<modul>.php (tanpa login) dan
| routes/admin/<modul>.php (area /admin), lalu didaftarkan di sini.
|
*/

require __DIR__.'/publik/beranda.php';

Route::prefix('admin')->name('admin.')->group(function () {
    require __DIR__.'/admin/autentikasi.php';

    Route::middleware(['auth', 'can:akses-admin'])->group(function () {
        require __DIR__.'/admin/dasbor.php';
        require __DIR__.'/admin/master-data.php';
    });
});
