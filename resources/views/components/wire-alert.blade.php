@props(['title' => '', 'info' => false, 'success' => false, 'warning' => false, 'danger' => false])

@php
    $classes = 'border-l-4 p-4 rounded';
    $iconClasses = '';
    $textClasses = '';
    
    if ($info) {
        $classes .= ' bg-blue-50 border-blue-400';
        $iconClasses = 'text-blue-400';
        $textClasses = 'text-blue-700';
    } elseif ($success) {
        $classes .= ' bg-green-50 border-green-400';
        $iconClasses = 'text-green-400';
        $textClasses = 'text-green-700';
    } elseif ($warning) {
        $classes .= ' bg-yellow-50 border-yellow-400';
        $iconClasses = 'text-yellow-400';
        $textClasses = 'text-yellow-700';
    } elseif ($danger) {
        $classes .= ' bg-red-50 border-red-400';
        $iconClasses = 'text-red-400';
        $textClasses = 'text-red-700';
    } else {
        $classes .= ' bg-gray-50 border-gray-400';
        $iconClasses = 'text-gray-400';
        $textClasses = 'text-gray-700';
    }
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="flex">
        <div class="flex-shrink-0">
            <i class="fa-solid fa-info-circle {{ $iconClasses }}"></i>
        </div>
        <div class="ml-3">
            @if($title)
                <p class="text-sm {{ $textClasses }} font-medium">{{ $title }}</p>
            @endif
            @if($slot->isNotEmpty())
                <div class="text-sm {{ $textClasses }} mt-1">
                    {{ $slot }}
                </div>
            @endif
        </div>
    </div>
</div>