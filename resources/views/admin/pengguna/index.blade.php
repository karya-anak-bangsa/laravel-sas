@extends('layouts.admin')

@section('judul', 'Pengguna')

@section('konten')
    <x-admin.header-halaman pretitle="Pengaturan" judul="Pengguna">
        @can('create', App\Models\Pengguna::class)
            <a href="{{ route('admin.pengguna.create') }}" class="btn btn-primary">Tambah pengguna</a>
        @endcan
    </x-admin.header-halaman>

    <form method="GET" action="{{ route('admin.pengguna.index') }}" class="card">
        <div class="card-body">
            <div class="form-row">
                <x-admin.input name="cari" label="Cari email" :value="$cari" type="search" />
                <div class="form-group filter-aksi">
                    <button type="submit" class="btn btn-outline">Cari</button>
                </div>
            </div>
        </div>
    </form>

    <div class="card">
        @if ($daftarPengguna->isEmpty())
            <x-admin.kosong judul="Tidak ada pengguna" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Email</th>
                            <th>Peran tetap</th>
                            <th>Data pribadi</th>
                            <th>Status</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarPengguna as $pengguna)
                            <tr>
                                <td class="cell-strong">{{ $pengguna->email }}</td>
                                <td>{{ $pengguna->peran->map(fn ($peran) => $peran->kode->label())->join(', ') ?: '—' }}</td>
                                <td>{{ $pengguna->tenagaPendidik?->nama_lengkap ?? $pengguna->tenagaKependidikan?->nama_lengkap ?? '—' }}</td>
                                <td>
                                    @if ($pengguna->aktif)
                                        <span class="status status-green">Aktif</span>
                                    @else
                                        <span class="status status-red">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="kolom-aksi">
                                    @can('update', $pengguna)
                                        <a href="{{ route('admin.pengguna.edit', $pengguna) }}" class="btn btn-outline btn-sm">Ubah</a>
                                    @endcan
                                    @if (! $pengguna->is(auth()->user()))
                                        @can('delete', $pengguna)
                                            <x-admin.tombol-hapus
                                                :action="route('admin.pengguna.destroy', $pengguna)"
                                                :konfirmasi="'Hapus akun '.$pengguna->email.'?'" />
                                        @endcan
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($daftarPengguna->hasPages())
            <div class="card-footer">
                {{ $daftarPengguna->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
