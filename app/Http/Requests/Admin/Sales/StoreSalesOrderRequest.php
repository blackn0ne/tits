<?php

namespace App\Http\Requests\Admin\Sales;

use App\Enums\SalesOrderItemType;
use App\Enums\SalesOrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSalesOrderRequest extends FormRequest
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
            'sales_client_id' => ['required', 'integer', 'exists:sales_clients,id'],
            'status' => ['required', Rule::enum(SalesOrderStatus::class)],
            'ordered_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.item_type' => ['required', Rule::enum(SalesOrderItemType::class)],
            'items.*.sales_product_id' => ['nullable', 'integer', 'exists:sales_products,id'],
            'items.*.sales_service_id' => ['nullable', 'integer', 'exists:sales_services,id'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
