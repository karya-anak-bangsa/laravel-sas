@extends('layouts.admin')

@section('judul', 'Murid')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Murid" />

    <x-admin.kartu-daftar judul="Daftar Murid" :paginator="$daftarMurid">
        <x-slot:aksi>
            @can('create', App\Models\Murid::class)
                <a href="{{ route('admin.master-data.murid.create') }}" class="btn btn-success">Tambah murid</a>
            @endcan
        </x-slot:aksi>

        <x-slot:pencarian>
            <form method="GET" action="{{ route('admin.master-data.murid.index') }}">
                <div class="form-row">
                    <x-admin.input name="cari" label="Cari nama, NISN, atau NIK" :value="$cari" type="search" />
                    <div class="form-group filter-aksi">
                        <button type="submit" class="btn btn-outline">Cari</button>
                    </div>
                </div>
            </form>
        </x-slot:pencarian>

        @if ($daftarMurid->isEmpty())
            <x-admin.kosong judul="Belum ada data murid" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama lengkap</th>
                            <th>NISN</th>
                            <th>Jenis kelamin</th>
                            <th>Tanggal lahir</th>
                            <th>Rombel (tahun ajaran aktif)</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarMurid as $murid)
                            <tr>
                                <td class="cell-strong">{{ $murid->nama_lengkap }}</td>
                                <td>{{ $murid->nisn ?? '—' }}</td>
                                <td>{{ $murid->jenis_kelamin->label() }}</td>
                                <td>{{ $murid->tanggal_lahir->translatedFormat('j F Y') }}</td>
                                <td>{{ $murid->rombelAktif->first()?->nama ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $murid)
                                        <a href="{{ route('admin.master-data.murid.edit', $murid) }}" class="btn btn-warning btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $murid)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.murid.destroy', $murid)"
                                            :konfirmasi="'Hapus data murid '.$murid->nama_lengkap.'?'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-admin.kartu-daftar>
@endsection
