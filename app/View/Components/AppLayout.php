<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /** Pilih layout utama untuk halaman pengguna yang sudah masuk. */
    public function render(): View
    {
        return view('layouts.app');
    }
}
