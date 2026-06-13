<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Transliterator;

class SlugGenerator
{
    /**
     * Generate a unique slug for a translation model scoped by language.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function uniqueForLanguage(
        string $source,
        int $languageId,
        string $modelClass,
        string $slugColumn = 'slug',
        ?int $ignoreId = null,
    ): string {
        $baseSlug = $this->fromText($source);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($modelClass, $slugColumn, $slug, $languageId, $ignoreId)) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function fromText(string $text): string
    {
        $normalized = trim($text);

        if ($normalized === '') {
            return 'item';
        }

        if (class_exists(Transliterator::class)) {
            $transliterated = transliterator_transliterate('Any-Latin; Latin-ASCII;', $normalized);

            if (is_string($transliterated) && $transliterated !== '') {
                $normalized = $transliterated;
            }
        }

        $slug = Str::slug($normalized);

        return $slug !== '' ? $slug : 'item';
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function slugExists(
        string $modelClass,
        string $slugColumn,
        string $slug,
        int $languageId,
        ?int $ignoreId,
    ): bool {
        $query = $modelClass::query()
            ->where($slugColumn, $slug)
            ->where('language_id', $languageId);

        if ($ignoreId !== null) {
            $query->whereKeyNot($ignoreId);
        }

        return $query->exists();
    }
}
