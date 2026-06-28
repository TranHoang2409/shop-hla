@extends('layouts.backend')

@section('title', 'Chi tiết đơn hàng')
@section('page_title', 'Chi tiết đơn hàng')

@section('content')
    @php
        $qrUrl = 'https://img.vietqr.io/image/' . $bankTransfer['bank_code'] . '-' . $bankTransfer['account_number'] . '-compact2.png?' . http_build_query([
            'amount' => (int) $order->total,
            'addInfo' => $order->order_number,
            'accountName' => $bankTransfer['account_name'],
        ]);
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="mb-4">
                <h1 class="fs-3 mb-1">Chi tiết đơn hàng</h1>
                <p class="mb-0">Xem sản phẩm, thanh toán, thông tin khách hàng và trạng thái hiện tại</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h2 class="h5 mb-1">{{ $order->order_number }}</h2>
                            <p class="text-muted mb-0">Ngày đặt: {{ optional($order->placed_at)->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="badge fs-6 bg-{{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
                    </div>

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

            @if ($order->isBankTransfer())
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h2 class="h5 mb-1">Thông tin chuyển khoản</h2>
                                <p class="text-muted mb-0">Đối chiếu giao dịch ngân hàng trước khi xác nhận.</p>
                            </div>
                            <span class="badge bg-{{ $order->paymentStatusBadgeClass() }}">{{ $order->paymentStatusLabel() }}</span>
                        </div>

                        <div class="bank-transfer-box">
                            <div class="bank-transfer-qr">
                                <img src="{{ $qrUrl }}" alt="QR chuyển khoản đơn {{ $order->order_number }}">
                            </div>
                            <div class="bank-transfer-info">
                                <div><span>Ngân hàng</span><strong>{{ $bankTransfer['bank_name'] }}</strong></div>
                                <div><span>Số tài khoản</span><strong>{{ $bankTransfer['account_number'] }}</strong></div>
                                <div><span>Chủ tài khoản</span><strong>{{ $bankTransfer['account_name'] }}</strong></div>
                                <div><span>Nội dung</span><strong>{{ $order->order_number }}</strong></div>
                                <div><span>Số tiền</span><strong>{{ number_format((float) $order->total) }} VNĐ</strong></div>
                                @if ($order->payment_confirmed_at)
                                    <div><span>Đã xác nhận lúc</span><strong>{{ $order->payment_confirmed_at->format('d/m/Y H:i') }}</strong></div>
                                @endif
                            </div>
                        </div>

                        @if ($order->isAwaitingBankTransfer())
                            <form action="{{ route('admin.orders.payment.confirm', $order) }}" method="POST" class="mt-3">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-success">
                                    <i class="fas fa-check me-1"></i> Xác nhận đã nhận tiền
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thông tin khách hàng</h2>
                    <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                    <p class="mb-1">{{ $order->customer_phone }}</p>
                    <p class="mb-3">{{ $order->customer_email }}</p>
                    <p class="mb-0">{{ $order->shipping_address }}</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Thanh toán</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phương thức</span>
                        <strong>{{ $order->paymentMethodLabel() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Trạng thái</span>
                        <span class="badge bg-{{ $order->paymentStatusBadgeClass() }}">{{ $order->paymentStatusLabel() }}</span>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Cập nhật trạng thái</h2>
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <select name="status" class="form-select mb-3">
                            @foreach ($statuses as $status => $label)
                                <option value="{{ $status }}" @selected($order->status === $status)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button class="btn btn-success w-100">Lưu trạng thái</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Tổng tiền</h2>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính</span>
                        <strong>{{ number_format((float) $order->subtotal) }} VNĐ</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển</span>
                        <strong>{{ number_format((float) $order->shipping_fee) }} VNĐ</strong>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2">
                        <span>Tổng cộng</span>
                        <strong class="text-success">{{ number_format((float) $order->total) }} VNĐ</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
