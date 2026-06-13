<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /** @use HasFactory<\Database\Factories\LanguageFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    public static function defaultCode(): string
    {
        return (string) config('app.locale', 'ru');
    }

    public static function isValidCode(string $code): bool
    {
        return static::query()->where('code', $code)->exists();
    }
}
