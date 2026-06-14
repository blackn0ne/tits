<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Project::class);

        $locale = app()->getLocale();
        $language = Language::query()->where('code', $locale)->first();

        $projects = Project::query()
            ->with(['translations.language', 'category.translations'])
            ->latest('published_at')
            ->latest('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(function (Project $project) use ($language) {
                $translation = $project->translations->firstWhere('language_id', $language?->id)
                    ?? $project->translations->first();

                $categoryTranslation = $project->category?->translations
                    ->firstWhere('language_id', $language?->id)
                    ?? $project->category?->translations->first();

                return [
                    'id' => $project->id,
                    'title' => $translation?->title ?? '—',
                    'slug' => $translation?->slug,
                    'status' => $project->status->value,
                    'status_label' => $project->status->label(),
                    'published_at' => $project->published_at?->format('d.m.Y'),
                    'site_visibility' => $project->siteVisibilityStatus(),
                    'visible_on_site' => $project->isVisibleOnSite(),
                    'category_name' => $categoryTranslation?->name,
                    'banner_url' => $project->banner_path ? Storage::disk('public')->url($project->banner_path) : null,
                ];
            });

        return Inertia::render('admin/Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Project::class);

        return Inertia::render('admin/Projects/Form', [
            'project' => null,
            'translationsByLanguage' => $this->emptyTranslations(),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $this->authorize('create', Project::class);

        $validated = $request->validated();

        $project = Project::query()->create([
            'project_category_id' => $validated['project_category_id'] ?? null,
            'user_id' => $request->user()->id,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        if ($request->hasFile('banner')) {
            $project->banner_path = $request->file('banner')->store('projects/banners', 'public');
            $project->save();
        }

        foreach ($validated['translations'] as $translationData) {
            if (blank($translationData['title'] ?? null)) {
                continue;
            }

            $project->translations()->create([
                'language_id' => $translationData['language_id'],
                'title' => $translationData['title'],
                'meta_title' => $translationData['meta_title'] ?? null,
                'meta_description' => $translationData['meta_description'] ?? null,
                'content' => $translationData['content'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.projects.index')
            ->with('status', 'project-saved');
    }

    public function edit(Project $project): Response
    {
        $this->authorize('update', $project);

        $project->load('translations.language');

        return Inertia::render('admin/Projects/Form', [
            'project' => [
                'id' => $project->id,
                'project_category_id' => $project->project_category_id,
                'status' => $project->status->value,
                'published_at' => $project->published_at?->format('Y-m-d'),
                'banner_url' => $project->banner_path ? Storage::disk('public')->url($project->banner_path) : null,
            ],
            'translationsByLanguage' => $this->translationsMap($project),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $this->authorize('update', $project);

        $validated = $request->validated();

        if ($request->boolean('remove_banner') && $project->banner_path) {
            Storage::disk('public')->delete($project->banner_path);
            $project->banner_path = null;
        }

        if ($request->hasFile('banner')) {
            if ($project->banner_path) {
                Storage::disk('public')->delete($project->banner_path);
            }

            $project->banner_path = $request->file('banner')->store('projects/banners', 'public');
        }

        $project->update([
            'project_category_id' => $validated['project_category_id'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        foreach ($validated['translations'] as $translationData) {
            if (blank($translationData['title'] ?? null)) {
                continue;
            }

            $project->translations()->updateOrCreate(
                ['language_id' => $translationData['language_id']],
                [
                    'title' => $translationData['title'],
                    'meta_title' => $translationData['meta_title'] ?? null,
                    'meta_description' => $translationData['meta_description'] ?? null,
                    'content' => $translationData['content'] ?? null,
                ],
            );
        }

        return redirect()
            ->route('admin.projects.index')
            ->with('status', 'project-saved');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        if ($project->banner_path) {
            Storage::disk('public')->delete($project->banner_path);
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('status', 'project-deleted');
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
                    'title' => '',
                    'meta_title' => '',
                    'meta_description' => '',
                    'slug' => '',
                    'content' => '',
                ],
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function translationsMap(Project $project): array
    {
        $languages = Language::query()->orderBy('name')->get(['id', 'name', 'code']);

        return $languages->mapWithKeys(function (Language $language) use ($project) {
            $translation = $project->translations->firstWhere('language_id', $language->id);

            return [
                $language->id => [
                    'language_id' => $language->id,
                    'code' => $language->code,
                    'name' => $language->name,
                    'title' => $translation?->title ?? '',
                    'meta_title' => $translation?->meta_title ?? '',
                    'meta_description' => $translation?->meta_description ?? '',
                    'slug' => $translation?->slug ?? '',
                    'content' => $translation?->content ?? '',
                ],
            ];
        })->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function categoryOptions(): array
    {
        $locale = app()->getLocale();
        $language = Language::query()->where('code', $locale)->first();

        return ProjectCategory::query()
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(function (ProjectCategory $category) use ($language) {
                $translation = $category->translations->firstWhere('language_id', $language?->id)
                    ?? $category->translations->first();

                return [
                    'id' => $category->id,
                    'name' => $translation?->name ?? '—',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, string>>
     */
    private function statusOptions(): array
    {
        return collect(ProjectStatus::cases())
            ->map(fn (ProjectStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ])
            ->values()
            ->all();
    }
}
