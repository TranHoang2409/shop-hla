@extends('layouts.frontend')

@section('title', 'Đặt hàng')

@section('content')
    @php
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
        $selectedPayment = old('payment_method', 'cod');
        $qrUrl = 'https://img.vietqr.io/image/' . $bankTransfer['bank_code'] . '-' . $bankTransfer['account_number'] . '-compact2.png?' . http_build_query([
            'amount' => (int) $total,
            'addInfo' => 'Thanh toan HLA Wifi Shop',
            'accountName' => $bankTransfer['account_name'],
        ]);
    @endphp

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-4">Thông tin nhận hàng</h1>

                        <form action="{{ route('checkout.store') }}" method="POST" class="row g-3">
                            @csrf

                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input type="text" class="form-control" name="customer_name"
                                    value="{{ old('customer_name', $user->name) }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" class="form-control" name="customer_phone"
                                    value="{{ old('customer_phone', $user->phone) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="customer_email"
                                    value="{{ old('customer_email', $user->email) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Địa chỉ giao hàng</label>
                                <textarea class="form-control" name="shipping_address" rows="4" required>{{ old('shipping_address', $user->address) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Ghi chú</label>
                                <textarea class="form-control" name="note" rows="3">{{ old('note') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Phương thức thanh toán</label>
                                <div class="row g-3">
                                    @foreach ($paymentMethods as $method => $label)
                                        <div class="col-md-6">
                                            <label class="payment-option h-100">
                                                <input type="radio" name="payment_method" value="{{ $method }}"
                                                    data-payment-method @checked($selectedPayment === $method)>
                                                <span>
                                                    <strong>{{ $label }}</strong>
                                                    <small>
                                                        {{ $method === 'bank_transfer' ? 'Quét QR hoặc chuyển khoản theo STK.' : 'Thanh toán trực tiếp khi nhận hàng.' }}
                                                    </small>
                                                </span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="col-12 {{ $selectedPayment === 'bank_transfer' ? '' : 'd-none' }}" data-bank-transfer-box>
                                <div class="bank-transfer-box">
                                    <div class="bank-transfer-qr">
                                        <img src="{{ $qrUrl }}" alt="QR chuyển khoản HLA Wifi Shop">
                                    </div>
                                    <div class="bank-transfer-info">
                                        <h2 class="h5 mb-3">Thông tin chuyển khoản</h2>
                                        <div><span>Ngân hàng</span><strong>{{ $bankTransfer['bank_name'] }}</strong></div>
                                        <div><span>Số tài khoản</span><strong>{{ $bankTransfer['account_number'] }}</strong></div>
                                        <div><span>Chủ tài khoản</span><strong>{{ $bankTransfer['account_name'] }}</strong></div>
                                        <div><span>Số tiền</span><strong>{{ number_format((float) $total) }} VNĐ</strong></div>
                                        <p class="mb-0 text-muted small">Sau khi đặt hàng, hệ thống sẽ tạo mã đơn. Admin xác nhận khi nhận được tiền.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-success btn-lg">Xác nhận đặt hàng</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Đơn hàng của bạn</h2>

                        @foreach ($cart as $item)
                            <div class="d-flex justify-content-between py-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $item['name'] }}</div>
                                    <small class="text-muted">{{ $item['quantity'] }} x {{ number_format((float) $item['price']) }} VNĐ</small>
                                </div>
                                <strong>{{ number_format((float) $item['price'] * $item['quantity']) }} VNĐ</strong>
                            </div>
                        @endforeach

                        <div class="d-flex justify-content-between pt-3">
                            <span>Tổng thanh toán</span>
                            <strong class="text-success">{{ number_format((float) $total) }} VNĐ</strong>
                        </div>

                        <div class="alert alert-light mt-3 mb-0">
                            Bạn có thể thanh toán COD hoặc chuyển khoản ngân hàng.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
