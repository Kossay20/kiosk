<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_AWAITING_CASHIER = 'awaiting_cashier';
    public const STATUS_AWAITING_KITCHEN = 'awaiting_kitchen';
    public const STATUS_IN_KITCHEN = 'in_kitchen';
    public const STATUS_READY_FOR_PICKUP = 'ready_for_pickup';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';

    public const CHANNEL_KIOSK = 'kiosk';
    public const CHANNEL_CASHIER = 'cashier';
    public const CHANNEL_MOBILE = 'mobile';

    protected $fillable = [
        'queue_number',
        'total_price',
        'status',
        'ordering_channel',
        'payment_channel',
        'payment_status',
        'cashier_id',
    ];

    protected $casts = [
        'total_price' => 'float',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING_PAYMENT,
            self::STATUS_AWAITING_CASHIER,
            self::STATUS_AWAITING_KITCHEN,
            self::STATUS_IN_KITCHEN,
            self::STATUS_READY_FOR_PICKUP,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function paymentStatuses(): array
    {
        return [
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_PAID,
            self::PAYMENT_STATUS_FAILED,
        ];
    }

    public static function paymentChannels(): array
    {
        return [
            self::CHANNEL_KIOSK,
            self::CHANNEL_CASHIER,
            self::CHANNEL_MOBILE,
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(Order_item::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function markAsPaid(?string $channel = null): void
    {
        $this->payment_status = self::PAYMENT_STATUS_PAID;
        if ($channel !== null) {
            $this->payment_channel = $channel;
        }
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function requiresCashier(): bool
    {
        return $this->status === self::STATUS_AWAITING_CASHIER;
    }
}
