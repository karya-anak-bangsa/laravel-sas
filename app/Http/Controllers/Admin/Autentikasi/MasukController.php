<?php

namespace App\Http\Controllers\Admin\Autentikasi;

use App\Actions\Autentikasi\AutentikasiPengguna;
use App\Http\Controllers\Controller;
use App\Http\Requests\Autentikasi\MasukRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MasukController extends Controller
{
    public function create(): View
    {
        return view('admin.autentikasi.masuk');
    }

    public function store(MasukRequest $request, AutentikasiPengguna $autentikasi): RedirectResponse
    {
        $autentikasi->handle(
            $request->string('email')->trim()->value(),
            $request->string('password')->value(),
            $request->boolean('ingat'),
            $request->ip(),
        );

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dasbor'));
    }
}
