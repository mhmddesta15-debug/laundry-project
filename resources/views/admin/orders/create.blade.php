{{-- Formulir kasir untuk membuat pesanan laundry bagi pelanggan terdaftar. --}}
<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">Pesanan Baru</h2></x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8"
         x-data="orderForm(@js($services), '{{ route('admin.customers.lookup') }}', @js(old('service_id')), @js(old('qty', 1)))">
        @include('partials.flash')

        <form method="POST" action="{{ route('admin.orders.store') }}" class="admin-panel space-y-6 rounded-2xl bg-white p-6">
            @csrf

            <fieldset>
                <legend class="mb-3 font-semibold text-gray-800">Pelanggan Terdaftar</legend>
                <label for="customer-search" class="text-sm font-medium">Cari nama atau No. HP / WhatsApp *</label>
                <input id="customer-search" type="search" x-model="searchQuery" @input="searchCustomers()"
                       autocomplete="off" class="mt-1 w-full rounded-lg border-gray-300 focus:border-sky-500 focus:ring-sky-500"
                       placeholder="Ketik nama atau nomor HP pelanggan">
                <input type="hidden" name="phone" :value="selectedCustomer ? selectedCustomer.phone : ''">
                <p class="mt-2 text-sm text-slate-500">Pelanggan harus membuat akun sendiri terlebih dahulu. Admin tidak membuat akun pelanggan dari form pesanan.</p>
                <div x-show="customers.length" x-cloak class="mt-2 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                    <template x-for="customer in customers" :key="customer.id">
                        <button type="button" @click="selectCustomer(customer)"
                                class="flex w-full flex-col items-start border-b border-slate-100 px-4 py-3 text-left last:border-0 hover:bg-sky-50">
                            <span class="font-semibold text-slate-800" x-text="customer.name"></span>
                            <span class="text-sm text-slate-500" x-text="customer.phone"></span>
                        </button>
                    </template>
                </div>
                <p x-show="selectedCustomer" x-cloak class="mt-2 text-sm text-green-700">
                    Pelanggan dipilih: <strong x-text="selectedCustomer ? selectedCustomer.name + ' (' + selectedCustomer.phone + ')' : ''"></strong>
                </p>
                <p x-show="searchMessage" x-cloak class="mt-2 text-sm text-amber-700" x-text="searchMessage"></p>
                <p x-show="searchError" x-cloak class="mt-2 text-sm text-rose-700">Data pelanggan gagal dicari. Coba lagi.</p>
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
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
                <button :disabled="!selectedCustomer" class="rounded-lg bg-sky-600 px-6 py-2 font-semibold text-white hover:bg-sky-700 disabled:cursor-not-allowed disabled:opacity-50">Simpan &amp; Cetak Nota</button>
            </div>
        </form>
    </div>

    <script>
        function orderForm(services, lookupUrl, serviceId, qty) {
            return {
                services, serviceId: serviceId || '', qty: qty || 1,
                searchQuery: '', customers: [], selectedCustomer: null,
                searchMessage: '', searchError: false, searchTimer: null,
                get selected() { return this.services.find(s => s.id == this.serviceId); },
                get total() { return this.selected ? Math.round(this.selected.price * (this.qty || 0)) : 0; },
                fmt(n) { return new Intl.NumberFormat('id-ID').format(n); },
                searchCustomers() {
                    clearTimeout(this.searchTimer);
                    this.selectedCustomer = null;
                    this.customers = [];
                    this.searchError = false;
                    this.searchMessage = '';

                    const query = this.searchQuery.trim();
                    if (query.length < 2) return;

                    this.searchTimer = setTimeout(async () => {
                        try {
                            const r = await fetch(lookupUrl + '?q=' + encodeURIComponent(query), { headers: { Accept: 'application/json' } });
                            if (!r.ok) throw new Error('Pencarian pelanggan gagal.');
                            const data = await r.json();
                            this.customers = data.customers;
                            if (!this.customers.length) {
                                this.searchMessage = 'Pelanggan tidak ditemukan. Pastikan pelanggan sudah mendaftar.';
                            }
                        } catch (e) {
                            this.searchError = true;
                        }
                    }, 250);
                },
                selectCustomer(customer) {
                    this.selectedCustomer = customer;
                    this.searchQuery = customer.name + ' - ' + customer.phone;
                    this.customers = [];
                    this.searchMessage = '';
                },
            };
        }
    </script>
</x-app-layout>
