<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model pengguna yang mewakili admin, pelanggan, dan akun autentikasi.
 * Bertanggung jawab atas identitas user, nomor telepon, dan relasi pesanan.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;
    use MustVerifyEmailTrait;

    protected $fillable = ['name', 'phone', 'email', 'address', 'role', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Normalisasi: +62812.. / 62812.. / 812.. / 0812.. -> 0812.. */
    public static function normalizePhone(string $phone): string
    {
        $p = preg_replace('/\D/', '', $phone);
        if (str_starts_with($p, '62')) {
            $p = '0' . substr($p, 2);
        } elseif (str_starts_with($p, '8')) {
            $p = '0' . $p;
        }
        return $p;
    }

    /** Ambil semua pesanan yang dimiliki pelanggan ini. */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /** Ambil semua pengeluaran yang dicatat oleh admin ini. */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'recorded_by');
    }
}
