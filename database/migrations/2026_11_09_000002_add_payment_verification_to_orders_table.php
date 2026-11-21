<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tambahkan status verifikasi pembayaran serta lokasi bukti dan catatan penolakan. */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 30)->default('belum_bayar')->change();
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_note')->nullable();
        });
    }

    /** Kembalikan status ke nilai lama sebelum menghapus kolom bukti pembayaran. */
    public function down(): void
    {
        DB::table('orders')
            ->whereNotIn('payment_status', ['belum_bayar', 'lunas'])
            ->update(['payment_status' => 'belum_bayar']);

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_path', 'payment_note']);
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->default('belum_bayar')->change();
        });
    }
};
