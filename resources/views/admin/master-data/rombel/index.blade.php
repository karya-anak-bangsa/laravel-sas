@extends('layouts.admin')

@section('judul', 'Rombel')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Rombongan Belajar (Rombel)">
        @can('create', App\Models\Rombel::class)
            <a href="{{ route('admin.master-data.rombel.create') }}" class="btn btn-primary">Tambah rombel</a>
        @endcan
    </x-admin.header-halaman>

    <form method="GET" action="{{ route('admin.master-data.rombel.index') }}" class="card">
        <div class="card-body">
            <div class="form-row cols-3">
                <x-admin.select name="tahun_ajaran" label="Tahun ajaran" kosong="Semua tahun ajaran"
                    :pilihan="$pilihanTahunAjaran" :terpilih="$idTahunAjaran" />
                <x-admin.select name="satuan_pendidikan" label="Satuan pendidikan" kosong="Semua satuan pendidikan"
                    :pilihan="$pilihanSatuanPendidikan" :terpilih="$idSatuanPendidikan" />
                <div class="form-group filter-aksi">
                    <button type="submit" class="btn btn-outline">Terapkan</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        @if ($daftarRombel->isEmpty())
            <x-admin.kosong judul="Belum ada rombel">
                Tidak ada rombel yang sesuai dengan saringan.
            </x-admin.kosong>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Tingkat</th>
                            <th>Satuan pendidikan</th>
                            <th>Konsentrasi keahlian</th>
                            <th>Tahun ajaran</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarRombel as $rombel)
                            <tr>
                                <td class="cell-strong">{{ $rombel->nama }}</td>
                                <td>{{ $rombel->tingkat->label() }}</td>
                                <td>{{ $rombel->satuanPendidikan?->nama ?? '—' }}</td>
                                <td>{{ $rombel->konsentrasiKeahlian?->nama ?? '—' }}</td>
                                <td>{{ $rombel->tahunAjaran?->nama ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $rombel)
                                        <a href="{{ route('admin.master-data.rombel.edit', $rombel) }}" class="btn btn-outline btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $rombel)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.rombel.destroy', $rombel)"
                                            :konfirmasi="'Hapus rombel '.$rombel->nama.'?'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($daftarRombel->hasPages())
            <div class="card-footer">
                {{ $daftarRombel->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
