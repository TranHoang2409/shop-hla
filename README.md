# HLA WiFi Shop

HLA WiFi Shop là đồ án website thương mại điện tử bán thiết bị WiFi và thiết bị mạng. Dự án được xây dựng bằng Laravel, có đầy đủ luồng khách hàng mua sản phẩm và khu vực quản trị cho admin.

## Chức năng chính

- Khách hàng xem trang chủ, danh sách sản phẩm, chi tiết sản phẩm.
- Tìm kiếm và sắp xếp sản phẩm theo từ khóa, giá, tên.
- Giỏ hàng lưu bằng session.
- Đặt hàng với hai phương thức thanh toán: COD và chuyển khoản ngân hàng.
- Khách hàng đăng ký, đăng nhập, cập nhật tài khoản và theo dõi đơn hàng.
- Admin quản lý sản phẩm, đơn hàng, khách hàng.
- Admin xác nhận thanh toán chuyển khoản và cập nhật trạng thái đơn hàng.
- Dashboard và báo cáo doanh thu theo tuần, tháng, năm.
- Thống kê sản phẩm bán chạy, bán chậm, khách hàng và tồn kho thấp.

## Công nghệ sử dụng

- PHP 8.2+
- Laravel 12
- MySQL/MariaDB hoặc SQLite
- Blade Template
- Bootstrap
- Font Awesome
- Slick Slider
- Chart.js
- PHPUnit

## Cài đặt nhanh

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Nếu dùng MySQL, cập nhật các biến `DB_*` trong file `.env` trước khi chạy migrate.

## Tài khoản mẫu

Sau khi chạy seed:

- Admin: `admin@hla.test` / `password`
- Khách hàng: `customer@hla.test` / `password`

## Kiểm thử

```bash
php artisan test
```

Test hiện bao phủ các luồng chính: phân quyền admin, checkout, quản lý sản phẩm, cập nhật đơn hàng, xác nhận chuyển khoản và báo cáo doanh thu.

## Cấu trúc nghiệp vụ

- `app/Models/Product.php`: sản phẩm, tồn kho, trạng thái đang bán.
- `app/Models/Order.php`: đơn hàng, trạng thái xử lý, trạng thái thanh toán.
- `app/Models/OrderItem.php`: chi tiết từng dòng sản phẩm trong đơn.
- `app/Http/Controllers/StoreController.php`: trang chủ, shop, chi tiết sản phẩm.
- `app/Http/Controllers/CartController.php`: giỏ hàng.
- `app/Http/Controllers/CheckoutController.php`: tạo đơn và trừ tồn kho.
- `app/Http/Controllers/Admin/*`: khu vực quản trị.

## Ghi chú

Thông tin chuyển khoản được cấu hình trong `config/payment.php` và có thể thay đổi bằng biến môi trường trong `.env`.
