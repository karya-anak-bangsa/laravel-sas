@extends('layouts.admin')

@section('konten')
    <x-admin.header-halaman pretitle="Sistem Akademik Sekolah" judul="Dasbor" />

    <div class="card">
        <div class="card-body">
            <p>Selamat datang, <strong>{{ $pengguna->email }}</strong>.</p>
            <p>Peran Anda: {{ $pengguna->kodePeran()->map(fn ($kode) => $kode->label())->join(', ') }}.</p>
            <p>
                @if ($semesterAktif)
                    Semester aktif: <strong>{{ $semesterAktif->namaLengkap() }}</strong>
                    ({{ $semesterAktif->tanggal_mulai->translatedFormat('j F Y') }} – {{ $semesterAktif->tanggal_selesai->translatedFormat('j F Y') }}).
                @else
                    Belum ada semester aktif.
                    @can('viewAny', App\Models\TahunAjaran::class)
                        <a href="{{ route('admin.master-data.tahun-ajaran.index') }}">Atur di Tahun Ajaran</a>.
                    @endcan
                @endif
            </p>
        </div>
    </div>
@endsection
