<?php

namespace App\Enums;

use App\Services\TranslationLoader;

enum UserRole: string
{
    case Admin = 'admin';
    case User = 'user';

    public function label(): string
    {
        return app(TranslationLoader::class)->get(
            app()->getLocale(),
            'roles.'.$this->value,
        );
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isUser(): bool
    {
        return $this === self::User;
    }

    /**
     * @return list<self>
     */
    public static function values(): array
    {
        return self::cases();
    }
}
