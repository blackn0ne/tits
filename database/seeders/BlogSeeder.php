<?php

namespace Database\Seeders;

use App\Enums\BlogPostStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $ru = Language::query()->where('code', 'ru')->firstOrFail();
        $kz = Language::query()->where('code', 'kz')->firstOrFail();
        $author = User::query()->where('username', 'admin')->firstOrFail();

        $categories = [
            [
                'icon' => 'Wrench',
                'sort_order' => 1,
                'translations' => [
                    'ru' => ['name' => 'Ремонт', 'slug' => 'remont'],
                    'kz' => ['name' => 'Жөндеу', 'slug' => 'zhondeu'],
                ],
            ],
            [
                'icon' => 'Printer',
                'sort_order' => 2,
                'translations' => [
                    'ru' => ['name' => 'Печать', 'slug' => 'pechat'],
                    'kz' => ['name' => 'Басып шығару', 'slug' => 'basypp-shygaru'],
                ],
            ],
            [
                'icon' => 'Globe',
                'sort_order' => 3,
                'translations' => [
                    'ru' => ['name' => 'Веб', 'slug' => 'veb'],
                    'kz' => ['name' => 'Веб', 'slug' => 'veb-kz'],
                ],
            ],
            [
                'icon' => 'Smartphone',
                'sort_order' => 4,
                'translations' => [
                    'ru' => ['name' => 'Мобайл', 'slug' => 'mobail'],
                    'kz' => ['name' => 'Мобайл', 'slug' => 'mobail-kz'],
                ],
            ],
            [
                'icon' => 'ShoppingCart',
                'sort_order' => 5,
                'translations' => [
                    'ru' => ['name' => 'E-commerce', 'slug' => 'e-commerce'],
                    'kz' => ['name' => 'E-commerce', 'slug' => 'e-commerce-kz'],
                ],
            ],
            [
                'icon' => 'Shield',
                'sort_order' => 6,
                'translations' => [
                    'ru' => ['name' => 'Безопасность', 'slug' => 'bezopasnost'],
                    'kz' => ['name' => 'Қауіпсіздік', 'slug' => 'qauipsizdik'],
                ],
            ],
        ];

        $categoryIds = [];

        foreach ($categories as $categoryData) {
            $category = BlogCategory::query()->updateOrCreate(
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

        $posts = [
            [
                'category' => 'remont',
                'published_at' => '2026-03-10',
                'ru' => [
                    'title' => 'Когда стоит нести ноутбук в сервис, а когда хватит чистки',
                    'slug' => 'kogda-nesti-noutbuk-v-servis',
                    'content' => '<p>Разбираем типичные симптомы — перегрев, шум, тормоза — и что можно сделать самому до визита в центр.</p>',
                ],
                'kz' => [
                    'title' => 'Ноутбукты қашан сервиске апару керек, қашан тазалау жеткілікті',
                    'slug' => 'noutbukty-kashan-serviske-aparu',
                    'content' => '<p>Қызуы, шуы, баяу жұмыс істеу сияқты белгілерді және орталыққа бармай тұрып өзіңіз не істей алатыныңызды қарастырамыз.</p>',
                ],
            ],
            [
                'category' => 'pechat',
                'published_at' => '2026-03-05',
                'ru' => [
                    'title' => 'Как подготовить макет к печати, чтобы не переплачивать',
                    'slug' => 'kak-podgotovit-maket-k-pechati',
                    'content' => '<p>Форматы файлов, поля, цвет и разрешение — короткий чеклист перед отправкой в типографию.</p>',
                ],
                'kz' => [
                    'title' => 'Артық төлемемес үшін басып шығаруға макетті қалай дайындау керек',
                    'slug' => 'maketti-kalay-daindau',
                    'content' => '<p>Файл форматтары, өрістер, түс және ажыратымдылық — типографияға жібермес бұрын қысқа тізім.</p>',
                ],
            ],
            [
                'category' => 'veb',
                'published_at' => '2026-02-28',
                'ru' => [
                    'title' => 'Что должно быть на сайте малого бизнеса в 2026 году',
                    'slug' => 'chto-dolzhno-byt-na-saite-malogo-biznesa',
                    'content' => '<p>Минимальный набор блоков и функций, без которых сайт не приносит заявок и доверия.</p>',
                ],
                'kz' => [
                    'title' => '2026 жылы шағын бизнес сайтында не болуы керек',
                    'slug' => 'shaqin-biznes-saytinda-ne-boluy-kerek',
                    'content' => '<p>Сайт өтінім мен сенім әкелмейтін минималды блоктар мен функциялар жиынтығы.</p>',
                ],
            ],
            [
                'category' => 'mobail',
                'published_at' => '2026-02-20',
                'ru' => [
                    'title' => 'Приложение или адаптивный сайт: что выбрать бизнесу',
                    'slug' => 'prilozhenie-ili-adaptivnyi-sait',
                    'content' => '<p>Сравниваем сценарии, бюджет и сроки — когда мобильное приложение действительно окупается.</p>',
                ],
                'kz' => [
                    'title' => 'Қосымша ма, бейімделген сайт па: бизнес не таңдауы керек',
                    'slug' => 'qosymsha-ne-beyimdelgen-sait',
                    'content' => '<p>Сценарийлерді, бюджет пен мерзімдерді салыстырамыз — мобильді қосымша қашан өзін ақтайды.</p>',
                ],
            ],
            [
                'category' => 'e-commerce',
                'published_at' => '2026-02-12',
                'ru' => [
                    'title' => '5 ошибок при запуске интернет-магазина',
                    'slug' => '5-oshibok-pri-zapuske-internet-magazina',
                    'content' => '<p>От каталога без фильтров до неудобной оплаты — типичные промахи, которые режут продажи.</p>',
                ],
                'kz' => [
                    'title' => 'Интернет-дүкен іске қосқанда 5 қате',
                    'slug' => 'internet-duken-5-kate',
                    'content' => '<p>Сүзгісіз каталогтан ыңғайсыз төлемге дейін — сатуды кемітетін типтік қателіктер.</p>',
                ],
            ],
            [
                'category' => 'bezopasnost',
                'published_at' => '2026-02-01',
                'ru' => [
                    'title' => 'Как защитить данные на рабочем компьютере',
                    'slug' => 'kak-zashchitit-dannye-na-rabochem-kompyutere',
                    'content' => '<p>Резервные копии, пароли и обновления — простые привычки, которые спасают от потери файлов.</p>',
                ],
                'kz' => [
                    'title' => 'Жұмыс компьютеріндегі деректерді қалай қорғауға болады',
                    'slug' => 'derekterdi-kalay-korgau',
                    'content' => '<p>Резервтік көшірмелер, құпия сөздер және жаңартулар — файл жоғалтудан сақтайтын қарапайым әдеттер.</p>',
                ],
            ],
        ];

        foreach ($posts as $postData) {
            $post = BlogPost::query()->updateOrCreate(
                [
                    'blog_category_id' => $categoryIds[$postData['category']],
                    'published_at' => $postData['published_at'],
                ],
                [
                    'user_id' => $author->id,
                    'status' => BlogPostStatus::Published,
                    'banner_path' => null,
                ],
            );

            foreach (['ru' => $ru, 'kz' => $kz] as $code => $language) {
                $post->translations()->updateOrCreate(
                    ['language_id' => $language->id],
                    $postData[$code],
                );
            }
        }
    }
}
