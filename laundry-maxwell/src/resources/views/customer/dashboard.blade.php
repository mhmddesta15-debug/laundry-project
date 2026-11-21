<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Riwayat Cucian Saya</h2></x-slot>

    @php $steps = \App\Models\Order::STEPS; $last = count($steps) - 1; @endphp

    <div class="mx-auto max-w-4xl space-y-4 px-4 py-8 sm:px-6 lg:px-8">
        @forelse ($orders as $o)
            @php $pct = round($o->stepIndex() / $last * 100); @endphp
            <a href="{{ route('customer.orders.show', $o) }}" class="block rounded-xl bg-white p-5 shadow transition hover:shadow-md">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="font-bold text-sky-700">{{ $o->no_resi }}</p>
                        <p class="text-sm text-gray-500">{{ $o->service->name }} · {{ $o->created_at->translatedFormat('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ in_array($o->status, ['siap_diambil', 'sudah_diambil']) ? 'bg-green-100 text-green-700' : 'bg-sky-100 text-sky-700' }}">{{ $o->statusLabel() }}</span>
                        <p class="mt-1 text-sm font-medium">Rp {{ number_format($o->total, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="mt-4 h-2 overflow-hidden rounded-full bg-gray-200">
                    <div class="h-2 rounded-full bg-sky-600" style="width: {{ $pct }}%"></div>
                </div>
            </a>
        @empty
            <div class="rounded-xl bg-white p-10 text-center text-gray-500 shadow">Belum ada riwayat transaksi.</div>
        @endforelse

        {{ $orders->links() }}
    </div>
</x-app-layout>
