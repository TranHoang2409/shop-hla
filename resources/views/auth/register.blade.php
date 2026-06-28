@extends('layouts.auth')

@section('title', 'Đăng ký')

@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card border-0 shadow-sm" style="max-width: 460px; width: 100%;">
            <div class="card-body p-5">
                <div class="text-center mb-3">
                    <a href="{{ route('home') }}" class="mb-4 d-inline-flex align-items-center text-decoration-none">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="HLA Wifi Shop" style="height: 40px;">
                    </a>
                    <h1 class="card-title mb-5 h5">Tạo tài khoản mới</h1>
                </div>

                <form action="{{ route('register.post') }}" method="POST" class="mt-3">
                    @csrf

                    <div class="mb-3">
                        <label for="fullName" class="form-label">Họ và tên</label>
                        <input id="fullName" type="text" name="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                            placeholder="Nguyễn Văn A" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input id="phone" type="text" name="phone"
                            class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}"
                            placeholder="0900000000" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Địa chỉ email</label>
                        <input id="email" type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="name@example.com" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ giao hàng</label>
                        <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3"
                            placeholder="Số nhà, đường, phường/xã, quận/huyện..." required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu</label>
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Tạo mật khẩu" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                        <input id="confirmPassword" type="password" name="password_confirmation"
                            class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Đăng ký</button>
                </form>

                <div class="text-center mt-3 small text-muted">
                    Đã có tài khoản? <a href="{{ route('login') }}" class="link-primary">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
@endsection
