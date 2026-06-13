<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCategoryTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectCategoryTranslationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_category_id',
        'language_id',
        'name',
        'slug',
    ];

    /**
     * @return BelongsTo<ProjectCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * @return BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
