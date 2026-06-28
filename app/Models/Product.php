<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public const CATEGORY_ROUTER = 'router';
    public const CATEGORY_MESH_WIFI = 'mesh-wifi';
    public const CATEGORY_ACCESS_POINT = 'access-point';
    public const CATEGORY_SWITCH = 'switch';
    public const CATEGORY_USB_WIFI = 'usb-wifi';
    public const CATEGORY_CAMERA = 'camera';
    public const CATEGORY_EXTENDER = 'extender';

    public const CATEGORY_DETAILS = [
        self::CATEGORY_ROUTER => [
            'label' => 'Router WiFi',
            'description' => 'Bộ phát WiFi chính cho gia đình, căn hộ và văn phòng nhỏ.',
            'icon' => 'fa-wifi',
            'keywords' => ['router', 'bo phat', 'wifi 6', 'wifi'],
        ],
        self::CATEGORY_MESH_WIFI => [
            'label' => 'Mesh WiFi',
            'description' => 'Bộ phủ sóng nhiều tầng, roaming mượt và dễ mở rộng thêm node.',
            'icon' => 'fa-network-wired',
            'keywords' => ['mesh', 'deco', 'roaming', 'phu song'],
        ],
        self::CATEGORY_ACCESS_POINT => [
            'label' => 'Access Point',
            'description' => 'Thiết bị gắn trần/gắn tường cho cửa hàng, quán cafe và văn phòng.',
            'icon' => 'fa-broadcast-tower',
            'keywords' => ['access point', 'ap', 'gan tran', 'gan tuong'],
        ],
        self::CATEGORY_SWITCH => [
            'label' => 'Switch mạng',
            'description' => 'Mở rộng cổng LAN cho camera, máy tính và access point.',
            'icon' => 'fa-ethernet',
            'keywords' => ['switch', 'lan', 'gigabit', 'poe'],
        ],
        self::CATEGORY_USB_WIFI => [
            'label' => 'USB WiFi',
            'description' => 'Nâng cấp kết nối không dây cho PC hoặc laptop.',
            'icon' => 'fa-usb',
            'keywords' => ['usb wifi', 'adapter', 'pc', 'laptop'],
        ],
        self::CATEGORY_CAMERA => [
            'label' => 'Camera an ninh',
            'description' => 'Camera WiFi trong nhà, ngoài trời và nhu cầu giám sát cơ bản.',
            'icon' => 'fa-video',
            'keywords' => ['camera', 'cam', 'an ninh', 'giam sat'],
        ],
        self::CATEGORY_EXTENDER => [
            'label' => 'Bộ mở rộng sóng',
            'description' => 'Xử lý điểm chết sóng WiFi trong nhà bằng repeater/range extender.',
            'icon' => 'fa-signal',
            'keywords' => ['extender', 'repeater', 'range', 'mo rong song'],
        ],
    ];

    public const CATEGORIES = [
        self::CATEGORY_ROUTER => self::CATEGORY_DETAILS[self::CATEGORY_ROUTER]['label'],
        self::CATEGORY_MESH_WIFI => self::CATEGORY_DETAILS[self::CATEGORY_MESH_WIFI]['label'],
        self::CATEGORY_ACCESS_POINT => self::CATEGORY_DETAILS[self::CATEGORY_ACCESS_POINT]['label'],
        self::CATEGORY_SWITCH => self::CATEGORY_DETAILS[self::CATEGORY_SWITCH]['label'],
        self::CATEGORY_USB_WIFI => self::CATEGORY_DETAILS[self::CATEGORY_USB_WIFI]['label'],
        self::CATEGORY_CAMERA => self::CATEGORY_DETAILS[self::CATEGORY_CAMERA]['label'],
        self::CATEGORY_EXTENDER => self::CATEGORY_DETAILS[self::CATEGORY_EXTENDER]['label'],
    ];

    protected $fillable = [
        'sku',
        'name',
        'category',
        'image',
        'price',
        'stock',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInCategory($query, ?string $category)
    {
        return self::isValidCategory($category)
            ? $query->where('category', $category)
            : $query;
    }

    public function scopeSearch($query, ?string $keyword)
    {
        $keyword = trim((string) $keyword);

        if ($keyword === '') {
            return $query;
        }

        $matchedCategories = self::matchingCategoryKeys($keyword);

        return $query->where(function ($builder) use ($keyword, $matchedCategories): void {
            $builder
                ->where('name', 'like', '%' . $keyword . '%')
                ->orWhere('sku', 'like', '%' . $keyword . '%')
                ->orWhere('category', 'like', '%' . $keyword . '%')
                ->orWhere('description', 'like', '%' . $keyword . '%');

            if ($matchedCategories !== []) {
                $builder->orWhereIn('category', $matchedCategories);
            }
        });
    }

    public function categoryName(): string
    {
        return self::CATEGORIES[$this->category] ?? 'Chưa phân loại';
    }

    public function categoryDescription(): string
    {
        return self::CATEGORY_DETAILS[$this->category]['description'] ?? '';
    }

    public static function categoryOptions(): array
    {
        return self::CATEGORY_DETAILS;
    }

    public static function isValidCategory(?string $category): bool
    {
        return is_string($category) && array_key_exists($category, self::CATEGORIES);
    }

    public static function matchingCategoryKeys(string $keyword): array
    {
        $needle = self::normalizeSearchText($keyword);

        if ($needle === '') {
            return [];
        }

        return collect(self::CATEGORY_DETAILS)
            ->filter(function (array $detail, string $key) use ($needle): bool {
                $haystack = self::normalizeSearchText($key . ' ' . $detail['label'] . ' ' . implode(' ', $detail['keywords']));

                return str_contains($haystack, $needle);
            })
            ->keys()
            ->all();
    }

    private static function normalizeSearchText(string $value): string
    {
        return Str::of($value)->ascii()->lower()->squish()->toString();
    }
}
