<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /** Autofill data pelanggan lama berdasarkan No. HP (dipakai form kasir) */
    public function lookup(Request $request)
    {
        $phone = User::normalizePhone((string) $request->query('phone'));
        $c = User::where('role', 'pelanggan')->where('phone', $phone)->first();

        return response()->json($c ? [
            'found' => true, 'name' => $c->name, 'email' => $c->email, 'address' => $c->address,
        ] : ['found' => false]);
    }
}
