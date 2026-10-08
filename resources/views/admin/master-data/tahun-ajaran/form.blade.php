@extends('layouts.admin')

@php($sedangMengubah = $tahunAjaran->exists)

@section('judul', $sedangMengubah ? 'Ubah Tahun Ajaran' : 'Tambah Tahun Ajaran')

@section('konten')
    <x-admin.header-halaman pretitle="Master Data" :judul="$sedangMengubah ? 'Ubah Tahun Ajaran' : 'Tambah Tahun Ajaran'" />

    <form method="POST" class="card"
        action="{{ $sedangMengubah
            ? route('admin.master-data.tahun-ajaran.update', $tahunAjaran)
            : route('admin.master-data.tahun-ajaran.store') }}">
        @csrf
        @if ($sedangMengubah)
            @method('PUT')
        @endif

        <div class="card-body">
            <x-admin.input name="nama" label="Tahun ajaran" :value="$tahunAjaran->nama" required
                maxlength="9" placeholder="2026/2027" bantuan="Format TTTT/TTTT, mis. 2026/2027." />

            <h2 class="card-title">Semester ganjil</h2>
            <div class="form-row">
                <x-admin.input type="date" name="ganjil_mulai" label="Tanggal mulai" required
                    :value="$tahunAjaran->semesterGanjil?->tanggal_mulai?->format('Y-m-d')" />
                <x-admin.input type="date" name="ganjil_selesai" label="Tanggal selesai" required
                    :value="$tahunAjaran->semesterGanjil?->tanggal_selesai?->format('Y-m-d')" />
            </div>

            <h2 class="card-title">Semester genap</h2>
            <div class="form-row">
                <x-admin.input type="date" name="genap_mulai" label="Tanggal mulai" required
                    :value="$tahunAjaran->semesterGenap?->tanggal_mulai?->format('Y-m-d')" />
                <x-admin.input type="date" name="genap_selesai" label="Tanggal selesai" required
                    :value="$tahunAjaran->semesterGenap?->tanggal_selesai?->format('Y-m-d')" />
            </div>
        </div>

        <div class="card-footer aksi-form">
            <button type="submit" class="btn btn-success"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan</button>
            <a href="{{ route('admin.master-data.tahun-ajaran.index') }}" class="btn btn-secondary"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Batal</a>
        </div>
    </form>
@endsection
