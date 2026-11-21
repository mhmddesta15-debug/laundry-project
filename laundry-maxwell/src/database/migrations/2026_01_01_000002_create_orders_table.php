<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no_resi', 25)->unique();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('admin_id')->constrained('users');      // kasir yang input
            $table->foreignId('service_id')->constrained('services');
            $table->decimal('qty', 8, 2);                              // kg / pcs
            $table->unsignedInteger('price_per_unit');                 // snapshot harga saat order
            $table->unsignedInteger('total');
            $table->string('status', 20)->default('diterima')->index();
            $table->enum('payment_status', ['belum_bayar', 'lunas'])->default('belum_bayar');
            $table->text('notes')->nullable();
            $table->timestamp('estimated_done_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
