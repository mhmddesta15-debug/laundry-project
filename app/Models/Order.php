<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model pesanan laundry yang memuat status kerja, total biaya, dan log progres cucian.
 * Digunakan untuk mengelola seluruh siklus pemesanan dari diterima hingga diambil pelanggan.
 */
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

    public const PAYMENT_STATUSES = [
        'belum_bayar' => 'Belum Bayar',
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'ditolak' => 'Ditolak',
        'lunas' => 'Lunas',
    ];

    protected $guarded = [];
    protected $casts = [
        'estimated_done_at' => 'datetime',
        'picked_up_at'      => 'datetime',
        'paid_at'           => 'datetime',
        'qty'               => 'float',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function admin(): BelongsTo    { return $this->belongsTo(User::class, 'admin_id'); }
    public function service(): BelongsTo  { return $this->belongsTo(Service::class); }
    public function statuses(): HasMany   { return $this->hasMany(OrderStatus::class)->oldest('created_at')->oldest('id'); }

    /** Kembalikan posisi status pesanan saat ini dalam urutan tahapan cucian. */
    public function stepIndex(): int
    {
        return (int) array_search($this->status, array_keys(self::STEPS));
    }

    /** Tentukan tahap cucian berikutnya, atau null jika pesanan sudah di tahap akhir. */
    public function nextStatus(): ?string
    {
        return array_keys(self::STEPS)[$this->stepIndex() + 1] ?? null;
    }

    /** Ubah kode status cucian menjadi label yang dapat dibaca pengguna. */
    public function statusLabel(): string
    {
        return self::STEPS[$this->status] ?? $this->status;
    }

    /** Ubah kode status pembayaran menjadi label yang dapat dibaca pengguna. */
    public function paymentStatusLabel(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? $this->payment_status;
    }

    /** Buat nomor resi unik dengan awalan tanggal untuk pesanan baru. */
    public static function generateResi(): string
    {
        do {
            $resi = 'LDR-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (self::where('no_resi', $resi)->exists());

        return $resi;
    }
}
