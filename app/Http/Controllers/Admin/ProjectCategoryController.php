<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectCategoryRequest;
use App\Http\Requests\Admin\UpdateProjectCategoryRequest;
use App\Models\Language;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectCategoryController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', ProjectCategory::class);

        $locale = app()->getLocale();
        $language = Language::query()->where('code', $locale)->first();

        $categories = ProjectCategory::query()
            ->with(['translations.language'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(function (ProjectCategory $category) use ($language) {
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

        return Inertia::render('admin/Projects/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', ProjectCategory::class);

        return Inertia::render('admin/Projects/Categories/Form', [
            'category' => null,
            'translationsByLanguage' => $this->emptyTranslations(),
        ]);
    }

    public function store(StoreProjectCategoryRequest $request): RedirectResponse
    {
        $this->authorize('create', ProjectCategory::class);

        $validated = $request->validated();

        $category = ProjectCategory::query()->create([
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
            ->route('admin.project-categories.index')
            ->with('status', 'project-category-saved');
    }

    public function edit(ProjectCategory $projectCategory): Response
    {
        $this->authorize('update', $projectCategory);

        $projectCategory->load('translations.language');

        return Inertia::render('admin/Projects/Categories/Form', [
            'category' => [
                'id' => $projectCategory->id,
                'icon' => $projectCategory->icon,
                'is_active' => $projectCategory->is_active,
                'sort_order' => $projectCategory->sort_order,
            ],
            'translationsByLanguage' => $this->translationsMap($projectCategory),
        ]);
    }

    public function update(UpdateProjectCategoryRequest $request, ProjectCategory $projectCategory): RedirectResponse
    {
        $this->authorize('update', $projectCategory);

        $validated = $request->validated();

        $projectCategory->update([
            'icon' => $validated['icon'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        foreach ($validated['translations'] as $translationData) {
            $projectCategory->translations()->updateOrCreate(
                ['language_id' => $translationData['language_id']],
                ['name' => $translationData['name']],
            );
        }

        return redirect()
            ->route('admin.project-categories.index')
            ->with('status', 'project-category-saved');
    }

    public function destroy(ProjectCategory $projectCategory): RedirectResponse
    {
        $this->authorize('delete', $projectCategory);

        $projectCategory->delete();

        return redirect()
            ->route('admin.project-categories.index')
            ->with('status', 'project-category-deleted');
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
    private function translationsMap(ProjectCategory $category): array
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
