@extends('layouts.backend')

@section('title', 'Quản lý đơn hàng')
@section('page_title', 'Quản lý đơn hàng')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="fs-3 mb-1">Đơn hàng</h1>
                <p class="mb-0">Theo dõi đơn hàng, thanh toán và trạng thái xử lý</p>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form class="row g-2 mb-4">
                <div class="col-md-6">
                    <input type="text" name="q" class="form-control" value="{{ $filters['q'] }}"
                        placeholder="Tìm mã đơn, tên khách hoặc email">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        @foreach ($statuses as $status => $label)
                            <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100">Lọc</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Thanh toán</th>
                            <th>Tổng tiền</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_number }}</td>
                                <td>
                                    <div>{{ $order->customer_name }}</div>
                                    <small class="text-muted">{{ $order->customer_email }}</small>
                                </td>
                                <td>{{ optional($order->placed_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
                                </td>
                                <td>
                                    <div class="small">{{ $order->paymentMethodLabel() }}</div>
                                    <span class="badge bg-{{ $order->paymentStatusBadgeClass() }}">{{ $order->paymentStatusLabel() }}</span>
                                </td>
                                <td>{{ number_format((float) $order->total) }} VNĐ</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng nào.</td>
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
@endsection
