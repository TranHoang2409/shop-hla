<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_loads_successfully(): void
    {
        Product::factory()->create();

        $this->get('/')
            ->assertOk()
            ->assertSee('HLA Wifi Shop');
    }

    public function test_shop_filters_products_by_category_price_stock_and_category_keyword(): void
    {
        Product::factory()->create([
            'name' => 'Router Office AX',
            'category' => Product::CATEGORY_ROUTER,
            'price' => 1200000,
            'stock' => 8,
        ]);
        Product::factory()->create([
            'name' => 'Camera Outdoor',
            'category' => Product::CATEGORY_CAMERA,
            'price' => 900000,
            'stock' => 0,
        ]);

        $this->get(route('shop', [
            'q' => 'router',
            'category' => Product::CATEGORY_ROUTER,
            'min_price' => 1000000,
            'max_price' => 1500000,
            'stock' => 'in_stock',
        ]))
            ->assertOk()
            ->assertSee('Router Office AX')
            ->assertDontSee('Camera Outdoor');
    }

    public function test_customer_can_place_an_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 1500000,
            'stock' => 5,
        ]);

        $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'image' => $product->image,
                        'stock' => $product->stock,
                        'quantity' => 2,
                    ],
                ],
            ])
            ->post(route('checkout.store'), [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'shipping_address' => $user->address,
                'payment_method' => Order::PAYMENT_METHOD_COD,
                'note' => 'Giao gio hanh chinh',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
            'payment_method' => Order::PAYMENT_METHOD_COD,
            'payment_status' => Order::PAYMENT_STATUS_UNPAID,
            'total' => 3000000,
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'line_total' => 3000000,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
    }

    public function test_customer_can_place_bank_transfer_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'price' => 1500000,
            'stock' => 5,
        ]);

        $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'image' => $product->image,
                        'stock' => $product->stock,
                        'quantity' => 1,
                    ],
                ],
            ])
            ->post(route('checkout.store'), [
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $user->phone,
                'shipping_address' => $user->address,
                'payment_method' => Order::PAYMENT_METHOD_BANK_TRANSFER,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'payment_method' => Order::PAYMENT_METHOD_BANK_TRANSFER,
            'payment_status' => Order::PAYMENT_STATUS_AWAITING_TRANSFER,
            'status' => Order::STATUS_PENDING,
        ]);
    }

    public function test_admin_cannot_place_an_order(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create([
            'price' => 1500000,
            'stock' => 5,
        ]);

        $this->actingAs($admin)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'id' => $product->id,
                        'sku' => $product->sku,
                        'name' => $product->name,
                        'price' => (float) $product->price,
                        'image' => $product->image,
                        'stock' => $product->stock,
                        'quantity' => 2,
                    ],
                ],
            ])
            ->post(route('checkout.store'), [
                'customer_name' => $admin->name,
                'customer_email' => $admin->email,
                'customer_phone' => $admin->phone,
                'shipping_address' => $admin->address,
                'payment_method' => Order::PAYMENT_METHOD_COD,
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);
    }
}
