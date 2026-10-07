@props(['judul' => 'Belum ada data'])

<div class="empty-state">
    <div class="empty-state-title">{{ $judul }}</div>
    @if ($slot->isNotEmpty())
        <div class="empty-state-desc">{{ $slot }}</div>
    @endif
</div>
