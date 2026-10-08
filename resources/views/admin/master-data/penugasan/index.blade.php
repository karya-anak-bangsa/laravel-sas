@extends('layouts.admin')

@section('judul', 'Penugasan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Penugasan" />

    <x-admin.kartu-daftar judul="Daftar Penugasan" :paginator="$daftarPenugasan">
        <x-slot:aksi>
            @can('create', App\Models\Penugasan::class)
                <a href="{{ route('admin.master-data.penugasan.create', array_filter(['tahun_ajaran' => $saringan['tahun_ajaran']])) }}" class="btn btn-success"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah penugasan</a>
            @endcan
        </x-slot:aksi>

        <x-slot:pencarian>
            <form method="GET" action="{{ route('admin.master-data.penugasan.index') }}">
                <div class="form-row cols-3">
                    <x-admin.select name="tahun_ajaran" label="Tahun ajaran" kosong="Semua tahun ajaran"
                        :pilihan="$pilihanTahunAjaran" :terpilih="$saringan['tahun_ajaran']" />
                    <x-admin.select name="peran" label="Peran" kosong="Semua peran"
                        :pilihan="$pilihanPeran" :terpilih="$saringan['peran']" />
                    <div class="form-group filter-aksi">
                        <button type="submit" class="btn btn-outline"><i class="fa-solid fa-filter" aria-hidden="true"></i> Terapkan</button>
                    </div>
                </div>
            </form>
        </x-slot:pencarian>

        @if ($daftarPenugasan->isEmpty())
            <x-admin.kosong judul="Belum ada penugasan">
                Penugasan menentukan siapa Kepala Sekolah, Wakil Kepala Sekolah, Ketua Jurusan, dan Wali Kelas pada suatu tahun ajaran.
            </x-admin.kosong>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tenaga pendidik</th>
                            <th>Peran</th>
                            <th>Konteks</th>
                            <th>Tahun ajaran</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarPenugasan as $penugasan)
                            <tr>
                                <td class="cell-strong">{{ $penugasan->tenagaPendidik?->nama_lengkap ?? '—' }}</td>
                                <td>{{ $penugasan->peran->kode->label() }}</td>
                                <td>{{ $penugasan->konteks() }}</td>
                                <td>{{ $penugasan->tahunAjaran?->nama ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $penugasan)
                                        <a href="{{ route('admin.master-data.penugasan.edit', $penugasan) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Ubah</a>
                                    @endcan
                                    @can('delete', $penugasan)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.penugasan.destroy', $penugasan)"
                                            konfirmasi="Hapus penugasan ini?" />
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
