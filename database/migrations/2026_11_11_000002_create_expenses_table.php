<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Buat tabel catatan pengeluaran operasional beserta admin pencatatnya. */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 150);
            $table->string('category', 80);
            $table->unsignedBigInteger('amount');
            $table->date('spent_on')->index();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /** Hapus tabel pengeluaran saat migrasi dibatalkan. */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
