<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\PaymentSetting;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_transfer_proof_for_cashier_verification(): void
    {
        Storage::fake('local');
        config([
            'laundry.bank_name' => 'Bank Contoh',
            'laundry.bank_account_number' => '1234567890',
            'laundry.bank_account_name' => 'Laundry Maxwell',
        ]);
        [$customer, $admin, $order] = $this->createOrder();

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('1234567890')
            ->assertSee('Rp 35.000');

        $this->actingAs($customer)
            ->post(route('customer.orders.payment-proof', $order), [
                'payment_proof' => UploadedFile::fake()->image('transfer.png'),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $order->refresh();
        $this->assertSame('menunggu_verifikasi', $order->payment_status);
        Storage::disk('local')->assertExists($order->payment_proof_path);

        $this->actingAs($admin)
            ->get(route('admin.orders.payment-proof', $order))
            ->assertOk();

        $this->actingAs($admin)
            ->patch(route('admin.orders.reject-payment', $order), [
                'payment_note' => 'Nominal tidak sesuai.',
            ])
            ->assertRedirect();
        $this->assertSame('ditolak', $order->fresh()->payment_status);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Nominal tidak sesuai.');

        $this->actingAs($customer)
            ->post(route('customer.orders.payment-proof', $order), [
                'payment_proof' => UploadedFile::fake()->image('transfer-baru.png'),
            ])
            ->assertRedirect();

        $this->actingAs($admin)
            ->patch(route('admin.orders.pay', $order))
            ->assertRedirect();
        $this->assertSame('lunas', $order->fresh()->payment_status);
        $this->assertNotNull($order->fresh()->paid_at);
    }

    public function test_customer_cannot_submit_proof_for_another_customers_order(): void
    {
        [$customer, , $order] = $this->createOrder();
        $otherCustomer = User::factory()->create();

        $this->actingAs($otherCustomer)
            ->post(route('customer.orders.payment-proof', $order), [
                'payment_proof' => UploadedFile::fake()->image('transfer.png'),
            ])
            ->assertForbidden();

        $this->assertSame('belum_bayar', $order->fresh()->payment_status);
    }

    public function test_public_tracking_page_directs_unpaid_customers_to_login(): void
    {
        [, , $order] = $this->createOrder();

        $this->get(route('tracking.search', ['q' => $order->no_resi]))
            ->assertOk()
            ->assertSee('Informasi rekening dan formulir unggah bukti transfer tersedia setelah Anda masuk')
            ->assertSee(route('login'));
    }

    public function test_admin_can_configure_bank_and_qris_for_customer_payment_page(): void
    {
        Storage::fake('local');
        [$customer, $admin, $order] = $this->createOrder();

        $this->actingAs($admin)
            ->put(route('admin.payment-settings.update'), [
                'bank_name' => 'Bank Contoh',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Laundry Maxwell',
                'qris_image' => UploadedFile::fake()->image('qris.png'),
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $settings = PaymentSetting::firstOrFail();
        $this->assertSame('Bank Contoh', $settings->bank_name);
        $this->assertSame('1234567890', $settings->bank_account_number);
        Storage::disk('local')->assertExists($settings->qris_image_path);

        $this->actingAs($admin)
            ->get(route('admin.payment-settings.qris'))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Bank Contoh')
            ->assertSee('1234567890')
            ->assertSee('Laundry Maxwell')
            ->assertSee(route('customer.payment-qris'));

        $this->actingAs($customer)
            ->get(route('customer.payment-qris'))
            ->assertOk();
    }

    public function test_customer_cannot_open_admin_payment_settings(): void
    {
        $customer = User::factory()->create();

        $this->actingAs($customer)
            ->get(route('admin.payment-settings.index'))
            ->assertForbidden();
    }

    private function createOrder(): array
    {
        $customer = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $service = Service::create([
            'name' => 'Cuci Kering',
            'slug' => 'cuci-kering-test',
            'unit' => 'kg',
            'price' => 7000,
            'duration_hours' => 48,
        ]);
        $order = Order::create([
            'no_resi' => Order::generateResi(),
            'customer_id' => $customer->id,
            'admin_id' => $admin->id,
            'service_id' => $service->id,
            'qty' => 5,
            'price_per_unit' => 7000,
            'total' => 35000,
            'status' => 'diterima',
        ]);

        return [$customer, $admin, $order];
    }
}
