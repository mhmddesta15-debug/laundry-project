<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Catatan kronologis setiap perubahan progres pesanan. */
class OrderStatus extends Model
{
    public $timestamps = false;
    protected $guarded = [];
    protected $casts = ['created_at' => 'datetime'];

    /** Ambil pengguna yang membuat catatan perubahan status ini. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
