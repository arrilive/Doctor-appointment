@props(['breadcrumbs' => [], 'title' => config('app.name', 'Laravel')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title }}</title>

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

  <script src="https://kit.fontawesome.com/9161014f5f.js" crossorigin="anonymous"></script>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  {{-- SweetAlert2 --}}
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  {{-- WireUI SIN Alpine (evita duplicados) --}}
  <wireui:scripts :alpine="false" />

  @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
  @include('layouts.includes.admin.navigation')
  @include('layouts.includes.admin.sidebar')

  <div class="p-4 sm:ml-64">
    {{-- Header: título + breadcrumb + acciones --}}
    <div class="mt-14 mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="flex flex-col gap-1">
        <h1 class="text-xl font-semibold text-gray-900">{{ $title }}</h1>
        @if(!empty($breadcrumbs))
          @include('layouts.includes.admin.breadcrumb')
        @endif
      </div>

      {{-- Slot de acciones (botón "Nuevo", etc.) --}}
      <div class="flex items-center gap-2">
        {{ $actions ?? '' }}
      </div>
    </div>

    {{-- Contenido principal --}}
    {{ $slot }}
  </div>

  @stack('modals')

  @livewireScripts
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>

  {{-- Disparar SweetAlert si hay flash "swal" --}}
  @if (session('swal'))
    <script>
      window.addEventListener('load', () => {
        Swal.fire(@json(session('swal')));
      });
    </script>
  @endif

  {{-- Deja este stack por si alguna vista quiere empujar scripts extras --}}
  @stack('scripts')
</body>
</html>
