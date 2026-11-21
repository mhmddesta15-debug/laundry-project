<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Tambahkan waktu pelunasan dan isi tanggal perkiraan untuk order lama yang sudah lunas. */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('paid_at')->nullable()->index();
        });

        DB::table('orders')
            ->where('payment_status', 'lunas')
            ->update(['paid_at' => DB::raw('updated_at')]);
    }

    /** Hapus kolom waktu pelunasan saat migrasi dibatalkan. */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('paid_at');
        });
    }
};
