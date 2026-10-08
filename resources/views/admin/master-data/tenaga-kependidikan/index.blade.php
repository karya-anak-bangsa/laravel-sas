@extends('layouts.admin')

@section('judul', 'Tenaga Kependidikan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Tenaga Kependidikan" />

    <x-admin.kartu-daftar judul="Daftar Tenaga Kependidikan" :paginator="$daftarTenagaKependidikan">
        <x-slot:aksi>
            @can('create', App\Models\TenagaKependidikan::class)
                <a href="{{ route('admin.master-data.tenaga-kependidikan.create') }}" class="btn btn-success">Tambah tenaga kependidikan</a>
            @endcan
        </x-slot:aksi>

        <x-slot:pencarian>
            <form method="GET" action="{{ route('admin.master-data.tenaga-kependidikan.index') }}">
                <div class="form-row">
                    <x-admin.input name="cari" label="Cari nama atau NIK" :value="$cari" type="search" />
                    <div class="form-group filter-aksi">
                        <button type="submit" class="btn btn-outline">Cari</button>
                    </div>
                </div>
            </form>
        </x-slot:pencarian>

        @if ($daftarTenagaKependidikan->isEmpty())
            <x-admin.kosong judul="Belum ada data tenaga kependidikan" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama lengkap</th>
                            <th>NIK</th>
                            <th>Jenis kelamin</th>
                            <th>Kecamatan</th>
                            <th>Akun</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarTenagaKependidikan as $tenagaKependidikan)
                            <tr>
                                <td class="cell-strong">{{ $tenagaKependidikan->nama_lengkap }}</td>
                                <td>{{ $tenagaKependidikan->nik ?? '—' }}</td>
                                <td>{{ $tenagaKependidikan->jenis_kelamin->label() }}</td>
                                <td>{{ $tenagaKependidikan->kecamatan ?? '—' }}</td>
                                <td>{{ $tenagaKependidikan->pengguna?->email ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $tenagaKependidikan)
                                        <a href="{{ route('admin.master-data.tenaga-kependidikan.edit', $tenagaKependidikan) }}" class="btn btn-warning btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $tenagaKependidikan)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.tenaga-kependidikan.destroy', $tenagaKependidikan)"
                                            :konfirmasi="'Hapus data tenaga kependidikan '.$tenagaKependidikan->nama_lengkap.'?'" />
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
