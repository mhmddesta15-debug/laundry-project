<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Data katalog layanan laundry yang dapat dipilih saat membuat pesanan. */
class Service extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    /** Ambil semua pesanan yang menggunakan layanan ini. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
