@extends('layouts.admin')

@php($sedangMengubah = $konsentrasiKeahlian->exists)

@section('judul', $sedangMengubah ? 'Ubah Konsentrasi Keahlian' : 'Tambah Konsentrasi Keahlian')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data · Spektrum Keahlian" :judul="$sedangMengubah ? 'Ubah Konsentrasi Keahlian' : 'Tambah Konsentrasi Keahlian'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.konsentrasi-keahlian.update', $konsentrasiKeahlian)
            : route('admin.master-data.konsentrasi-keahlian.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <x-admin.select name="id_program_keahlian" label="Program keahlian" required
                :pilihan="$pilihanProgramKeahlian" :terpilih="$konsentrasiKeahlian->id_program_keahlian" />

            <div class="form-row">
                <x-admin.input name="nama" label="Nama konsentrasi keahlian" :value="$konsentrasiKeahlian->nama" required maxlength="150" />
                <x-admin.input name="singkatan" label="Singkatan" :value="$konsentrasiKeahlian->singkatan" required maxlength="10"
                    bantuan="Huruf/angka tanpa spasi, mis. RPL." />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="{{ route('admin.master-data.konsentrasi-keahlian.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection
