@extends('layouts.frontend')

@section('title', 'Giỏ hàng')

@section('content')
    @php
        $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
    @endphp

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h2 mb-1">Giỏ hàng</h1>
                <p class="text-muted mb-0">Kiểm tra số lượng trước khi sang bước đặt hàng.</p>
            </div>
            <a href="{{ route('shop') }}" class="btn btn-outline-secondary">Tiếp tục mua hàng</a>
        </div>

        @if ($cart)
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th width="160">Số lượng</th>
                                <th>Thành tiền</th>
                                <th width="100"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="{{ asset($item['image']) }}" alt="{{ $item['name'] }}" width="80"
                                                class="rounded-3">
                                            <div>
                                                <div class="fw-semibold">{{ $item['name'] }}</div>
                                                <small class="text-muted">{{ $item['sku'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ number_format((float) $item['price']) }} VNĐ</td>
                                    <td>
                                        <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="d-flex gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                                min="1" max="{{ $item['stock'] }}" class="form-control">
                                            <button class="btn btn-outline-success">Lưu</button>
                                        </form>
                                    </td>
                                    <td class="fw-semibold">
                                        {{ number_format((float) $item['price'] * $item['quantity']) }} VNĐ
                                    </td>
                                    <td>
                                        <form action="{{ route('cart.destroy', $item['id']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row justify-content-end mt-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h2 class="h5 mb-3">Tổng đơn tạm tính</h2>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tạm tính</span>
                                <strong>{{ number_format((float) $total) }} VNĐ</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-4">
                                <span>Phí vận chuyển</span>
                                <strong>0 VNĐ</strong>
                            </div>
                            <a href="{{ route('checkout.create') }}" class="btn btn-success w-100 mb-2">Tiến hành đặt hàng</a>
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-danger w-100">Xóa toàn bộ giỏ hàng</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning">Giỏ hàng của bạn đang trống.</div>
        @endif
    </div>
@endsection
