@props(['name', 'label', 'type' => 'text', 'value' => null, 'bantuan' => null])

@php($id = $attributes->get('id', $name))

<div class="form-group">
    <label class="form-label" for="{{ $id }}">
        {{ $label }}@if ($attributes->has('required'))<span class="wajib" aria-hidden="true"> *</span>@endif
    </label>
    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>
    @if ($bantuan)
        <div class="form-hint">{{ $bantuan }}</div>
    @endif
    @error($name)
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
