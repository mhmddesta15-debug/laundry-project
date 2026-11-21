{{-- Daftar pesanan admin untuk mencari order dan memantau progres serta pembayaran. --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-sky-700">RUANG KASIR</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Daftar Pesanan</h1>
                <p class="mt-1 text-sm text-slate-500">Pantau cucian, kelola status, dan periksa pembayaran pelanggan.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.services.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">Kelola Layanan</a>
                <a href="{{ route('admin.orders.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700"><span class="text-lg leading-none">+</span> Order Baru</a>
            </div>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.flash')

        <div class="mb-6 overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-slate-800 to-sky-900 p-6 text-white shadow-lg shadow-slate-900/10 sm:p-8">
            <div class="flex items-center justify-between gap-6">
                <div>
                    <p class="text-sm font-medium text-sky-200">Panel operasional</p>
                    <h2 class="mt-2 text-xl font-bold sm:text-2xl">Semua pesanan, lebih mudah dipantau.</h2>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-slate-300">Gunakan pencarian dan filter untuk menemukan resi, melihat progres cucian, atau memastikan status pembayaran.</p>
                </div>
                <div class="hidden h-16 w-16 shrink-0 place-items-center rounded-2xl border border-white/10 bg-white/10 text-3xl sm:grid">🫧</div>
            </div>
        </div>

        <form method="GET" class="mb-5 grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:grid-cols-[minmax(220px,1fr)_190px_210px_auto]">
            <label class="relative">
                <span class="sr-only">Cari pesanan</span>
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.8" cy="8.8" r="5.8" stroke="currentColor" stroke-width="1.6"/><path d="m13.2 13.2 4 4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </span>
                <input name="q" value="{{ request('q') }}" placeholder="Cari resi / nama / No. HP" class="w-full rounded-xl border-slate-200 py-2.5 pl-9 text-sm placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500">
            </label>
            <select name="status" class="rounded-xl border-slate-200 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua Status</option>
                @foreach (\App\Models\Order::STEPS as $k => $l)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>
                @endforeach
            </select>
            <select name="payment_status" class="rounded-xl border-slate-200 py-2.5 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua Pembayaran</option>
                @foreach (\App\Models\Order::PAYMENT_STATUSES as $k => $l)
                    <option value="{{ $k }}" @selected(request('payment_status') === $k)>{{ $l }}</option>
                @endforeach
            </select>
            <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">Terapkan Filter</button>
        </form>

        <div class="admin-table overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 class="font-bold text-slate-900">Pesanan masuk</h2>
                    <p class="mt-1 text-sm text-slate-500">Menampilkan {{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }} dari {{ $orders->total() }} pesanan</p>
                </div>
                @if (request()->hasAny(['q', 'status', 'payment_status']))
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-900">Hapus filter</a>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3.5">Resi</th>
                            <th class="px-5 py-3.5">Pelanggan</th>
                            <th class="px-5 py-3.5">Layanan</th>
                            <th class="px-5 py-3.5 text-right">Total</th>
                            <th class="px-5 py-3.5">Progres</th>
                            <th class="px-5 py-3.5">Pembayaran</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($orders as $o)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <span class="font-bold text-sky-700">{{ $o->no_resi }}</span>
                                    <span class="mt-1 block text-xs text-slate-400">{{ $o->created_at->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-semibold text-slate-800">{{ $o->customer->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ $o->customer->phone }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium text-slate-800">{{ $o->service->name }}</span>
                                    <span class="mt-1 block text-xs text-slate-500">{{ rtrim(rtrim(number_format($o->qty, 2, '.', ''), '0'), '.') }} {{ $o->service->unit }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-800">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ in_array($o->status, ['siap_diambil', 'sudah_diambil']) ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700' }}">{{ $o->statusLabel() }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $o->payment_status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : ($o->payment_status === 'ditolak' ? 'bg-rose-50 text-rose-700' : ($o->payment_status === 'menunggu_verifikasi' ? 'bg-amber-50 text-amber-700' : 'bg-slate-100 text-slate-600')) }}">{{ $o->paymentStatusLabel() }}</span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('admin.orders.show', $o) }}" class="inline-flex rounded-lg px-3 py-2 font-semibold text-sky-700 transition hover:bg-sky-50">Kelola <span class="ml-1" aria-hidden="true">→</span></a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-16 text-center">
                                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-2xl">🧺</div>
                                    <p class="mt-4 font-semibold text-slate-800">{{ request()->hasAny(['q', 'status', 'payment_status']) ? 'Pesanan tidak ditemukan' : 'Belum ada pesanan' }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ request()->hasAny(['q', 'status', 'payment_status']) ? 'Coba ubah kata kunci atau filter yang digunakan.' : 'Pesanan baru akan muncul di halaman ini.' }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
