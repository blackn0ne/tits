<?php

namespace App\Http\Requests\Admin\Sales;

use App\Enums\SalesCatalogStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'status' => ['required', Rule::enum(SalesCatalogStatus::class)],
            'kaspi_link' => ['nullable', 'url', 'max:500'],
            'image' => ['nullable', 'image', 'max:2048'],
            'remove_image' => ['sometimes', 'boolean'],
        ];
    }
}
