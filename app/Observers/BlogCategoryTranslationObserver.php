<?php

namespace App\Observers;

use App\Models\BlogCategoryTranslation;
use App\Services\SlugGenerator;

class BlogCategoryTranslationObserver
{
    public function __construct(private SlugGenerator $slugGenerator) {}

    public function saving(BlogCategoryTranslation $translation): void
    {
        if (! $translation->isDirty('name') && filled($translation->slug)) {
            return;
        }

        $translation->slug = $this->slugGenerator->uniqueForLanguage(
            $translation->name,
            $translation->language_id,
            BlogCategoryTranslation::class,
            'slug',
            $translation->id,
        );
    }
}
