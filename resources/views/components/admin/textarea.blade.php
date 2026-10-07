@props(['name', 'label', 'value' => null, 'rows' => 3])

@php($id = $attributes->get('id', $name))

<div class="form-group">
    <label class="form-label" for="{{ $id }}">
        {{ $label }}@if ($attributes->has('required'))<span class="wajib" aria-hidden="true"> *</span>@endif
    </label>
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}"
        {{ $attributes->except('id')->class(['form-control', 'is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <div class="form-error">{{ $message }}</div>
    @enderror
</div>
