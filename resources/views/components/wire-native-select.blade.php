@props(['label' => '', 'name' => '', 'placeholder' => '', 'value' => null, 'options' => []])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
            {{ $label }}
        </label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900
                        focus:border-indigo-500 focus:ring-indigo-500 ' . ($errors->has($name) ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : '')
        ]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" {{ ($value ?? old($name)) == $optionValue ? 'selected' : '' }}>
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>