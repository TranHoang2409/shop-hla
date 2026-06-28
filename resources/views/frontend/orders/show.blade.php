@extends('layouts.frontend')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    @php
        $qrUrl = 'https://img.vietqr.io/image/' . $bankTransfer['bank_code'] . '-' . $bankTransfer['account_number'] . '-compact2.png?' . http_build_query([
            'amount' => (int) $order->total,
            'addInfo' => $order->order_number,
            'accountName' => $bankTransfer['account_name'],
        ]);
    @endphp

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Đơn hàng {{ $order->order_number }}</h1>
                <p class="text-muted mb-0">Ngày đặt: {{ optional($order->placed_at)->format('d/m/Y H:i') }}</p>
            </div>
            <span class="badge fs-6 bg-{{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Sản phẩm trong đơn</h2>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Đơn giá</th>
                                        <th>Số lượng</th>
                                        <th>Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                <small class="text-muted">{{ $item->product_sku }}</small>
                                            </td>
                                            <td>{{ number_format((float) $item->unit_price) }} VNĐ</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format((float) $item->line_total) }} VNĐ</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Thông tin nhận hàng</h2>
                        <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                        <p class="mb-1">{{ $order->customer_phone }}</p>
                        <p class="mb-3">{{ $order->customer_email }}</p>
                        <p class="mb-0">{{ $order->shipping_address }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5 mb-3">Thanh toán</h2>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phương thức</span>
                            <strong>{{ $order->paymentMethodLabel() }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span>Trạng thái</span>
                            <span class="badge bg-{{ $order->paymentStatusBadgeClass() }}">{{ $order->paymentStatusLabel() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span>
                            <strong>{{ number_format((float) $order->subtotal) }} VNĐ</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí giao hàng</span>
                            <strong>{{ number_format((float) $order->shipping_fee) }} VNĐ</strong>
                        </div>
                        <div class="d-flex justify-content-between border-top pt-2">
                            <span>Tổng thanh toán</span>
                            <strong class="text-success">{{ number_format((float) $order->total) }} VNĐ</strong>
                        </div>

                        @if ($order->isAwaitingBankTransfer())
                            <div class="bank-transfer-mini mt-4">
                                <img src="{{ $qrUrl }}" alt="QR chuyển khoản đơn {{ $order->order_number }}">
                                <div>
                                    <h3 class="h6 mb-2">Chuyển khoản để admin xác nhận</h3>
                                    <p class="mb-1"><strong>{{ $bankTransfer['bank_name'] }}</strong> - {{ $bankTransfer['account_number'] }}</p>
                                    <p class="mb-1">Chủ TK: <strong>{{ $bankTransfer['account_name'] }}</strong></p>
                                    <p class="mb-0">Nội dung: <strong>{{ $order->order_number }}</strong></p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
