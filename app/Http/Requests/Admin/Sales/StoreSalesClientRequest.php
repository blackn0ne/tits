<?php

namespace App\Http\Requests\Admin\Sales;

use App\Models\SalesClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesClientRequest extends FormRequest
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
            'full_name' => [
                'required',
                'string',
                'max:255',
                Rule::notIn([SalesClient::STORE_NAME]),
            ],
            'phone' => ['required', 'string', 'max:50'],
        ];
    }
}
