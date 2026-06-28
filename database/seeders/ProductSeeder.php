<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->products() as $product) {
            Product::updateOrCreate(
                ['sku' => $product['sku']],
                [
                    ...$product,
                    'is_active' => true,
                ],
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function products(): array
    {
        return [
            [
                'sku' => 'HLA-RT-AX1800',
                'category' => Product::CATEGORY_ROUTER,
                'name' => 'Router WiFi 6 AX1800',
                'image' => 'assets/img/banner_img_01.jpg',
                'price' => 1290000,
                'stock' => 24,
                'description' => 'Router WiFi 6 cho gia đình, hỗ trợ nhiều thiết bị truy cập cùng lúc và vùng phủ sóng ổn định.',
            ],
            [
                'sku' => 'HLA-RT-AX3000',
                'category' => Product::CATEGORY_ROUTER,
                'name' => 'Router WiFi 6 AX3000',
                'image' => 'assets/img/banner_img_02.jpg',
                'price' => 1890000,
                'stock' => 18,
                'description' => 'Router băng tần kép tốc độ cao, phù hợp căn hộ lớn, văn phòng nhỏ và nhu cầu streaming.',
            ],
            [
                'sku' => 'HLA-MESH-M4',
                'category' => Product::CATEGORY_MESH_WIFI,
                'name' => 'Bộ Mesh WiFi AC1200',
                'image' => 'assets/img/banner_img_03.jpg',
                'price' => 1590000,
                'stock' => 16,
                'description' => 'Bộ mesh phủ sóng nhiều tầng, roaming mượt và dễ mở rộng thêm node khi cần.',
            ],
            [
                'sku' => 'HLA-MESH-X20',
                'category' => Product::CATEGORY_MESH_WIFI,
                'name' => 'Bộ Mesh WiFi 6 X20',
                'image' => 'assets/img/banner_img_01.jpg',
                'price' => 2790000,
                'stock' => 12,
                'description' => 'Mesh WiFi 6 cho nhà phố và căn hộ rộng, tối ưu tốc độ khi có nhiều thiết bị thông minh.',
            ],
            [
                'sku' => 'HLA-AP-AC1350',
                'category' => Product::CATEGORY_ACCESS_POINT,
                'name' => 'Access Point AC1350',
                'image' => 'assets/img/banner_img_02.jpg',
                'price' => 1450000,
                'stock' => 20,
                'description' => 'Access point gắn trần cho quán cafe, cửa hàng và văn phòng nhỏ cần mạng ổn định.',
            ],
            [
                'sku' => 'HLA-AP-AX1800',
                'category' => Product::CATEGORY_ACCESS_POINT,
                'name' => 'Access Point WiFi 6 AX1800',
                'image' => 'assets/img/banner_img_03.jpg',
                'price' => 2490000,
                'stock' => 10,
                'description' => 'Access point WiFi 6 hỗ trợ tải cao, phù hợp không gian đông người dùng.',
            ],
            [
                'sku' => 'HLA-SW-8G',
                'category' => Product::CATEGORY_SWITCH,
                'name' => 'Switch Gigabit 8 Port',
                'image' => 'assets/img/banner_img_01.jpg',
                'price' => 490000,
                'stock' => 30,
                'description' => 'Switch 8 cổng gigabit cho camera, máy tính, access point và thiết bị mạng nội bộ.',
            ],
            [
                'sku' => 'HLA-SW-16G',
                'category' => Product::CATEGORY_SWITCH,
                'name' => 'Switch Gigabit 16 Port',
                'image' => 'assets/img/banner_img_02.jpg',
                'price' => 990000,
                'stock' => 14,
                'description' => 'Switch 16 cổng cho văn phòng nhỏ, dễ triển khai và vận hành ổn định.',
            ],
            [
                'sku' => 'HLA-USB-AC600',
                'category' => Product::CATEGORY_USB_WIFI,
                'name' => 'USB WiFi AC600',
                'image' => 'assets/img/banner_img_03.jpg',
                'price' => 220000,
                'stock' => 40,
                'description' => 'USB WiFi nhỏ gọn cho máy tính bàn hoặc laptop cần nâng cấp kết nối không dây.',
            ],
            [
                'sku' => 'HLA-CAM-C200',
                'category' => Product::CATEGORY_CAMERA,
                'name' => 'Camera WiFi Full HD',
                'image' => 'assets/img/banner_img_01.jpg',
                'price' => 590000,
                'stock' => 22,
                'description' => 'Camera WiFi trong nhà, hỗ trợ quan sát từ xa và phù hợp nhu cầu an ninh cơ bản.',
            ],
            [
                'sku' => 'HLA-CAM-C500',
                'category' => Product::CATEGORY_CAMERA,
                'name' => 'Camera WiFi ngoài trời',
                'image' => 'assets/img/banner_img_02.jpg',
                'price' => 1190000,
                'stock' => 15,
                'description' => 'Camera ngoài trời có khả năng chống chịu thời tiết, phù hợp cửa hàng và gia đình.',
            ],
            [
                'sku' => 'HLA-RANGE-AC750',
                'category' => Product::CATEGORY_EXTENDER,
                'name' => 'Bộ mở rộng sóng AC750',
                'image' => 'assets/img/banner_img_03.jpg',
                'price' => 390000,
                'stock' => 28,
                'description' => 'Thiết bị mở rộng sóng giúp cải thiện vùng phủ WiFi tại các góc khuất trong nhà.',
            ],
        ];
    }
}
