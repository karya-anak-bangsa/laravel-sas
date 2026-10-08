@props(['action', 'konfirmasi' => 'Hapus data ini?'])

{{-- Konfirmasi ditangani oleh resources/js/admin.js lewat atribut data-konfirmasi. --}}
<form method="POST" action="{{ $action }}" data-konfirmasi="{{ $konfirmasi }}" class="form-inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
</form>
