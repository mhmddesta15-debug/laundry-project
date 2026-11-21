<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaymentSettingController extends Controller
{
    /** Tampilkan rekening dan QRIS yang saat ini dipakai pelanggan untuk membayar. */
    public function index()
    {
        $settings = PaymentSetting::current();

        return view('admin.payment-settings', compact('settings'));
    }

    /** Validasi dan simpan rekening outlet serta unggahan atau penghapusan QRIS. */
    public function update(Request $request)
    {
        $data = $request->validate([
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'qris_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120', 'prohibited_if:remove_qris,1'],
            'remove_qris' => ['nullable', 'boolean'],
        ]);

        $settings = PaymentSetting::current();
        $previousQris = $settings->qris_image_path;
        $newQris = null;

        if ($request->hasFile('qris_image')) {
            $newQris = $request->file('qris_image')->store('payment-methods', 'local');
            if (! $newQris) {
                throw new RuntimeException('Gambar QRIS gagal disimpan.');
            }
        }

        $settings->fill([
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account_number' => $data['bank_account_number'] ?? null,
            'bank_account_name' => $data['bank_account_name'] ?? null,
            'qris_image_path' => $request->boolean('remove_qris')
                ? null
                : ($newQris ?? $previousQris),
        ])->save();

        if (($request->boolean('remove_qris') || $newQris) && $previousQris) {
            Storage::disk('local')->delete($previousQris);
        }

        return back()->with('success', 'Informasi pembayaran berhasil diperbarui.');
    }

    /** Kirim berkas QRIS dari penyimpanan privat untuk ditampilkan di halaman pembayaran. */
    public function qrisImage(): BinaryFileResponse
    {
        $path = PaymentSetting::current()->qris_image_path;
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }
}
