@extends('layouts.public')

@section('content')
{{-- HERO + LACAK RESI --}}
<section id="home" class="bg-gradient-to-br from-sky-600 via-sky-500 to-cyan-400 text-white">
    <div class="mx-auto grid max-w-6xl items-center gap-10 px-4 py-16 md:grid-cols-2 md:py-24">
        <div>
            <p class="mb-3 inline-block rounded-full bg-white/20 px-3 py-1 text-xs font-semibold">Laundry Kiloan &amp; Satuan</p>
            <h1 class="text-4xl font-extrabold leading-tight md:text-5xl">{{ config('laundry.tagline') }}</h1>
            <p class="mt-4 max-w-md text-sky-50">Datang ke outlet kami, titipkan cucian, dan pantau progresnya kapan saja — tanpa perlu login.</p>

            <form action="{{ route('tracking.search') }}" method="GET"
                  class="mt-8 flex max-w-xl flex-col gap-2 rounded-2xl bg-white p-3 shadow-xl sm:flex-row">
                <input name="q" required maxlength="30" placeholder="No. Resi (LDR-…) atau No. HP"
                       class="flex-1 rounded-xl border-slate-300 text-slate-800 focus:border-sky-500 focus:ring-sky-500">
                <button class="rounded-xl bg-sky-700 px-6 py-2.5 font-semibold text-white hover:bg-sky-800">Cek Status Cucian</button>
            </form>
            @error('q')<p class="mt-2 text-sm text-red-100">{{ $message }}</p>@enderror
        </div>

        <div class="hidden justify-center md:flex">
            <div class="grid h-72 w-72 place-items-center rounded-full bg-white/15 text-9xl backdrop-blur">🧺</div>
        </div>
    </div>
</section>

{{-- LAYANAN --}}
<section id="layanan" class="mx-auto max-w-6xl px-4 py-16">
    <h2 class="text-center text-3xl font-bold">Layanan Kami</h2>
    <p class="mx-auto mt-2 max-w-xl text-center text-slate-500">Pilih layanan sesuai kebutuhan dan kecepatan yang Anda inginkan.</p>
    <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['⚡', 'Laundry Express', 'Selesai 3, 6, atau 12 jam untuk cucian mendesak.'],
            ['👕', 'Cuci Kiloan', 'Cuci, kering, setrika, dan lipat rapi per kilogram.'],
            ['🧥', 'Dry Clean', 'Perawatan khusus jas, gaun, dan bahan sensitif.'],
            ['🛏️', 'Bedcover & Sepatu', 'Barang besar dan sepatu dicuci satuan.'],
        ] as [$icon, $title, $desc])
            <div class="rounded-2xl border bg-white p-6 text-center shadow-sm transition hover:-translate-y-1 hover:shadow-md">
                <div class="text-4xl">{{ $icon }}</div>
                <h3 class="mt-3 font-semibold">{{ $title }}</h3>
                <p class="mt-1 text-sm text-slate-500">{{ $desc }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- HARGA / KATALOG --}}
<section id="harga" class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
        <h2 class="text-center text-3xl font-bold">Harga / Katalog</h2>
        <p class="mt-2 text-center text-slate-500">Harga transparan, tanpa biaya tersembunyi.</p>

        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @forelse ($services as $s)
                <div class="flex flex-col rounded-2xl border bg-slate-50 p-6 transition hover:shadow-md">
                    <h3 class="text-lg font-semibold">{{ $s->name }}</h3>
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
    <div class="grid h-64 place-items-center rounded-3xl bg-sky-100 text-8xl">🏬</div>
    <div>
        <h2 class="text-3xl font-bold">Tentang Kami</h2>
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
                <div class="rounded-2xl bg-white p-6 text-center shadow-sm">
                    <div class="text-4xl">{{ $i }}</div>
                    <h3 class="mt-3 font-semibold">{{ $t }}</h3>
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
            <article class="overflow-hidden rounded-2xl border bg-white shadow-sm">
                <div class="grid h-36 place-items-center bg-slate-100 text-5xl">📰</div>
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
    <div class="rounded-3xl bg-slate-900 p-8 text-white md:p-12">
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
