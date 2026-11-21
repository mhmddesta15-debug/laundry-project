<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Nota {{ $order->no_resi }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 12px; background: #eee; }
        .nota { width: 58mm; max-width: 100%; margin: 0 auto; background: #fff; padding: 8px; }
        .c { text-align: center; } .r { text-align: right; } .b { font-weight: bold; }
        hr { border: 0; border-top: 1px dashed #000; margin: 6px 0; }
        table { width: 100%; border-collapse: collapse; } td { vertical-align: top; padding: 1px 0; }
        .resi { font-size: 15px; letter-spacing: 1px; }
        .actions { text-align: center; margin: 12px 0; font-family: sans-serif; }
        .actions button, .actions a { padding: 8px 16px; margin: 0 4px; border: 1px solid #333; background: #fff; cursor: pointer; text-decoration: none; color: #000; font-size: 13px; }
        @media print { body { background: #fff; padding: 0; } .actions { display: none; } .nota { padding: 0; width: 58mm; } @page { margin: 2mm; size: 58mm auto; } }
    </style>
</head>
<body>
<div class="actions">
    <button onclick="window.print()">🖨️ Cetak</button>
    <a href="{{ route('admin.orders.show', $order) }}">Kelola Pesanan</a>
    <a href="{{ route('admin.orders.create') }}">+ Order Baru</a>
</div>

<div class="nota">
    <div class="c b">{{ strtoupper(config('laundry.name')) }}</div>
    <div class="c">{{ config('laundry.address') }}</div>
    <div class="c">Telp/WA: {{ config('laundry.phone') }}</div>
    <hr>
    <div class="c">No. Resi</div>
    <div class="c b resi">{{ $order->no_resi }}</div>
    <hr>
    <table>
        <tr><td>Tanggal</td><td class="r">{{ $order->created_at->format('d/m/Y H:i') }}</td></tr>
        <tr><td>Kasir</td><td class="r">{{ $order->admin->name }}</td></tr>
        <tr><td>Pelanggan</td><td class="r">{{ $order->customer->name }}</td></tr>
        <tr><td>No. HP</td><td class="r">{{ $order->customer->phone }}</td></tr>
    </table>
    <hr>
    <div class="b">{{ $order->service->name }}</div>
    <table>
        <tr>
            <td>{{ rtrim(rtrim(number_format($order->qty, 2, '.', ''), '0'), '.') }} {{ $order->service->unit }} x {{ number_format($order->price_per_unit, 0, ',', '.') }}</td>
            <td class="r">{{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>
    <hr>
    <table>
        <tr class="b"><td>TOTAL</td><td class="r">Rp {{ number_format($order->total, 0, ',', '.') }}</td></tr>
        <tr><td>Status Bayar</td><td class="r">{{ $order->payment_status === 'lunas' ? 'LUNAS' : 'BELUM BAYAR' }}</td></tr>
        <tr><td>Estimasi</td><td class="r">{{ $order->estimated_done_at?->format('d/m/Y H:i') }}</td></tr>
    </table>
    @if ($order->notes)<hr><div>Catatan: {{ $order->notes }}</div>@endif
    <hr>
    <div class="c">Lacak cucian Anda di:</div>
    <div class="c b">{{ url('/') }}</div>
    <div class="c">Masukkan No. Resi / No. HP</div>
    <hr>
    <div class="c">Terima kasih 🙏</div>
    <div class="c">Cucian yang tidak diambil &gt; 30 hari<br>di luar tanggung jawab kami.</div>
</div>
</body>
</html>
