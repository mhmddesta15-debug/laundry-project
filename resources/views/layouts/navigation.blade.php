{{-- Layout navigasi: menampilkan menu utama sesuai peran admin maupun pelanggan. --}}

<nav x-data="{ open: false }" class="app-navigation border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-[72px] justify-between">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <x-application-logo class="app-brand-mark h-10 w-10 shrink-0 text-sky-600" aria-hidden="true" focusable="false" />
                        <span class="hidden text-sm font-bold tracking-tight text-slate-800 sm:block">{{ config('laundry.name', 'Laundry Maxwell') }}</span>
                    </a>
                </div>
                <div class="hidden items-center gap-1 sm:ms-8 sm:flex">
                    @if (Auth::user()->role === 'admin')
                        <a href="{{ route('admin.orders.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.orders.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">Pesanan</a>
                        <a href="{{ route('admin.finance.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.finance.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">Keuangan</a>
                        <a href="{{ route('admin.customers.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.customers.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">Pelanggan</a>
                        <a href="{{ route('admin.services.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.services.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">Jenis Layanan</a>
                        <a href="{{ route('admin.payment-settings.index') }}" class="rounded-xl px-4 py-2 text-sm font-semibold transition {{ request()->routeIs('admin.payment-settings.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">Pembayaran</a>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="rounded-xl px-4 py-2 text-sm font-semibold {{ request()->routeIs('customer.*') ? 'bg-sky-50 text-sky-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-800' }}">Dashboard</a>
                    @endif
                    <a href="{{ route('home') }}" class="rounded-xl px-4 py-2 text-sm font-semibold text-slate-500 transition hover:bg-slate-50 hover:text-slate-800">Beranda</a>
                </div>
            </div>
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50">
                            <span class="grid h-8 w-8 place-items-center rounded-full bg-sky-100 text-xs font-bold text-sky-700">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                            <span>{{ Auth::user()->name }}</span>
                            <span class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-xl p-2 text-slate-500 transition hover:bg-slate-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-slate-100 bg-white px-4 py-3 sm:hidden">
        <div class="space-y-1">
            @if (Auth::user()->role === 'admin')
                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pesanan</a>
                <a href="{{ route('admin.finance.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Keuangan</a>
                <a href="{{ route('admin.customers.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pelanggan</a>
                <a href="{{ route('admin.services.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Jenis Layanan</a>
                <a href="{{ route('admin.payment-settings.index') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Pembayaran</a>
            @else
                <a href="{{ route('customer.dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Dashboard</a>
            @endif
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Beranda</a>
        </div>
        <div class="mt-3 border-t border-slate-100 pt-4">
            <div class="px-3">
                <div class="font-semibold text-slate-800">{{ Auth::user()->name }}</div>
                <div class="text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-sm text-slate-600 hover:bg-slate-50">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-600 hover:bg-slate-50">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>
