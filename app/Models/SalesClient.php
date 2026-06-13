<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesClient extends Model
{
    public const STORE_NAME = 'Магазин';

    /** @use HasFactory<\Database\Factories\SalesClientFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'phone',
    ];

    public static function storeClient(): self
    {
        return static::query()->firstOrCreate(
            ['full_name' => self::STORE_NAME],
            ['phone' => '—'],
        );
    }

    public function isStore(): bool
    {
        return $this->full_name === self::STORE_NAME;
    }

    /**
     * @return HasMany<SalesOrder, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(SalesOrder::class);
    }
}
