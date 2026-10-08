@extends('layouts.admin')

@section('judul', 'Tahun Ajaran')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" judul="Tahun Ajaran" />

    <x-admin.kartu-daftar judul="Daftar Tahun Ajaran" :paginator="$daftarTahunAjaran">
        <x-slot:aksi>
            @can('create', App\Models\TahunAjaran::class)
                <a href="{{ route('admin.master-data.tahun-ajaran.create') }}" class="btn btn-success"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah tahun ajaran</a>
            @endcan
        </x-slot:aksi>

        @if ($daftarTahunAjaran->isEmpty())
            <x-admin.kosong judul="Belum ada tahun ajaran" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tahun ajaran</th>
                            <th>Semester ganjil</th>
                            <th>Semester genap</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarTahunAjaran as $tahunAjaran)
                            <tr>
                                <td class="cell-strong">{{ $tahunAjaran->nama }}</td>
                                @foreach ([$tahunAjaran->semesterGanjil, $tahunAjaran->semesterGenap] as $semester)
                                    <td>
                                        @if ($semester)
                                            <div>{{ $semester->tanggal_mulai->translatedFormat('j M Y') }} – {{ $semester->tanggal_selesai->translatedFormat('j M Y') }}</div>
                                            @if ($semester->aktif)
                                                <span class="status status-green">Aktif</span>
                                            @else
                                                @can('update', $tahunAjaran)
                                                    <form method="POST" action="{{ route('admin.master-data.semester-aktif.update', $semester) }}" class="form-inline"
                                                        data-konfirmasi="Jadikan semester {{ $semester->jenis->label() }} {{ $tahunAjaran->nama }} sebagai semester aktif?">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-ghost btn-sm"><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Aktifkan</button>
                                                    </form>
                                                @endcan
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                @endforeach
                                <td class="kolom-aksi">
                                    @can('update', $tahunAjaran)
                                        <a href="{{ route('admin.master-data.tahun-ajaran.edit', $tahunAjaran) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Ubah</a>
                                    @endcan
                                    @can('delete', $tahunAjaran)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.tahun-ajaran.destroy', $tahunAjaran)"
                                            :konfirmasi="'Hapus tahun ajaran '.$tahunAjaran->nama.'?'" />
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
