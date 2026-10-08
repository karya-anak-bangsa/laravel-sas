@extends('layouts.admin')

@php($sedangMengubah = $pengguna->exists)

@section('judul', $sedangMengubah ? 'Ubah Pengguna' : 'Tambah Pengguna')

@section('konten')
    <x-admin.header-halaman pretitle="Pengaturan" :judul="$sedangMengubah ? 'Ubah Pengguna' : 'Tambah Pengguna'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah ? route('admin.pengguna.update', $pengguna) : route('admin.pengguna.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <h2 class="card-title">Akun</h2>
            <div class="form-row">
                <x-admin.input type="email" name="email" label="Email" :value="$pengguna->email" required maxlength="255"
                    autocomplete="off" autocapitalize="none" bantuan="Dipakai untuk masuk." />
            </div>
            <div class="form-row">
                <x-admin.input type="password" name="password" :label="$sedangMengubah ? 'Password baru' : 'Password'"
                    :required="! $sedangMengubah" autocomplete="new-password"
                    :bantuan="$sedangMengubah ? 'Kosongkan jika tidak ingin mengganti password.' : 'Minimal 8 karakter.'" />
                <x-admin.input type="password" name="password_confirmation" label="Ulangi password" autocomplete="new-password" />
            </div>

            <div class="form-group">
                <input type="hidden" name="aktif" value="0">
                <label class="form-check">
                    <input type="checkbox" name="aktif" value="1" @checked(old('aktif', $pengguna->aktif))> Akun aktif (dapat masuk)
                </label>
                @error('aktif')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <h2 class="card-title">Peran tetap</h2>
            <div class="form-group">
                @foreach ($pilihanPeran as $kode)
                    <label class="form-check">
                        <input type="checkbox" name="peran[]" value="{{ $kode->value }}" @checked(in_array($kode->value, $peranTerpilih, true))>
                        {{ $kode->label() }}
                    </label>
                @endforeach
                <div class="form-hint">
                    Kepala/Wakil Kepala Sekolah, Ketua Jurusan, dan Wali Kelas diatur lewat menu Penugasan.
                </div>
                @error('peran')
                    <div class="form-error">{{ $message }}</div>
                @enderror
                @error('peran.*')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            <h2 class="card-title">Data pribadi</h2>
            <div class="form-row">
                <x-admin.select name="id_tenaga_pendidik" label="Tautkan ke data tenaga pendidik" kosong="— Tidak ditautkan —"
                    :pilihan="$pilihanTenagaPendidik" :terpilih="$pengguna->tenagaPendidik?->id_tenaga_pendidik" />
                <x-admin.select name="id_tenaga_kependidikan" label="Tautkan ke data tenaga kependidikan" kosong="— Tidak ditautkan —"
                    :pilihan="$pilihanTenagaKependidikan" :terpilih="$pengguna->tenagaKependidikan?->id_tenaga_kependidikan" />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan</button>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Batal</a>
        </div>
    </form>
@endsection
