@props(['title' => 'Cataloguito · Tu catálogo, siempre al día'])
<!doctype html>
<html lang="es-AR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <meta name="theme-color" content="#b93828">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preload"
        href="{{ Vite::asset('node_modules/@fontsource-variable/manrope/files/manrope-latin-wght-normal.woff2') }}"
        as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body><a class="skip-link" href="#main">Saltar al contenido</a>{{ $slot }}
    <div x-data="{ text: '', timer: null, show(message) { clearTimeout(this.timer); this.text=message; this.timer=setTimeout(()=>this.text='',5000) } }"
        @notice.window="show($event.detail)" x-show="text" x-cloak class="toast" role="status">
        <span x-text="text"></span><button type="button" @click="clearTimeout(timer);text=''" aria-label="Cerrar aviso">×</button>
    </div>
</body>

</html>
