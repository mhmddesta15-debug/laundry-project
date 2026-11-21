<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Buat tabel untuk menyimpan informasi rekening dan lokasi file QRIS outlet. */
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 150)->nullable();
            $table->string('qris_image_path')->nullable();
            $table->timestamps();
        });
    }

    /** Hapus tabel pengaturan pembayaran saat migrasi dibatalkan. */
    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
