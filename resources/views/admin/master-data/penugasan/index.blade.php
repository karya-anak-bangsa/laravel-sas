@extends('layouts.admin')

@section('judul', 'Penugasan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Penugasan">
        @can('create', App\Models\Penugasan::class)
            <a href="{{ route('admin.master-data.penugasan.create', array_filter(['tahun_ajaran' => $saringan['tahun_ajaran']])) }}" class="btn btn-primary">Tambah penugasan</a>
        @endcan
    </x-admin.header-halaman>

    <form method="GET" action="{{ route('admin.master-data.penugasan.index') }}" class="card">
        <div class="card-body">
            <div class="form-row cols-3">
                <x-admin.select name="tahun_ajaran" label="Tahun ajaran" kosong="Semua tahun ajaran"
                    :pilihan="$pilihanTahunAjaran" :terpilih="$saringan['tahun_ajaran']" />
                <x-admin.select name="peran" label="Peran" kosong="Semua peran"
                    :pilihan="$pilihanPeran" :terpilih="$saringan['peran']" />
                <div class="form-group filter-aksi">
                    <button type="submit" class="btn btn-outline">Terapkan</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
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
                                        <a href="{{ route('admin.master-data.penugasan.edit', $penugasan) }}" class="btn btn-outline btn-sm">Ubah</a>
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

        @if ($daftarPenugasan->hasPages())
            <div class="card-footer">
                {{ $daftarPenugasan->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
