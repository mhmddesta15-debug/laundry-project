<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Controller yang menangani seluruh alur pesanan laundry.
 * Tugas utamanya adalah membuat order, menampilkan daftar, memvalidasi pembayaran,
 * dan mengubah status progres cucian dari admin.
 */
class OrderController extends Controller
{
    /** Tampilkan pesanan dengan filter status, pembayaran, atau pencarian resi/pelanggan. */
    public function index(Request $request)
    {
        $orders = Order::with(['customer', 'service'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->payment_status, fn ($q, $s) => $q->where('payment_status', $s))
            ->when($request->q, function ($q, $s) {
                $q->where(function ($w) use ($s) {
                    $w->where('no_resi', 'like', "%$s%")
                      ->orWhereHas('customer', fn ($c) => $c
                          ->where('name', 'like', "%$s%")
                          ->orWhere('phone', 'like', "%$s%"));
                });
            })
            ->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /** Tampilkan formulir order baru dengan pilihan layanan aktif. */
    public function create()
    {
        return view('admin.orders.create', [
            'services' => Service::where('is_active', true)->orderBy('duration_hours')->get(['id', 'name', 'unit', 'price', 'duration_hours']),
        ]);
    }

    /** Validasi order, cari pelanggan, lalu simpan order dan riwayat awal secara atomik. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'phone'      => 'required|string|max:20',
            'service_id' => 'required|exists:services,id',
            'qty'        => 'required|numeric|min:0.1|max:999',
            'notes'      => 'nullable|string|max:500',
        ]);

        $phone   = User::normalizePhone($data['phone']);
        $service = Service::findOrFail($data['service_id']);
        $customer = User::where('role', 'pelanggan')->where('phone', $phone)->first();
        if (! $customer) {
            return back()->withInput()->withErrors([
                'phone' => 'Pelanggan belum terdaftar. Minta pelanggan membuat akun terlebih dahulu.',
            ]);
        }

        $order = DB::transaction(function () use ($data, $customer, $service, $request) {
            $order = Order::create([
                'no_resi'           => Order::generateResi(),
                'customer_id'       => $customer->id,
                'admin_id'          => $request->user()->id,
                'service_id'        => $service->id,
                'qty'               => $data['qty'],
                'price_per_unit'    => $service->price,
                'total'             => (int) round($service->price * $data['qty']),
                'status'            => 'diterima',
                'notes'             => $data['notes'] ?? null,
                'estimated_done_at' => now()->addHours($service->duration_hours),
            ]);

            $order->statuses()->create([
                'status'  => 'diterima',
                'note'    => 'Cucian diterima di outlet',
                'user_id' => $request->user()->id,
            ]);

            return $order;
        });

        return redirect()->route('admin.orders.nota', $order)->with('success', 'Order berhasil dibuat.');
    }

    /** Tampilkan detail pesanan beserta riwayat status dan pengguna yang memperbaruinya. */
    public function show(Order $order)
    {
        $order->load(['customer', 'service', 'statuses.user']);
        return view('admin.orders.show', compact('order'));
    }

    /** Tampilkan nota pesanan untuk dicetak atau diberikan kepada pelanggan. */
    public function nota(Order $order)
    {
        $order->load(['customer', 'service', 'admin']);
        return view('admin.orders.nota', compact('order'));
    }

    /** Ubah progres cucian dan tambahkan catatan riwayat dalam satu transaksi database. */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Order::STEPS)),
            'note'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($order, $data, $request) {
            $order->update([
                'status'       => $data['status'],
                'picked_up_at' => $data['status'] === 'sudah_diambil' ? now() : $order->picked_up_at,
            ]);

            $order->statuses()->create([
                'status'  => $data['status'],
                'note'    => $data['note'] ?? null,
                'user_id' => $request->user()->id,
            ]);
        });

        // TODO (opsional): kirim notifikasi WhatsApp ketika status = 'siap_diambil' (pakai Queue)
        return back()->with('success', 'Status diperbarui: ' . Order::STEPS[$data['status']]);
    }

    /** Tandai pembayaran sebagai lunas dan simpan waktu konfirmasi untuk laporan keuangan. */
    public function markPaid(Order $order)
    {
        abort_if($order->payment_status === 'lunas', 422);
        $order->update([
            'payment_status' => 'lunas',
            'paid_at' => now(),
        ]);
        return back()->with('success', 'Pembayaran ditandai lunas.');
    }

    /** Tolak bukti transfer dengan catatan agar pelanggan dapat mengirim ulang bukti. */
    public function rejectPayment(Request $request, Order $order)
    {
        abort_unless($order->payment_status === 'menunggu_verifikasi', 422);

        $data = $request->validate([
            'payment_note' => 'nullable|string|max:255',
        ]);

        $order->update([
            'payment_status' => 'ditolak',
            'payment_note' => $data['payment_note'] ?? null,
        ]);

        return back()->with('success', 'Bukti transfer ditolak. Pelanggan dapat mengunggah bukti baru.');
    }

    /** Kirim bukti transfer privat hanya saat pesanan menunggu verifikasi. */
    public function paymentProof(Order $order): BinaryFileResponse
    {
        abort_unless($order->payment_status === 'menunggu_verifikasi' && $order->payment_proof_path, 404);

        $path = Storage::disk('local')->path($order->payment_proof_path);
        abort_unless(is_file($path), 404);

        return response()->file($path);
    }
}
