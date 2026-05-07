@extends('layouts.app')

@section('title', 'Cart')

@section('content')

    <div class="container py-5">
        <h2 class="mb-4">Giỏ hàng</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @php
            $cart = session('cart', []);
            $total = 0;
        @endphp

        @if (count($cart) > 0)
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Ảnh</th>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th width="160">Số lượng</th>
                        <th>Thành tiền</th>
                        <th width="100">Xóa</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($cart as $id => $item)
                        @php
                            $subTotal = $item['price'] * $item['quantity'];
                            $total += $subTotal;
                        @endphp

                        <tr>
                            <td>
                                <img src="{{ asset($item['image']) }}" width="70">
                            </td>

                            <td>{{ $item['name'] }}</td>

                            <td>{{ number_format($item['price']) }} VNĐ</td>

                            <td>
                                <form action="{{ route('cart.update') }}" method="POST" class="d-flex">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">

                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1"
                                        class="form-control me-2">

                                    <button type="submit" class="btn btn-success btn-sm">
                                        Cập nhật
                                    </button>
                                </form>
                            </td>

                            <td>{{ number_format($subTotal) }} VNĐ</td>

                            <td>
                                <form action="{{ route('cart.remove') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $id }}">

                                    <button type="submit" class="btn btn-danger btn-sm">
                                        Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row">
                <div class="col-md-6">
                    <a href="{{ route('shop') }}" class="btn btn-secondary">
                        Tiếp tục mua hàng
                    </a>

                    <form action="{{ route('cart.clear') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger">
                            Xóa toàn bộ giỏ hàng
                        </button>
                    </form>
                </div>

                <div class="col-md-6 text-end">
                    <h4>Tổng tiền: {{ number_format($total) }} VNĐ</h4>

                    <button class="btn btn-success btn-lg mt-2">
                        Đặt hàng
                    </button>
                </div>
            </div>
        @else
            <div class="alert alert-warning">
                Giỏ hàng của bạn đang trống.
            </div>

            <a href="{{ route('shop') }}" class="btn btn-success">
                Quay lại mua hàng
            </a>
        @endif
    </div>

@endsection
