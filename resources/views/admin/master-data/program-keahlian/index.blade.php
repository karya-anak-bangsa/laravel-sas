@extends('layouts.admin')

@section('judul', 'Program Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" judul="Program Keahlian">
        @can('create', App\Models\ProgramKeahlian::class)
            <a href="{{ route('admin.master-data.program-keahlian.create') }}" class="btn btn-primary">Tambah program keahlian</a>
        @endcan
    </x-admin.header-halaman>

    <div class="card">
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
                                        <a href="{{ route('admin.master-data.program-keahlian.edit', $programKeahlian) }}" class="btn btn-outline btn-sm">Ubah</a>
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

        @if ($daftarProgramKeahlian->hasPages())
            <div class="card-footer">
                {{ $daftarProgramKeahlian->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
