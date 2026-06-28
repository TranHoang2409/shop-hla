@extends('layouts.frontend')

@section('title', 'Lịch sử đơn hàng')

@section('content')
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Lịch sử đơn hàng</h1>
                <p class="text-muted mb-0">Theo dõi trạng thái xử lý của từng đơn hàng.</p>
            </div>
            <a href="{{ route('shop') }}" class="btn btn-outline-success">Mua thêm sản phẩm</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Tổng tiền</th>
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
                                <td>{{ number_format((float) $order->total) }} VNĐ</td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-secondary">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Bạn chưa có đơn hàng nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
