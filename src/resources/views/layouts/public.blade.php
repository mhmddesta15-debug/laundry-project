<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('laundry.name') . ' – ' . config('laundry.tagline'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 antialiased">
@php
    $nav = [
        'Home'          => url('/') . '#home',
        'Layanan Kami'  => url('/') . '#layanan',
        'Harga/Katalog' => url('/') . '#harga',
        'Tentang Kami'  => url('/') . '#tentang',
        'Keunggulan'    => url('/') . '#keunggulan',
        'Blog'          => url('/') . '#blog',
        'Hubungi Kami'  => url('/') . '#kontak',
    ];
@endphp

<header class="sticky top-0 z-40 border-b bg-white/90 backdrop-blur" x-data="{ open: false }">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold text-sky-700">
            <span class="grid h-9 w-9 place-items-center rounded-xl bg-sky-600 text-white">🧺</span>
            {{ config('laundry.name') }}
        </a>

        <nav class="hidden items-center gap-6 text-sm font-medium lg:flex">
            @foreach ($nav as $label => $href)
                <a href="{{ $href }}" class="text-slate-600 transition hover:text-sky-700">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg border border-sky-600 px-4 py-2 text-sm font-semibold text-sky-700 hover:bg-sky-50">Masuk</a>
            @endauth
        </div>

        <button @click="open = !open" class="rounded-lg p-2 text-slate-600 lg:hidden" aria-label="Menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div x-show="open" x-cloak x-transition class="border-t bg-white px-4 py-3 lg:hidden">
        @foreach ($nav as $label => $href)
            <a href="{{ $href }}" @click="open=false" class="block py-2 text-slate-700">{{ $label }}</a>
        @endforeach
        <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="mt-2 block rounded-lg bg-sky-600 py-2 text-center font-semibold text-white">
            {{ auth()->check() ? 'Dashboard' : 'Masuk' }}
        </a>
    </div>
</header>

<main>@yield('content')</main>

<footer class="mt-16 bg-slate-900 text-slate-300">
    <div class="mx-auto grid max-w-6xl gap-8 px-4 py-12 md:grid-cols-3">
        <div>
            <p class="text-lg font-bold text-white">{{ config('laundry.name') }}</p>
            <p class="mt-2 text-sm">{{ config('laundry.tagline') }}</p>
        </div>
        <div class="text-sm leading-relaxed">
            <p class="font-semibold text-white">Kontak</p>
            <p class="mt-2">📍 {{ config('laundry.address') }}</p>
            <p>📞 {{ config('laundry.phone') }}</p>
            <p>✉️ {{ config('laundry.email') }}</p>
        </div>
        <div class="text-sm">
            <p class="font-semibold text-white">Jam Operasional</p>
            <p class="mt-2">{{ config('laundry.hours') }}</p>
            <p class="mt-2 text-slate-400">Layanan antar-jemput belum tersedia. Silakan datang langsung ke outlet.</p>
        </div>
    </div>
    <div class="border-t border-slate-800 py-4 text-center text-xs text-slate-500">
        © {{ date('Y') }} {{ config('laundry.name') }}. All rights reserved.
    </div>
</footer>
<style>[x-cloak]{display:none!important}</style>
</body>
</html>
