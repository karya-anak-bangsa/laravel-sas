@extends('layouts.admin')

@section('judul', 'Orang Tua/Wali')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Orang Tua/Wali">
        @can('create', App\Models\OrangTuaWali::class)
            <a href="{{ route('admin.master-data.orang-tua-wali.create') }}" class="btn btn-primary">Tambah orang tua/wali</a>
        @endcan
    </x-admin.header-halaman>

    <form method="GET" action="{{ route('admin.master-data.orang-tua-wali.index') }}" class="card">
        <div class="card-body">
            <div class="form-row">
                <x-admin.input name="cari" label="Cari nama atau nomor HP" :value="$cari" type="search" />
                <div class="form-group filter-aksi">
                    <button type="submit" class="btn btn-outline">Cari</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        @if ($daftarOrangTuaWali->isEmpty())
            <x-admin.kosong judul="Belum ada data orang tua/wali" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Pekerjaan</th>
                            <th>Murid</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarOrangTuaWali as $orangTuaWali)
                            <tr>
                                <td class="cell-strong">{{ $orangTuaWali->nama }}</td>
                                <td>{{ $orangTuaWali->nomor_hp ?? '—' }}</td>
                                <td>{{ $orangTuaWali->pekerjaan?->label() ?? '—' }}</td>
                                <td>
                                    {{ $orangTuaWali->murid
                                        ->map(fn ($murid) => $murid->nama_lengkap.' ('.strtolower($murid->pivot->hubungan->label()).')')
                                        ->join(', ') ?: '—' }}
                                </td>
                                <td class="kolom-aksi">
                                    @can('update', $orangTuaWali)
                                        <a href="{{ route('admin.master-data.orang-tua-wali.edit', $orangTuaWali) }}" class="btn btn-outline btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $orangTuaWali)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.orang-tua-wali.destroy', $orangTuaWali)"
                                            :konfirmasi="'Hapus data '.$orangTuaWali->nama.'?'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($daftarOrangTuaWali->hasPages())
            <div class="card-footer">
                {{ $daftarOrangTuaWali->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
