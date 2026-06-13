<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogCategoryRequest;
use App\Http\Requests\Admin\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class BlogCategoryController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', BlogCategory::class);

        $locale = app()->getLocale();
        $language = Language::query()->where('code', $locale)->first();

        $categories = BlogCategory::query()
            ->with(['translations.language'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(function (BlogCategory $category) use ($language) {
                $translation = $category->translations->firstWhere('language_id', $language?->id)
                    ?? $category->translations->first();

                return [
                    'id' => $category->id,
                    'icon' => $category->icon,
                    'is_active' => $category->is_active,
                    'sort_order' => $category->sort_order,
                    'name' => $translation?->name ?? '—',
                    'slug' => $translation?->slug,
                ];
            });

        return Inertia::render('admin/Blog/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BlogCategory::class);

        return Inertia::render('admin/Blog/Categories/Form', [
            'category' => null,
            'translationsByLanguage' => $this->emptyTranslations(),
        ]);
    }

    public function store(StoreBlogCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', BlogCategory::class);

        $validated = $request->validated();

        $category = BlogCategory::query()->create([
            'icon' => $validated['icon'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        foreach ($validated['translations'] as $translationData) {
            $category->translations()->create([
                'language_id' => $translationData['language_id'],
                'name' => $translationData['name'],
            ]);
        }

        return redirect()
            ->route('admin.blog-categories.index')
            ->with('status', 'blog-category-saved');
    }

    public function edit(BlogCategory $blogCategory): Response
    {
        $this->authorize('update', $blogCategory);

        $blogCategory->load('translations.language');

        return Inertia::render('admin/Blog/Categories/Form', [
            'category' => [
                'id' => $blogCategory->id,
                'icon' => $blogCategory->icon,
                'is_active' => $blogCategory->is_active,
                'sort_order' => $blogCategory->sort_order,
            ],
            'translationsByLanguage' => $this->translationsMap($blogCategory),
        ]);
    }

    public function update(UpdateBlogCategoryRequest $request, BlogCategory $blogCategory): RedirectResponse
    {
        $this->authorize('update', $blogCategory);

        $validated = $request->validated();

        $blogCategory->update([
            'icon' => $validated['icon'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        foreach ($validated['translations'] as $translationData) {
            $blogCategory->translations()->updateOrCreate(
                ['language_id' => $translationData['language_id']],
                ['name' => $translationData['name']],
            );
        }

        return redirect()
            ->route('admin.blog-categories.index')
            ->with('status', 'blog-category-saved');
    }

    public function destroy(BlogCategory $blogCategory): RedirectResponse
    {
        $this->authorize('delete', $blogCategory);

        $blogCategory->delete();

        return redirect()
            ->route('admin.blog-categories.index')
            ->with('status', 'blog-category-deleted');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function emptyTranslations(): array
    {
        return Language::query()
            ->orderBy('name')
            ->get(['id', 'name', 'code'])
            ->mapWithKeys(fn (Language $language) => [
                $language->id => [
                    'language_id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                    'value' => '',
                    'slug' => '',
                ],
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function translationsMap(BlogCategory $category): array
    {
        $languages = Language::query()->orderBy('name')->get(['id', 'name', 'code']);

        return $languages->mapWithKeys(function (Language $language) use ($category) {
            $translation = $category->translations->firstWhere('language_id', $language->id);

            return [
                $language->id => [
                    'language_id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                    'value' => $translation?->name ?? '',
                    'slug' => $translation?->slug ?? '',
                ],
            ];
        })->all();
    }
}
