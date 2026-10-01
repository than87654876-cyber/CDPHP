<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartPaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_completion_route_does_not_mark_order_paid(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 100000,
            'final_amount' => 100000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('thanhtoan_hoantat', ['id' => $order->id]));

        $response->assertRedirect(route('giohang'));
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame('pending', $order->fresh()->order_status);
    }

    public function test_bank_transfer_webhook_marks_order_paid_when_amount_and_content_match(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 150000,
            'final_amount' => 150000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $response = $this->postJson(route('api.payments.bank-transfer.notify'), [
            'order_id' => $order->id,
            'amount' => 150000,
            'content' => 'FDL-' . $order->id,
            'status' => 'success',
            'transaction_id' => 'txn_123',
            'bank_reference' => 'ref_123',
            'bank_code' => 'BIDV',
            'paid_at' => now()->toDateTimeString(),
        ]);

        $response->assertOk();
        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('confirmed', $order->order_status);
        $this->assertSame('txn_123', $order->payment_transaction_id);
        $this->assertSame('ref_123', $order->payment_bank_reference);
        $this->assertSame('BIDV', $order->payment_bank);
    }

    public function test_transfer_payment_route_redirects_gracefully_when_order_id_is_missing(): void
    {
        $response = $this->get(route('thanhtoan_chuyenkhoan'));
        $response->assertRedirect(route('giohang'));
        $response->assertSessionHas('error');
    }

    public function test_transfer_payment_route_redirects_gracefully_when_order_does_not_exist(): void
    {
        $response = $this->get(route('thanhtoan_chuyenkhoan', ['order_id' => 999999]));
        $response->assertRedirect(route('giohang'));
        $response->assertSessionHas('error');
    }

    public function test_transfer_payment_route_renders_correctly_for_valid_pending_order(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 120000,
            'final_amount' => 120000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get(route('thanhtoan_chuyenkhoan', ['order_id' => $order->id]));
        $response->assertOk();
        $response->assertViewIs('client.transfer_payment');
        $response->assertSee('#FDL-' . $order->id);
        $response->assertSee('8899408675');
        $response->assertSee('120.000');
    }

    public function test_transfer_payment_route_redirects_to_tracuu_when_already_paid(): void
    {
        $user = User::factory()->create(['role' => 'customer']);
        $order = Order::create([
            'user_id' => $user->id,
            'total_amount' => 120000,
            'final_amount' => 120000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'order_status' => 'confirmed',
        ]);

        $response = $this->actingAs($user)->get(route('thanhtoan_chuyenkhoan', ['order_id' => $order->id]));
        $response->assertRedirect(route('tracuu', ['order_id' => 'FDL-' . $order->id]));
        $response->assertSessionHas('success');
    }

    public function test_bank_transfer_webhook_handles_guest_orders_without_crashing(): void
    {
        $guest = User::factory()->create(['role' => 'guest']);
        $order = Order::create([
            'user_id' => $guest->id,
            'total_amount' => 80000,
            'final_amount' => 80000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending',
            'order_status' => 'pending',
        ]);

        $response = $this->postJson(route('api.payments.bank-transfer.notify'), [
            'order_id' => $order->id,
            'amount' => 80000,
            'content' => 'FDL-' . $order->id,
            'status' => 'success',
            'transaction_id' => 'txn_guest_123',
            'bank_reference' => 'ref_guest_123',
            'bank_code' => 'BIDV',
        ]);

        $response->assertOk();
        $order->refresh();
        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('confirmed', $order->order_status);
    }
}
