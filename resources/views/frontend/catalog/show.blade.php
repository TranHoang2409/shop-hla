@extends('layouts.frontend')

@section('title', $product->name)

@section('content')
    @php
        $detailText = mb_strtolower($product->name . ' ' . $product->description);
        $stockPercent = max(12, min(100, (int) round(($product->stock / 25) * 100)));
        $usageTags = collect([
            str_contains($detailText, 'wifi 6') ? 'WiFi 6' : null,
            str_contains($detailText, 'mesh') ? 'Mesh roaming' : null,
            str_contains($detailText, 'văn phòng') ? 'Văn phòng nhỏ' : null,
            str_contains($detailText, 'gia đình') || str_contains($detailText, 'căn hộ') ? 'Gia đình' : null,
            str_contains($detailText, 'cafe') ? 'Quán cafe' : null,
        ])->filter()->values();
    @endphp

    <section class="product-hero">
        <div class="container py-5">
            <nav class="product-breadcrumb mb-4">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span>/</span>
                <a href="{{ route('shop', ['category' => $product->category]) }}">{{ $product->categoryName() }}</a>
                <span>/</span>
                <strong>{{ $product->name }}</strong>
            </nav>

            <div class="row g-4 g-xl-5 align-items-start">
                <div class="col-lg-6">
                    <div class="product-gallery-card">
                        <div class="product-gallery-badge">
                            {{ $product->stock > 0 ? 'Sẵn hàng' : 'Hết hàng' }}
                        </div>
                        <img src="{{ asset($product->image) }}" class="img-fluid product-detail-image"
                            alt="{{ $product->name }}">
                    </div>

                    <div class="product-meta-grid mt-4">
                        <div class="product-meta-item">
                            <span>Mã sản phẩm</span>
                            <strong>{{ $product->sku }}</strong>
                        </div>
                        <div class="product-meta-item">
                            <span>Danh mục</span>
                            <strong>{{ $product->categoryName() }}</strong>
                        </div>
                        <div class="product-meta-item">
                            <span>Bảo hành</span>
                            <strong>12 - 24 tháng</strong>
                        </div>
                        <div class="product-meta-item">
                            <span>Thanh toán</span>
                            <strong>COD khi nhận hàng</strong>
                        </div>
                        <div class="product-meta-item">
                            <span>Tư vấn kỹ thuật</span>
                            <strong>Miễn phí trước khi mua</strong>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="product-summary-panel">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="product-chip product-chip-dark">{{ $product->sku }}</span>
                            <span class="product-chip">{{ $product->categoryName() }}</span>
                            @foreach ($usageTags as $tag)
                                <span class="product-chip">{{ $tag }}</span>
                            @endforeach
                        </div>

                        <h1 class="product-title">{{ $product->name }}</h1>
                        <p class="product-subtitle">
                            Giải pháp thiết bị mạng phù hợp cho mô hình triển khai thực tế, ưu tiên độ ổn định,
                            khả năng mở rộng và vận hành dễ dàng.
                        </p>

                        <div class="product-price-card">
                            <div>
                                <span class="product-price-label">Giá bán tại HLA</span>
                                <div class="product-price">{{ number_format((float) $product->price) }} VNĐ</div>
                            </div>
                            <div class="product-stock-box">
                                <span>Tồn kho khả dụng</span>
                                <strong>{{ $product->stock }} thiết bị</strong>
                            </div>
                        </div>

                        <div class="stock-bar-wrap mt-3 mb-4">
                            <div class="d-flex justify-content-between small text-muted mb-2">
                                <span>Mức tồn kho</span>
                                <span>{{ $product->stock > 5 ? 'Ổn định' : 'Sắp hết hàng' }}</span>
                            </div>
                            <div class="stock-bar">
                                <span style="width: {{ $stockPercent }}%"></span>
                            </div>
                        </div>

                        <p class="product-description">{{ $product->description }}</p>

                        <form action="{{ route('cart.store') }}" method="POST" class="product-buy-card">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            <div class="row g-3 align-items-end">
                                <div class="col-sm-4">
                                    <label class="form-label">Số lượng</label>
                                    <input type="number" class="form-control form-control-lg" name="quantity" min="1"
                                        max="{{ max($product->stock, 1) }}" value="1"
                                        {{ $product->stock < 1 ? 'disabled' : '' }}>
                                </div>

                                <div class="col-sm-8 d-grid">
                                    <button class="btn btn-success btn-lg product-buy-button"
                                        {{ $product->stock < 1 ? 'disabled' : '' }}>
                                        {{ $product->stock > 0 ? 'Thêm vào giỏ hàng' : 'Tạm hết hàng' }}
                                    </button>
                                </div>
                            </div>

                            <div class="purchase-note">
                                <div><strong>Giao hàng:</strong> nội thành ưu tiên xử lý trong ngày.</div>
                                <div><strong>Hỗ trợ:</strong> tư vấn chọn thiết bị theo diện tích và số người dùng.</div>
                            </div>
                        </form>

                        <div class="product-trust-grid mt-4">
                            <div class="product-trust-item">
                                <strong>Chính hãng</strong>
                                <span>Hóa đơn và serial minh bạch</span>
                            </div>
                            <div class="product-trust-item">
                                <strong>Bảo hành</strong>
                                <span>Tiếp nhận và theo dõi rõ quy trình</span>
                            </div>
                            <div class="product-trust-item">
                                <strong>Triển khai</strong>
                                <span>Phù hợp gia đình, quán và văn phòng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container pb-5">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="product-detail-card h-100">
                    <h2>Mô tả và bối cảnh sử dụng</h2>
                    <p>{{ $product->description }}</p>
                    <p class="mb-0">
                        Đây là lựa chọn phù hợp nếu bạn cần một thiết bị mạng có thể đưa vào vận hành nhanh,
                        dễ kiểm soát và đủ ổn định cho nhu cầu thực tế thay vì chỉ chạy đẹp trên thông số.
                    </p>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="product-detail-card h-100">
                    <h2>Cam kết khi mua tại HLA</h2>
                    <ul class="product-benefit-list mb-0">
                        <li>Tư vấn chọn router, mesh hoặc access point theo diện tích triển khai.</li>
                        <li>Kiểm tra tồn kho và cập nhật tình trạng đơn hàng minh bạch.</li>
                        <li>Hỗ trợ bảo hành theo serial và thông tin mua hàng đã lưu trên hệ thống.</li>
                        <li>Phù hợp để test toàn bộ luồng user/admin của dự án ngay sau khi seed dữ liệu mẫu.</li>
                    </ul>
                </div>
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-5 pt-4 border-top">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="h4 mb-1">Sản phẩm liên quan</h2>
                        <p class="text-muted mb-0">Các lựa chọn khác cùng nhóm nhu cầu triển khai.</p>
                    </div>
                    <a href="{{ route('shop', ['category' => $product->category]) }}" class="btn btn-outline-success">Xem cùng danh mục</a>
                </div>

                <div class="row g-4">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="col-md-6 col-xl-3">
                            <div class="card border-0 shadow-sm h-100 product-related-card">
                                <img src="{{ asset($relatedProduct->image) }}" class="card-img-top product-thumb"
                                    alt="{{ $relatedProduct->name }}">
                                <div class="card-body">
                                    <small class="text-muted">{{ $relatedProduct->sku }}</small>
                                    <h3 class="h6 mt-2">{{ $relatedProduct->name }}</h3>
                                    <p class="mb-3 fw-semibold">{{ number_format((float) $relatedProduct->price) }} VNĐ</p>
                                    <a href="{{ route('products.show', $relatedProduct) }}"
                                        class="btn btn-sm btn-outline-success">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection
