<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Seed the application's languages.
     */
    public function run(): void
    {
        Language::query()->updateOrCreate(
            ['code' => 'ru'],
            ['name' => 'Русский'],
        );

        Language::query()->updateOrCreate(
            ['code' => 'kz'],
            ['name' => 'Қазақша'],
        );
    }
}
