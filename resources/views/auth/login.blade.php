@extends('layouts.auth')

@section('title', 'Đăng nhập')

@section('content')
    <div class="container d-flex align-items-center justify-content-center min-vh-100 py-5">
        <div class="card border-0 shadow-sm" style="max-width: 420px; width: 100%;">
            <div class="card-body p-5">
                <div class="text-center mb-3">
                    <a href="{{ route('home') }}" class="mb-4 d-inline-flex align-items-center text-decoration-none">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="HLA Wifi Shop" style="height: 40px;">
                    </a>
                    <h1 class="card-title mb-5 h5">Đăng nhập vào tài khoản</h1>
                </div>

                <form action="{{ route('login.post') }}" method="POST" class="mt-3">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Địa chỉ email</label>
                        <input id="email" type="email" name="email"
                            class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                            placeholder="name@example.com" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label d-flex justify-content-between">
                            <span>Mật khẩu</span>
                        </label>
                        <input id="password" type="password" name="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="Nhập mật khẩu"
                            required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input id="remember" class="form-check-input" type="checkbox" name="remember" value="1">
                            <label class="form-check-label small" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
                </form>

                <div class="text-center mt-3 small text-muted">
                    Chưa có tài khoản? <a href="{{ route('register') }}" class="link-primary">Đăng ký</a>
                </div>
            </div>
        </div>
    </div>
@endsection
