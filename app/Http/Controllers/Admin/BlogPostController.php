<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BlogPostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogPostRequest;
use App\Http\Requests\Admin\UpdateBlogPostRequest;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BlogPostController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', BlogPost::class);

        $locale = app()->getLocale();
        $language = Language::query()->where('code', $locale)->first();

        $posts = BlogPost::query()
            ->with(['translations.language', 'category.translations'])
            ->latest('published_at')
            ->latest('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(function (BlogPost $post) use ($language) {
                $translation = $post->translations->firstWhere('language_id', $language?->id)
                    ?? $post->translations->first();

                $categoryTranslation = $post->category?->translations
                    ->firstWhere('language_id', $language?->id)
                    ?? $post->category?->translations->first();

                return [
                    'id' => $post->id,
                    'title' => $translation?->title ?? '—',
                    'slug' => $translation?->slug,
                    'status' => $post->status->value,
                    'status_label' => $post->status->label(),
                    'published_at' => $post->published_at?->toDateString(),
                    'category_name' => $categoryTranslation?->name,
                    'banner_url' => $post->banner_path ? Storage::disk('public')->url($post->banner_path) : null,
                ];
            });

        return Inertia::render('admin/Blog/Posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', BlogPost::class);

        return Inertia::render('admin/Blog/Posts/Form', [
            'post' => null,
            'translationsByLanguage' => $this->emptyTranslations(),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(StoreBlogPostRequest $request): RedirectResponse
    {
        $this->authorize('create', BlogPost::class);

        $validated = $request->validated();

        $post = BlogPost::query()->create([
            'blog_category_id' => $validated['blog_category_id'] ?? null,
            'user_id' => $request->user()->id,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        if ($request->hasFile('banner')) {
            $post->banner_path = $request->file('banner')->store('blog/banners', 'public');
            $post->save();
        }

        foreach ($validated['translations'] as $translationData) {
            $post->translations()->create([
                'language_id' => $translationData['language_id'],
                'title' => $translationData['title'],
                'meta_title' => $translationData['meta_title'] ?? null,
                'meta_description' => $translationData['meta_description'] ?? null,
                'content' => $translationData['content'] ?? null,
            ]);
        }

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'blog-post-saved');
    }

    public function edit(BlogPost $blogPost): Response
    {
        $this->authorize('update', $blogPost);

        $blogPost->load('translations.language');

        return Inertia::render('admin/Blog/Posts/Form', [
            'post' => [
                'id' => $blogPost->id,
                'blog_category_id' => $blogPost->blog_category_id,
                'status' => $blogPost->status->value,
                'published_at' => $blogPost->published_at?->format('Y-m-d'),
                'banner_url' => $blogPost->banner_path ? Storage::disk('public')->url($blogPost->banner_path) : null,
            ],
            'translationsByLanguage' => $this->translationsMap($blogPost),
            'categories' => $this->categoryOptions(),
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateBlogPostRequest $request, BlogPost $blogPost): RedirectResponse
    {
        $this->authorize('update', $blogPost);

        $validated = $request->validated();

        if ($request->boolean('remove_banner') && $blogPost->banner_path) {
            Storage::disk('public')->delete($blogPost->banner_path);
            $blogPost->banner_path = null;
        }

        if ($request->hasFile('banner')) {
            if ($blogPost->banner_path) {
                Storage::disk('public')->delete($blogPost->banner_path);
            }

            $blogPost->banner_path = $request->file('banner')->store('blog/banners', 'public');
        }

        $blogPost->update([
            'blog_category_id' => $validated['blog_category_id'] ?? null,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        foreach ($validated['translations'] as $translationData) {
            $blogPost->translations()->updateOrCreate(
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
            ->route('admin.blog-posts.index')
            ->with('status', 'blog-post-saved');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $this->authorize('delete', $blogPost);

        if ($blogPost->banner_path) {
            Storage::disk('public')->delete($blogPost->banner_path);
        }

        $blogPost->delete();

        return redirect()
            ->route('admin.blog-posts.index')
            ->with('status', 'blog-post-deleted');
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
    private function translationsMap(BlogPost $post): array
    {
        $languages = Language::query()->orderBy('name')->get(['id', 'name', 'code']);

        return $languages->mapWithKeys(function (Language $language) use ($post) {
            $translation = $post->translations->firstWhere('language_id', $language->id);

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

        return BlogCategory::query()
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(function (BlogCategory $category) use ($language) {
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
        return collect(BlogPostStatus::cases())
            ->map(fn (BlogPostStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ])
            ->values()
            ->all();
    }
}
