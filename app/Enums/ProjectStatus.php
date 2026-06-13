<?php

namespace App\Enums;

use App\Services\TranslationLoader;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return app(TranslationLoader::class)->get(
            app()->getLocale(),
            'admin.projects.statuses.'.$this->value,
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
