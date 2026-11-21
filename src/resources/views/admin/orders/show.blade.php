<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-semibold text-gray-800">Pesanan {{ $order->no_resi }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.orders.nota', $order) }}" target="_blank" class="rounded-lg border px-4 py-2 text-sm">🖨️ Cetak Nota</a>
                <a href="{{ route('admin.orders.index') }}" class="rounded-lg border px-4 py-2 text-sm">← Kembali</a>
            </div>
        </div>
    </x-slot>

    @php $next = $order->nextStatus(); @endphp

    <div class="mx-auto grid max-w-6xl gap-6 px-4 py-8 sm:px-6 lg:grid-cols-3 lg:px-8">
        <div class="space-y-6 lg:col-span-2">
            @include('partials.flash')

            {{-- Detail --}}
            <div class="rounded-xl bg-white p-6 shadow">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div><dt class="text-gray-500">Pelanggan</dt><dd class="font-medium">{{ $order->customer->name }}</dd></div>
                    <div><dt class="text-gray-500">No. HP</dt><dd class="font-medium">{{ $order->customer->phone }}</dd></div>
                    <div><dt class="text-gray-500">Layanan</dt><dd class="font-medium">{{ $order->service->name }}</dd></div>
                    <div><dt class="text-gray-500">Jumlah</dt><dd class="font-medium">{{ rtrim(rtrim(number_format($order->qty, 2, '.', ''), '0'), '.') }} {{ $order->service->unit }}</dd></div>
                    <div><dt class="text-gray-500">Total</dt><dd class="font-medium">Rp {{ number_format($order->total, 0, ',', '.') }}</dd></div>
                    <div><dt class="text-gray-500">Pembayaran</dt>
                        <dd class="font-medium {{ $order->payment_status === 'lunas' ? 'text-green-600' : 'text-red-500' }}">{{ $order->payment_status === 'lunas' ? 'Lunas' : 'Belum Bayar' }}</dd></div>
                    <div><dt class="text-gray-500">Masuk</dt><dd class="font-medium">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Estimasi Selesai</dt><dd class="font-medium">{{ $order->estimated_done_at?->translatedFormat('d M Y, H:i') }}</dd></div>
                    @if ($order->notes)<div class="col-span-2"><dt class="text-gray-500">Catatan</dt><dd>{{ $order->notes }}</dd></div>@endif
                </dl>

                @if ($order->payment_status !== 'lunas')
                    <form method="POST" action="{{ route('admin.orders.pay', $order) }}" class="mt-4">
                        @csrf @method('PATCH')
                        <button class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">Tandai Lunas</button>
                    </form>
                @endif
            </div>

            {{-- Update status --}}
            <div class="rounded-xl bg-white p-6 shadow">
                <h3 class="mb-4 font-semibold">Perbarui Status</h3>
                <p class="mb-4 text-sm text-gray-500">Status saat ini: <b class="text-sky-700">{{ $order->statusLabel() }}</b></p>

                @if ($next)
                    <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="mb-4">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next }}">
                        <button class="w-full rounded-lg bg-sky-600 py-3 font-semibold text-white hover:bg-sky-700">
                            Lanjut ke tahap: {{ \App\Models\Order::STEPS[$next] }} →
                        </button>
                    </form>
                @endif

                <form method="POST" action="{{ route('admin.orders.status', $order) }}" class="grid gap-3 sm:grid-cols-3">
                    @csrf @method('PATCH')
                    <select name="status" class="rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                        @foreach (\App\Models\Order::STEPS as $k => $l)
                            <option value="{{ $k }}" @selected($order->status === $k)>{{ $l }}</option>
                        @endforeach
                    </select>
                    <input name="note" maxlength="255" placeholder="Catatan (opsional)" class="rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                    <button class="rounded-lg bg-gray-800 py-2 text-white">Simpan</button>
                </form>
            </div>
        </div>

        {{-- Riwayat --}}
        <div class="rounded-xl bg-white p-6 shadow">
            <h3 class="mb-4 font-semibold">Riwayat Progres</h3>
            <ol class="space-y-4 border-l-2 border-sky-200 pl-4">
                @foreach ($order->statuses->reverse() as $log)
                    <li class="relative">
                        <span class="absolute -left-[22px] top-1 h-3 w-3 rounded-full bg-sky-600"></span>
                        <p class="text-sm font-medium">{{ \App\Models\Order::STEPS[$log->status] ?? $log->status }}</p>
                        <p class="text-xs text-gray-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }}{{ $log->user ? ' · ' . $log->user->name : '' }}</p>
                        @if ($log->note)<p class="text-sm text-gray-600">{{ $log->note }}</p>@endif
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</x-app-layout>
