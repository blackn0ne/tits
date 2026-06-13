<?php

namespace App\Models;

use App\Enums\SalesCatalogStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesService extends Model
{
    /** @use HasFactory<\Database\Factories\SalesServiceFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'price',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
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
