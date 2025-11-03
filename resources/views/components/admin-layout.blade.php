@props(['breadcrumbs' => [], 'title' => config('app.name', 'Laravel')])

<div class="font-sans antialiased bg-gray-50">
    @include('layouts.includes.admin.navigation')
    @include('layouts.includes.admin.sidebar')

    <div class="p-4 sm:ml-64">
        <div class="mt-14">
            @if(count($breadcrumbs) > 0)
                <div class="mb-4">
                    @include('layouts.includes.admin.breadcrumb')
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>

@stack('modals')
@livewireScripts
