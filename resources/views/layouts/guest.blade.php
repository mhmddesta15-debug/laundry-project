{{-- Layout autentikasi: menyediakan shell halaman login dan register dengan branding laundry. --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', config('laundry.name', 'Laundry Maxwell')) }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="auth-shell min-h-screen flex flex-col sm:justify-center items-center px-4 py-10">
            <div>
                <a href="/" class="flex flex-col items-center gap-3 text-center">
                    <x-application-logo class="app-brand-mark h-16 w-16 text-sky-600" aria-hidden="true" focusable="false" />
                    <span class="text-lg font-extrabold tracking-tight text-slate-800">{{ config('laundry.name', 'Laundry Maxwell') }}</span>
                </a>
            </div>

            <div class="auth-card mt-7 w-full overflow-hidden bg-white px-6 py-6 sm:max-w-md sm:px-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
