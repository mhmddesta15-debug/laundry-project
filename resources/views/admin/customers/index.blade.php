{{-- Daftar pelanggan admin dengan ringkasan kontak dan jumlah pesanan. --}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-sky-700">AKUN PELANGGAN</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Pelanggan Terdaftar</h1>
            <p class="mt-1 text-sm text-slate-500">Daftar pelanggan yang membuat akun sendiri dan dapat dilayani outlet.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="admin-table overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <form method="GET" action="{{ route('admin.customers.index') }}" class="flex flex-col gap-3 border-b border-slate-100 p-5 sm:flex-row">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, no. HP, atau email"
                       class="w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500 sm:max-w-md">
                <button class="rounded-lg bg-sky-600 px-5 py-2 font-semibold text-white hover:bg-sky-700">Cari</button>
            </form>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3.5">Pelanggan</th>
                            <th class="px-5 py-3.5">No. HP</th>
                            <th class="px-5 py-3.5">Alamat</th>
                            <th class="px-5 py-3.5">Pesanan</th>
                            <th class="px-5 py-3.5">Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($customers as $customer)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">{{ $customer->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $customer->email ?: 'Tidak mencantumkan email' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-700">{{ $customer->phone }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $customer->address ?: '-' }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $customer->orders_count }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $customer->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <p class="font-semibold text-slate-800">Belum ada pelanggan terdaftar</p>
                                    <p class="mt-1 text-sm text-slate-500">Pelanggan yang mendaftar sendiri akan tampil di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($customers->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $customers->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
