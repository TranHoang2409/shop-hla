<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_reports_show_monthly_product_sales_using_delivered_orders_only(): void
    {
        Carbon::setTestNow('2026-05-14 10:00:00');

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();

        $bestSeller = Product::factory()->create([
            'name' => 'Router AX55',
            'sku' => 'AX55',
            'price' => 1000000,
        ]);
        $shippingOnly = Product::factory()->create([
            'name' => 'Mesh M4',
            'sku' => 'M4',
            'price' => 800000,
        ]);
        $unsold = Product::factory()->create([
            'name' => 'AP Zero',
            'sku' => 'AP0',
            'price' => 600000,
        ]);

        $this->createOrderWithItem(
            user: $customer,
            product: $bestSeller,
            quantity: 2,
            placedAt: now()->startOfMonth()->addDays(2),
            status: Order::STATUS_DELIVERED,
        );

        $this->createOrderWithItem(
            user: $customer,
            product: $bestSeller,
            quantity: 4,
            placedAt: now()->subMonth()->startOfMonth()->addDay(),
            status: Order::STATUS_DELIVERED,
        );

        $this->createOrderWithItem(
            user: $customer,
            product: $shippingOnly,
            quantity: 3,
            placedAt: now()->startOfMonth()->addDays(4),
            status: Order::STATUS_SHIPPING,
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.reports'));

        $response->assertOk()
            ->assertViewHas('selectedPeriod', 'month')
            ->assertViewHas('periodSummary', function (array $summary) {
                return $summary['orders'] === 1
                    && $summary['quantity_sold'] === 2
                    && $summary['products_sold'] === 1
                    && $summary['revenue'] === 2000000.0;
            })
            ->assertViewHas('revenueSummaryRows', function ($rows) {
                return $rows->count() === 1
                    && $rows->first()['label'] === '04/05'
                    && $rows->first()['orders'] === 1
                    && $rows->first()['quantity'] === 2
                    && $rows->first()['revenue'] === 2000000.0;
            })
            ->assertViewHas('productSales', function ($rows) use ($bestSeller, $shippingOnly, $unsold) {
                $sales = $rows->keyBy(fn (array $row) => (string) ($row['id'] ?? $row['sku'] ?? $row['name']));

                return $sales->get((string) $bestSeller->id)['quantity_sold'] === 2
                    && $sales->get((string) $bestSeller->id)['order_count'] === 1
                    && $sales->get((string) $shippingOnly->id)['quantity_sold'] === 0
                    && $sales->get((string) $unsold->id)['quantity_sold'] === 0;
            });
    }

    public function test_admin_reports_can_switch_to_yearly_product_sales(): void
    {
        Carbon::setTestNow('2026-05-14 10:00:00');

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Deco X20',
            'sku' => 'DX20',
            'price' => 1500000,
        ]);

        $this->createOrderWithItem(
            user: $customer,
            product: $product,
            quantity: 2,
            placedAt: now()->startOfMonth()->addDays(2),
            status: Order::STATUS_DELIVERED,
        );

        $this->createOrderWithItem(
            user: $customer,
            product: $product,
            quantity: 3,
            placedAt: now()->subMonth()->startOfMonth()->addDays(5),
            status: Order::STATUS_DELIVERED,
        );

        $this->createOrderWithItem(
            user: $customer,
            product: $product,
            quantity: 4,
            placedAt: now()->subYear()->startOfMonth()->addDays(3),
            status: Order::STATUS_DELIVERED,
        );

        $response = $this->actingAs($admin)
            ->get(route('admin.reports', ['period' => 'year']));

        $response->assertOk()
            ->assertViewHas('selectedPeriod', 'year')
            ->assertViewHas('periodSummary', function (array $summary) {
                return $summary['orders'] === 2
                    && $summary['quantity_sold'] === 5
                    && $summary['products_sold'] === 1
                    && $summary['revenue'] === 7500000.0;
            })
            ->assertViewHas('revenueSummaryRows', function ($rows) {
                $rowsByLabel = $rows->keyBy('label');

                return $rowsByLabel->has('04/2026')
                    && $rowsByLabel->has('05/2026')
                    && ! $rowsByLabel->has('05/2025')
                    && $rowsByLabel->get('04/2026')['quantity'] === 3
                    && $rowsByLabel->get('05/2026')['quantity'] === 2
                    && $rowsByLabel->get('04/2026')['revenue'] === 4500000.0
                    && $rowsByLabel->get('05/2026')['revenue'] === 3000000.0;
            })
            ->assertViewHas('productSales', function ($rows) use ($product) {
                $sales = $rows->keyBy(fn (array $row) => (string) ($row['id'] ?? $row['sku'] ?? $row['name']));

                return $sales->get((string) $product->id)['quantity_sold'] === 5
                    && $sales->get((string) $product->id)['order_count'] === 2;
            });
    }

    public function test_admin_reports_count_revenue_by_delivered_date(): void
    {
        Carbon::setTestNow('2026-06-16 10:00:00');

        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Router Cross Month',
            'sku' => 'RCM',
            'price' => 2000000,
        ]);

        $order = $this->createOrderWithItem(
            user: $customer,
            product: $product,
            quantity: 1,
            placedAt: Carbon::parse('2026-05-31 10:00:00'),
            status: Order::STATUS_DELIVERED,
        );
        $order->update(['delivered_at' => Carbon::parse('2026-06-01 09:00:00')]);

        $response = $this->actingAs($admin)
            ->get(route('admin.reports', ['period' => 'month']));

        $response->assertOk()
            ->assertViewHas('periodSummary', function (array $summary) {
                return $summary['orders'] === 1
                    && $summary['quantity_sold'] === 1
                    && $summary['revenue'] === 2000000.0;
            })
            ->assertViewHas('revenueSummaryRows', function ($rows) {
                return $rows->count() === 1
                    && $rows->first()['label'] === '01/06'
                    && $rows->first()['revenue'] === 2000000.0;
            });
    }

    private function createOrderWithItem(
        User $user,
        Product $product,
        int $quantity,
        Carbon $placedAt,
        string $status
    ): Order {
        $total = (float) $product->price * $quantity;

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => 'TEST-' . Str::upper(Str::random(10)),
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
            'delivered_at' => $status === Order::STATUS_DELIVERED
                ? $placedAt->copy()->addDay()
                : null,
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
