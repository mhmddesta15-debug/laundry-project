{{-- Katalog layanan admin dengan ringkasan status, harga, dan aksi pengelolaan. --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold text-sky-700">KATALOG OUTLET</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Jenis Layanan</h1>
                <p class="mt-1 text-sm text-slate-500">Atur nama, harga, satuan, dan estimasi layanan laundry.</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">
                <span class="text-lg leading-none">+</span> Tambah Layanan
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.flash')

        <div class="mb-6 grid gap-4 sm:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Semua layanan</p>
                <p class="mt-2 text-3xl font-bold text-slate-900">{{ $stats['total'] }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-white to-emerald-50 p-5 shadow-sm">
                <p class="text-sm font-medium text-emerald-700">Layanan aktif</p>
                <p class="mt-2 text-3xl font-bold text-emerald-800">{{ $stats['active'] }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-5 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Layanan nonaktif</p>
                <p class="mt-2 text-3xl font-bold text-slate-700">{{ $stats['inactive'] }}</p>
            </div>
        </div>

        <div class="admin-table overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h2 class="font-bold text-slate-900">Daftar katalog</h2>
                    <p class="mt-1 text-sm text-slate-500">Layanan nonaktif tidak akan muncul saat membuat pesanan baru.</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3.5">Layanan</th>
                            <th class="px-5 py-3.5">Harga</th>
                            <th class="px-5 py-3.5">Estimasi</th>
                            <th class="px-5 py-3.5">Pesanan</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($services as $service)
                            <tr class="transition hover:bg-slate-50/80">
                                <td class="px-5 py-4">
                                    <div class="font-semibold text-slate-900">{{ $service->name }}</div>
                                    <div class="mt-1 max-w-xs truncate text-xs text-slate-500">{{ $service->description ?: 'Belum ada deskripsi' }}</div>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-800">Rp {{ number_format($service->price, 0, ',', '.') }} <span class="font-normal text-slate-500">/ {{ $service->unit }}</span></td>
                                <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $service->duration_hours < 24 ? $service->duration_hours . ' jam' : rtrim(rtrim(number_format($service->duration_hours / 24, 1, ',', '.'), '0'), ',') . ' hari' }}</td>
                                <td class="px-5 py-4 text-slate-600">{{ $service->orders_count }} pesanan</td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $service->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $service->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="rounded-lg px-3 py-2 font-semibold text-sky-700 transition hover:bg-sky-50">Edit</a>
                                    <form method="POST" action="{{ route('admin.services.destroy', $service) }}" class="inline" onsubmit="return confirm('Hapus layanan {{ $service->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg px-3 py-2 font-semibold text-rose-600 transition hover:bg-rose-50">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-16 text-center">
                                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-sky-50 text-2xl">🧺</div>
                                    <p class="mt-4 font-semibold text-slate-800">Belum ada jenis layanan</p>
                                    <p class="mt-1 text-sm text-slate-500">Tambahkan layanan pertama untuk mulai membuat katalog.</p>
                                    <a href="{{ route('admin.services.create') }}" class="mt-4 inline-flex rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700">Tambah Layanan</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($services->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">{{ $services->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
