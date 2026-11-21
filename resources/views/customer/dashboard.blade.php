{{-- Dashboard pelanggan untuk melihat pesanan dan membuka detail pelacakan. --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-700">Ruang pelanggan</p>
                <h1 class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">Riwayat Cucian Saya</h1>
                <p class="mt-1 text-sm text-slate-500">Semua pesanan dan progres cucian Anda, tersusun rapi di satu tempat.</p>
            </div>
            <a href="{{ route('home') }}#layanan" class="inline-flex items-center gap-2 text-sm font-bold text-sky-700 transition hover:text-sky-900">
                Lihat layanan <span aria-hidden="true">→</span>
            </a>
        </div>
    </x-slot>

    @php $steps = \App\Models\Order::STEPS; $last = max(count($steps) - 1, 1); @endphp

    <div class="mx-auto max-w-6xl space-y-7 px-4 py-8 sm:px-6 lg:px-8">
        <section class="customer-welcome rounded-3xl p-6 text-white sm:p-8 lg:p-10">
            <div class="relative z-10 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-cyan-100">Halo, {{ Auth::user()->name }} 👋</p>
                    <h2 class="mt-2 max-w-xl text-2xl font-extrabold leading-tight sm:text-3xl">Hari yang lebih ringan dimulai dari pakaian yang bersih.</h2>
                    <p class="mt-3 max-w-lg text-sm leading-6 text-sky-100">Pantau perjalanan cucian Anda dan lihat kapan pesanan siap diambil.</p>
                </div>
                <div class="hidden h-24 w-24 shrink-0 place-items-center rounded-3xl border border-white/20 bg-white/10 text-5xl shadow-inner sm:grid">🫧</div>
            </div>
        </section>

        <section class="grid gap-4 sm:grid-cols-2">
            <div class="customer-stat-card flex items-center gap-4 rounded-2xl p-5">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-sky-50 text-2xl">🧺</span>
                <div>
                    <p class="text-sm font-medium text-slate-500">Total pesanan</p>
                    <p class="mt-1 text-2xl font-extrabold tracking-tight text-slate-900">{{ $orders->total() }}</p>
                </div>
            </div>
            <div class="customer-stat-card flex items-center gap-4 rounded-2xl p-5">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-emerald-50 text-2xl">✨</span>
                <div>
                    <p class="text-sm font-medium text-slate-500">Cara melacak</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">Gunakan nomor resi Anda</p>
                </div>
            </div>
        </section>

        <section>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Pesanan terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih pesanan untuk melihat detail dan status pembayaran.</p>
                </div>
            </div>

            <div class="space-y-4">
                @forelse ($orders as $o)
                    @php $pct = round($o->stepIndex() / $last * 100); @endphp
                    <a href="{{ route('customer.orders.show', $o) }}" class="customer-order-card block rounded-2xl bg-white p-5 transition sm:p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-4">
                                <span class="hidden h-12 w-12 shrink-0 place-items-center rounded-2xl bg-sky-50 text-xl sm:grid">🧺</span>
                                <div class="min-w-0">
                                    <p class="font-extrabold tracking-wide text-sky-800">{{ $o->no_resi }}</p>
                                    <p class="mt-1 text-sm font-semibold text-slate-800">{{ $o->service->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $o->created_at->translatedFormat('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ in_array($o->status, ['siap_diambil', 'sudah_diambil']) ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">{{ $o->statusLabel() }}</span>
                                <p class="mt-2 font-bold text-slate-900">Rp {{ number_format($o->total, 0, ',', '.') }}</p>
                                <p class="mt-1 text-xs font-semibold {{ $o->payment_status === 'lunas' ? 'text-emerald-600' : ($o->payment_status === 'ditolak' ? 'text-rose-600' : 'text-amber-600') }}">{{ $o->paymentStatusLabel() }}</p>
                            </div>
                        </div>
                        <div class="mt-5">
                            <div class="flex items-center justify-between text-xs font-semibold text-slate-500">
                                <span>Progres cucian</span>
                                <span>{{ $pct }}%</span>
                            </div>
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                <div class="h-full rounded-full bg-gradient-to-r from-sky-500 to-cyan-400 transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="admin-panel rounded-3xl bg-white px-6 py-12 text-center sm:py-16">
                        <div class="mx-auto grid h-16 w-16 place-items-center rounded-3xl bg-sky-50 text-3xl">🫧</div>
                        <h3 class="mt-5 text-lg font-extrabold text-slate-900">Belum ada cucian yang tercatat</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">Setelah Anda menitipkan cucian di outlet, pesanan dan progresnya akan tampil di sini.</p>
                        <a href="{{ route('home') }}#kontak" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">Hubungi outlet <span aria-hidden="true">→</span></a>
                    </div>
                @endforelse
            </div>

            @if ($orders->hasPages())
                <div class="mt-5">{{ $orders->links() }}</div>
            @endif
        </section>
    </div>
</x-app-layout>
