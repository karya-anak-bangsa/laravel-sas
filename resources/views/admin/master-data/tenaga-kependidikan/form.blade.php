@extends('layouts.admin')

@php($sedangMengubah = $tenagaKependidikan->exists)

@section('judul', $sedangMengubah ? 'Ubah Tenaga Kependidikan' : 'Tambah Tenaga Kependidikan')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Tenaga Kependidikan' : 'Tambah Tenaga Kependidikan'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.tenaga-kependidikan.update', $tenagaKependidikan)
            : route('admin.master-data.tenaga-kependidikan.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <h2 class="card-title">Identitas (sesuai KTP)</h2>
            <div class="form-row">
                <x-admin.input name="nama_lengkap" label="Nama lengkap" :value="$tenagaKependidikan->nama_lengkap" required maxlength="150" />
                <x-admin.input name="nik" label="NIK" :value="$tenagaKependidikan->nik"
                    inputmode="numeric" maxlength="16" bantuan="16 digit, boleh dikosongkan." />
            </div>
            <div class="form-row">
                <x-admin.input name="tempat_lahir" label="Tempat lahir" :value="$tenagaKependidikan->tempat_lahir" maxlength="100" />
                <x-admin.input type="date" name="tanggal_lahir" label="Tanggal lahir"
                    :value="$tenagaKependidikan->tanggal_lahir?->format('Y-m-d')" />
            </div>
            <div class="form-row">
                <x-admin.select name="jenis_kelamin" label="Jenis kelamin" required
                    :pilihan="$pilihanJenisKelamin" :terpilih="$tenagaKependidikan->jenis_kelamin" />
                <x-admin.select name="agama" label="Agama"
                    :pilihan="$pilihanAgama" :terpilih="$tenagaKependidikan->agama" />
            </div>

            <h2 class="card-title">Alamat</h2>
            <x-admin.input name="alamat" label="Alamat" :value="$tenagaKependidikan->alamat" maxlength="255" />
            <div class="form-row">
                <x-admin.input name="rt" label="RT" :value="$tenagaKependidikan->rt" inputmode="numeric" maxlength="3" />
                <x-admin.input name="rw" label="RW" :value="$tenagaKependidikan->rw" inputmode="numeric" maxlength="3" />
            </div>
            <div class="form-row">
                <x-admin.input name="kelurahan_desa" label="Kelurahan/desa" :value="$tenagaKependidikan->kelurahan_desa" maxlength="100" />
                <x-admin.input name="kecamatan" label="Kecamatan" :value="$tenagaKependidikan->kecamatan" maxlength="100" />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan</button>
            <a href="{{ route('admin.master-data.tenaga-kependidikan.index') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Batal</a>
        </div>
    </form>
@endsection
