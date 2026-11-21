{{-- Formulir admin untuk mengatur rekening transfer dan gambar QRIS outlet. --}}
<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold text-sky-700">PENGATURAN OUTLET</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Metode Pembayaran</h1>
            <p class="mt-1 text-sm text-slate-500">Atur nomor rekening dan gambar QRIS yang ditampilkan kepada pelanggan.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8 sm:px-6 lg:px-8">
        @include('partials.flash')

        <form method="POST" action="{{ route('admin.payment-settings.update') }}" enctype="multipart/form-data" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            <section>
                <h2 class="font-semibold text-slate-900">Transfer bank</h2>
                <p class="mt-1 text-sm text-slate-500">Isi informasi rekening outlet. Bagian ini boleh dikosongkan jika hanya menerima QRIS.</p>
                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="bank_name" class="text-sm font-medium text-slate-700">Nama bank</label>
                        <input id="bank_name" name="bank_name" value="{{ old('bank_name', $settings->bank_name) }}" maxlength="100" class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: BCA">
                        @error('bank_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="bank_account_number" class="text-sm font-medium text-slate-700">Nomor rekening</label>
                        <input id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $settings->bank_account_number) }}" maxlength="50" class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Masukkan nomor rekening">
                        @error('bank_account_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="bank_account_name" class="text-sm font-medium text-slate-700">Nama pemilik rekening</label>
                        <input id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $settings->bank_account_name) }}" maxlength="150" class="mt-1 w-full rounded-lg border-slate-300 focus:border-sky-500 focus:ring-sky-500" placeholder="Nama sesuai rekening">
                        @error('bank_account_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="border-t border-slate-100 pt-6">
                <h2 class="font-semibold text-slate-900">Pembayaran QRIS</h2>
                <p class="mt-1 text-sm text-slate-500">Unggah gambar kode QRIS resmi outlet (JPG, PNG, atau WEBP; maks. 5 MB).</p>
                @if ($settings->qris_image_path)
                    <img src="{{ route('admin.payment-settings.qris') }}" alt="Kode QRIS saat ini" class="mt-4 h-64 w-64 rounded-lg border border-slate-200 bg-white object-contain p-2">
                    <label class="mt-3 flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="remove_qris" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                        Hapus gambar QRIS saat ini
                    </label>
                @endif
                <input type="file" name="qris_image" accept="image/jpeg,image/png,image/webp" class="mt-4 block w-full rounded-lg border border-slate-300 text-sm file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2">
                @error('qris_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </section>

            <div class="flex justify-end border-t border-slate-100 pt-5">
                <button class="rounded-xl bg-sky-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-sky-600/20 hover:bg-sky-700">Simpan Metode Pembayaran</button>
            </div>
        </form>
    </div>
</x-app-layout>
