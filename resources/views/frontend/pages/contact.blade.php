@extends('layouts.frontend')

@section('title', 'Liên hệ')

@section('content')
    <div id="mapid" style="width: 100%; height: 320px;"></div>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h1 class="h3 mb-3">Thông tin liên hệ</h1>
                        <p class="text-muted">Liên hệ để được tư vấn chọn router, mesh hoặc access point phù hợp.</p>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-3"><strong>Hotline:</strong> 0900 000 001</li>
                            <li class="mb-3"><strong>Email:</strong> support@hla.test</li>
                            <li class="mb-3"><strong>Địa chỉ:</strong> 123 Nguyễn Văn Cừ, Quận 5, TP. Hồ Chí Minh</li>
                            <li><strong>Giờ làm việc:</strong> 08:00 - 18:00, Thứ 2 đến Thứ 7</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <h2 class="h4 mb-3">Khi cần hỗ trợ nhanh</h2>
                        <div class="feature-box">
                            <p class="mb-2"><strong>Trước khi mua:</strong> mô tả diện tích phủ sóng, số tầng, số lượng người dùng.</p>
                            <p class="mb-2"><strong>Sau khi mua:</strong> cung cấp mã đơn hàng để tra cứu nhanh trạng thái xử lý.</p>
                            <p class="mb-0"><strong>Bảo hành:</strong> chuẩn bị serial và thông tin mua hàng để kiểm tra điều kiện tiếp nhận.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        const map = L.map('mapid').setView([10.762622, 106.660172], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([10.762622, 106.660172]).addTo(map)
            .bindPopup('HLA Wifi Shop')
            .openPopup();
    </script>
@endsection
