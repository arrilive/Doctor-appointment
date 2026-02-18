@props([
    'tab' => '',
    'active' => '',
])

<div x-show="tab === '{{ $tab }}'" @if ($active !== $tab) style="display: none;" @endif>
    {{ $slot }}
</div>
