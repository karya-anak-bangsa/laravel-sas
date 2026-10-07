<?php

namespace App\Http\Controllers\Admin\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DasborController extends Controller
{
    public function __invoke(Request $request): View
    {
        return view('admin.dasbor.index', [
            'pengguna' => $request->user()->loadMissing(['peran', 'penugasanAktif']),
            'semesterAktif' => Semester::query()->aktif()->with('tahunAjaran')->first(),
        ]);
    }
}
