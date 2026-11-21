<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function search(Request $request)
    {
        $request->validate(['q' => 'required|string|max:30']);
        $q = trim($request->q);

        // 1) Cocokkan Nomor Resi
        $order = Order::with(['service', 'statuses', 'customer'])
            ->where('no_resi', strtoupper($q))->first();

        if ($order) {
            return view('tracking.show', ['order' => $order, 'public' => true]);
        }

        // 2) Bukan resi -> anggap Nomor HP; tampilkan daftar (tanpa harga/alamat demi privasi)
        $phone  = User::normalizePhone($q);
        $orders = Order::with('service')
            ->whereHas('customer', fn ($c) => $c->where('phone', $phone))
            ->latest()->limit(10)->get();

        return view('tracking.list', compact('orders', 'q'));
    }
}
