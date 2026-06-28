<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SampleOrderSeeder extends Seeder
{
    /**
     * Seed fake sales for products that already exist in the database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);

        DB::transaction(function (): void {
            $this->clearExistingSampleOrders();

            $products = Product::query()
                ->active()
                ->orderBy('price')
                ->orderByDesc('stock')
                ->orderBy('id')
                ->get();

            if ($products->isEmpty()) {
                return;
            }

            $customers = User::query()
                ->where('role', User::ROLE_CUSTOMER)
                ->orderBy('id')
                ->get();

            $remainingStock = $products
                ->mapWithKeys(fn (Product $product) => [$product->id => max(0, (int) $product->stock)])
                ->all();

            foreach ($this->buildOrderDefinitions($products, $customers, $remainingStock) as $orderData) {
                $this->createOrder($orderData);
            }
        });
    }

    private function clearExistingSampleOrders(): void
    {
        Order::with('items')
            ->where('order_number', 'like', 'HLA-SAMPLE-%')
            ->get()
            ->each(function (Order $order): void {
                if ($order->status !== Order::STATUS_CANCELLED) {
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            Product::whereKey($item->product_id)->increment('stock', $item->quantity);
                        }
                    }
                }

                $order->delete();
            });
    }

    /**
     * @param  Collection<int, Product>  $products
     * @param  Collection<int, User>  $customers
     * @param  array<int, int>  $remainingStock
     * @return array<int, array<string, mixed>>
     */
    private function buildOrderDefinitions(Collection $products, Collection $customers, array &$remainingStock): array
    {
        $year = (int) now()->format('Y');
        $currentMonth = (int) now()->format('n');
        $definitions = [];
        $sequence = 1;

        for ($month = 1; $month <= $currentMonth; $month++) {
            $ordersInMonth = $month === $currentMonth ? 7 : min(4, 2 + (int) ceil($products->count() / 8));
            $days = $this->orderDaysForMonth($month, $ordersInMonth);

            foreach ($days as $index => $day) {
                $placedAt = Carbon::create($year, $month, $day, 9 + ($index % 8), 30, 0);
                $items = $this->pickItemsForOrder($products, $remainingStock, $sequence, $month === $currentMonth);

                if ($items === []) {
                    continue;
                }

                $customer = $customers->get($sequence % max(1, $customers->count())) ?? $customers->first();

                $definitions[] = [
                    'order_number' => 'HLA-SAMPLE-' . $placedAt->format('md') . '-' . str_pad((string) $sequence, 3, '0', STR_PAD_LEFT),
                    'customer' => $customer,
                    'status' => Order::STATUS_DELIVERED,
                    'placed_at' => $placedAt,
                    'delivered_at' => $placedAt->copy()->addDays($index % 2),
                    'note' => 'Don hang mau de test doanh thu va san pham ban chay.',
                    'items' => $items,
                ];

                $sequence++;
            }
        }

        foreach ($this->currentOperationalOrders($products, $customers, $remainingStock, $sequence) as $orderData) {
            $definitions[] = $orderData;
        }

        return $definitions;
    }

    /**
     * @return array<int, int>
     */
    private function orderDaysForMonth(int $month, int $ordersInMonth): array
    {
        if ($month === (int) now()->format('n')) {
            $today = now()->day;
            $weekStart = now()->copy()->startOfWeek()->day;

            return collect([3, 7, 10, 14, max(1, $weekStart), max(1, $today - 1), $today])
                ->filter(fn (int $day) => $day <= $today)
                ->unique()
                ->take($ordersInMonth)
                ->values()
                ->all();
        }

        return collect([4, 12, 20, 27])
            ->take($ordersInMonth)
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, Product>  $products
     * @param  array<int, int>  $remainingStock
     * @return array<int, array{product: Product, quantity: int}>
     */
    private function pickItemsForOrder(Collection $products, array &$remainingStock, int $sequence, bool $preferRecentBestsellers): array
    {
        $items = [];
        $selectedProductIds = [];
        $lineCount = min($products->count(), $sequence % 3 === 0 ? 3 : 2);
        $bestSellerCount = max(1, (int) ceil($products->count() * 0.35));
        $bestSellers = $products->take($bestSellerCount)->values();
        $regularProducts = $products->slice($bestSellerCount)->values();

        for ($line = 0; $line < $lineCount; $line++) {
            $pool = ($preferRecentBestsellers || $line === 0 || $regularProducts->isEmpty())
                ? $bestSellers
                : $regularProducts;

            $product = $pool->get(($sequence + $line) % max(1, $pool->count()));

            if (! $product || in_array($product->id, $selectedProductIds, true) || ($remainingStock[$product->id] ?? 0) <= 0) {
                $product = $products->first(function (Product $candidate) use ($remainingStock, $selectedProductIds) {
                    return ($remainingStock[$candidate->id] ?? 0) > 0
                        && ! in_array($candidate->id, $selectedProductIds, true);
                });
            }

            if (! $product) {
                break;
            }

            $quantity = $this->quantityFor($product, $sequence, $line, $preferRecentBestsellers);
            $quantity = min($quantity, $remainingStock[$product->id]);

            if ($quantity <= 0) {
                continue;
            }

            $remainingStock[$product->id] -= $quantity;
            $selectedProductIds[] = $product->id;

            $items[] = [
                'product' => $product,
                'quantity' => $quantity,
            ];
        }

        return $items;
    }

    private function quantityFor(Product $product, int $sequence, int $line, bool $preferRecentBestsellers): int
    {
        $price = (float) $product->price;

        if ($price >= 400000) {
            return 1;
        }

        if ($price >= 200000) {
            return $preferRecentBestsellers && $line === 0 ? 2 : 1;
        }

        return 2 + (($sequence + $line) % 2);
    }

    /**
     * @param  Collection<int, Product>  $products
     * @param  Collection<int, User>  $customers
     * @param  array<int, int>  $remainingStock
     * @return array<int, array<string, mixed>>
     */
    private function currentOperationalOrders(Collection $products, Collection $customers, array &$remainingStock, int $sequence): array
    {
        $statuses = [
            Order::STATUS_SHIPPING,
            Order::STATUS_PENDING,
            Order::STATUS_CONFIRMED,
            Order::STATUS_CANCELLED,
        ];

        return collect($statuses)
            ->map(function (string $status, int $index) use ($products, $customers, &$remainingStock, $sequence) {
                $placedAt = now()->copy()->subDays(max(0, 3 - $index))->setTime(10 + $index, 15);
                $items = $this->pickItemsForOrder($products, $remainingStock, $sequence + $index, true);

                if ($items === []) {
                    return null;
                }

                $customer = $customers->get(($sequence + $index) % max(1, $customers->count())) ?? $customers->first();

                return [
                    'order_number' => 'HLA-SAMPLE-' . $placedAt->format('md') . '-OPEN-' . ($index + 1),
                    'customer' => $customer,
                    'status' => $status,
                    'placed_at' => $placedAt,
                    'delivered_at' => null,
                    'note' => 'Don mau trang thai ' . $status . ' de test don hang gan day.',
                    'items' => $items,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $orderData
     */
    private function createOrder(array $orderData): void
    {
        if (Order::where('order_number', $orderData['order_number'])->exists()) {
            return;
        }

        /** @var User $user */
        $user = $orderData['customer'];

        $subtotal = collect($orderData['items'])
            ->sum(fn (array $line) => (float) $line['product']->price * $line['quantity']);

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderData['order_number'],
            'status' => $orderData['status'],
            'payment_method' => 'cod',
            'subtotal' => $subtotal,
            'shipping_fee' => 0,
            'total' => $subtotal,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone,
            'shipping_address' => $user->address,
            'note' => $orderData['note'],
            'placed_at' => $orderData['placed_at'],
            'delivered_at' => $orderData['delivered_at'],
        ]);

        foreach ($orderData['items'] as $item) {
            /** @var Product $product */
            $product = $item['product'];

            if ($orderData['status'] !== Order::STATUS_CANCELLED) {
                Product::whereKey($product->id)->decrement('stock', $item['quantity']);
            }

            $order->items()->create([
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'unit_price' => $product->price,
                'quantity' => $item['quantity'],
                'line_total' => (float) $product->price * $item['quantity'],
            ]);
        }
    }
}
