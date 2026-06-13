<?php

namespace App\Enums;

use App\Services\TranslationLoader;

enum BlogPostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    public function label(): string
    {
        return app(TranslationLoader::class)->get(
            app()->getLocale(),
            'admin.blog.statuses.'.$this->value,
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
