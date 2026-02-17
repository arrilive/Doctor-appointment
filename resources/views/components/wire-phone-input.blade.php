@props(['label' => '', 'name' => '', 'placeholder' => '', 'value' => null, 'mask' => '(999) 999-9999'])

<div class="mb-4">
    
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
        </label>
    @endif

    <input
        type="text"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ $value ?? old($name) }}"
        placeholder="{{ $placeholder }}"
        x-data
        x-mask:dynamic="$input.replace(/\D/g, '').length <= 10 ? '(999) 999-9999' : '(999) 999-9999'"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm ' . ($errors->has($name) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '')
        ]) }}
    >

    @error($name)
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
    @enderror

</div>