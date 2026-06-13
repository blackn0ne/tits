<?php

namespace App\Http\Requests\Admin\Sales;

use App\Enums\SalesCatalogStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesServiceRequest extends FormRequest
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
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(SalesCatalogStatus::class)],
        ];
    }
}
