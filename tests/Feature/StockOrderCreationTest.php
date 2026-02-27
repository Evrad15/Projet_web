<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockOrderCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_manager_can_create_order_successfully(): void
    {
        $stockManager = User::factory()->create([
            'role' => 'stock_manager',
            'email_verified_at' => now(),
        ]);

        $supplier = User::factory()->create([
            'role' => 'supplier_manager',
            'email_verified_at' => now(),
        ]);

        $product = Product::create([
            'name' => 'PS5',
            'description' => 'Console',
            'price' => 1000,
            'quantity' => 10,
        ]);

        $deliveryDate = now()->addDay()->toDateString();

        $response = $this->actingAs($stockManager)->post(route('stock.order.store_order'), [
            'supplier_manager_id' => $supplier->id,
            'delivery_date' => $deliveryDate,
            'products' => [
                ['name' => $product->name, 'quantity' => 5],
            ],
        ]);

        $response->assertRedirect(route('dashboard.stock'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('orders', [
            'supplier_manager_id' => $supplier->id,
            'delivery_date' => $deliveryDate,
            'status' => 'En_cours',
        ]);

        $orderId = Order::query()->value('id');

        $this->assertDatabaseHas('order_items', [
            'order_id' => $orderId,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_order_creation_rolls_back_if_product_does_not_exist(): void
    {
        $stockManager = User::factory()->create([
            'role' => 'stock_manager',
            'email_verified_at' => now(),
        ]);

        $supplier = User::factory()->create([
            'role' => 'supplier_manager',
            'email_verified_at' => now(),
        ]);

        $deliveryDate = now()->addDay()->toDateString();

        $response = $this->actingAs($stockManager)->post(route('stock.order.store_order'), [
            'supplier_manager_id' => $supplier->id,
            'delivery_date' => $deliveryDate,
            'products' => [
                ['name' => 'PRODUIT_INEXISTANT', 'quantity' => 2],
            ],
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_order_creation_fails_with_past_delivery_date(): void
    {
        $stockManager = User::factory()->create([
            'role' => 'stock_manager',
            'email_verified_at' => now(),
        ]);

        $supplier = User::factory()->create([
            'role' => 'supplier_manager',
            'email_verified_at' => now(),
        ]);

        Product::create([
            'name' => 'TV',
            'description' => 'Ecran',
            'price' => 500,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($stockManager)->post(route('stock.order.store_order'), [
            'supplier_manager_id' => $supplier->id,
            'delivery_date' => now()->subDay()->toDateString(),
            'products' => [
                ['name' => 'TV', 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('delivery_date');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }
}
