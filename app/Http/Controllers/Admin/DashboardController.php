<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $selectedPeriod = $request->query('period', 'year');

        if (! in_array($selectedPeriod, ['week', 'month', 'year'], true)) {
            $selectedPeriod = 'year';
        }

        $periodWindow = $this->resolveReportingWindow($selectedPeriod);
        $periodStart = $periodWindow['start'];
        $periodEnd = $periodWindow['end'];

        $deliveredOrders = Order::with('items')
            ->where('status', Order::STATUS_DELIVERED)
            ->whereBetween(DB::raw('COALESCE(delivered_at, placed_at)'), [$periodStart, $periodEnd])
            ->get();

        $salesChart = $this->buildSalesChart($deliveredOrders, $selectedPeriod, $periodStart);

        $customerSegments = Order::query()
            ->where('status', Order::STATUS_DELIVERED)
            ->selectRaw('customer_email, COUNT(*) as order_count')
            ->groupBy('customer_email')
            ->get();

        $topSellingProducts = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('order_items.product_id, order_items.product_name, order_items.product_sku, products.image as image, SUM(order_items.quantity) as quantity_sold, SUM(order_items.line_total) as revenue')
            ->where('orders.status', Order::STATUS_DELIVERED)
            ->whereBetween(DB::raw('COALESCE(orders.delivered_at, orders.placed_at)'), [$periodStart, $periodEnd])
            ->groupBy('order_items.product_id', 'order_items.product_name', 'order_items.product_sku', 'products.image')
            ->orderByDesc('quantity_sold')
            ->orderByDesc('revenue')
            ->take(5)
            ->get();

        return view('backend.dashboard.index', [
            'stats' => [
                'products' => Product::count(),
                'active_products' => Product::active()->count(),
                'orders' => Order::count(),
                'pending_orders' => Order::whereIn('status', [
                    Order::STATUS_PENDING,
                    Order::STATUS_CONFIRMED,
                    Order::STATUS_PREPARING,
                    Order::STATUS_SHIPPING,
                ])->count(),
                'revenue' => Order::where('status', Order::STATUS_DELIVERED)->sum('total'),
            ],
            'recentOrders' => Order::with(['user', 'items.product'])->latest('placed_at')->take(6)->get(),
            'lowStockProducts' => Product::active()->orderBy('stock')->take(5)->get(),
            'topSellingProducts' => $topSellingProducts,
            'salesChart' => $salesChart,
            'selectedPeriod' => $selectedPeriod,
            'periodOptions' => [
                'year' => 'Năm nay',
                'month' => 'Tháng này',
                'week' => 'Tuần này',
            ],
            'periodLabel' => $periodWindow['label'],
            'customerOverview' => [
                'first_time' => (int) $customerSegments->where('order_count', 1)->count(),
                'returning' => (int) $customerSegments->where('order_count', '>', 1)->count(),
                'customers' => (int) $customerSegments->count(),
                'products' => Product::count(),
                'orders' => Order::count(),
            ],
        ]);
    }

    private function buildSalesChart(Collection $orders, string $period, Carbon $periodStart): Collection
    {
        return match ($period) {
            'week' => collect(range(0, 6))
                ->map(function (int $dayOffset) use ($orders, $periodStart) {
                    $date = $periodStart->copy()->addDays($dayOffset);
                    $dayOrders = $orders->filter(fn (Order $order) => $this->reportingDate($order)->isSameDay($date));

                    return $this->salesChartPoint($date->format('d/m'), $dayOrders);
                })
                ->values(),
            'month' => collect(range(1, $periodStart->copy()->endOfMonth()->day))
                ->map(function (int $day) use ($orders, $periodStart) {
                    $date = $periodStart->copy()->day($day);
                    $dayOrders = $orders->filter(fn (Order $order) => $this->reportingDate($order)->isSameDay($date));

                    return $this->salesChartPoint((string) $day, $dayOrders);
                })
                ->values(),
            default => collect(range(1, 12))
                ->map(function (int $month) use ($orders, $periodStart) {
                    $monthOrders = $orders->filter(function (Order $order) use ($month, $periodStart) {
                        $reportingDate = $this->reportingDate($order);

                        return (int) $reportingDate->format('Y') === (int) $periodStart->format('Y')
                            && (int) $reportingDate->format('n') === $month;
                    });

                    return $this->salesChartPoint(Carbon::create($periodStart->year, $month, 1)->format('m/Y'), $monthOrders);
                })
                ->values(),
        };
    }

    private function salesChartPoint(string $label, Collection $orders): array
    {
        return [
            'label' => $label,
            'revenue' => round(((float) $orders->sum('total')) / 1000000, 1),
            'quantity' => (int) $orders->sum(fn (Order $order) => $order->items->sum('quantity')),
        ];
    }

    public function reports(Request $request)
    {
        $selectedPeriod = $request->query('period', 'month');

        if (! in_array($selectedPeriod, ['week', 'month', 'year'], true)) {
            $selectedPeriod = 'month';
        }

        $periodWindow = $this->resolveReportingWindow($selectedPeriod);
        $periodStart = $periodWindow['start'];
        $periodEnd = $periodWindow['end'];

        $deliveredOrdersQuery = Order::query()
            ->where('status', Order::STATUS_DELIVERED);

        $periodDeliveredOrders = (clone $deliveredOrdersQuery)
            ->whereBetween(DB::raw('COALESCE(delivered_at, placed_at)'), [$periodStart, $periodEnd])
            ->get();

        $deliveredOrders = Order::where('status', Order::STATUS_DELIVERED)
            ->orderBy('placed_at')
            ->get();

        $revenueBreakdown = $this->buildRevenueBreakdown($periodDeliveredOrders, $selectedPeriod, $periodStart);
        $revenueSummaryRows = $revenueBreakdown
            ->filter(fn (array $row) => $row['orders'] > 0)
            ->values();

        $statusSummary = collect(Order::statuses())
            ->map(function (string $label, string $status) {
                return [
                    'label' => $label,
                    'count' => Order::where('status', $status)->count(),
                ];
            })
            ->values();

        $products = Product::query()
            ->orderBy('name')
            ->get();

        $aggregatedProductSales = OrderItem::query()
            ->selectRaw('product_id, product_name, product_sku, SUM(quantity) as quantity_sold, SUM(line_total) as revenue, COUNT(DISTINCT order_id) as order_count')
            ->whereHas('order', function ($query) use ($periodStart, $periodEnd) {
                $query->where('status', Order::STATUS_DELIVERED)
                    ->whereBetween(DB::raw('COALESCE(delivered_at, placed_at)'), [$periodStart, $periodEnd]);
            })
            ->groupBy('product_id', 'product_name', 'product_sku')
            ->get();

        $productSalesById = $aggregatedProductSales
            ->filter(fn ($sale) => filled($sale->product_id))
            ->keyBy(fn ($sale) => (int) $sale->product_id);

        $currentProductIds = $products->pluck('id');

        $productSales = $products
            ->map(function (Product $product) use ($productSalesById) {
                $sale = $productSalesById->get($product->id);

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'image' => $product->image,
                    'stock' => $product->stock,
                    'is_active' => (bool) $product->is_active,
                    'is_archived' => false,
                    'quantity_sold' => $sale ? (int) $sale->quantity_sold : 0,
                    'revenue' => $sale ? (float) $sale->revenue : 0,
                    'order_count' => $sale ? (int) $sale->order_count : 0,
                ];
            })
            ->concat(
                $aggregatedProductSales
                    ->filter(function ($sale) use ($currentProductIds) {
                        return blank($sale->product_id) || ! $currentProductIds->contains((int) $sale->product_id);
                    })
                    ->map(function ($sale) {
                        return [
                            'id' => null,
                            'name' => $sale->product_name,
                            'sku' => $sale->product_sku,
                            'image' => null,
                            'stock' => null,
                            'is_active' => false,
                            'is_archived' => true,
                            'quantity_sold' => (int) $sale->quantity_sold,
                            'revenue' => (float) $sale->revenue,
                            'order_count' => (int) $sale->order_count,
                        ];
                    })
            )
            ->values();

        $rankedProductSales = $this->sortProductSales($productSales, descending: true);
        $bestSellingProducts = $rankedProductSales
            ->filter(fn (array $row) => $row['quantity_sold'] > 0)
            ->take(5)
            ->values();
        $slowSellingProducts = $this->sortProductSales($productSales, descending: false)
            ->take(5)
            ->values();
        $topProduct = $bestSellingProducts->first();

        return view('backend.reports.index', [
            'revenueBreakdown' => $revenueBreakdown,
            'revenueSummaryRows' => $revenueSummaryRows,
            'revenueBreakdownTitle' => $selectedPeriod === 'year' ? 'Doanh thu theo tháng' : 'Doanh thu theo ngày',
            'statusSummary' => $statusSummary,
            'deliveredRevenue' => $deliveredOrders->sum('total'),
            'averageOrderValue' => $deliveredOrders->count() > 0
                ? $deliveredOrders->avg('total')
                : 0,
            'selectedPeriod' => $selectedPeriod,
            'periodOptions' => [
                'week' => 'Tuần này',
                'month' => 'Tháng này',
                'year' => 'Năm nay',
            ],
            'periodLabel' => $periodWindow['label'],
            'periodDescription' => $periodWindow['description'],
            'periodSummary' => [
                'orders' => $periodDeliveredOrders->count(),
                'revenue' => (float) $periodDeliveredOrders->sum('total'),
                'quantity_sold' => (int) $productSales->sum('quantity_sold'),
                'products_sold' => $productSales->filter(fn (array $row) => $row['quantity_sold'] > 0)->count(),
            ],
            'productSales' => $rankedProductSales,
            'bestSellingProducts' => $bestSellingProducts,
            'slowSellingProducts' => $slowSellingProducts,
            'topProduct' => $topProduct,
        ]);
    }

    private function buildRevenueBreakdown(Collection $orders, string $period, Carbon $periodStart): Collection
    {
        return match ($period) {
            'week' => collect(range(0, 6))
                ->map(function (int $dayOffset) use ($orders, $periodStart) {
                    $date = $periodStart->copy()->addDays($dayOffset);
                    $dayOrders = $orders->filter(fn (Order $order) => $this->reportingDate($order)->isSameDay($date));

                    return $this->revenueBreakdownPoint($date->format('d/m'), $dayOrders);
                })
                ->values(),
            'month' => collect(range(1, $periodStart->copy()->endOfMonth()->day))
                ->map(function (int $day) use ($orders, $periodStart) {
                    $date = $periodStart->copy()->day($day);
                    $dayOrders = $orders->filter(fn (Order $order) => $this->reportingDate($order)->isSameDay($date));

                    return $this->revenueBreakdownPoint($date->format('d/m'), $dayOrders);
                })
                ->values(),
            default => collect(range(1, 12))
                ->map(function (int $month) use ($orders, $periodStart) {
                    $monthOrders = $orders->filter(function (Order $order) use ($month, $periodStart) {
                        $reportingDate = $this->reportingDate($order);

                        return (int) $reportingDate->format('Y') === (int) $periodStart->format('Y')
                            && (int) $reportingDate->format('n') === $month;
                    });

                    return $this->revenueBreakdownPoint(Carbon::create($periodStart->year, $month, 1)->format('m/Y'), $monthOrders);
                })
                ->values(),
        };
    }

    private function revenueBreakdownPoint(string $label, Collection $orders): array
    {
        return [
            'label' => $label,
            'orders' => $orders->count(),
            'quantity' => (int) $orders->sum(fn (Order $order) => $order->items->sum('quantity')),
            'revenue' => (float) $orders->sum('total'),
        ];
    }

    private function reportingDate(Order $order): Carbon
    {
        return $order->delivered_at ?? $order->placed_at;
    }

    /**
     * @return array{start: Carbon, end: Carbon, label: string, description: string}
     */
    private function resolveReportingWindow(string $period): array
    {
        $reference = now();

        return match ($period) {
            'week' => [
                'start' => $reference->copy()->startOfWeek(),
                'end' => $reference->copy()->endOfWeek(),
                'label' => 'Tuần này',
                'description' => $reference->copy()->startOfWeek()->format('d/m/Y') . ' - ' . $reference->copy()->endOfWeek()->format('d/m/Y'),
            ],
            'year' => [
                'start' => $reference->copy()->startOfYear(),
                'end' => $reference->copy()->endOfYear(),
                'label' => 'Năm ' . $reference->format('Y'),
                'description' => $reference->copy()->startOfYear()->format('d/m/Y') . ' - ' . $reference->copy()->endOfYear()->format('d/m/Y'),
            ],
            default => [
                'start' => $reference->copy()->startOfMonth(),
                'end' => $reference->copy()->endOfMonth(),
                'label' => 'Tháng ' . $reference->format('m/Y'),
                'description' => $reference->copy()->startOfMonth()->format('d/m/Y') . ' - ' . $reference->copy()->endOfMonth()->format('d/m/Y'),
            ],
        };
    }

    private function sortProductSales(Collection $productSales, bool $descending): Collection
    {
        return $productSales
            ->sort(function (array $left, array $right) use ($descending): int {
                if ($left['quantity_sold'] !== $right['quantity_sold']) {
                    return $descending
                        ? $right['quantity_sold'] <=> $left['quantity_sold']
                        : $left['quantity_sold'] <=> $right['quantity_sold'];
                }

                if ($left['revenue'] !== $right['revenue']) {
                    return $descending
                        ? $right['revenue'] <=> $left['revenue']
                        : $left['revenue'] <=> $right['revenue'];
                }

                return strcmp($left['name'], $right['name']);
            })
            ->values();
    }
}
