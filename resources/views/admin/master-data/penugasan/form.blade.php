@extends('layouts.admin')

@php
    use App\Enums\KodePeran;

    $sedangMengubah = $penugasan->exists;
@endphp

@section('judul', $sedangMengubah ? 'Ubah Penugasan' : 'Tambah Penugasan')

@section('konten')
    <x-admin.header-halaman :pretitle="'Master Data · Tahun Ajaran '.$tahunAjaran->nama" :judul="$sedangMengubah ? 'Ubah Penugasan' : 'Tambah Penugasan'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.penugasan.update', $penugasan)
            : route('admin.master-data.penugasan.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <input type="hidden" name="id_tahun_ajaran" value="{{ $tahunAjaran->id_tahun_ajaran }}">

        <div class="card-body">
            @error('id_tahun_ajaran')
                <div class="alert alert-error"><div class="alert-body">{{ $message }}</div></div>
            @enderror

            <div class="form-row">
                <x-admin.select name="id_tenaga_pendidik" label="Tenaga pendidik" required
                    :pilihan="$pilihanTenagaPendidik" :terpilih="$penugasan->id_tenaga_pendidik" />
                <x-admin.select name="kode_peran" label="Peran" required
                    :pilihan="$pilihanPeran" :terpilih="$penugasan->peran?->kode" />
            </div>

            {{-- Isian konteks ditampilkan sesuai peran terpilih (resources/js/admin.js). --}}
            <div data-tampil-untuk="kode_peran:{{ KodePeran::WaliKelas->value }}">
                <x-admin.select name="id_rombel" label="Rombel" :pilihan="$pilihanRombel" :terpilih="$penugasan->id_rombel" />
                @if ($pilihanRombel->isEmpty())
                    <div class="form-hint">Belum ada rombel pada tahun ajaran {{ $tahunAjaran->nama }}.</div>
                @endif
            </div>

            <div data-tampil-untuk="kode_peran:{{ KodePeran::KetuaJurusan->value }}">
                <x-admin.select name="id_konsentrasi_keahlian" label="Konsentrasi keahlian"
                    :pilihan="$pilihanKonsentrasiKeahlian" :terpilih="$penugasan->id_konsentrasi_keahlian" />
            </div>

            <div class="form-row" data-tampil-untuk="kode_peran:{{ KodePeran::KepalaSekolah->value }},{{ KodePeran::WakilKepalaSekolah->value }}">
                <x-admin.select name="id_satuan_pendidikan" label="Satuan pendidikan"
                    :pilihan="$pilihanSatuanPendidikan" :terpilih="$penugasan->id_satuan_pendidikan" />
                <div data-tampil-untuk="kode_peran:{{ KodePeran::WakilKepalaSekolah->value }}">
                    <x-admin.input name="bidang" label="Bidang (khusus Wakil Kepala Sekolah)" :value="$penugasan->bidang"
                        maxlength="100" placeholder="mis. Kurikulum" />
                </div>
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan</button>
            <a href="{{ route('admin.master-data.penugasan.index', ['tahun_ajaran' => $tahunAjaran->id_tahun_ajaran]) }}" class="btn btn-secondary"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Batal</a>
        </div>
    </form>
@endsection
