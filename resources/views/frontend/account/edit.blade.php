@extends('layouts.frontend')

@section('title', 'Tài khoản')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 mb-3">Quản lý tài khoản</h1>
                        <p class="text-muted">Cập nhật thông tin giao hàng mặc định và mật khẩu đăng nhập.</p>

                        <form action="{{ route('account.update') }}" method="POST" class="row g-3 mt-2">
                            @csrf
                            @method('PUT')

                            <div class="col-md-6">
                                <label class="form-label">Họ tên</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $user->phone) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="{{ old('email', $user->email) }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Địa chỉ</label>
                                <textarea name="address" class="form-control" rows="3" required>{{ old('address', $user->address) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="password" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="col-12">
                                <button class="btn btn-success">Lưu thay đổi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
