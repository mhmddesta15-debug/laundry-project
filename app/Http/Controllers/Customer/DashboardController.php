<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DashboardController extends Controller
{
    /** Tampilkan daftar pesanan milik pelanggan yang sedang masuk. */
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('service')->latest()->paginate(10);
        return view('customer.dashboard', compact('orders'));
    }

    /** Tampilkan detail pesanan hanya jika pesanan itu milik pelanggan saat ini. */
    public function show(Request $request, Order $order)
    {
        abort_unless($order->customer_id === $request->user()->id, 403);
        $order->load(['service', 'statuses', 'customer']);
        return view('tracking.show', [
            'order' => $order,
            'public' => false,
            'paymentSettings' => PaymentSetting::current(),
        ]);
    }

    /** Simpan bukti transfer pelanggan dan kirim pesanan ke antrean verifikasi kasir. */
    public function uploadPaymentProof(Request $request, Order $order)
    {
        abort_unless($order->customer_id === $request->user()->id, 403);
        abort_unless(in_array($order->payment_status, ['belum_bayar', 'ditolak'], true), 422);

        $data = $request->validate([
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $path = $data['payment_proof']->store('payment-proofs', 'local');
        if (! $path) {
            throw new RuntimeException('Bukti pembayaran gagal disimpan.');
        }

        $previousProof = $order->payment_proof_path;
        $order->update([
            'payment_status' => 'menunggu_verifikasi',
            'payment_proof_path' => $path,
            'payment_note' => null,
        ]);

        if ($previousProof) {
            Storage::disk('local')->delete($previousProof);
        }

        return back()->with('success', 'Bukti transfer berhasil dikirim dan sedang menunggu verifikasi kasir.');
    }
}
