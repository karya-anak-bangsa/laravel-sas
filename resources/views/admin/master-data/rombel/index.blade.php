@extends('layouts.admin')

@section('judul', 'Rombel')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Rombongan Belajar (Rombel)" />

    <x-admin.kartu-daftar judul="Daftar Rombel" :paginator="$daftarRombel">
        <x-slot:aksi>
            @can('create', App\Models\Rombel::class)
                <a href="{{ route('admin.master-data.rombel.create') }}" class="btn btn-success"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah Data</a>
            @endcan
        </x-slot:aksi>

        <x-slot:pencarian>
            <form method="GET" action="{{ route('admin.master-data.rombel.index') }}">
                <div class="form-row cols-3">
                    <x-admin.select name="tahun_ajaran" label="Tahun ajaran" kosong="Semua tahun ajaran"
                        :pilihan="$pilihanTahunAjaran" :terpilih="$idTahunAjaran" />
                    <x-admin.select name="satuan_pendidikan" label="Satuan pendidikan" kosong="Semua satuan pendidikan"
                        :pilihan="$pilihanSatuanPendidikan" :terpilih="$idSatuanPendidikan" />
                    <div class="form-group filter-aksi">
                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter" aria-hidden="true"></i> Terapkan</button>
                    </div>
                </div>
            </form>
        </x-slot:pencarian>

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
                            <th>Anggota</th>
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
                                <td>{{ $rombel->murid_count }} murid</td>
                                <td>{{ $rombel->tahunAjaran?->nama ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $rombel)
                                        <a href="{{ route('admin.master-data.rombel.anggota.index', $rombel) }}" class="btn btn-outline btn-sm"><i class="fa-solid fa-users" aria-hidden="true"></i> Anggota</a>
                                        <a href="{{ route('admin.master-data.rombel.edit', $rombel) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Ubah</a>
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
    </x-admin.kartu-daftar>
@endsection
