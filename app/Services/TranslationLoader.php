<?php

namespace App\Services;

use Illuminate\Support\Arr;
use JsonException;
use RuntimeException;

class TranslationLoader
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $loaded = [];

    /**
     * @return array<string, mixed>
     */
    public function all(string $code): array
    {
        return $this->loadFile($code);
    }

    /**
     * @return array<string, mixed>
     */
    public function forLocale(?string $locale = null): array
    {
        return $this->all($locale ?? app()->getLocale());
    }

    public function get(string $code, string $key, ?string $default = null): string
    {
        $value = Arr::get($this->all($code), $key, $default);

        return is_string($value) ? $value : ($default ?? $key);
    }

    public function has(string $code): bool
    {
        return is_file($this->path($code));
    }

    /**
     * @return array<string, mixed>
     */
    private function loadFile(string $code): array
    {
        $path = $this->path($code);

        if (! is_file($path)) {
            return [];
        }

        $cacheKey = $code.'.'.filemtime($path);

        if (isset($this->loaded[$cacheKey])) {
            return $this->loaded[$cacheKey];
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException("Unable to read translation file [{$path}].");
        }

        try {
            /** @var array<string, mixed> $translations */
            $translations = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("Invalid JSON in translation file [{$path}].", 0, $exception);
        }

        $this->loaded[$cacheKey] = $translations;

        return $translations;
    }

    private function path(string $code): string
    {
        return lang_path("{$code}.json");
    }
}
