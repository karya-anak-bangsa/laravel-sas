@extends('layouts.admin')

@php($sedangMengubah = $programKeahlian->exists)

@section('judul', $sedangMengubah ? 'Ubah Program Keahlian' : 'Tambah Program Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" :judul="$sedangMengubah ? 'Ubah Program Keahlian' : 'Tambah Program Keahlian'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.program-keahlian.update', $programKeahlian)
            : route('admin.master-data.program-keahlian.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <x-admin.select name="id_bidang_keahlian" label="Bidang keahlian" required
                :pilihan="$pilihanBidangKeahlian" :terpilih="$programKeahlian->id_bidang_keahlian" />

            <x-admin.input name="nama" label="Nama program keahlian" :value="$programKeahlian->nama" required maxlength="150" />
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('admin.master-data.program-keahlian.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
