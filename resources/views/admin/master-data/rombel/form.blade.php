@extends('layouts.admin')

@php($sedangMengubah = $rombel->exists)

@section('judul', $sedangMengubah ? 'Ubah Rombel' : 'Tambah Rombel')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Rombel' : 'Tambah Rombel'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.rombel.update', $rombel)
            : route('admin.master-data.rombel.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <div class="form-row">
                <x-admin.select name="id_tahun_ajaran" label="Tahun ajaran" required
                    :pilihan="$pilihanTahunAjaran" :terpilih="$rombel->id_tahun_ajaran" />
                <x-admin.select name="id_satuan_pendidikan" label="Satuan pendidikan" required
                    :pilihan="$pilihanSatuanPendidikan" :terpilih="$rombel->id_satuan_pendidikan" />
            </div>

            <div class="form-row">
                <x-admin.select name="tingkat" label="Tingkat" required
                    :pilihan="$pilihanTingkat" :terpilih="$rombel->tingkat" />
                <x-admin.select name="id_konsentrasi_keahlian" label="Konsentrasi keahlian" kosong="— Tidak ada (SMP) —"
                    :pilihan="$pilihanKonsentrasiKeahlian" :terpilih="$rombel->id_konsentrasi_keahlian" />
            </div>

            <x-admin.input name="nama" label="Nama rombel" :value="$rombel->nama" required maxlength="50"
                placeholder="mis. X PH 1" bantuan="Nama bebas, unik per satuan pendidikan dan tahun ajaran." />
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.master-data.rombel.index') }}" class="btn btn-outline">Batal</a>
        </div>
    </form>
@endsection
