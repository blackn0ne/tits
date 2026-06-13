<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $ru = Language::query()->where('code', 'ru')->firstOrFail();
        $kz = Language::query()->where('code', 'kz')->firstOrFail();
        $author = User::query()->where('username', 'admin')->firstOrFail();

        $categories = [
            [
                'icon' => 'Smartphone',
                'sort_order' => 1,
                'translations' => [
                    'ru' => ['name' => 'Мобильная разработка', 'slug' => 'mobilnaya-razrabotka'],
                    'kz' => ['name' => 'Мобильді әзірлеу', 'slug' => 'mobildi-aziru'],
                ],
            ],
            [
                'icon' => 'Globe',
                'sort_order' => 2,
                'translations' => [
                    'ru' => ['name' => 'Веб-разработка', 'slug' => 'veb-razrabotka'],
                    'kz' => ['name' => 'Веб-әзірлеу', 'slug' => 'veb-aziru'],
                ],
            ],
            [
                'icon' => 'ShoppingCart',
                'sort_order' => 3,
                'translations' => [
                    'ru' => ['name' => 'E-commerce', 'slug' => 'e-commerce-proekty'],
                    'kz' => ['name' => 'E-commerce', 'slug' => 'e-commerce-zhobalar'],
                ],
            ],
        ];

        $categoryIds = [];

        foreach ($categories as $categoryData) {
            $category = ProjectCategory::query()->updateOrCreate(
                ['icon' => $categoryData['icon'], 'sort_order' => $categoryData['sort_order']],
                ['is_active' => true],
            );

            foreach (['ru' => $ru, 'kz' => $kz] as $code => $language) {
                $category->translations()->updateOrCreate(
                    ['language_id' => $language->id],
                    $categoryData['translations'][$code],
                );
            }

            $categoryIds[$categoryData['translations']['ru']['slug']] = $category->id;
        }

        $projects = [
            [
                'category' => 'mobilnaya-razrabotka',
                'published_at' => '2026-01-15',
                'ru' => [
                    'title' => 'Приложение для сервисного центра',
                    'slug' => 'prilozhenie-dlya-servisnogo-centra',
                    'content' => '<p>Запись на ремонт, статус заказа и уведомления клиентам — всё в одном мобильном приложении.</p>',
                ],
                'kz' => [
                    'title' => 'Сервис орталығына арналған қосымша',
                    'slug' => 'servis-ortalygy-kosymshasy',
                    'content' => '<p>Жөндеуге жазылу, тапсырыс күйі және клиенттерге хабарландыру — бәрі бір мобильді қосымшада.</p>',
                ],
            ],
            [
                'category' => 'veb-razrabotka',
                'published_at' => '2026-01-10',
                'ru' => [
                    'title' => 'Корпоративный сайт под ключ',
                    'slug' => 'korporativnyi-sait-pod-klyuch',
                    'content' => '<p>Современный сайт с адаптивной вёрсткой, формами заявок и интеграцией с мессенджерами.</p>',
                ],
                'kz' => [
                    'title' => 'Айналдыруға дайын корпоративтік сайт',
                    'slug' => 'korporativtik-sait',
                    'content' => '<p>Бейімделген макет, өтінім формалары және мессенджерлермен интеграциясы бар заманауи сайт.</p>',
                ],
            ],
            [
                'category' => 'e-commerce-proekty',
                'published_at' => '2026-01-05',
                'ru' => [
                    'title' => 'Интернет-магазин техники',
                    'slug' => 'internet-magazin-tekhniki',
                    'content' => '<p>Каталог, корзина, онлайн-оплата и личный кабинет — полноценная торговая площадка для локального бизнеса.</p>',
                ],
                'kz' => [
                    'title' => 'Техника интернет-дүкені',
                    'slug' => 'tekhnika-internet-dukeni',
                    'content' => '<p>Каталог, себет, онлайн төлем және жеке кабинет — жергілікті бизнес үшін толық сауда алаңы.</p>',
                ],
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::query()->updateOrCreate(
                [
                    'project_category_id' => $categoryIds[$projectData['category']],
                    'published_at' => $projectData['published_at'],
                ],
                [
                    'user_id' => $author->id,
                    'status' => ProjectStatus::Published,
                    'banner_path' => null,
                ],
            );

            foreach (['ru' => $ru, 'kz' => $kz] as $code => $language) {
                $project->translations()->updateOrCreate(
                    ['language_id' => $language->id],
                    $projectData[$code],
                );
            }
        }
    }
}
