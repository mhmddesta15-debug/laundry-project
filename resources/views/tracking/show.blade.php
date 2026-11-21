{{-- Detail pelacakan pesanan dan, untuk pelanggan masuk, informasi serta aksi pembayaran. --}}
@extends('layouts.public')
@section('title', 'Status ' . $order->no_resi)

@section('content')
@php
    $steps   = \App\Models\Order::STEPS;
    $current = $order->stepIndex();
    $logs    = $order->statuses->keyBy('status');
    $public  = $public ?? true;
    $percent = round($current / (count($steps) - 1) * 100);
@endphp

<section class="mx-auto max-w-3xl px-4 py-12">
    @include('partials.flash')

    {{-- Ringkasan --}}
    <div class="tracking-card mb-8 rounded-2xl bg-white p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-sm text-slate-500">Nomor Resi</p>
                <h1 class="text-2xl font-bold text-sky-700">{{ $order->no_resi }}</h1>
            </div>
            <span class="rounded-full px-3 py-1 text-sm font-semibold
                {{ in_array($order->status, ['siap_diambil', 'sudah_diambil']) ? 'bg-green-100 text-green-700' : 'bg-sky-100 text-sky-700' }}">
                {{ $order->statusLabel() }}
            </span>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-slate-500">Pelanggan</dt>
                <dd class="font-medium">{{ $public ? \Illuminate\Support\Str::mask($order->customer->name, '*', 2) : $order->customer->name }}</dd></div>
            <div><dt class="text-slate-500">Layanan</dt>
                <dd class="font-medium">{{ $order->service->name }}</dd></div>
            <div><dt class="text-slate-500">Jumlah</dt>
                <dd class="font-medium">{{ rtrim(rtrim(number_format($order->qty, 2, '.', ''), '0'), '.') }} {{ $order->service->unit }}</dd></div>
            <div><dt class="text-slate-500">Estimasi Selesai</dt>
                <dd class="font-medium">{{ $order->estimated_done_at?->translatedFormat('d M Y, H:i') ?? '-' }}</dd></div>
            @unless ($public)
                <div><dt class="text-slate-500">Total</dt>
                    <dd class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</dd></div>
            @endunless
            <div><dt class="text-slate-500">Pembayaran</dt>
                <dd class="font-medium {{ $order->payment_status === 'lunas' ? 'text-green-700' : ($order->payment_status === 'ditolak' ? 'text-red-600' : 'text-amber-600') }}">{{ $order->paymentStatusLabel() }}</dd></div>
        </dl>

        <div class="mt-6">
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-sky-600 transition-all" style="width: {{ $percent }}%"></div>
            </div>
            <p class="mt-1 text-right text-xs text-slate-500">{{ $percent }}%</p>
        </div>
    </div>

    @if ($public && in_array($order->payment_status, ['belum_bayar', 'ditolak'], true))
        <div class="mb-8 rounded-2xl border border-amber-200 bg-amber-50 p-5">
            <h2 class="font-semibold text-amber-900">Ingin membayar pesanan ini?</h2>
            <p class="mt-1 text-sm text-amber-800">Informasi rekening dan formulir unggah bukti transfer tersedia setelah Anda masuk ke akun pelanggan.</p>
            <a href="{{ auth()->check() && auth()->user()->role === 'pelanggan' ? route('customer.dashboard') : route('login') }}"
               class="mt-4 inline-block rounded-lg bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700">
                {{ auth()->check() && auth()->user()->role === 'pelanggan' ? 'Buka Akun Saya' : 'Masuk untuk Membayar' }}
            </a>
        </div>
    @endif

    @unless ($public)
        @if ($order->payment_status !== 'lunas')
            <div class="mb-8 rounded-2xl bg-white p-6 shadow">
                <h2 class="text-lg font-semibold">Pembayaran Transfer</h2>
                <p class="mt-1 text-sm text-slate-600">Transfer sesuai nominal berikut, lalu unggah foto bukti transfer untuk diperiksa kasir.</p>

                <div class="mt-4 rounded-xl bg-sky-50 p-4">
                    <p class="text-sm text-slate-600">Nominal yang harus ditransfer</p>
                    <p class="text-2xl font-bold text-sky-700">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                    @if ($paymentSettings->bank_name && $paymentSettings->bank_account_number && $paymentSettings->bank_account_name)
                        <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                            <div><dt class="text-slate-500">Bank</dt><dd class="font-semibold">{{ $paymentSettings->bank_name }}</dd></div>
                            <div><dt class="text-slate-500">Nomor Rekening</dt><dd class="font-semibold">{{ $paymentSettings->bank_account_number }}</dd></div>
                            <div><dt class="text-slate-500">Atas Nama</dt><dd class="font-semibold">{{ $paymentSettings->bank_account_name }}</dd></div>
                        </dl>
                    @elseif (! $paymentSettings->qris_image_path)
                        <p class="mt-3 text-sm text-amber-800">Informasi rekening belum diatur. Silakan hubungi outlet untuk mendapatkan nomor rekening sebelum transfer.</p>
                    @endif
                    @if ($paymentSettings->qris_image_path)
                        <div class="mt-4 border-t border-sky-200 pt-4">
                            <p class="text-sm font-semibold text-slate-700">Atau bayar dengan QRIS</p>
                            <img src="{{ route('customer.payment-qris') }}" alt="Kode QRIS pembayaran" class="mt-3 h-64 w-64 rounded-lg border border-slate-200 bg-white object-contain p-2">
                        </div>
                    @endif
                </div>

                @if ($order->payment_status === 'menunggu_verifikasi')
                    <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-800">
                        Bukti transfer sudah dikirim dan sedang menunggu verifikasi kasir.
                    </div>
                @else
                    @if ($order->payment_status === 'ditolak')
                        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                            Bukti transfer sebelumnya ditolak.
                            @if ($order->payment_note) Alasan: {{ $order->payment_note }}. @endif
                            Silakan unggah bukti yang benar.
                        </div>
                    @endif
                    <form method="POST" action="{{ route('customer.orders.payment-proof', $order) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                        @csrf
                        <label for="payment_proof" class="block text-sm font-medium text-slate-700">Foto bukti transfer (JPG, PNG, atau WEBP; maks. 5 MB)</label>
                        <input id="payment_proof" type="file" name="payment_proof" accept="image/jpeg,image/png,image/webp" required
                               class="block w-full rounded-lg border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2">
                        @error('payment_proof')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                        <button class="rounded-lg bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700">Kirim Bukti Transfer</button>
                    </form>
                @endif
            </div>
        @else
            <div class="mb-8 rounded-2xl border border-green-200 bg-green-50 p-5 text-green-800">
                Pembayaran telah dikonfirmasi lunas oleh kasir.
            </div>
        @endif
    @endunless

    {{-- Stepper vertikal --}}
    <div class="rounded-2xl bg-white p-6 shadow">
        <h2 class="mb-6 font-semibold">Progres Cucian</h2>

        <ol class="relative">
            @foreach ($steps as $key => $label)
                @php
                    $i      = $loop->index;
                    $done   = $i < $current;
                    $active = $i === $current;
                    $log    = $logs->get($key);
                @endphp
                <li class="relative flex gap-4 pb-8 last:pb-0">
                    @unless ($loop->last)
                        <span class="absolute left-4 top-8 -ml-px h-full w-0.5 {{ $done ? 'bg-sky-600' : 'bg-slate-200' }}"></span>
                    @endunless

                    <span class="relative z-10 flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-sm font-bold
                        {{ $done ? 'bg-sky-600 text-white' : ($active ? 'animate-pulse bg-sky-600 text-white ring-4 ring-sky-200' : 'bg-slate-200 text-slate-500') }}">
                        @if ($done) ✓ @else {{ $i + 1 }} @endif
                    </span>

                    <div>
                        <p class="font-medium {{ $i > $current ? 'text-slate-400' : 'text-slate-900' }}">
                            {{ $label }}
                            @if ($active)<span class="ml-1 text-xs font-normal text-sky-600">(saat ini)</span>@endif
                        </p>
                        @if ($log)
                            <p class="text-xs text-slate-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</p>
                            @if ($log->note)<p class="mt-0.5 text-sm text-slate-600">{{ $log->note }}</p>@endif
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>

        @if ($order->status === 'siap_diambil')
            <div class="mt-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                🎉 Cucian Anda sudah siap! Silakan ambil di outlet dengan menunjukkan nomor resi ini.
            </div>
        @endif
    </div>

    <a href="{{ $public ? route('home') : route('customer.dashboard') }}" class="mt-6 inline-block text-sky-700 hover:underline">
        ← {{ $public ? 'Kembali ke Beranda' : 'Kembali ke Riwayat' }}
    </a>
</section>
@endsection
