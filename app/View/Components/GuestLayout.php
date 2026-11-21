<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /** Pilih layout sederhana untuk halaman autentikasi pengunjung. */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
