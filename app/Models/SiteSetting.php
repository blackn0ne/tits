<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteSetting extends Model
{
    /** @use HasFactory<\Database\Factories\SiteSettingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'logo_path',
        'favicon_path',
        'og_image_path',
        'phone',
        'address',
        'social_links',
        'maintenance_mode',
        'title_separator',
        'default_robots',
        'twitter_handle',
        'google_site_verification',
        'yandex_verification',
        'sitemap_enabled',
        'robots_txt',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'maintenance_mode' => 'boolean',
            'sitemap_enabled' => 'boolean',
        ];
    }

    /**
     * @return HasMany<SiteSettingTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SiteSettingTranslation::class);
    }

    public static function instance(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
