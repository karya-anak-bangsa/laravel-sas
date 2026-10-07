@extends('layouts.admin')

@section('konten')
    <div class="page-header">
        <div class="page-header-row">
            <div>
                <div class="page-pretitle">Sistem Akademik Sekolah</div>
                <h1 class="page-title">Dasbor</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p>Selamat datang, <strong>{{ $pengguna->nama_pengguna }}</strong>.</p>
            <p>Peran Anda: {{ $pengguna->peran->map(fn ($peran) => $peran->kode->label())->join(', ') }}.</p>
        </div>
    </div>
@endsection
