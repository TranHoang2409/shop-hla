@extends('layouts.backend')

@section('title', 'Tổng quan')

@section('content')
    @php
        $firstTimeCustomers = (int) ($customerOverview['first_time'] ?? 0);
        $returningCustomers = (int) ($customerOverview['returning'] ?? 0);
        $customerSegmentTotal = max($firstTimeCustomers + $returningCustomers, 1);
        $firstTimeRate = round(($firstTimeCustomers / $customerSegmentTotal) * 100);
        $returningRate = round(($returningCustomers / $customerSegmentTotal) * 100);
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="mb-6">
                <h1 class="fs-3 mb-1">Tổng quan</h1>
                <p class="mb-0">Theo dõi nhanh số liệu bán hàng, tồn kho và đơn hàng.</p>
                <span class="d-none">Tổng quan vận hành</span>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2 admin-dashboard-stat">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-shape icon-md bg-primary text-white rounded-2">
                        <i class="fas fa-box fs-5"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="mb-3 fs-6">Tổng sản phẩm</h2>
                        <h3 class="fw-bold mb-0 admin-dashboard-stat-value">{{ number_format($stats['products']) }}</h3>
                        <p class="text-primary mb-0 small">{{ number_format($stats['active_products']) }} sản phẩm đang hiển thị</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2 admin-dashboard-stat">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-shape icon-md bg-success text-white rounded-2">
                        <i class="fas fa-shopping-cart fs-5"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="mb-3 fs-6">Tổng đơn hàng</h2>
                        <h3 class="fw-bold mb-0 admin-dashboard-stat-value">{{ number_format($stats['orders']) }}</h3>
                        <p class="text-success mb-0 small">{{ number_format($stats['pending_orders']) }} đơn đang xử lý</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-2 admin-dashboard-stat">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-shape icon-md bg-info text-white rounded-2">
                        <i class="fas fa-money-bill-wave fs-5"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="mb-3 fs-6">Doanh thu đã giao</h2>
                        <h3 class="fw-bold mb-0 admin-dashboard-stat-value is-money">{{ number_format((float) $stats['revenue']) }} VNĐ</h3>
                        <p class="text-info mb-0 small">Chỉ tính đơn hoàn tất giao hàng</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-12">
            <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2 admin-dashboard-stat">
                <div class="d-flex gap-3 align-items-start">
                    <div class="icon-shape icon-md bg-warning text-white rounded-2">
                        <i class="fas fa-chart-pie fs-5"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="mb-3 fs-6">Báo cáo</h2>
                        <h3 class="fw-bold mb-0 admin-dashboard-stat-value">Phân tích</h3>
                        <p class="text-warning mb-0 small">
                            <a href="{{ route('admin.reports') }}" class="text-warning text-decoration-none">Xem báo cáo bán hàng</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                        <div>
                            <h3 class="fw-bold h4">{{ number_format((float) $stats['revenue']) }} VNĐ</h3>
                            <span>Tổng doanh thu</span>
                        </div>
                        <div>
                            <i class="fas fa-wallet fs-1 text-primary"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <div class="text-muted">Doanh thu từ các đơn đã giao</div>
                        <div><a href="{{ route('admin.reports') }}" class="link-primary text-decoration-underline">Xem</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                        <div>
                            <h3 class="fw-bold h4">{{ number_format($stats['pending_orders']) }}</h3>
                            <span>Đơn đang xử lý</span>
                        </div>
                        <div>
                            <i class="fas fa-truck fs-1 text-danger"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <div class="text-muted">Chờ xác nhận, đã xác nhận, chuẩn bị và đang giao</div>
                        <div><a href="{{ route('admin.orders.index') }}" class="link-primary text-decoration-underline">Xem</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
                        <div>
                            <h3 class="fw-bold h4">{{ number_format($stats['active_products']) }}</h3>
                            <span>Sản phẩm đang bán</span>
                        </div>
                        <div>
                            <i class="fas fa-signal fs-1 text-warning"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center small">
                        <div class="text-muted">Sản phẩm đang hiển thị ngoài cửa hàng</div>
                        <div><a href="{{ route('admin.products.index') }}" class="link-primary text-decoration-underline">Xem</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
                    <div>
                        <h3 class="h5 mb-0">Doanh thu và số lượng bán</h3>
                        <small class="text-muted">{{ $periodLabel }}</small>
                    </div>
                    <div>
                        <select class="form-select form-select-sm"
                            onchange="window.location.href = '{{ route('admin.dashboard') }}?period=' + this.value">
                            @foreach ($periodOptions as $period => $label)
                                <option value="{{ $period }}" @selected($selectedPeriod === $period)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="admin-chart-wrap is-short">
                        <canvas id="dashboardSalesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
                    <h3 class="h5 mb-0">Tổng quan khách hàng</h3>
                    <span class="badge bg-light text-secondary border">Tất cả đơn đã giao</span>
                </div>
                <div class="card-body p-4">
                    <h3 class="h6">Tỷ lệ khách hàng</h3>
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <div class="admin-chart-wrap is-compact">
                                <canvas id="dashboardCustomerChart"></canvas>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="row">
                                <div class="col-6 border-end">
                                    <div class="text-center">
                                        <h2 class="mb-1">{{ number_format($firstTimeCustomers) }}</h2>
                                        <p class="text-success mb-2">Mua lần đầu</p>
                                        <span class="badge bg-success"><i class="fas fa-arrow-up-left me-1"></i>{{ $firstTimeRate }}%</span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h2 class="mb-1">{{ number_format($returningCustomers) }}</h2>
                                        <p class="text-warning mb-2">Khách quay lại</p>
                                        <span class="badge bg-success"><i class="fas fa-arrow-up-left me-1"></i>{{ $returningRate }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center border-top mt-4 pt-4">
                        <div class="col-4 border-end">
                            <h3 class="fw-bold mb-2">{{ number_format($customerOverview['products']) }}</h3>
                            <small class="text-secondary">Sản phẩm</small>
                        </div>
                        <div class="col-4 border-end">
                            <h3 class="fw-bold mb-2">{{ number_format($customerOverview['customers']) }}</h3>
                            <small class="text-secondary">Khách hàng</small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-2">{{ number_format($customerOverview['orders']) }}</h3>
                            <small class="text-secondary">Đơn hàng</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                    <h4 class="mb-0 h5">Sản phẩm bán chạy</h4>
                    <a href="{{ route('admin.reports', ['period' => $selectedPeriod]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="far fa-calendar-alt"></i> {{ $periodLabel }}
                    </a>
                </div>

                <ul class="list-group list-group-flush">
                    @forelse ($topSellingProducts as $product)
                        @php($productImage = $product->image ?: 'assets/img/logo.png')
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <img src="{{ asset($productImage) }}" class="rounded"
                                style="width: 48px; height: 48px; object-fit: cover;" alt="{{ $product->product_name }}">
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $product->product_name }}</p>
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <small class="fw-semibold">{{ number_format((float) $product->revenue) }} VNĐ</small>
                                    <small>•</small>
                                    <small>{{ number_format((int) $product->quantity_sold) }} sản phẩm</small>
                                </div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border border-primary">
                                {{ number_format((int) $product->quantity_sold) }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Chưa có dữ liệu bán hàng.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                    <h4 class="mb-0 h5">Sản phẩm sắp hết hàng</h4>
                    <a href="{{ route('admin.products.index') }}" class="small text-primary text-decoration-underline">Xem tất cả</a>
                </div>

                <ul class="list-group list-group-flush">
                    @forelse ($lowStockProducts as $product)
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <img src="{{ asset($product->image ?: 'assets/img/logo.png') }}" class="rounded"
                                style="width: 48px; height: 48px; object-fit: cover;" alt="{{ $product->name }}">
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $product->name }}</p>
                                <small>ID: {{ $product->sku }}</small>
                            </div>
                            <div class="d-flex flex-column gap-0 align-items-center">
                                <span class="fw-semibold text-primary">{{ str_pad((string) $product->stock, 2, '0', STR_PAD_LEFT) }}</span>
                                <small class="text-muted">Tồn kho</small>
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Không có sản phẩm sắp hết hàng.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
                    <h4 class="mb-0 h5">Đơn hàng gần đây</h4>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="far fa-calendar-alt"></i> Theo tuần
                    </a>
                </div>

                <ul class="list-group list-group-flush">
                    @forelse ($recentOrders as $order)
                        @php($orderThumb = optional(optional($order->items->first())->product)->image ?: 'assets/img/logo.png')
                        <li class="list-group-item d-flex align-items-center gap-3">
                            <img src="{{ asset($orderThumb) }}" class="rounded"
                                style="width: 48px; height: 48px; object-fit: cover;" alt="{{ $order->order_number }}">
                            <div class="flex-grow-1">
                                <p class="mb-1">{{ $order->order_number }}</p>
                                <div class="d-flex align-items-center gap-2 text-muted">
                                    <small class="fw-semibold">{{ $order->customer_name }}</small>
                                    <small>•</small>
                                    <small>{{ number_format((float) $order->total) }} VNĐ</small>
                                </div>
                            </div>
                            <span class="badge bg-{{ $order->statusBadgeClass() }}-subtle text-{{ $order->statusBadgeClass() }}">
                                {{ $order->statusLabel() }}
                            </span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted">Chưa có đơn hàng gần đây.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
    <script src="{{ asset('assets/js/chart.umd.min.js') }}"></script>
    <script>
        (() => {
            if (typeof Chart === 'undefined') {
                return;
            }

            const labels = @json($salesChart->pluck('label')->values());
            const revenue = @json($salesChart->pluck('revenue')->map(fn ($value) => (float) $value)->values());
            const quantity = @json($salesChart->pluck('quantity')->map(fn ($value) => (int) $value)->values());
            const customerData = @json([$firstTimeCustomers, $returningCustomers]);

            const createEmptyState = (canvasId, message) => {
                const canvas = document.getElementById(canvasId);
                if (!canvas) {
                    return;
                }

                const wrapper = canvas.parentElement;
                wrapper.innerHTML = `<div class="admin-chart-empty">${message}</div>`;
            };

            Chart.defaults.font.family = 'Poppins, system-ui, sans-serif';
            Chart.defaults.color = '#526071';
            Chart.defaults.plugins.legend.labels.usePointStyle = true;
            Chart.defaults.plugins.legend.labels.boxWidth = 10;

            if (labels.length > 0 && (revenue.some(value => value > 0) || quantity.some(value => value > 0))) {
                new Chart(document.getElementById('dashboardSalesChart'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: revenue,
                            backgroundColor: '#f7a085',
                            borderRadius: 6,
                            borderSkipped: false
                        }, {
                            label: 'Số lượng bán',
                            data: quantity,
                            backgroundColor: '#E66239',
                            borderRadius: 6,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(148, 163, 184, 0.18)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            } else {
                createEmptyState('dashboardSalesChart', 'Chưa có dữ liệu để hiển thị biểu đồ.');
            }

            if (customerData.some(value => value > 0)) {
                new Chart(document.getElementById('dashboardCustomerChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Mua lần đầu', 'Khách quay lại'],
                        datasets: [{
                            data: customerData,
                            backgroundColor: ['#36c997', '#f59e0b'],
                            borderWidth: 0,
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            } else {
                createEmptyState('dashboardCustomerChart', 'Chưa có dữ liệu phân nhóm khách hàng.');
            }
        })();
    </script>
@endsection
