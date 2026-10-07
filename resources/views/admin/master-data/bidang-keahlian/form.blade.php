@extends('layouts.admin')

@php($sedangMengubah = $bidangKeahlian->exists)

@section('judul', $sedangMengubah ? 'Ubah Bidang Keahlian' : 'Tambah Bidang Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" :judul="$sedangMengubah ? 'Ubah Bidang Keahlian' : 'Tambah Bidang Keahlian'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.bidang-keahlian.update', $bidangKeahlian)
            : route('admin.master-data.bidang-keahlian.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <x-admin.input name="nama" label="Nama bidang keahlian" :value="$bidangKeahlian->nama" required maxlength="150" />
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.master-data.bidang-keahlian.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
@endsection
