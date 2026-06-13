<?php

namespace App\Enums;

use App\Services\TranslationLoader;

enum SalesCatalogStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return app(TranslationLoader::class)->get(
            app()->getLocale(),
            'admin.sales.statuses.'.$this->value,
        );
    }

    /**
     * @return list<self>
     */
    public static function values(): array
    {
        return self::cases();
    }
}
