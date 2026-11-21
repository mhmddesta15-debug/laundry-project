<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun admin/kasir
        $admin = User::create([
            'name' => 'Admin Kasir', 'phone' => '081200000000', 'email' => 'admin@laundry.test',
            'role' => 'admin', 'password' => Hash::make('password'),
        ]);

        // Katalog harga
        $catalog = [
            ['Express 3 Jam',  'kg',  15000, 3,  'Cepat kilat, selesai dalam 3 jam (cuci + kering + setrika).'],
            ['Express 6 Jam',  'kg',  12000, 6,  'Pilihan hemat untuk cucian mendesak, selesai 6 jam.'],
            ['Express 12 Jam', 'kg',  10000, 12, 'Titip pagi, ambil malam di hari yang sama.'],
            ['Reguler 2 Hari', 'kg',   7000, 48, 'Paling ekonomis untuk cucian harian.'],
            ['Cuci Kering Saja','kg',  5000, 24, 'Dicuci & dikeringkan tanpa disetrika.'],
            ['Dry Clean',      'pcs', 25000, 72, 'Untuk jas, gaun, dan pakaian bahan khusus.'],
            ['Cuci Bedcover',  'pcs', 35000, 48, 'Bedcover / selimut tebal, bersih dan wangi.'],
            ['Cuci Sepatu',    'pcs', 30000, 48, 'Perawatan sepatu menyeluruh.'],
        ];
        $services = [];
        foreach ($catalog as [$name, $unit, $price, $hours, $desc]) {
            $services[] = Service::create([
                'name' => $name, 'slug' => Str::slug($name), 'unit' => $unit,
                'price' => $price, 'duration_hours' => $hours, 'description' => $desc,
            ]);
        }

        // Data demo: pelanggan + order sedang proses (untuk uji lacak resi)
        $customer = User::create([
            'name' => 'Budi Santoso', 'phone' => '081234567890', 'email' => 'budi@example.test',
            'address' => 'Bekasi', 'role' => 'pelanggan', 'password' => Hash::make('081234567890'),
        ]);

        $svc = $services[3];
        $order = Order::create([
            'no_resi' => 'LDR-DEMO-0001', 'customer_id' => $customer->id, 'admin_id' => $admin->id,
            'service_id' => $svc->id, 'qty' => 4.5, 'price_per_unit' => $svc->price,
            'total' => (int) round($svc->price * 4.5), 'status' => 'setrika',
            'estimated_done_at' => now()->addHours(6),
        ]);
        foreach ([
            ['diterima', 'Cucian diterima di outlet', 300],
            ['cuci', null, 240],
            ['pengeringan', null, 120],
            ['setrika', 'Sedang disetrika', 30],
        ] as [$st, $note, $minsAgo]) {
            $order->statuses()->create([
                'status' => $st, 'note' => $note, 'user_id' => $admin->id,
                'created_at' => now()->subMinutes($minsAgo),
            ]);
        }
    }
}
