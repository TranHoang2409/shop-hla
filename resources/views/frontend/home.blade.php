@extends('layouts.frontend')

@section('title', 'HLA Wifi Shop')

@section('extra_css')
    <link rel="stylesheet" href="{{ asset('assets/css/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slick-theme.min.css') }}">
@endsection

@php
    $bannerSlides = [
        [
            'image' => asset('assets/img/banner_img_01.jpg'),
            'alt' => 'Thiết bị WiFi',
            'url' => route('shop'),
        ],
        [
            'image' => asset('assets/img/banner_img_02.jpg'),
            'alt' => 'Thiết bị WiFi',
            'url' => route('shop'),
        ],
        [
            'image' => asset('assets/img/banner_img_03.jpg'),
            'alt' => 'Thiết bị WiFi',
            'url' => route('shop'),
        ],
    ];
@endphp

@section('content')
    <section class="slider-section slider-bg">
        <div class="home-slick-slider">
            @foreach ($bannerSlides as $slide)
                <div class="item">
                    <a href="{{ $slide['url'] }}">
                        <img class="img-fluid" src="{{ $slide['image'] }}" alt="{{ $slide['alt'] }}">
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <h2 class="h5">Tư vấn đúng mô hình triển khai</h2>
                    <p class="text-muted mb-0">Chọn router, mesh hoặc access point theo diện tích, số người dùng và tải truy cập.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <h2 class="h5">Đặt hàng rõ trạng thái</h2>
                    <p class="text-muted mb-0">Khách hàng theo dõi được lịch sử đơn, admin cập nhật trạng thái xuyên suốt vòng đời đơn.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <h2 class="h5">Vận hành được ngay</h2>
                    <p class="text-muted mb-0">Repo đã có dữ liệu mẫu cho admin, khách hàng và sản phẩm để chạy thử toàn luồng.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Danh mục thiết bị</h2>
                <p class="text-muted mb-0">Đi nhanh vào đúng nhóm router, mesh, access point hoặc phụ kiện mạng.</p>
            </div>
            <a href="{{ route('shop') }}" class="btn btn-outline-success">Tất cả danh mục</a>
        </div>

        <div class="row g-3">
            @foreach ($categoryCards as $category)
                <div class="col-md-6 col-xl-3">
                    <a href="{{ route('shop', ['category' => $category['key']]) }}"
                        class="d-block h-100 p-3 border rounded text-decoration-none text-dark bg-white">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <i class="fas {{ $category['icon'] }} text-success"></i>
                            <span class="badge bg-light text-dark">{{ $category['total'] }} sản phẩm</span>
                        </div>
                        <strong>{{ $category['label'] }}</strong>
                        <p class="text-muted small mb-0 mt-2">{{ $category['description'] }}</p>
                    </a>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1">Sản phẩm nổi bật</h2>
                <p class="text-muted mb-0">Các mẫu thiết bị đang có sẵn để test luồng mua hàng.</p>
            </div>
            <a href="{{ route('shop') }}" class="btn btn-outline-success">Xem toàn bộ</a>
        </div>

        <div class="row g-4">
            @foreach ($featuredProducts as $product)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm product-card">
                        <img src="{{ asset($product->image) }}" class="card-img-top product-thumb" alt="{{ $product->name }}">
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted">{{ $product->sku }}</small>
                            <h3 class="h5 mt-2">{{ $product->name }}</h3>
                            <p class="text-muted flex-grow-1">
                                {{ \Illuminate\Support\Str::limit($product->description, 90) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ number_format((float) $product->price) }} VNĐ</strong>
                                <a href="{{ route('products.show', $product) }}" class="btn btn-success">Chi tiết</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('extra_js')
    <script src="{{ asset('assets/js/jquery-1.11.0.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-migrate-1.2.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/slick.min.js') }}"></script>
    <script>
        $('.home-slick-slider').slick({
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: false,
            autoplay: true,
            autoplaySpeed: 5000
        });
    </script>
@endsection
