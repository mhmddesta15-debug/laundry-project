<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['customer', 'service'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
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

    public function create()
    {
        return view('admin.orders.create', [
            'services' => Service::where('is_active', true)->orderBy('duration_hours')->get(['id', 'name', 'unit', 'price', 'duration_hours']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:100',
            'phone'      => 'required|string|max:20',
            'email'      => 'nullable|email|max:150',
            'address'    => 'nullable|string|max:500',
            'service_id' => 'required|exists:services,id',
            'qty'        => 'required|numeric|min:0.1|max:999',
            'notes'      => 'nullable|string|max:500',
        ]);

        $phone   = User::normalizePhone($data['phone']);
        $service = Service::findOrFail($data['service_id']);

        // Email unik hanya jika pelanggan baru
        $existing = User::where('phone', $phone)->first();
        if (! $existing && ! empty($data['email']) && User::where('email', $data['email'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'Email sudah dipakai pelanggan lain.']);
        }

        $order = DB::transaction(function () use ($data, $phone, $service, $request) {
            // Daftarkan pelanggan baru di tempat, atau pakai yang sudah ada
            $customer = User::firstOrCreate(
                ['phone' => $phone],
                [
                    'name'     => $data['name'],
                    'email'    => $data['email'] ?? null,
                    'address'  => $data['address'] ?? null,
                    'role'     => 'pelanggan',
                    'password' => Hash::make($phone), // password awal = No. HP (sarankan diganti)
                ]
            );

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

    public function show(Order $order)
    {
        $order->load(['customer', 'service', 'statuses.user']);
        return view('admin.orders.show', compact('order'));
    }

    public function nota(Order $order)
    {
        $order->load(['customer', 'service', 'admin']);
        return view('admin.orders.nota', compact('order'));
    }

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

    public function markPaid(Order $order)
    {
        $order->update(['payment_status' => 'lunas']);
        return back()->with('success', 'Pembayaran ditandai lunas.');
    }
}
