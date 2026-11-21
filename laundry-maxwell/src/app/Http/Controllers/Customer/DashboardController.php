<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()->with('service')->latest()->paginate(10);
        return view('customer.dashboard', compact('orders'));
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->customer_id === $request->user()->id, 403);
        $order->load(['service', 'statuses', 'customer']);
        return view('tracking.show', ['order' => $order, 'public' => false]);
    }
}
