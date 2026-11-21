<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    /** Urutan tahapan progres cucian */
    public const STEPS = [
        'diterima'      => 'Diterima',
        'cuci'          => 'Dicuci',
        'pengeringan'   => 'Pengeringan',
        'setrika'       => 'Disetrika',
        'siap_diambil'  => 'Siap Diambil',
        'sudah_diambil' => 'Sudah Diambil',
    ];

    protected $guarded = [];
    protected $casts = [
        'estimated_done_at' => 'datetime',
        'picked_up_at'      => 'datetime',
        'qty'               => 'float',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function admin(): BelongsTo    { return $this->belongsTo(User::class, 'admin_id'); }
    public function service(): BelongsTo  { return $this->belongsTo(Service::class); }
    public function statuses(): HasMany   { return $this->hasMany(OrderStatus::class)->oldest('created_at')->oldest('id'); }

    public function stepIndex(): int
    {
        return (int) array_search($this->status, array_keys(self::STEPS));
    }

    public function nextStatus(): ?string
    {
        return array_keys(self::STEPS)[$this->stepIndex() + 1] ?? null;
    }

    public function statusLabel(): string
    {
        return self::STEPS[$this->status] ?? $this->status;
    }

    public static function generateResi(): string
    {
        do {
            $resi = 'LDR-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('no_resi', $resi)->exists());

        return $resi;
    }
}
