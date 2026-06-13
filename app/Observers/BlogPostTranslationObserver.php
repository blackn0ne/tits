<?php

namespace App\Observers;

use App\Models\BlogPostTranslation;
use App\Services\SlugGenerator;

class BlogPostTranslationObserver
{
    public function __construct(private SlugGenerator $slugGenerator) {}

    public function saving(BlogPostTranslation $translation): void
    {
        if (! $translation->isDirty('title') && filled($translation->slug)) {
            return;
        }

        $translation->slug = $this->slugGenerator->uniqueForLanguage(
            $translation->title,
            $translation->language_id,
            BlogPostTranslation::class,
            'slug',
            $translation->id,
        );
    }
}
