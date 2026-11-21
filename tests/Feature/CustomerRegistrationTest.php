<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_register_their_own_account(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('register'));

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('No. HP / WhatsApp');

        $this->get(route('login'))
            ->assertOk()
            ->assertSee(route('register'));

        $this->post(route('register'), [
            'name' => 'Pelanggan Baru',
            'phone' => '+62 812-3456-7890',
            'email' => 'Baru@Example.Test',
            'address' => 'Bekasi',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', [
            'name' => 'Pelanggan Baru',
            'phone' => '081234567890',
            'email' => 'baru@example.test',
            'role' => 'pelanggan',
        ]);
    }

    public function test_admin_can_view_registered_customers_but_cannot_create_customer_accounts_from_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create([
            'role' => 'pelanggan',
            'name' => 'Pelanggan Terdaftar',
            'phone' => '081234567890',
        ]);
        $service = Service::create([
            'name' => 'Cuci Reguler',
            'slug' => 'cuci-reguler',
            'unit' => 'kg',
            'price' => 7000,
            'duration_hours' => 48,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.create'))
            ->assertOk()
            ->assertSee('Cari nama atau No. HP / WhatsApp');

        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee('Pelanggan Terdaftar')
            ->assertSee('081234567890');

        $this->actingAs($admin)
            ->get(route('admin.customers.lookup', ['q' => 'Pelanggan Terdaftar']))
            ->assertOk()
            ->assertJsonPath('customers.0.name', 'Pelanggan Terdaftar')
            ->assertJsonPath('customers.0.phone', '081234567890');

        $this->actingAs($admin)
            ->get(route('admin.customers.lookup', ['q' => '081234567890']))
            ->assertOk()
            ->assertJsonPath('customers.0.name', 'Pelanggan Terdaftar');

        $this->actingAs($admin)
            ->post(route('admin.orders.store'), [
                'name' => 'Pelanggan Baru',
                'phone' => '081298765432',
                'service_id' => $service->id,
                'qty' => 1,
            ])
            ->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', ['phone' => '081298765432']);

        $this->actingAs($admin)
            ->post(route('admin.orders.store'), [
                'phone' => $customer->phone,
                'service_id' => $service->id,
                'qty' => 1,
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }
}
