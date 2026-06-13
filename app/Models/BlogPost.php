<?php

namespace App\Models;

use App\Enums\BlogPostStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    /** @use HasFactory<\Database\Factories\BlogPostFactory> */
    use HasFactory, SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'blog_category_id',
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
            'status' => BlogPostStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<BlogCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<BlogPostTranslation, $this>
     */
    public function translations(): HasMany
    {
        return $this->hasMany(BlogPostTranslation::class);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query
            ->where('status', BlogPostStatus::Published)
            ->where(function (Builder $builder): void {
                $builder
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
