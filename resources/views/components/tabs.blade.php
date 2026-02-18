@props([
    'active' => 'default',
])

<div x-data="{ tab: '{{ $active }}' }">
    {{-- Menú de pestañas --}}
    <div class="border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-gray-500">
            {{ $header ?? '' }}
        </ul>
    </div>

    {{-- Contenido de los tabs --}}
    <div class="px-4 py-4 mt-4">
        {{ $slot }}
    </div>
</div>
