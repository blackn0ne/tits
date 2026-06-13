<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogPostTranslation extends Model
{
    /** @use HasFactory<\Database\Factories\BlogPostTranslationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'blog_post_id',
        'language_id',
        'title',
        'meta_title',
        'meta_description',
        'slug',
        'content',
    ];

    /**
     * @return BelongsTo<BlogPost, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }

    /**
     * @return BelongsTo<Language, $this>
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
