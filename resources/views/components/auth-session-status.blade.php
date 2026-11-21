{{-- Component: Menampilkan status autentikasi seperti pesan sukses atau error dari Laravel Breeze. --}}

@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif

