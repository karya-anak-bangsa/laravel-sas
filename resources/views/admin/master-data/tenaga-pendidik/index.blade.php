@extends('layouts.admin')

@section('judul', 'Tenaga Pendidik')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Tenaga Pendidik">
        @can('create', App\Models\TenagaPendidik::class)
            <a href="{{ route('admin.master-data.tenaga-pendidik.create') }}" class="btn btn-primary">Tambah tenaga pendidik</a>
        @endcan
    </x-admin.header-halaman>

    <form method="GET" action="{{ route('admin.master-data.tenaga-pendidik.index') }}" class="card">
        <div class="card-body">
            <div class="form-row cols-3">
                <x-admin.input name="cari" label="Cari nama atau NUPTK" :value="$saringan['cari']" type="search" />
                <x-admin.select name="satuan_pendidikan" label="Satuan pendidikan" kosong="Semua satuan pendidikan"
                    :pilihan="$pilihanSatuanPendidikan" :terpilih="$saringan['satuan_pendidikan']" />
                <x-admin.select name="status" label="Status" kosong="Semua status"
                    :pilihan="$pilihanStatus" :terpilih="$saringan['status']" />
            </div>
            <button type="submit" class="btn btn-outline">Terapkan</button>
        </div>
    </form>

    <div class="card">
        @if ($daftarTenagaPendidik->isEmpty())
            <x-admin.kosong judul="Belum ada data tenaga pendidik">
                Tidak ada data yang sesuai dengan pencarian atau saringan.
            </x-admin.kosong>
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama lengkap</th>
                            <th>NUPTK</th>
                            <th>Satuan pendidikan</th>
                            <th>Status</th>
                            <th>Pendidikan</th>
                            <th>Masa kerja</th>
                            <th>Akun</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarTenagaPendidik as $tenagaPendidik)
                            <tr>
                                <td class="cell-strong">{{ $tenagaPendidik->nama_lengkap }}</td>
                                <td>{{ $tenagaPendidik->nuptk ?? '—' }}</td>
                                <td>{{ $tenagaPendidik->satuanPendidikan?->nama ?? '—' }}</td>
                                <td>{{ $tenagaPendidik->status->label() }}</td>
                                <td>{{ $tenagaPendidik->pendidikan_terakhir->label() }}</td>
                                <td>{{ $tenagaPendidik->masaKerja() }}</td>
                                <td>{{ $tenagaPendidik->pengguna?->email ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $tenagaPendidik)
                                        <a href="{{ route('admin.master-data.tenaga-pendidik.edit', $tenagaPendidik) }}" class="btn btn-outline btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $tenagaPendidik)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.tenaga-pendidik.destroy', $tenagaPendidik)"
                                            :konfirmasi="'Hapus data tenaga pendidik '.$tenagaPendidik->nama_lengkap.'?'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($daftarTenagaPendidik->hasPages())
            <div class="card-footer">
                {{ $daftarTenagaPendidik->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
