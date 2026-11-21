<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Menyimpan metode pembayaran outlet yang ditampilkan kepada pelanggan. */
class PaymentSetting extends Model
{
    protected $guarded = [];

    /** Ambil pengaturan pembayaran tersimpan atau nilai awal dari konfigurasi aplikasi. */
    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'bank_name' => config('laundry.bank_name'),
            'bank_account_number' => config('laundry.bank_account_number'),
            'bank_account_name' => config('laundry.bank_account_name'),
        ]);
    }
}
