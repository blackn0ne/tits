<?php

namespace App\Observers;

use App\Models\ProjectTranslation;
use App\Services\SlugGenerator;

class ProjectTranslationObserver
{
    public function __construct(private SlugGenerator $slugGenerator) {}

    public function saving(ProjectTranslation $translation): void
    {
        if (! $translation->isDirty('title') && filled($translation->slug)) {
            return;
        }

        $translation->slug = $this->slugGenerator->uniqueForLanguage(
            $translation->title,
            $translation->language_id,
            ProjectTranslation::class,
            'slug',
            $translation->id,
        );
    }
}
