<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Buat log perubahan status pesanan dengan pengguna pencatat dan waktu perubahan. */
    public function up(): void
    {
        // Log riwayat progres setiap order
        Schema::create('order_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20);
            $table->string('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /** Hapus tabel riwayat status saat migrasi dibatalkan. */
    public function down(): void
    {
        Schema::dropIfExists('order_statuses');
    }
};
