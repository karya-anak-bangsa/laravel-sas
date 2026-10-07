@extends('layouts.admin')

@php($sedangMengubah = $tenagaPendidik->exists)

@section('judul', $sedangMengubah ? 'Ubah Tenaga Pendidik' : 'Tambah Tenaga Pendidik')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Tenaga Pendidik' : 'Tambah Tenaga Pendidik'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.tenaga-pendidik.update', $tenagaPendidik)
            : route('admin.master-data.tenaga-pendidik.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <h2 class="card-title">Identitas</h2>
            <div class="form-row">
                <x-admin.input name="nama_lengkap" label="Nama lengkap" :value="$tenagaPendidik->nama_lengkap" required maxlength="150" />
                <x-admin.input name="nuptk" label="NUPTK" :value="$tenagaPendidik->nuptk"
                    inputmode="numeric" maxlength="16" bantuan="16 digit, boleh dikosongkan." />
            </div>
            <div class="form-row cols-3">
                <x-admin.input name="tempat_lahir" label="Tempat lahir" :value="$tenagaPendidik->tempat_lahir" maxlength="100" />
                <x-admin.input type="date" name="tanggal_lahir" label="Tanggal lahir"
                    :value="$tenagaPendidik->tanggal_lahir?->format('Y-m-d')" />
                <x-admin.select name="pendidikan_terakhir" label="Pendidikan terakhir" required
                    :pilihan="$pilihanPendidikan" :terpilih="$tenagaPendidik->pendidikan_terakhir" />
            </div>

            <h2 class="card-title">Kepegawaian</h2>
            <div class="form-row">
                <x-admin.select name="id_satuan_pendidikan" label="Satuan pendidikan" required
                    :pilihan="$pilihanSatuanPendidikan" :terpilih="$tenagaPendidik->id_satuan_pendidikan" />
                <x-admin.select name="status" label="Status" required
                    :pilihan="$pilihanStatus" :terpilih="$tenagaPendidik->status" />
            </div>
            <div class="form-row">
                <x-admin.input type="date" name="tmt_gtt" label="TMT GTT" :value="$tenagaPendidik->tmt_gtt?->format('Y-m-d')" />
                <x-admin.input type="date" name="tmt_gty" label="TMT GTY" :value="$tenagaPendidik->tmt_gty?->format('Y-m-d')" />
            </div>
            <div class="form-row">
                <x-admin.input type="number" name="masa_kerja_tahun" label="Masa kerja (tahun)" required
                    min="0" max="60" :value="$tenagaPendidik->masa_kerja_tahun" />
                <x-admin.input type="number" name="masa_kerja_bulan" label="Masa kerja (bulan)" required
                    min="0" max="11" :value="$tenagaPendidik->masa_kerja_bulan" bantuan="Diisi manual, tidak dihitung dari TMT." />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.master-data.tenaga-pendidik.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
@endsection
