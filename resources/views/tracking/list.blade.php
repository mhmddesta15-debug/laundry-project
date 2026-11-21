{{-- Hasil pencarian yang menampilkan sejumlah pesanan terkait nomor HP pelanggan. --}}
@extends('layouts.public')
@section('title', 'Hasil Pencarian')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-12">
    <div class="mb-7">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-700">Pencarian pesanan</p>
        <h1 class="mt-2 text-2xl font-extrabold tracking-tight text-slate-900">Hasil untuk "{{ $q }}"</h1>
        <p class="mt-1 text-sm text-slate-500">Pilih nomor resi untuk melihat rincian progres cucian.</p>
    </div>

    @forelse ($orders as $o)
        <a href="{{ route('tracking.search', ['q' => $o->no_resi]) }}"
           class="tracking-card mt-4 flex items-center justify-between rounded-2xl bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md">
            <div>
                <p class="font-semibold text-sky-700">{{ $o->no_resi }}</p>
                <p class="text-sm text-slate-500">{{ $o->service->name }} · {{ $o->created_at->translatedFormat('d M Y') }}</p>
            </div>
            <span class="rounded-full px-3 py-1 text-xs font-semibold
                {{ in_array($o->status, ['siap_diambil', 'sudah_diambil']) ? 'bg-green-100 text-green-700' : 'bg-sky-100 text-sky-700' }}">
                {{ $o->statusLabel() }}
            </span>
        </a>
    @empty
        <div class="mt-6 rounded-2xl bg-white p-8 text-center shadow-sm">
            <p class="text-4xl">🔍</p>
            <p class="mt-2 font-medium">Data tidak ditemukan</p>
            <p class="text-sm text-slate-500">Periksa kembali nomor resi / nomor HP Anda, atau hubungi outlet kami.</p>
        </div>
    @endforelse

    <a href="{{ route('home') }}" class="mt-6 inline-block text-sky-700 hover:underline">← Kembali ke Beranda</a>
</section>
@endsection
