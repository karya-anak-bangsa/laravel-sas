@extends('layouts.admin')

@section('judul', 'Satuan Pendidikan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Satuan Pendidikan" />

    <x-admin.kartu-daftar judul="Daftar Satuan Pendidikan" :paginator="$daftarSatuanPendidikan">
        <x-slot:aksi>
            @can('create', App\Models\SatuanPendidikan::class)
                <a href="{{ route('admin.master-data.satuan-pendidikan.create') }}" class="btn btn-success"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah satuan pendidikan</a>
            @endcan
        </x-slot:aksi>

        @if ($daftarSatuanPendidikan->isEmpty())
            <x-admin.kosong judul="Belum ada satuan pendidikan" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Bentuk</th>
                            <th>NPSN</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarSatuanPendidikan as $satuanPendidikan)
                            <tr>
                                <td class="cell-strong">{{ $satuanPendidikan->nama }}</td>
                                <td>{{ $satuanPendidikan->bentuk_pendidikan->label() }}</td>
                                <td>{{ $satuanPendidikan->npsn ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $satuanPendidikan)
                                        <a href="{{ route('admin.master-data.satuan-pendidikan.edit', $satuanPendidikan) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Ubah</a>
                                    @endcan
                                    @can('delete', $satuanPendidikan)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.satuan-pendidikan.destroy', $satuanPendidikan)"
                                            :konfirmasi="'Hapus satuan pendidikan '.$satuanPendidikan->nama.'?'" />
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
