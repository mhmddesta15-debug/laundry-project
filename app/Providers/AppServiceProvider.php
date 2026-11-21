<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /** Daftarkan binding layanan aplikasi ke service container jika diperlukan. */
    public function register(): void
    {
        //
    }

    /** Jalankan konfigurasi global aplikasi saat proses bootstrap. */
    public function boot(): void
    {
        //
    }
}
