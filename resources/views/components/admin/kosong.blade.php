@props(['judul' => 'Belum ada data'])

<div class="empty-state">
    <div class="empty-state-icon"><i class="fa-regular fa-folder-open" aria-hidden="true"></i></div>
    <div class="empty-state-title">{{ $judul }}</div>
    @if ($slot->isNotEmpty())
        <div class="empty-state-desc">{{ $slot }}</div>
    @endif
</div>
