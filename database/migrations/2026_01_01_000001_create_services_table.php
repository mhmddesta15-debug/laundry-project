<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Buat tabel katalog layanan, termasuk satuan, harga, durasi, dan status aktif. */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('unit', ['kg', 'pcs'])->default('kg');
            $table->unsignedInteger('price');                     // Rupiah per unit
            $table->unsignedSmallInteger('duration_hours')->default(48);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /** Hapus tabel layanan saat migrasi dibatalkan. */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
