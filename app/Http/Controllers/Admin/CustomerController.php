<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /** Tampilkan daftar pelanggan beserta jumlah pesanan dan dukungan pencarian. */
    public function index(Request $request): View
    {
        $customers = User::where('role', 'pelanggan')
            ->withCount('orders')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = $request->string('q')->trim()->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /** Sediakan pencarian ringkas pelanggan untuk formulir order admin. */
    public function lookup(Request $request)
    {
        if ($request->filled('q')) {
            $search = trim((string) $request->query('q'));
            if (mb_strlen($search) < 2) {
                return response()->json(['customers' => []]);
            }

            $phone = User::normalizePhone($search);
            $customers = User::where('role', 'pelanggan')
                ->where(function ($query) use ($search, $phone): void {
                    $query->where('name', 'like', "%{$search}%");
                    if ($phone !== '') {
                        $query->orWhere('phone', 'like', '%' . $phone . '%');
                    }
                })
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'phone']);

            return response()->json([
                'customers' => $customers,
            ]);
        }

        $phone = User::normalizePhone((string) $request->query('phone'));
        $c = User::where('role', 'pelanggan')->where('phone', $phone)->first();

        return response()->json($c ? [
            'found' => true, 'name' => $c->name, 'email' => $c->email, 'address' => $c->address,
        ] : ['found' => false]);
    }
}
