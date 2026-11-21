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
    {{-- Ringkasan --}}
    <div class="mb-8 rounded-2xl bg-white p-6 shadow">
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
                <div><dt class="text-slate-500">Pembayaran</dt>
                    <dd class="font-medium">{{ $order->payment_status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}</dd></div>
            @endunless
        </dl>

        <div class="mt-6">
            <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                <div class="h-2 rounded-full bg-sky-600 transition-all" style="width: {{ $percent }}%"></div>
            </div>
            <p class="mt-1 text-right text-xs text-slate-500">{{ $percent }}%</p>
        </div>
    </div>

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
