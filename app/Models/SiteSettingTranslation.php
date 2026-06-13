<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteSettingTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\SiteSettingTranslationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'site_setting_id',
        'language_id',
        'site_name',
        'description',
        'keywords',
        'home_title',
        'home_meta_description',
        'blog_index_title',
        'blog_index_meta_description',
        'projects_index_title',
        'projects_index_meta_description',
    ];

    /**
     * @return BelongsTo<SiteSetting, $this>
     */
    public function siteSetting(): BelongsTo
    {
        return $this->belongsTo(SiteSetting::class);
    }

    /**
     * @return BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
