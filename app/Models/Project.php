<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_category_id',
        'user_id',
        'banner_path',
        'status',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<ProjectCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<ProjectTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(ProjectTranslation::class);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query
            ->where('status', ProjectStatus::Published)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    #[Scope]
    protected function visibleOnSite(Builder $query): void
    {
        $query->published();

        $query->whereHas('translations', function (Builder $builder): void {
            $builder->where('title', '!=', '');
        });
    }

    public function isVisibleOnSite(): bool
    {
        if ($this->status !== ProjectStatus::Published) {
            return false;
        }

        if ($this->published_at !== null && $this->published_at->isAfter(now())) {
            return false;
        }

        return $this->translations()->where('title', '!=', '')->exists();
    }
}
