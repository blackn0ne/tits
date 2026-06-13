<?php

namespace App\Observers;

use App\Models\ProjectCategoryTranslation;
use App\Services\SlugGenerator;

class ProjectCategoryTranslationObserver
{
    public function __construct(private SlugGenerator $slugGenerator) {}

    public function saving(ProjectCategoryTranslation $translation): void
    {
        if (! $translation->isDirty('name') && filled($translation->slug)) {
            return;
        }

        $translation->slug = $this->slugGenerator->uniqueForLanguage(
            $translation->name,
            $translation->language_id,
            ProjectCategoryTranslation::class,
            'slug',
            $translation->id,
        );
    }
}
