{{-- Formulir bersama untuk menambah atau mengubah layanan laundry. --}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-sky-700">KATALOG OUTLET</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">{{ $service->exists ? 'Edit Jenis Layanan' : 'Tambah Jenis Layanan' }}</h1>
            <p class="mt-1 text-sm text-slate-500">Lengkapi informasi layanan yang ditawarkan outlet.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.flash')

        <form method="POST" action="{{ $service->exists ? route('admin.services.update', $service) : route('admin.services.store') }}" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @csrf
            @if ($service->exists)
                @method('PUT')
            @endif
            <div class="border-b border-slate-100 bg-gradient-to-r from-sky-50 to-white px-6 py-5">
                <h2 class="font-bold text-slate-900">Informasi layanan</h2>
                <p class="mt-1 text-sm text-slate-500">Data ini digunakan di katalog dan saat kasir membuat pesanan.</p>
            </div>
            <div class="grid gap-5 p-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="text-sm font-semibold text-slate-700">Nama layanan <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" value="{{ old('name', $service->name) }}" required maxlength="100" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Cuci Kering Express">
                    @error('name')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="price" class="text-sm font-semibold text-slate-700">Harga (Rp) <span class="text-rose-500">*</span></label>
                    <input id="price" type="number" name="price" value="{{ old('price', $service->price) }}" required min="1" max="4294967295" step="1" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="7000">
                    @error('price')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="unit" class="text-sm font-semibold text-slate-700">Satuan <span class="text-rose-500">*</span></label>
                    <select id="unit" name="unit" required class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="kg" @selected(old('unit', $service->unit ?: 'kg') === 'kg')>Kilogram (kg)</option>
                        <option value="pcs" @selected(old('unit', $service->unit) === 'pcs')>Satuan (pcs)</option>
                    </select>
                    @error('unit')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="duration_hours" class="text-sm font-semibold text-slate-700">Estimasi selesai (jam) <span class="text-rose-500">*</span></label>
                    <input id="duration_hours" type="number" name="duration_hours" value="{{ old('duration_hours', $service->duration_hours ?: 48) }}" required min="1" max="720" step="1" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="48">
                    <p class="mt-1 text-xs text-slate-500">Maksimal 720 jam (30 hari).</p>
                    @error('duration_hours')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-700">Status layanan</label>
                    <label class="mt-2 flex min-h-11 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 px-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $service->exists ? $service->is_active : true)) class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm text-slate-700">Aktif dan tersedia untuk pesanan baru</span>
                    </label>
                    @error('is_active')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="description" class="text-sm font-semibold text-slate-700">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" maxlength="1000" class="mt-2 w-full rounded-xl border-slate-300 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Jelaskan layanan secara singkat...">{{ old('description', $service->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-slate-100 bg-slate-50/70 px-6 py-4 sm:flex-row sm:justify-between">
                <a href="{{ route('admin.services.index') }}" class="inline-flex justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Kembali</a>
                <button class="inline-flex justify-center rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 transition hover:bg-sky-700">{{ $service->exists ? 'Simpan Perubahan' : 'Simpan Layanan' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
