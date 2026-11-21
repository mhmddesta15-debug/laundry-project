{{-- Beranda publik yang menampilkan informasi outlet dan pilihan layanan laundry aktif. --}}
@extends('layouts.public')

@section('content')
{{-- HERO + LACAK RESI --}}
<section id="home" class="landing-hero text-white">
    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 md:grid-cols-2 md:py-24">
        <div>
            <p class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-cyan-50">
                <span class="h-2 w-2 rounded-full bg-emerald-300"></span> Laundry Kiloan &amp; Satuan
            </p>
            <h1 class="max-w-xl text-4xl font-extrabold leading-[1.08] tracking-tight md:text-6xl">{{ config('laundry.tagline') }}</h1>
            <p class="mt-5 max-w-md text-base leading-7 text-sky-50 md:text-lg">Datang ke outlet kami, titipkan cucian, dan pantau progresnya kapan saja — tanpa perlu login.</p>

            <form action="{{ route('tracking.search') }}" method="GET"
                  class="mt-8 flex max-w-xl flex-col gap-2 rounded-2xl border border-white/60 bg-white p-2.5 shadow-2xl shadow-sky-950/20 sm:flex-row">
                <input name="q" required maxlength="30" placeholder="No. Resi (LDR-…) atau No. HP"
                       class="min-w-0 flex-1 rounded-xl border-transparent text-slate-800 placeholder:text-slate-400 focus:border-sky-300 focus:ring-sky-500">
                <button class="rounded-xl bg-slate-900 px-6 py-3 font-semibold text-white shadow-md transition hover:bg-sky-950">Cek Status Cucian <span aria-hidden="true">→</span></button>
            </form>
            @error('q')<p class="mt-2 text-sm text-red-100">{{ $message }}</p>@enderror
            <p class="mt-4 flex items-center gap-2 text-xs font-medium text-sky-100"><span class="text-emerald-300">✓</span> Cek progres cucian praktis, cukup dengan nomor resi</p>
        </div>

        <div class="hidden justify-center md:flex">
            <div class="landing-art-card">
                <div class="relative z-10 text-center">
                    <div class="mx-auto grid h-36 w-36 place-items-center rounded-[2.5rem] border border-white/40 bg-white/20 text-7xl shadow-2xl backdrop-blur-sm">🧺</div>
                    <p class="mt-6 text-lg font-bold tracking-wide">Bersih, segar, siap dipakai</p>
                    <p class="mt-1 text-sm text-sky-100">Perawatan pakaian dengan sepenuh hati</p>
                </div>
                <span class="absolute left-7 top-8 rounded-2xl border border-white/20 bg-white/15 px-3 py-2 text-xs font-semibold text-white shadow-lg backdrop-blur">✨ Wangi tahan lama</span>
                <span class="absolute bottom-8 right-6 rounded-2xl border border-white/20 bg-white/15 px-3 py-2 text-xs font-semibold text-white shadow-lg backdrop-blur">💧 Perawatan terbaik</span>
            </div>
        </div>
    </div>
</section>

{{-- LAYANAN --}}
<section id="layanan" class="mx-auto max-w-6xl px-4 py-16">
    <p class="text-center text-xs font-bold uppercase tracking-[0.2em] text-sky-700">Solusi untuk setiap pakaian</p>
    <h2 class="public-section-title mt-2 text-center text-3xl font-extrabold md:text-4xl">Layanan Kami</h2>
    <p class="mx-auto mt-2 max-w-xl text-center text-slate-500">Pilih layanan sesuai kebutuhan dan kecepatan yang Anda inginkan.</p>
    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['⚡', 'Laundry Express', 'Selesai 3, 6, atau 12 jam untuk cucian mendesak.'],
            ['👕', 'Cuci Kiloan', 'Cuci, kering, setrika, dan lipat rapi per kilogram.'],
            ['🧥', 'Dry Clean', 'Perawatan khusus jas, gaun, dan bahan sensitif.'],
            ['🛏️', 'Bedcover & Sepatu', 'Barang besar dan sepatu dicuci satuan.'],
        ] as [$icon, $title, $desc])
            <div class="landing-card rounded-2xl bg-white p-6 text-center transition duration-300 hover:-translate-y-1">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-3xl ring-1 ring-sky-100">{{ $icon }}</div>
                <h3 class="mt-4 font-bold text-slate-800">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- HARGA / KATALOG --}}
