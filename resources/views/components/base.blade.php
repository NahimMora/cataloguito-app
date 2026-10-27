@props(['title' => 'Vitrina · Tu catálogo, siempre al día'])
<!doctype html>
<html lang="es-AR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="theme-color" content="#4338ca">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preload"
        href="{{ Vite::asset('node_modules/@fontsource-variable/manrope/files/manrope-latin-wght-normal.woff2') }}"
        as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body><a class="skip-link" href="#main">Saltar al contenido</a>{{ $slot }}
    <div x-data="{ text: '' }" @notice.window="text=$event.detail; setTimeout(()=>text='',3500)" x-show="text"
        x-cloak class="toast" role="status" x-text="text"></div>
</body>

</html>
