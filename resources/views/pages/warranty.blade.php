@extends('layouts.app')

@section('title', 'Chính sách bảo hành')

@section('content')
    <div class="container py-5">
        <h2 class="mb-4 text-center">Chính sách bảo hành</h2>

        <div class="row justify-content-center">
            <div class="col-md-10">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="text-success">1. Thời gian bảo hành</h4>
                        <p>
                            Các sản phẩm thiết bị WiFi tại HLA được bảo hành theo chính sách của nhà sản xuất,
                            thông thường từ 12 đến 24 tháng tùy từng sản phẩm.
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="text-success">2. Điều kiện bảo hành</h4>
                        <ul>
                            <li>Sản phẩm còn trong thời hạn bảo hành.</li>
                            <li>Sản phẩm còn tem bảo hành, mã serial rõ ràng.</li>
                            <li>Lỗi phát sinh do nhà sản xuất.</li>
                            <li>Khách hàng cung cấp hóa đơn hoặc thông tin mua hàng tại HLA.</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="text-success">3. Trường hợp không được bảo hành</h4>
                        <ul>
                            <li>Sản phẩm bị rơi vỡ, va đập, cháy nổ, vào nước.</li>
                            <li>Sản phẩm bị tháo lắp, sửa chữa tại nơi không được ủy quyền.</li>
                            <li>Tem bảo hành bị rách, mất hoặc bị chỉnh sửa.</li>
                            <li>Lỗi do sử dụng sai hướng dẫn của nhà sản xuất.</li>
                        </ul>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h4 class="text-success">4. Quy trình bảo hành</h4>
                        <ol>
                            <li>Khách hàng liên hệ HLA để được kiểm tra thông tin sản phẩm.</li>
                            <li>Nhân viên tiếp nhận và kiểm tra tình trạng thiết bị.</li>
                            <li>Sản phẩm đủ điều kiện sẽ được gửi bảo hành.</li>
                            <li>HLA thông báo kết quả và thời gian nhận lại sản phẩm.</li>
                        </ol>
                    </div>
                </div>

                <div class="alert alert-success">
                    <strong>Liên hệ bảo hành:</strong> 010-020-0340 hoặc email info@company.com
                </div>

            </div>
        </div>
    </div>
@endsection
