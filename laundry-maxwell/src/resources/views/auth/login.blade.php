<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="mb-1 text-xl font-bold text-gray-800">Masuk</h1>
    <p class="mb-4 text-sm text-gray-500">Gunakan No. HP atau Email yang didaftarkan di kasir.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="login" value="No. HP / Email" />
            <x-text-input id="login" class="mt-1 block w-full" type="text" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">Pelanggan baru: password awal adalah No. HP Anda.</p>
        </div>

        <label for="remember_me" class="mt-4 inline-flex items-center">
            <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500" name="remember">
            <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
        </label>

        <div class="mt-6 flex items-center justify-between">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('home') }}">← Beranda</a>
            <x-primary-button>Masuk</x-primary-button>
        </div>
    </form>
</x-guest-layout>
