<?php

namespace App\Http\Controllers;

use App\Models\Service;

/**
 * Controller halaman depan yang menampilkan layanan laundry aktif
 * untuk pelanggan sebelum masuk ke proses pemesanan.
 */
class HomeController extends Controller
{
    /** Tampilkan beranda bersama daftar layanan yang masih aktif. */
    public function index()
    {
        $services = Service::where('is_active', true)->orderBy('duration_hours')->get();
        return view('home', compact('services'));
    }
}
