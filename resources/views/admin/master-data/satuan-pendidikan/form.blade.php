@extends('layouts.admin')

@php($sedangMengubah = $satuanPendidikan->exists)

@section('judul', $sedangMengubah ? 'Ubah Satuan Pendidikan' : 'Tambah Satuan Pendidikan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Satuan Pendidikan' : 'Tambah Satuan Pendidikan'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.satuan-pendidikan.update', $satuanPendidikan)
            : route('admin.master-data.satuan-pendidikan.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <x-admin.input name="nama" label="Nama satuan pendidikan" :value="$satuanPendidikan->nama" required maxlength="100" />

            <div class="form-row">
                <x-admin.select name="bentuk_pendidikan" label="Bentuk pendidikan" required
                    :pilihan="collect($pilihanBentukPendidikan)->mapWithKeys(fn ($bentuk) => [$bentuk->value => $bentuk->label()])"
                    :terpilih="$satuanPendidikan->bentuk_pendidikan" />

                <x-admin.input name="npsn" label="NPSN" :value="$satuanPendidikan->npsn"
                    inputmode="numeric" maxlength="8" bantuan="8 digit, boleh dikosongkan." />
            </div>

            <x-admin.textarea name="alamat" label="Alamat" :value="$satuanPendidikan->alamat" />
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.master-data.satuan-pendidikan.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
@endsection
