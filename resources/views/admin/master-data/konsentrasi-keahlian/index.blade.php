@extends('layouts.admin')

@section('judul', 'Konsentrasi Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" judul="Konsentrasi Keahlian">
        @can('create', App\Models\KonsentrasiKeahlian::class)
            <a href="{{ route('admin.master-data.konsentrasi-keahlian.create') }}" class="btn btn-primary">Tambah konsentrasi keahlian</a>
        @endcan
    </x-admin.header-halaman>

    <div class="card">
        @if ($daftarKonsentrasiKeahlian->isEmpty())
            <x-admin.kosong judul="Belum ada konsentrasi keahlian" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Singkatan</th>
                            <th>Program keahlian</th>
                            <th>Bidang keahlian</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarKonsentrasiKeahlian as $konsentrasiKeahlian)
                            <tr>
                                <td class="cell-strong">{{ $konsentrasiKeahlian->nama }}</td>
                                <td>{{ $konsentrasiKeahlian->singkatan }}</td>
                                <td>{{ $konsentrasiKeahlian->programKeahlian?->nama ?? '—' }}</td>
                                <td>{{ $konsentrasiKeahlian->programKeahlian?->bidangKeahlian?->nama ?? '—' }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $konsentrasiKeahlian)
                                        <a href="{{ route('admin.master-data.konsentrasi-keahlian.edit', $konsentrasiKeahlian) }}" class="btn btn-outline btn-sm">Ubah</a>
                                    @endcan
                                    @can('delete', $konsentrasiKeahlian)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.konsentrasi-keahlian.destroy', $konsentrasiKeahlian)"
                                            :konfirmasi="'Hapus konsentrasi keahlian '.$konsentrasiKeahlian->nama.'?'" />
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($daftarKonsentrasiKeahlian->hasPages())
            <div class="card-footer">
                {{ $daftarKonsentrasiKeahlian->links('layouts.partials.admin-paginasi') }}
            </div>
        @endif
    </div>
@endsection
