@extends('layouts.backend')

@section('title', 'Báo cáo')

@section('content')
    @php
        $lowStockCount = $productSales->filter(fn (array $row) => ! is_null($row['stock']) && $row['stock'] > 0 && $row['stock'] <= 5)->count();
        $outOfStockCount = $productSales->filter(fn (array $row) => ! is_null($row['stock']) && $row['stock'] === 0)->count();
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="admin-page-heading d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fs-3 mb-1">Báo cáo</h1>
                    <p class="mb-0">Theo dõi doanh thu và sản phẩm bán theo kỳ</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @foreach ($periodOptions as $period => $label)
                        <a href="{{ route('admin.reports', ['period' => $period]) }}"
                            class="btn btn-sm {{ $selectedPeriod === $period ? 'btn-primary' : 'btn-light border' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 admin-summary-card">
                <div class="card-body p-4">
                    <h6 class="mb-4">Tổng doanh thu</h6>
                    <h3 class="mb-1 fw-bold">{{ number_format((float) $periodSummary['revenue']) }} VNĐ</h3>
                    <p class="mb-0 text-success small">{{ $periodLabel }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 admin-summary-card">
                <div class="card-body p-4">
                    <h6 class="mb-4">Sản phẩm đã bán</h6>
                    <h3 class="mb-1 fw-bold">{{ number_format($periodSummary['quantity_sold']) }}</h3>
                    <p class="mb-0 text-success small">{{ number_format($periodSummary['products_sold']) }} mã sản phẩm phát sinh bán</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 admin-summary-card">
                <div class="card-body p-4">
                    <h6 class="mb-4">Sắp hết hàng</h6>
                    <h3 class="mb-1 fw-bold">{{ number_format($lowStockCount) }}</h3>
                    <p class="mb-0 text-danger small">Tồn kho từ 1 đến 5 sản phẩm</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="card h-100 admin-summary-card">
                <div class="card-body p-4">
                    <h6 class="mb-4">Hết hàng</h6>
                    <h3 class="mb-1 fw-bold">{{ number_format($outOfStockCount) }}</h3>
                    <p class="mb-0 text-danger small">Sản phẩm hiện không còn để bán</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start mb-3 gap-2">
                        <div>
                            <h2 class="mb-0 fs-5">Tổng quan doanh thu</h2>
                            <small class="text-muted">{{ $periodLabel }} · {{ $periodDescription }}</small>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach ($periodOptions as $period => $label)
                                <a href="{{ route('admin.reports', ['period' => $period]) }}"
                                    class="btn btn-sm {{ $selectedPeriod === $period ? 'btn-primary' : 'btn-light border' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="admin-chart-wrap">
                        <canvas id="reportsSalesChart"></canvas>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="#product-sales-detail" class="small">Xem báo cáo chi tiết</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="mb-0 fs-5">Sản phẩm nổi bật</h2>
                        </div>
                    </div>

                    <div class="list-group list-group-flush">
                        @forelse ($bestSellingProducts as $row)
                            <div class="list-group-item p-3 d-flex align-items-center admin-feature-row">
                                <div class="me-3">
                                    <img src="{{ asset($row['image'] ?: 'assets/img/shop_01.jpg') }}" alt="{{ $row['name'] }}"
                                        class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="admin-feature-content">
                                        <div class="min-w-0">
                                            <h6 class="mb-0">{{ $row['name'] }}</h6>
                                            <small class="text-secondary">{{ number_format($row['quantity_sold']) }} sản phẩm đã bán</small>
                                        </div>
                                        <div class="text-end admin-money">
                                            <strong>{{ number_format((float) $row['revenue']) }} VNĐ</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item p-3 text-muted">Chưa có dữ liệu sản phẩm nổi bật trong kỳ này.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="product-sales-detail" class="row g-3">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="mb-0 fs-5">Chi tiết bán hàng theo sản phẩm</h2>
                            <small class="text-muted">Chỉ tính sản phẩm thuộc đơn đã giao trong {{ strtolower($periodLabel) }}</small>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đã bán</th>
                                    <th>Số đơn</th>
                                    <th>Doanh thu</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productSales as $row)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $row['name'] }}</div>
                                            <small class="text-muted">{{ $row['sku'] ?: 'Không có SKU' }}</small>
                                        </td>
                                        <td>{{ number_format($row['quantity_sold']) }}</td>
                                        <td>{{ number_format($row['order_count']) }}</td>
                                        <td>{{ number_format((float) $row['revenue']) }} VNĐ</td>
                                        <td>{{ is_null($row['stock']) ? '—' : number_format($row['stock']) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu bán hàng theo sản phẩm.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card mb-3">
                <div class="card-body p-4">
                    <h2 class="mb-3 fs-5">Bán chạy nhất</h2>
                    @if ($topProduct)
                        <h3 class="h5 mb-1">{{ $topProduct['name'] }}</h3>
                        <p class="text-muted mb-3">{{ $topProduct['sku'] ?: 'Không có SKU' }}</p>
                        <div class="mb-2"><strong>{{ number_format($topProduct['quantity_sold']) }}</strong> sản phẩm đã bán</div>
                        <div class="text-muted">{{ number_format((float) $topProduct['revenue']) }} VNĐ doanh thu</div>
                    @else
                        <p class="text-muted mb-0">Chưa có sản phẩm nổi bật trong kỳ này.</p>
                    @endif
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body p-4">
                    <h2 class="mb-3 fs-5">Bán chậm</h2>
                    <div class="d-flex flex-column gap-3">
                        @forelse ($slowSellingProducts as $row)
                            <div class="admin-compact-row">
                                <div class="min-w-0">
                                    <div class="fw-semibold">{{ $row['name'] }}</div>
                                    <small class="text-muted">{{ $row['sku'] ?: 'Không có SKU' }}</small>
                                </div>
                                <div class="text-end admin-number-cell">
                                    <strong>{{ number_format($row['quantity_sold']) }}</strong>
                                    <small class="d-block text-muted">đã bán</small>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Chưa có dữ liệu để so sánh.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <h2 class="mb-3 fs-5">{{ $revenueBreakdownTitle }}</h2>
                    <div class="d-flex flex-column gap-3">
                        @forelse ($revenueSummaryRows as $row)
                            <div class="admin-breakdown-row">
                                <div class="min-w-0">
                                    <div class="fw-semibold">{{ $row['label'] }}</div>
                                    <small class="text-muted">
                                        {{ $row['orders'] }} đơn hàng · {{ number_format($row['quantity']) }} sản phẩm
                                    </small>
                                </div>
                                <div class="text-end admin-money">
                                    <strong>{{ number_format((float) $row['revenue']) }} VNĐ</strong>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Chưa có dữ liệu doanh thu trong kỳ này.</p>
                        @endforelse
                    </div>
                </div>
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

            const labels = @json($revenueBreakdown->pluck('label')->values());
            const revenue = @json($revenueBreakdown->pluck('revenue')->map(fn ($value) => (float) $value)->values());

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

            if (labels.length > 0 && revenue.some(value => value > 0)) {
                new Chart(document.getElementById('reportsSalesChart'), {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Doanh thu',
                            data: revenue,
                            borderColor: '#E66239',
                            backgroundColor: 'rgba(230, 98, 57, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#E66239'
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        },
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
                                display: false
                            }
                        }
                    }
                });
            } else {
                createEmptyState('reportsSalesChart', 'Chưa có dữ liệu doanh thu để vẽ biểu đồ.');
            }
        })();
    </script>
@endsection
