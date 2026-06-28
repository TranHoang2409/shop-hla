@extends('layouts.backend')

@section('title', 'Chi tiết khách hàng')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h1 class="fs-3 mb-1">{{ $customer->name }}</h1>
                    <p class="mb-0">Thông tin liên hệ, thống kê mua hàng và các đơn gần đây</p>
                </div>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Quay lại</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-1">Tổng đơn</p>
                    <h2 class="h4 mb-0">{{ number_format($summary['orders']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-1">Đơn đang xử lý</p>
                    <h2 class="h4 mb-0">{{ number_format($summary['active_orders']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-1">Đơn đã giao</p>
                    <h2 class="h4 mb-0">{{ number_format($summary['delivered_orders']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <p class="text-muted mb-1">Tổng mua</p>
                    <h2 class="h4 mb-0">{{ number_format($summary['total_spent']) }} VNĐ</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thông tin khách hàng</h2>
                    <p class="mb-1"><strong>{{ $customer->name }}</strong></p>
                    <p class="mb-1">{{ $customer->phone ?: 'Chưa có số điện thoại' }}</p>
                    <p class="mb-3">{{ $customer->email }}</p>
                    <p class="mb-0">{{ $customer->address ?: 'Chưa có địa chỉ' }}</p>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Lịch sử đơn hàng</h2>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th class="text-end">Tổng tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="fw-semibold">{{ $order->order_number }}</td>
                                        <td>{{ optional($order->placed_at)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->statusBadgeClass() }}">
                                                {{ $order->statusLabel() }}
                                            </span>
                                        </td>
                                        <td class="text-end">{{ number_format((float) $order->total) }} VNĐ</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                                class="btn btn-sm btn-outline-primary">Xem đơn</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Khách hàng chưa có đơn hàng.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
