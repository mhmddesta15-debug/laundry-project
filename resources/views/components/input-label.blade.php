{{-- Component: Label form yang memudahkan pengguna memahami field input. --}}

@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
</label>

