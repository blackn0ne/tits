<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Seed the application's site settings.
     */
    public function run(): void
    {
        $setting = SiteSetting::query()->firstOrCreate([], [
            'phone' => '+7 (700) 000-00-00',
            'address' => 'Казахстан',
            'social_links' => [
                'facebook' => null,
                'instagram' => 'https://instagram.com',
                'telegram' => 'https://t.me',
                'whatsapp' => null,
                'youtube' => null,
                'tiktok' => null,
            ],
        ]);

        $translations = [
            'ru' => [
                'site_name' => 'TITS',
                'description' => 'TITS — современная платформа',
                'keywords' => 'tits, платформа, казахстан',
            ],
            'kz' => [
                'site_name' => 'TITS',
                'description' => 'TITS — заманауи платформа',
                'keywords' => 'tits, платформа, қазақстан',
            ],
        ];

        foreach ($translations as $code => $data) {
            $language = Language::query()->where('code', $code)->first();

            if ($language === null) {
                continue;
            }

            $setting->translations()->updateOrCreate(
                ['language_id' => $language->id],
                $data,
            );
        }
    }
}
