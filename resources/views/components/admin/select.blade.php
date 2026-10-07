@props(['name', 'label', 'pilihan' => [], 'terpilih' => null, 'kosong' => '— Pilih —'])

{{-- $pilihan: array [nilai => teks]. $kosong = false untuk menghilangkan opsi kosong. --}}
@php
    $id = $attributes->get('id', $name);
    $nilaiTerpilih = (string) old($name, $terpilih instanceof \BackedEnum ? $terpilih->value : $terpilih);
@endphp

<div class="form-group">
    <label class="form-label" for="{{ $id }}">
        {{ $label }}@if ($attributes->has('required'))<span class="wajib" aria-hidden="true"> *</span>@endif
    </label>
    <select id="{{ $id }}" name="{{ $name }}"
        {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
        @if ($kosong !== false)
            <option value="">{{ $kosong }}</option>
        @endif
        @foreach ($pilihan as $nilai => $teks)
            <option value="{{ $nilai }}" @selected($nilaiTerpilih === (string) $nilai)>{{ $teks }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
