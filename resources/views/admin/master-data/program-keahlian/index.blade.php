@extends('layouts.admin')

@section('judul', 'Program Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" judul="Program Keahlian" />

    <x-admin.kartu-daftar judul="Daftar Program Keahlian" :paginator="$daftarProgramKeahlian">
        <x-slot:aksi>
            @can('create', App\Models\ProgramKeahlian::class)
                <a href="{{ route('admin.master-data.program-keahlian.create') }}" class="btn btn-success">Tambah program keahlian</a>
            @endcan
        </x-slot:aksi>

        @if ($daftarProgramKeahlian->isEmpty())
            <x-admin.kosong judul="Belum ada program keahlian" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Bidang keahlian</th>
                            <th>Jumlah konsentrasi</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarProgramKeahlian as $programKeahlian)
                            <tr>
                                <td class="cell-strong">{{ $programKeahlian->nama }}</td>
                                <td>{{ $programKeahlian->bidangKeahlian?->nama ?? '—' }}</td>
                                <td>{{ $programKeahlian->konsentrasi_keahlian_count }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $programKeahlian)
                                        <a href="{{ route('admin.master-data.program-keahlian.edit', $programKeahlian) }}" class="btn btn-warning btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $programKeahlian)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.program-keahlian.destroy', $programKeahlian)"
                                            :konfirmasi="'Hapus program keahlian '.$programKeahlian->nama.'?'" />
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
