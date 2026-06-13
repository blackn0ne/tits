<?php

namespace App\Models;

use App\Enums\SalesCatalogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesProduct extends Model
{
    /** @use HasFactory<\Database\Factories\SalesProductFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'short_description',
        'price',
        'quantity',
        'unit',
        'status',
        'kaspi_link',
        'image_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'status' => SalesCatalogStatus::class,
        ];
    }

    /**
     * @return HasMany<SalesOrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }
}
