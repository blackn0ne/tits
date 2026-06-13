<?php

namespace App\Models;

use App\Enums\SalesOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    /** @use HasFactory<\Database\Factories\SalesOrderFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sales_client_id',
        'user_id',
        'status',
        'total',
        'ordered_at',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SalesOrderStatus::class,
            'total' => 'decimal:2',
            'ordered_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<SalesClient, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(SalesClient::class, 'sales_client_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<SalesOrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }
}
