<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PREPARING = 'preparing';

    public const STATUS_SHIPPING = 'shipping';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_COD = 'cod';

    public const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';

    public const PAYMENT_STATUS_UNPAID = 'unpaid';

    public const PAYMENT_STATUS_AWAITING_TRANSFER = 'awaiting_transfer';

    public const PAYMENT_STATUS_PAID = 'paid';

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'payment_method',
        'payment_status',
        'paid_at',
        'payment_confirmed_at',
        'subtotal',
        'shipping_fee',
        'total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'note',
        'placed_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'total' => 'decimal:2',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
            'payment_confirmed_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xác nhận',
            self::STATUS_CONFIRMED => 'Đang xử lý',
            self::STATUS_PREPARING => 'Đang chuẩn bị',
            self::STATUS_SHIPPING => 'Đang giao',
            self::STATUS_DELIVERED => 'Đã giao',
            self::STATUS_CANCELLED => 'Đã hủy',
        ];
    }

    public static function paymentMethods(): array
    {
        return [
            self::PAYMENT_METHOD_COD => 'Thanh toán khi nhận hàng',
            self::PAYMENT_METHOD_BANK_TRANSFER => 'Chuyển khoản ngân hàng',
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_UNPAID => 'Chưa thanh toán',
            self::PAYMENT_STATUS_AWAITING_TRANSFER => 'Chờ chuyển khoản',
            self::PAYMENT_STATUS_PAID => 'Đã thanh toán',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'warning',
            self::STATUS_CONFIRMED => 'info',
            self::STATUS_PREPARING => 'primary',
            self::STATUS_SHIPPING => 'secondary',
            self::STATUS_DELIVERED => 'success',
            self::STATUS_CANCELLED => 'danger',
            default => 'dark',
        };
    }

    public function paymentMethodLabel(): string
    {
        return self::paymentMethods()[$this->payment_method] ?? $this->payment_method;
    }

    public function paymentStatusLabel(): string
    {
        return self::paymentStatuses()[$this->payment_status] ?? $this->payment_status;
    }

    public function paymentStatusBadgeClass(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_PAID => 'success',
            self::PAYMENT_STATUS_AWAITING_TRANSFER => 'warning',
            self::PAYMENT_STATUS_UNPAID => 'secondary',
            default => 'dark',
        };
    }

    public function isBankTransfer(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_BANK_TRANSFER;
    }

    public function isAwaitingBankTransfer(): bool
    {
        return $this->isBankTransfer()
            && $this->payment_status !== self::PAYMENT_STATUS_PAID
            && $this->status !== self::STATUS_CANCELLED;
    }
}
