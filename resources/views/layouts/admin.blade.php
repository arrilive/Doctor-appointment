<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- WireUI -->
    <wireui:scripts />

    <!-- Styles -->
    @livewireStyles

    <!-- FontAwesome -->
    <script src="https://kit.fontawesome.com/ce759f6076.js" crossorigin="anonymous"></script>
</head>
<body class="font-sans antialiased bg-gray-50">

    <!-- Navbar -->
    @include('layouts.includes.admin.navigation')

    <!-- Sidebar -->
    @include('layouts.includes.admin.sidebar')

    <!-- Main Content -->
    <main class="p-4 sm:ml-64 mt-14 bg-blue-200">
    {{ $slot }}
    </main>

    <!-- Modals -->
    @stack('modals')

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Flowbite -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>
</html>
