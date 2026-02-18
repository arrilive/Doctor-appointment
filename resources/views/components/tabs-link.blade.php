@props([
    'tab' => '',
    'active' => '',
    'hasError' => false,
])

<li class="me-2">
    <a href="#" @click.prevent="tab = '{{ $tab }}'"
       :class="{
           'text-red-600 border-red-600': {{ $hasError ? 'true' : 'false' }} && tab !== '{{ $tab }}',
           'text-red-600 border-red-600 active': {{ $hasError ? 'true' : 'false' }} && tab === '{{ $tab }}',
           'text-blue-600 border-blue-600 active': !{{ $hasError ? 'true' : 'false' }} && tab === '{{ $tab }}',
           'border-transparent hover:text-blue-600 hover:border-gray-300': !{{ $hasError ? 'true' : 'false' }} && tab !== '{{ $tab }}'
       }"
       class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-colors duration-200 cursor-pointer">
        {{ $slot }}
        @if($hasError)
            <i class="fa-solid fa-circle-exclamation text-xs ms-2 animate-pulse"></i>
        @endif
    </a>
</li>