<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->unique();
            }

            if (! Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable();
            }

            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('pelanggan')->index();
            }

            if (! Schema::hasColumn('users', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable();
            }

            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Keep compatibility columns so rolling back cannot discard customer data.
    }
};
