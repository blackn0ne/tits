<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'favicon' => ['nullable', 'image', 'max:1024', 'dimensions:max_width=512,max_height=512'],
            'og_image' => ['nullable', 'image', 'max:4096'],
            'remove_logo' => ['sometimes', 'boolean'],
            'remove_favicon' => ['sometimes', 'boolean'],
            'remove_og_image' => ['sometimes', 'boolean'],
            'maintenance_mode' => ['sometimes', 'boolean'],
            'sitemap_enabled' => ['sometimes', 'boolean'],
            'title_separator' => ['required', 'string', 'max:10'],
            'default_robots' => ['required', 'string', 'max:50'],
            'twitter_handle' => ['nullable', 'string', 'max:50'],
            'google_site_verification' => ['nullable', 'string', 'max:100'],
            'yandex_verification' => ['nullable', 'string', 'max:100'],
            'robots_txt' => ['nullable', 'string', 'max:5000'],
            'social' => ['nullable', 'array'],
            'social.facebook' => ['nullable', 'url', 'max:255'],
            'social.instagram' => ['nullable', 'url', 'max:255'],
            'social.telegram' => ['nullable', 'url', 'max:255'],
            'social.whatsapp' => ['nullable', 'url', 'max:255'],
            'social.youtube' => ['nullable', 'url', 'max:255'],
            'social.tiktok' => ['nullable', 'url', 'max:255'],
            'translations' => ['required', 'array'],
            'translations.*.language_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'translations.*.site_name' => ['required', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string', 'max:1000'],
            'translations.*.keywords' => ['nullable', 'string', 'max:500'],
            'translations.*.home_title' => ['nullable', 'string', 'max:255'],
            'translations.*.home_meta_description' => ['nullable', 'string', 'max:1000'],
            'translations.*.blog_index_title' => ['nullable', 'string', 'max:255'],
            'translations.*.blog_index_meta_description' => ['nullable', 'string', 'max:1000'],
            'translations.*.projects_index_title' => ['nullable', 'string', 'max:255'],
            'translations.*.projects_index_meta_description' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
