@props(['judul', 'pretitle' => null])

{{-- Judul halaman admin; isi slot dipakai untuk tombol aksi di sisi kanan. --}}
<div class="page-header">
    <div class="page-header-row">
        <div>
            @if ($pretitle)
                <div class="page-pretitle">{{ $pretitle }}</div>
            @endif
            <h1 class="page-title">{{ $judul }}</h1>
        </div>
        @if ($slot->isNotEmpty())
            <div class="page-actions">{{ $slot }}</div>
        @endif
    </div>
</div>
