<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    public const METHOD_CASH = 'cash';
    public const METHOD_QRIS = 'qris';
    public const METHOD_CARD = 'card';

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    protected $fillable = ['order_id', 'method', 'status', 'amount'];

    protected $casts = [
        'amount' => 'float',
    ];

    public static function methods(): array
    {
        return [self::METHOD_CASH, self::METHOD_QRIS, self::METHOD_CARD];
    }

    public static function statuses(): array
    {
        return [self::STATUS_UNPAID, self::STATUS_PAID, self::STATUS_FAILED];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
