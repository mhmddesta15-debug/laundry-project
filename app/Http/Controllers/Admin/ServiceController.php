<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Controller untuk mengatur daftar layanan laundry yang tersedia, aktif, dan harga-nya.
 * Digunakan oleh admin untuk menambah, mengubah, dan menghapus jenis layanan.
 */
class ServiceController extends Controller
{
    /** Tampilkan katalog layanan dan ringkasan jumlah layanan aktif/nonaktif. */
    public function index()
    {
        $services = Service::withCount('orders')
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(12);
        $stats = [
            'total' => Service::count(),
            'active' => Service::where('is_active', true)->count(),
            'inactive' => Service::where('is_active', false)->count(),
        ];

        return view('admin.services.index', compact('services', 'stats'));
    }

    /** Tampilkan formulir kosong untuk menambahkan layanan baru. */
    public function create()
    {
        return view('admin.services.form', ['service' => new Service]);
    }

    /** Validasi lalu simpan layanan baru dengan slug yang unik. */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        Service::create($data);

        return redirect()->route('admin.services.index')->with('success', 'Jenis layanan berhasil ditambahkan.');
    }

    /** Tampilkan formulir berisi data layanan yang akan diubah. */
    public function edit(Service $service)
    {
        return view('admin.services.form', compact('service'));
    }

    /** Perbarui data layanan dan pastikan slug tetap unik. */
    public function update(Request $request, Service $service)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['name'], $service);

        $service->update($data);

        return redirect()->route('admin.services.index')->with('success', 'Jenis layanan berhasil diperbarui.');
    }

    /** Hapus layanan yang belum dipakai pesanan; layanan terpakai harus dinonaktifkan. */
    public function destroy(Service $service)
    {
        if ($service->orders()->exists()) {
            return back()->withErrors([
                'service' => 'Layanan ini sudah digunakan pada pesanan dan tidak dapat dihapus. Nonaktifkan layanan agar tidak muncul pada pesanan baru.',
            ]);
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Jenis layanan berhasil dihapus.');
    }

    /** Validasi dan ambil kolom layanan yang boleh disimpan dari formulir. */
    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'unit' => ['required', 'in:kg,pcs'],
            'price' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }

    /** Buat slug dari nama layanan dan tambahkan akhiran jika sudah digunakan. */
    private function uniqueSlug(string $name, ?Service $service = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $suffix = 2;

        while (Service::where('slug', $slug)
            ->when($service, fn ($query) => $query->where('id', '!=', $service->getKey()))
            ->exists()) {
            $slug = $baseSlug . '-' . $suffix++;
        }

        return $slug;
    }
}
