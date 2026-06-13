<?php

namespace App\Enums;

use App\Services\TranslationLoader;

enum SalesOrderStatus: string
{
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return app(TranslationLoader::class)->get(
            app()->getLocale(),
            'admin.sales.order_statuses.'.$this->value,
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
