@extends('layouts.admin')

@php($sedangMengubah = $orangTuaWali->exists)

@section('judul', $sedangMengubah ? 'Ubah Orang Tua/Wali' : 'Tambah Orang Tua/Wali')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Orang Tua/Wali' : 'Tambah Orang Tua/Wali'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.orang-tua-wali.update', $orangTuaWali)
            : route('admin.master-data.orang-tua-wali.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            @if ($murid)
                <input type="hidden" name="id_murid" value="{{ $murid->id_murid }}">
                <div class="alert alert-info">
                    <div class="alert-body">Data ini akan langsung ditautkan ke murid <strong>{{ $murid->nama_lengkap }}</strong>.</div>
                </div>
                <x-admin.select name="hubungan" label="Hubungan dengan murid" required
                    :pilihan="$pilihanHubungan" :terpilih="$hubungan" />
            @endif

            @if ($sedangMengubah && $orangTuaWali->murid->isNotEmpty())
                <div class="alert alert-info">
                    <div class="alert-body">
                        Tertaut ke: {{ $orangTuaWali->murid->map(fn ($m) => $m->nama_lengkap.' ('.strtolower($m->pivot->hubungan->label()).')')->join(', ') }}.
                        Perubahan berlaku untuk semua murid tersebut.
                    </div>
                </div>
            @endif

            <div class="form-row">
                <x-admin.input name="nama" label="Nama" :value="$orangTuaWali->nama" required maxlength="150" />
                <x-admin.input type="tel" name="nomor_hp" label="Nomor HP (WhatsApp)" :value="$orangTuaWali->nomor_hp"
                    inputmode="tel" maxlength="20" placeholder="081234567890" />
            </div>
            <div class="form-row cols-3">
                <x-admin.select name="pendidikan" label="Pendidikan" :pilihan="$pilihanPendidikan" :terpilih="$orangTuaWali->pendidikan" />
                <x-admin.select name="pekerjaan" label="Pekerjaan" :pilihan="$pilihanPekerjaan" :terpilih="$orangTuaWali->pekerjaan" />
                <x-admin.select name="penghasilan" label="Penghasilan per bulan" :pilihan="$pilihanPenghasilan" :terpilih="$orangTuaWali->penghasilan" />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan</button>
            <a href="{{ $murid ? route('admin.master-data.murid.edit', $murid) : route('admin.master-data.orang-tua-wali.index') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Batal</a>
        </div>
    </form>
@endsection
