<?php

namespace App\Http\Requests\Admin;

use App\Enums\BlogPostStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('blog_category_id') === '' || $this->input('blog_category_id') === null) {
            $this->merge(['blog_category_id' => null]);
        }

        if ($this->input('published_at') === '') {
            $this->merge(['published_at' => null]);
        }
    }

    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'blog_category_id' => ['nullable', 'integer', Rule::exists('blog_categories', 'id')],
            'status' => ['required', Rule::enum(BlogPostStatus::class)],
            'published_at' => ['nullable', 'date'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'remove_banner' => ['sometimes', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.*.language_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'translations.*.title' => ['required', 'string', 'max:255'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_description' => ['nullable', 'string', 'max:500'],
            'translations.*.content' => ['nullable', 'string'],
        ];
    }
}
