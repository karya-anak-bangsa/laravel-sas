@extends('layouts.admin')

@section('judul', 'Pengguna')

@section('konten')
    <x-admin.header-halaman pretitle="Pengaturan" judul="Pengguna" />

    <x-admin.kartu-daftar judul="Daftar Pengguna" :paginator="$daftarPengguna">
        <x-slot:aksi>
            @can('create', App\Models\Pengguna::class)
                <a href="{{ route('admin.pengguna.create') }}" class="btn btn-success"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah pengguna</a>
            @endcan
        </x-slot:aksi>

        <x-slot:pencarian>
            <form method="GET" action="{{ route('admin.pengguna.index') }}">
                <div class="form-row">
                    <x-admin.input name="cari" label="Cari email" :value="$cari" type="search" />
                    <div class="form-group filter-aksi">
                        <button type="submit" class="btn btn-outline"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Cari</button>
                    </div>
                </div>
            </form>
        </x-slot:pencarian>

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
                                        <a href="{{ route('admin.pengguna.edit', $pengguna) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Ubah</a>
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
    </x-admin.kartu-daftar>
@endsection
