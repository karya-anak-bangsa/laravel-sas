@props(['judul', 'paginator'])

{{--
    Kartu daftar untuk setiap halaman index admin:
    header = judul + jumlah data + tombol aksi (slot `aksi`),
    body = pencarian/saringan (slot `pencarian`) + tabel atau keadaan kosong (slot utama),
    footer = info jumlah data + paginasi (selalu tampil).
--}}
<div class="card">
    <div class="card-header">
        <div>
            <div class="card-title">{{ $judul }}</div>
            <div class="card-subtitle">{{ $paginator->total() }} data</div>
        </div>
        @isset($aksi)
            <div class="card-options">{{ $aksi }}</div>
        @endisset
    </div>

    <div class="card-body">
        @isset($pencarian)
            <div class="kartu-pencarian">{{ $pencarian }}</div>
        @endisset

        {{ $slot }}
    </div>

    <div class="card-footer">
        {{ $paginator->links('layouts.partials.admin-paginasi') }}
    </div>
</div>
