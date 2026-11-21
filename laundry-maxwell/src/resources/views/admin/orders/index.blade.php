<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Kasir – Daftar Pesanan</h2>
            <a href="{{ route('admin.orders.create') }}" class="rounded-lg bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">+ Order Baru</a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.flash')

        <form method="GET" class="mb-4 flex flex-col gap-2 sm:flex-row">
            <input name="q" value="{{ request('q') }}" placeholder="Cari resi / nama / No. HP"
                   class="flex-1 rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
            <select name="status" class="rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                <option value="">Semua Status</option>
                @foreach (\App\Models\Order::STEPS as $k => $l)
                    <option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>
                @endforeach
            </select>
            <button class="rounded-lg bg-gray-800 px-5 py-2 text-white">Filter</button>
        </form>

        <div class="overflow-x-auto rounded-xl bg-white shadow">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-4 py-3">Resi</th><th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Layanan</th><th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3">Status</th><th class="px-4 py-3">Bayar</th><th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                @forelse ($orders as $o)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-semibold text-sky-700">{{ $o->no_resi }}</td>
                        <td class="px-4 py-3">{{ $o->customer->name }}<br><span class="text-xs text-gray-500">{{ $o->customer->phone }}</span></td>
                        <td class="px-4 py-3">{{ $o->service->name }}<br><span class="text-xs text-gray-500">{{ rtrim(rtrim(number_format($o->qty, 2, '.', ''), '0'), '.') }} {{ $o->service->unit }}</span></td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($o->total, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                {{ in_array($o->status, ['siap_diambil', 'sudah_diambil']) ? 'bg-green-100 text-green-700' : 'bg-sky-100 text-sky-700' }}">{{ $o->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium {{ $o->payment_status === 'lunas' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $o->payment_status === 'lunas' ? 'Lunas' : 'Belum' }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.orders.show', $o) }}" class="text-sky-700 hover:underline">Kelola</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">Belum ada pesanan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
