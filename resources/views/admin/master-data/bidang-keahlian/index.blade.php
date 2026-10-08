@extends('layouts.admin')

@section('judul', 'Bidang Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" judul="Bidang Keahlian" />

    <x-admin.kartu-daftar judul="Daftar Bidang Keahlian" :paginator="$daftarBidangKeahlian">
        <x-slot:aksi>
            @can('create', App\Models\BidangKeahlian::class)
                <a href="{{ route('admin.master-data.bidang-keahlian.create') }}" class="btn btn-success"><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah bidang keahlian</a>
            @endcan
        </x-slot:aksi>

        @if ($daftarBidangKeahlian->isEmpty())
            <x-admin.kosong judul="Belum ada bidang keahlian" />
        @else
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Jumlah program</th>
                            <th class="kolom-aksi">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daftarBidangKeahlian as $bidangKeahlian)
                            <tr>
                                <td class="cell-strong">{{ $bidangKeahlian->nama }}</td>
                                <td>{{ $bidangKeahlian->program_keahlian_count }}</td>
                                <td class="kolom-aksi">
                                    @can('update', $bidangKeahlian)
                                        <a href="{{ route('admin.master-data.bidang-keahlian.edit', $bidangKeahlian) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Ubah</a>
                                    @endcan
                                    @can('delete', $bidangKeahlian)
                                        <x-admin.tombol-hapus
                                            :action="route('admin.master-data.bidang-keahlian.destroy', $bidangKeahlian)"
                                            :konfirmasi="'Hapus bidang keahlian '.$bidangKeahlian->nama.'?'" />
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
