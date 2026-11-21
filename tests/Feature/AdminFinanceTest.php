<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_finance_report_and_record_and_delete_expenses(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $order = $this->createOrder($customer, $admin, [
            'payment_status' => 'lunas',
            'paid_at' => '2026-11-11 10:00:00',
            'total' => 35000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.finance.index', [
                'start_date' => '2026-11-11',
                'end_date' => '2026-11-11',
            ]))
            ->assertOk()
            ->assertSee('Keuangan')
            ->assertSee('Rp 35.000')
            ->assertSee($order->no_resi);

        $this->actingAs($admin)
            ->post(route('admin.finance.expenses.store'), [
                'title' => 'Detergen',
                'category' => 'Bahan laundry',
                'amount' => 10000,
                'spent_on' => '2026-11-11',
                'notes' => 'Stok mingguan',
            ])
            ->assertRedirect(route('admin.finance.index', [
                'start_date' => '2026-11-11',
                'end_date' => '2026-11-11',
            ]))
            ->assertSessionHasNoErrors();

        $expense = Expense::firstOrFail();
        $this->assertSame($admin->id, $expense->recorded_by);
        $this->assertSame('2026-11-11', $expense->spent_on->toDateString());

        $this->actingAs($admin)
            ->get(route('admin.finance.index', [
                'start_date' => '2026-11-11',
                'end_date' => '2026-11-11',
            ]))
            ->assertOk()
            ->assertSee('Rp 10.000')
            ->assertSee('Rp 25.000')
            ->assertSee('Detergen');

        $this->actingAs($admin)
            ->delete(route('admin.finance.expenses.destroy', $expense))
            ->assertRedirect();
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_finance_income_uses_payment_date_and_only_counts_paid_orders(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'pelanggan']);
        $paidOrder = $this->createOrder($customer, $admin, [
            'payment_status' => 'lunas',
            'paid_at' => '2026-11-10 15:00:00',
            'total' => 20000,
        ]);
        $this->createOrder($customer, $admin, [
            'payment_status' => 'belum_bayar',
            'total' => 90000,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.finance.index', [
                'start_date' => '2026-11-10',
                'end_date' => '2026-11-10',
            ]))
            ->assertOk()
            ->assertSee($paidOrder->no_resi)
            ->assertDontSee('Rp 90.000')
            ->assertSee('Rp 20.000');
    }

    public function test_customers_cannot_access_finance_management(): void
    {
        $customer = User::factory()->create(['role' => 'pelanggan']);

        $this->actingAs($customer)
            ->get(route('admin.finance.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->post(route('admin.finance.expenses.store'), [
                'title' => 'Detergen',
                'category' => 'Bahan',
                'amount' => 10000,
                'spent_on' => '2026-11-11',
            ])
            ->assertForbidden();
    }

    public function test_finance_report_rejects_reversed_date_ranges(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.finance.index', [
                'start_date' => '2026-11-12',
                'end_date' => '2026-11-11',
            ]))
            ->assertSessionHasErrors('end_date');
    }

    private function createOrder(User $customer, User $admin, array $attributes = []): Order
    {
        $service = Service::create([
            'name' => 'Cuci Kering',
            'slug' => 'cuci-kering-'.uniqid(),
            'unit' => 'kg',
            'price' => 7000,
            'duration_hours' => 48,
        ]);

        return Order::create(array_merge([
            'no_resi' => Order::generateResi(),
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
            'service_id' => $service->id,
            'qty' => 5,
            'price_per_unit' => 7000,
            'total' => 35000,
            'status' => 'diterima',
        ], $attributes));
    }
}