<section id="harga" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <p class="text-center text-xs font-bold uppercase tracking-[0.2em] text-sky-700">Jelas sejak awal</p>
        <h2 class="public-section-title mt-2 text-center text-3xl font-extrabold md:text-4xl">Harga / Katalog</h2>
        <p class="mt-2 text-center text-slate-500">Harga transparan, tanpa biaya tersembunyi.</p>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($services as $s)
                <div class="landing-price-card flex flex-col rounded-2xl bg-white p-6 transition duration-300 hover:-translate-y-1">
                    <div class="mb-5 grid h-11 w-11 place-items-center rounded-xl bg-cyan-50 text-xl">✦</div>
                    <h3 class="text-lg font-bold text-slate-800">{{ $s->name }}</h3>
                    <p class="mt-1 flex-1 text-sm text-slate-500">{{ $s->description }}</p>
                    <p class="mt-4 text-2xl font-extrabold text-sky-700">
                        Rp {{ number_format($s->price, 0, ',', '.') }}
                        <span class="text-sm font-normal text-slate-500">/ {{ $s->unit }}</span>
                    </p>
                    <p class="mt-1 text-xs text-slate-400">Estimasi {{ $s->duration_hours < 24 ? $s->duration_hours . ' jam' : round($s->duration_hours / 24) . ' hari' }}</p>
                </div>
            @empty
                <p class="col-span-full text-center text-slate-500">Katalog belum tersedia.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- TENTANG --}}
<section id="tentang" class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 md:grid-cols-2">
    <div class="landing-card grid h-64 place-items-center rounded-3xl bg-gradient-to-br from-sky-100 via-cyan-50 to-emerald-50 text-8xl">🏬</div>
    <div>
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-sky-700">Tentang outlet kami</p>
        <h2 class="public-section-title mt-2 text-3xl font-extrabold">Tentang Kami</h2>
        <p class="mt-4 leading-relaxed text-slate-600">
            {{ config('laundry.name') }} adalah layanan laundry profesional dengan mesin modern dan deterjen berkualitas.
            Kami melayani pelanggan langsung di outlet, dan setiap cucian dapat dipantau progresnya secara online melalui nomor resi.
        </p>
    </div>
</section>

{{-- KEUNGGULAN --}}
<section id="keunggulan" class="bg-sky-50 py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-center text-3xl font-bold">Keunggulan Kami</h2>
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['⏱️', 'Tepat Waktu', 'Estimasi jelas dan ditepati.'],
                ['📲', 'Lacak Online', 'Pantau progres cucian kapan saja.'],
                ['🌸', 'Wangi Tahan Lama', 'Pewangi premium untuk hasil segar.'],
                ['💰', 'Harga Bersahabat', 'Kualitas terbaik dengan harga jujur.'],
            ] as [$i, $t, $d])
                <div class="landing-card rounded-2xl bg-white p-6 text-center transition duration-300 hover:-translate-y-1">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-3xl">{{ $i }}</div>
                    <h3 class="mt-4 font-bold text-slate-800">{{ $t }}</h3>
                    <p class="mt-1 text-sm text-slate-500">{{ $d }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- BLOG (placeholder; ganti dengan tabel posts bila perlu) --}}
<section id="blog" class="mx-auto max-w-6xl px-4 py-16">
    <h2 class="text-center text-3xl font-bold">Blog</h2>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @foreach ([
            'Tips Merawat Pakaian Putih Agar Tetap Cerah',
            'Kapan Waktu Tepat Memilih Dry Clean?',
            'Cara Mencuci Sepatu Agar Tidak Cepat Rusak',
        ] as $post)
            <article class="landing-card overflow-hidden rounded-2xl bg-white">
                <div class="grid h-36 place-items-center bg-gradient-to-br from-sky-100 to-cyan-50 text-5xl">📰</div>
                <div class="p-5">
                    <h3 class="font-semibold leading-snug">{{ $post }}</h3>
                    <p class="mt-2 text-sm text-slate-500">Segera hadir…</p>
                </div>
            </article>
        @endforeach
    </div>
</section>

{{-- KONTAK --}}
<section id="kontak" class="mx-auto max-w-6xl px-4 pb-8">
    <div class="contact-card rounded-3xl bg-slate-900 p-8 text-white md:p-12">
        <h2 class="text-3xl font-bold">Hubungi Kami</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-4">
            <div><p class="text-sm text-slate-400">Alamat</p><p class="mt-1">{{ config('laundry.address') }}</p></div>
            <div><p class="text-sm text-slate-400">Telepon / WhatsApp</p>
                <p class="mt-1">{{ config('laundry.phone') }}</p>
                <a href="https://wa.me/{{ config('laundry.whatsapp') }}" target="_blank" rel="noopener"
                   class="mt-2 inline-block rounded-lg bg-green-500 px-4 py-1.5 text-sm font-semibold hover:bg-green-600">Chat WhatsApp</a></div>
            <div><p class="text-sm text-slate-400">Email</p><p class="mt-1">{{ config('laundry.email') }}</p></div>
            <div><p class="text-sm text-slate-400">Jam Buka</p><p class="mt-1">{{ config('laundry.hours') }}</p></div>
        </div>
    </div>
</section>
@endsection
