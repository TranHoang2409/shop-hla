@extends('layouts.frontend')

@section('title', 'Chính sách bảo hành')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <h1 class="h2 mb-2">Chính sách bảo hành</h1>
                    <p class="text-muted mb-0">Áp dụng cho các thiết bị WiFi được mua tại HLA Wifi Shop.</p>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 text-success">1. Thời gian bảo hành</h2>
                        <p class="mb-0">
                            Phần lớn router, mesh WiFi và access point được bảo hành từ 12 đến 24 tháng tùy nhà sản xuất
                            và dòng sản phẩm.
                        </p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 text-success">2. Điều kiện được tiếp nhận</h2>
                        <ul class="mb-0">
                            <li>Sản phẩm còn trong hạn bảo hành.</li>
                            <li>Serial hoặc tem nhận diện còn nguyên vẹn.</li>
                            <li>Lỗi phát sinh do nhà sản xuất hoặc phần cứng thiết bị.</li>
                            <li>Có thể đối chiếu được thông tin mua hàng tại HLA Wifi Shop.</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h2 class="h5 text-success">3. Trường hợp từ chối bảo hành</h2>
                        <ul class="mb-0">
                            <li>Thiết bị bị rơi vỡ, vào nước, cháy nổ hoặc dùng sai nguồn điện.</li>
                            <li>Tem serial bị rách, mất hoặc có dấu hiệu can thiệp.</li>
                            <li>Thiết bị đã bị tháo mở hoặc sửa chữa ngoài hệ thống được ủy quyền.</li>
                            <li>Lỗi đến từ cấu hình sai hoặc môi trường triển khai không phù hợp.</li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="h5 text-success">4. Quy trình xử lý</h2>
                        <ol class="mb-3">
                            <li>Khách hàng liên hệ hotline hoặc email, cung cấp mã đơn và serial thiết bị.</li>
                            <li>HLA kiểm tra điều kiện tiếp nhận, sau đó hướng dẫn gửi thiết bị hoặc mang trực tiếp.</li>
                            <li>Thiết bị đủ điều kiện sẽ được chuyển bảo hành và cập nhật tiến độ cho khách.</li>
                        </ol>
                        <div class="alert alert-success mb-0">
                            Liên hệ bảo hành: <strong>0900 000 001</strong> hoặc <strong>support@hla.test</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
