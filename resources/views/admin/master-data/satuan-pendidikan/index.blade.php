@extends('layouts.admin')

@section('judul', 'Satuan Pendidikan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Satuan Pendidikan">
        @can('create', App\Models\SatuanPendidikan::class)
            <a href="{{ route('admin.master-data.satuan-pendidikan.create') }}" class="btn btn-success">Tambah satuan pendidikan</a>
        @endcan
    </x-admin.header-halaman>

    <div class="card">
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
                                        <a href="{{ route('admin.master-data.satuan-pendidikan.edit', $satuanPendidikan) }}" class="btn btn-warning btn-sm">Ubah</a>
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

        @if ($daftarSatuanPendidikan->hasPages())
            <div class="card-footer">
                {{ $daftarSatuanPendidikan->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
