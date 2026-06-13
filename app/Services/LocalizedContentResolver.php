<?php

namespace App\Services;

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Language;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectCategoryTranslation;
use App\Models\ProjectTranslation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LocalizedContentResolver
{
    public function languageId(?string $locale = null): ?int
    {
        $code = $locale ?? app()->getLocale();

        return Language::query()->where('code', $code)->value('id');
    }

    /**
     * @param  Collection<int, Model>  $translations
     */
    public function pickTranslation(Collection $translations, ?string $locale = null): ?Model
    {
        $languageId = $this->languageId($locale);

        if ($languageId !== null) {
            $match = $translations->firstWhere('language_id', $languageId);

            if ($match !== null) {
                return $match;
            }
        }

        return $translations->first();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function mapBlogPost(BlogPost $post, ?string $locale = null): ?array
    {
        /** @var BlogPostTranslation|null $translation */
        $translation = $this->pickTranslation($post->translations, $locale);

        if ($translation === null) {
            return null;
        }

        $categoryTranslation = $post->category
            ? $this->pickTranslation($post->category->translations, $locale)
            : null;

        return [
            'id' => $post->id,
            'title' => $translation->title,
            'meta_title' => $translation->meta_title,
            'meta_description' => $translation->meta_description,
            'slug' => $translation->slug,
            'content' => $translation->content,
            'excerpt' => $this->excerpt($translation->content),
            'published_at' => $post->published_at?->toIso8601String(),
            'published_at_label' => $post->published_at?->translatedFormat('d M Y'),
            'banner_url' => $post->banner_path ? Storage::disk('public')->url($post->banner_path) : null,
            'category' => $categoryTranslation instanceof BlogCategoryTranslation ? [
                'name' => $categoryTranslation->name,
                'slug' => $categoryTranslation->slug,
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function mapProject(Project $project, ?string $locale = null): ?array
    {
        /** @var ProjectTranslation|null $translation */
        $translation = $this->pickTranslation($project->translations, $locale);

        if ($translation === null) {
            return null;
        }

        $categoryTranslation = $project->category
            ? $this->pickTranslation($project->category->translations, $locale)
            : null;

        return [
            'id' => $project->id,
            'title' => $translation->title,
            'meta_title' => $translation->meta_title,
            'meta_description' => $translation->meta_description,
            'slug' => $translation->slug,
            'content' => $translation->content,
            'excerpt' => $this->excerpt($translation->content),
            'published_at' => $project->published_at?->toIso8601String(),
            'published_at_label' => $project->published_at?->translatedFormat('d M Y'),
            'banner_url' => $project->banner_path ? Storage::disk('public')->url($project->banner_path) : null,
            'category' => $categoryTranslation instanceof ProjectCategoryTranslation ? [
                'name' => $categoryTranslation->name,
                'slug' => $categoryTranslation->slug,
            ] : null,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function mapBlogCategory(BlogCategory $category, ?string $locale = null): ?array
    {
        /** @var BlogCategoryTranslation|null $translation */
        $translation = $this->pickTranslation($category->translations, $locale);

        if ($translation === null) {
            return null;
        }

        return [
            'id' => $category->id,
            'name' => $translation->name,
            'slug' => $translation->slug,
            'icon' => $category->icon,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public function mapProjectCategory(ProjectCategory $category, ?string $locale = null): ?array
    {
        /** @var ProjectCategoryTranslation|null $translation */
        $translation = $this->pickTranslation($category->translations, $locale);

        if ($translation === null) {
            return null;
        }

        return [
            'id' => $category->id,
            'name' => $translation->name,
            'slug' => $translation->slug,
            'icon' => $category->icon,
        ];
    }

    public function excerpt(?string $html, int $limit = 160): string
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $html)) ?? '');

        return Str::limit($text, $limit);
    }
}
