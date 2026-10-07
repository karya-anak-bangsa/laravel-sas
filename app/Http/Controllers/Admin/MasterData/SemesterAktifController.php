<?php

namespace App\Http\Controllers\Admin\MasterData;

use App\Actions\MasterData\AktifkanSemester;
use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SemesterAktifController extends Controller
{
    /**
     * Jadikan semester ini sebagai semester aktif.
     */
    public function update(Semester $semester, AktifkanSemester $aktifkan): RedirectResponse
    {
        // Semester milik tahun ajaran yang sudah dihapus dianggap tidak ada.
        $tahunAjaran = $semester->tahunAjaran ?? abort(404);

        Gate::authorize('update', $tahunAjaran);

        $aktifkan->handle($semester);

        return redirect()->route('admin.master-data.tahun-ajaran.index')
            ->with('status', "Semester {$semester->namaLengkap()} sekarang aktif.");
    }
}
