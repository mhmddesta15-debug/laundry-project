{{-- Laporan keuangan admin: ringkasan, filter tanggal, pemasukan, dan catatan pengeluaran. --}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-sky-700">LAPORAN OUTLET</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Keuangan</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau pemasukan pesanan lunas dan catat pengeluaran operasional.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.flash')

        <form method="GET" class="grid gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[1fr_1fr_auto_auto] sm:items-end">
            <div>
                <label for="start_date" class="text-sm font-medium text-slate-700">Dari tanggal</label>
                <input id="start_date" type="date" name="start_date" value="{{ $startDate }}" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>
            <div>
                <label for="end_date" class="text-sm font-medium text-slate-700">Sampai tanggal</label>
                <input id="end_date" type="date" name="end_date" value="{{ $endDate }}" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500">
            </div>
            <button class="rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">Tampilkan</button>
            <a href="{{ route('admin.finance.index') }}" class="rounded-xl border border-slate-200 px-5 py-2.5 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Bulan ini</a>
        </form>

        @if ($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <section class="grid gap-4 sm:grid-cols-3">
            <article class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pemasukan</p>
                <p class="mt-2 text-2xl font-bold text-emerald-700">Rp {{ number_format($income, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-400">Dari pesanan berstatus lunas pada periode ini</p>
            </article>
            <article class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pengeluaran</p>
                <p class="mt-2 text-2xl font-bold text-rose-700">Rp {{ number_format($expenses, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-400">Total biaya operasional yang dicatat</p>
            </article>
            <article class="rounded-2xl border border-sky-100 bg-gradient-to-br from-sky-50 to-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Saldo bersih</p>
                <p class="mt-2 text-2xl font-bold {{ $net >= 0 ? 'text-sky-800' : 'text-rose-700' }}">Rp {{ number_format($net, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-slate-400">Pemasukan dikurangi pengeluaran</p>
            </article>
        </section>

        <section class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.65fr)]">
            <div class="space-y-6">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="font-bold text-slate-900">Riwayat pemasukan</h2>
                        <p class="mt-1 text-sm text-slate-500">Pesanan yang pembayarannya telah dikonfirmasi lunas.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Tanggal lunas</th>
                                    <th class="px-5 py-3">Resi / pelanggan</th>
                                    <th class="px-5 py-3 text-right">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($incomeOrders as $order)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $order->paid_at?->format('d M Y, H:i') ?? '—' }}</td>
                                        <td class="px-5 py-4">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-sky-700 hover:text-sky-900">{{ $order->no_resi }}</a>
                                            <span class="mt-1 block text-xs text-slate-500">{{ $order->customer->name }}</span>
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-emerald-700">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-5 py-8 text-center text-slate-500">Belum ada pemasukan pada periode ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($incomeOrders->hasPages())
                        <div class="border-t border-slate-100 px-5 py-3">{{ $incomeOrders->links() }}</div>
                    @endif
                </div>

                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <h2 class="font-bold text-slate-900">Riwayat pengeluaran</h2>
                        <p class="mt-1 text-sm text-slate-500">Biaya operasional yang tercatat pada periode ini.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3">Tanggal / kategori</th>
                                    <th class="px-5 py-3">Keterangan</th>
                                    <th class="px-5 py-3 text-right">Jumlah</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($expenseEntries as $expense)
                                    <tr>
                                        <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                            {{ $expense->spent_on->format('d M Y') }}
                                            <span class="mt-1 block text-xs text-slate-400">{{ $expense->category }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="font-semibold text-slate-800">{{ $expense->title }}</span>
                                            @if ($expense->notes)<span class="mt-1 block text-xs text-slate-500">{{ $expense->notes }}</span>@endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-rose-700">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-5 py-4 text-right">
                                            <form method="POST" action="{{ route('admin.finance.expenses.destroy', $expense) }}" onsubmit="return confirm('Hapus catatan pengeluaran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="font-semibold text-rose-600 hover:text-rose-800">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-5 py-8 text-center text-slate-500">Belum ada pengeluaran pada periode ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if ($expenseEntries->hasPages())
                        <div class="border-t border-slate-100 px-5 py-3">{{ $expenseEntries->links() }}</div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('admin.finance.expenses.store') }}" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                @csrf
                <div>
                    <h2 class="font-bold text-slate-900">Catat pengeluaran</h2>
                    <p class="mt-1 text-sm text-slate-500">Masukkan biaya operasional outlet.</p>
                </div>
                <div>
                    <label for="title" class="text-sm font-medium text-slate-700">Nama pengeluaran</label>
                    <input id="title" name="title" value="{{ old('title') }}" maxlength="150" required class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Detergen">
                    @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="category" class="text-sm font-medium text-slate-700">Kategori</label>
                    <input id="category" name="category" value="{{ old('category') }}" maxlength="80" required class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Bahan laundry">
                    @error('category')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="amount" class="text-sm font-medium text-slate-700">Jumlah (Rp)</label>
                    <input id="amount" type="number" name="amount" value="{{ old('amount') }}" min="1" max="999999999999" step="1" required class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="50000">
                    @error('amount')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="spent_on" class="text-sm font-medium text-slate-700">Tanggal pengeluaran</label>
                    <input id="spent_on" type="date" name="spent_on" value="{{ old('spent_on', now()->toDateString()) }}" required class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500">
                    @error('spent_on')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="notes" class="text-sm font-medium text-slate-700">Catatan (opsional)</label>
                    <textarea id="notes" name="notes" rows="3" maxlength="1000" class="mt-1 w-full rounded-xl border-slate-200 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Detail tambahan">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <button class="w-full rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">Simpan Pengeluaran</button>
            </form>
        </section>
    </div>
</x-app-layout>
