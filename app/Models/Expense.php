<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Catatan biaya operasional yang digunakan untuk menghitung saldo keuangan outlet. */
class Expense extends Model
{
    /** Kolom pengeluaran yang boleh diisi melalui formulir admin. */
    protected $fillable = [
        'title',
        'category',
        'amount',
        'spent_on',
        'notes',
        'recorded_by',
    ];

    /** Ubah nominal menjadi bilangan bulat dan tanggal menjadi objek tanggal. */
    protected $casts = [
        'amount' => 'integer',
        'spent_on' => 'date',
    ];
}
