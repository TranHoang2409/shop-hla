@extends('layouts.frontend')

@section('title', 'Giới thiệu')

@section('content')
    <div class="container py-5">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="badge bg-success-subtle text-success mb-3">Về HLA Wifi Shop</span>
                <h1 class="display-6 fw-bold mb-3">Bán thiết bị WiFi theo đúng nhu cầu triển khai thực tế.</h1>
                <p class="text-muted">
                    HLA Wifi Shop tập trung vào router, mesh WiFi, access point và thiết bị mạng cho gia đình,
                    cửa hàng nhỏ, quán cafe và văn phòng. Dự án hiện tại được tổ chức lại theo luồng bán hàng thực,
                    từ quản lý sản phẩm đến theo dõi đơn hàng.
                </p>
            </div>
            <div class="col-lg-6">
                <img src="{{ asset('assets/img/about-hero.jpg') }}" alt="About HLA" class="img-fluid">
            </div>
        </div>

        <div class="row g-4 mt-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <h2 class="h5">Thiết bị phù hợp</h2>
                    <p class="text-muted mb-0">Ưu tiên sản phẩm dễ triển khai, dễ quản lý và rõ hiệu năng sử dụng.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <h2 class="h5">Quản lý đơn hàng</h2>
                    <p class="text-muted mb-0">Admin theo dõi trạng thái đơn từ lúc chờ xác nhận đến khi giao thành công.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <h2 class="h5">Báo cáo doanh thu</h2>
                    <p class="text-muted mb-0">Doanh thu được tổng hợp từ các đơn đã giao để sát hơn với vận hành thực tế.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
