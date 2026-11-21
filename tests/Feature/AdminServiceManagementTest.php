<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_a_service(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.services.store'), [
                'name' => 'Cuci Express',
                'unit' => 'kg',
                'price' => 12000,
                'duration_hours' => 6,
                'description' => 'Selesai dalam enam jam.',
                'is_active' => 1,
            ])
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHasNoErrors();

        $service = Service::where('name', 'Cuci Express')->firstOrFail();
        $this->assertSame('cuci-express', $service->slug);
        $this->assertTrue($service->is_active);

        $this->actingAs($admin)
            ->get(route('admin.services.index'))
            ->assertOk()
            ->assertSee('Cuci Express')
            ->assertSee('Tambah Layanan');
        $this->get(route('admin.services.create'))->assertOk()->assertSee('Tambah Jenis Layanan');
        $this->get(route('admin.services.edit', $service))->assertOk()->assertSee('Edit Jenis Layanan');

        $this->actingAs($admin)
            ->put(route('admin.services.update', $service), [
                'name' => 'Cuci Express Plus',
                'unit' => 'pcs',
                'price' => 18000,
                'duration_hours' => 12,
                'description' => 'Layanan satuan.',
                'is_active' => 0,
            ])
            ->assertRedirect(route('admin.services.index'))
            ->assertSessionHasNoErrors();

        $service->refresh();
        $this->assertSame('cuci-express-plus', $service->slug);
        $this->assertSame('pcs', $service->unit);
        $this->assertFalse($service->is_active);

        $this->actingAs($admin)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect(route('admin.services.index'));

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_admin_cannot_delete_a_service_used_by_an_order(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $service = Service::create([
            'name' => 'Laundry Reguler',
            'slug' => 'laundry-reguler',
            'unit' => 'kg',
            'price' => 7000,
            'duration_hours' => 48,
            'is_active' => true,
        ]);
        Order::create([
            'no_resi' => Order::generateResi(),
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
            'service_id' => $service->id,
            'qty' => 2,
            'price_per_unit' => 7000,
            'total' => 14000,
            'status' => 'diterima',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.services.destroy', $service))
            ->assertRedirect()
            ->assertSessionHasErrors('service');

        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    public function test_customer_cannot_manage_services(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)
            ->get(route('admin.services.index'))
            ->assertForbidden();
    }
}
