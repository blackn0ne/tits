<?php

namespace App\Http\Requests\Admin\Sales;

use App\Models\SalesClient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSalesClientRequest extends FormRequest
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
        /** @var SalesClient $client */
        $client = $this->route('client');

        if ($client->isStore()) {
            return [
                'full_name' => ['required', 'string', 'max:255', Rule::in([SalesClient::STORE_NAME])],
                'phone' => ['required', 'string', 'max:50'],
            ];
        }

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
