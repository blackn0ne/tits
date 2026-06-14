<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProjectStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProjectRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('project_category_id') === '' || $this->input('project_category_id') === null) {
            $this->merge(['project_category_id' => null]);
        }

        if ($this->input('published_at') === '') {
            $this->merge(['published_at' => null]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasTitle = collect($this->input('translations', []))
                ->contains(fn (mixed $row): bool => filled(is_array($row) ? ($row['title'] ?? null) : null));

            if (! $hasTitle) {
                $validator->errors()->add('translations', __('validation.at_least_one_translation'));
            }
        });
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
            'project_category_id' => ['nullable', 'integer', Rule::exists('project_categories', 'id')],
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'published_at' => ['nullable', 'date'],
            'banner' => ['nullable', 'image', 'max:4096'],
            'remove_banner' => ['sometimes', 'boolean'],
            'translations' => ['required', 'array'],
            'translations.*.language_id' => ['required', 'integer', Rule::exists('languages', 'id')],
            'translations.*.title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_title' => ['nullable', 'string', 'max:255'],
            'translations.*.meta_description' => ['nullable', 'string', 'max:500'],
            'translations.*.content' => ['nullable', 'string'],
        ];
    }
}
