<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Order Baru (Walk-in)</h2></x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8"
         x-data="orderForm(@js($services), '{{ route('admin.customers.lookup') }}', @js(old('service_id')), @js(old('qty', 1)))">
        @include('partials.flash')

        <form method="POST" action="{{ route('admin.orders.store') }}" class="space-y-6 rounded-xl bg-white p-6 shadow">
            @csrf

            <fieldset>
                <legend class="mb-3 font-semibold text-gray-800">Data Pelanggan</legend>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium">No. HP / WhatsApp *</label>
                        <input name="phone" x-model="phone" @blur="lookup()" value="{{ old('phone') }}" required inputmode="tel"
                               class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500" placeholder="0812xxxxxxx">
                        <p x-show="found" x-cloak class="mt-1 text-xs text-green-600">✓ Pelanggan lama ditemukan, data terisi otomatis.</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Nama *</label>
                        <input name="name" x-model="name" required class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email</label>
                        <input type="email" name="email" x-model="email" class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="text-sm font-medium">Alamat</label>
                        <input name="address" x-model="address" class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend class="mb-3 font-semibold text-gray-800">Detail Cucian</legend>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-sm font-medium">Jenis Layanan *</label>
                        <select name="service_id" x-model="serviceId" required class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                            <option value="">-- Pilih --</option>
                            <template x-for="s in services" :key="s.id">
                                <option :value="s.id" x-text="`${s.name} – Rp ${fmt(s.price)}/${s.unit}`"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Berat / Jumlah <span x-text="selected ? '(' + selected.unit + ')' : ''"></span> *</label>
                        <input type="number" name="qty" x-model.number="qty" step="0.1" min="0.1" required
                               class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium">Catatan</label>
                        <textarea name="notes" rows="2" class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-between rounded-lg bg-sky-50 px-4 py-3">
                    <span class="text-sm text-slate-600">Estimasi selesai: <b x-text="selected ? selected.duration_hours + ' jam' : '-'"></b></span>
                    <span class="text-lg font-bold text-sky-700">Total: Rp <span x-text="fmt(total)"></span></span>
                </div>
            </fieldset>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.orders.index') }}" class="rounded-lg border px-5 py-2">Batal</a>
                <button class="rounded-lg bg-sky-600 px-6 py-2 font-semibold text-white hover:bg-sky-700">Simpan &amp; Cetak Nota</button>
            </div>
        </form>
    </div>

    <script>
        function orderForm(services, lookupUrl, serviceId, qty) {
            return {
                services, serviceId: serviceId || '', qty: qty || 1,
                phone: @js(old('phone', '')), name: @js(old('name', '')),
                email: @js(old('email', '')), address: @js(old('address', '')), found: false,
                get selected() { return this.services.find(s => s.id == this.serviceId); },
                get total() { return this.selected ? Math.round(this.selected.price * (this.qty || 0)) : 0; },
                fmt(n) { return new Intl.NumberFormat('id-ID').format(n); },
                async lookup() {
                    if (this.phone.length < 8) return;
                    try {
                        const r = await fetch(lookupUrl + '?phone=' + encodeURIComponent(this.phone), { headers: { Accept: 'application/json' } });
                        const d = await r.json();
                        this.found = d.found;
                        if (d.found) { this.name = d.name || ''; this.email = d.email || ''; this.address = d.address || ''; }
                    } catch (e) {}
                },
            };
        }
    </script>
</x-app-layout>
