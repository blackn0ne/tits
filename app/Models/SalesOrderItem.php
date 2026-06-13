<?php

namespace App\Models;

use App\Enums\SalesOrderItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\SalesOrderItemFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'sales_order_id',
        'item_type',
        'sales_product_id',
        'sales_service_id',
        'name',
        'quantity',
        'unit',
        'unit_price',
        'line_total',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'item_type' => SalesOrderItemType::class,
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<SalesOrder, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'sales_order_id');
    }

    /**
     * @return BelongsTo<SalesProduct, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(SalesProduct::class, 'sales_product_id');
    }

    /**
     * @return BelongsTo<SalesService, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(SalesService::class, 'sales_service_id');
    }
}
