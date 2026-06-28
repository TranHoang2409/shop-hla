<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_dashboard_period_filter_loads_sales_chart_data(): void
    {
        Carbon::setTestNow('2026-05-19 10:00:00');

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Router Test AX',
            'price' => 2000000,
        ]);

        $this->createOrderWithItem($customer, $product, 2, now()->startOfWeek(), Order::STATUS_DELIVERED);
        $this->createOrderWithItem($customer, $product, 1, now()->subMonth(), Order::STATUS_DELIVERED);

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => 'week']));

        $response->assertOk()
            ->assertViewHas('selectedPeriod', 'week')
            ->assertViewHas('salesChart', function ($chart) {
                return $chart->count() === 7
                    && $chart->sum('quantity') === 2
                    && $chart->sum('revenue') === 4.0;
            });
    }

    public function test_admin_product_search_and_create_work(): void
    {
        $admin = User::factory()->admin()->create();

        Product::factory()->create(['name' => 'Router Alpha', 'sku' => 'ALPHA']);
        Product::factory()->create(['name' => 'Switch Beta', 'sku' => 'BETA']);

        $this->actingAs($admin)
            ->get(route('admin.products.index', ['q' => 'Alpha']))
            ->assertOk()
            ->assertSee('Router Alpha')
            ->assertDontSee('Switch Beta');

        $this->actingAs($admin)
            ->post(route('admin.products.store'), [
                'name' => 'Access Point New',
                'sku' => 'AP-NEW',
                'category' => Product::CATEGORY_ACCESS_POINT,
                'price' => 3500000,
                'stock' => 12,
                'description' => 'San pham tao tu admin test.',
                'is_active' => '1',
                'image_path' => 'assets/img/banner_img_01.jpg',
            ])
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Access Point New',
            'sku' => 'AP-NEW',
            'category' => Product::CATEGORY_ACCESS_POINT,
            'stock' => 12,
            'is_active' => true,
        ]);
    }

    public function test_admin_product_filters_by_category_status_and_stock(): void
    {
        $admin = User::factory()->admin()->create();

        Product::factory()->create([
            'name' => 'Router Active Low Stock',
            'category' => Product::CATEGORY_ROUTER,
            'stock' => 3,
            'is_active' => true,
        ]);
        Product::factory()->create([
            'name' => 'Camera Inactive Out Stock',
            'category' => Product::CATEGORY_CAMERA,
            'stock' => 0,
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.index', [
                'category' => Product::CATEGORY_ROUTER,
                'status' => 'active',
                'stock' => 'low',
            ]))
            ->assertOk()
            ->assertSee('Router Active Low Stock')
            ->assertDontSee('Camera Inactive Out Stock');
    }

    public function test_admin_order_filters_and_status_update_work(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 3,
            'price' => 1500000,
        ]);

        $order = $this->createOrderWithItem($customer, $product, 2, now(), Order::STATUS_PENDING);

        $this->actingAs($admin)
            ->get(route('admin.orders.index', ['status' => Order::STATUS_PENDING]))
            ->assertOk()
            ->assertSee($order->order_number);

        $this->actingAs($admin)
            ->patch(route('admin.orders.status', $order), [
                'status' => Order::STATUS_CANCELLED,
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CANCELLED,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
    }

    public function test_admin_can_confirm_bank_transfer_payment(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 3,
            'price' => 1500000,
        ]);

        $order = $this->createOrderWithItem($customer, $product, 1, now(), Order::STATUS_PENDING);
        $order->update([
            'payment_method' => Order::PAYMENT_METHOD_BANK_TRANSFER,
            'payment_status' => Order::PAYMENT_STATUS_AWAITING_TRANSFER,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.orders.payment.confirm', $order))
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_STATUS_PAID,
        ]);
    }

    public function test_admin_can_manage_customer_views(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create([
            'name' => 'Nguyen Van A',
            'email' => 'customer-a@example.test',
            'phone' => '0900000001',
        ]);
        $otherCustomer = User::factory()->create([
            'name' => 'Tran Thi B',
            'email' => 'customer-b@example.test',
        ]);
        $product = Product::factory()->create([
            'price' => 1200000,
            'stock' => 5,
        ]);

        $order = $this->createOrderWithItem($customer, $product, 2, now(), Order::STATUS_DELIVERED);

        $this->actingAs($admin)
            ->get(route('admin.customers.index', ['q' => 'Van A']))
            ->assertOk()
            ->assertSee('Nguyen Van A')
            ->assertDontSee('Tran Thi B');

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee($customer->email)
            ->assertSee($order->order_number);

        $this->actingAs($admin)
            ->get(route('admin.customers.show', $admin))
            ->assertNotFound();
    }

    private function createOrderWithItem(
        User $user,
        Product $product,
        int $quantity,
        Carbon $placedAt,
        string $status
    ): Order {
        if ($status !== Order::STATUS_CANCELLED) {
            $product->decrement('stock', $quantity);
        }

        $total = (float) $product->price * $quantity;

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'ADMIN-' . Str::upper(Str::random(10)),
            'status' => $status,
            'payment_method' => 'cod',
            'subtotal' => $total,
            'shipping_fee' => 0,
            'total' => $total,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'shipping_address' => $user->address,
            'note' => null,
            'placed_at' => $placedAt,
            'delivered_at' => $status === Order::STATUS_DELIVERED ? $placedAt->copy()->addDay() : null,
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'unit_price' => $product->price,
            'quantity' => $quantity,
            'line_total' => $total,
        ]);

        return $order;
    }
}
