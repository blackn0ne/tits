<?php

namespace Database\Factories;

use App\Enums\SalesOrderItemType;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesOrderItem>
 */
class SalesOrderItemFactory extends Factory
{
    protected $model = SalesOrderItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 5);
        $unitPrice = fake()->randomFloat(2, 1000, 100000);

        return [
            'sales_order_id' => SalesOrder::factory(),
            'item_type' => SalesOrderItemType::Product,
            'sales_product_id' => SalesProduct::factory(),
            'sales_service_id' => null,
            'name' => fake()->words(2, true),
            'quantity' => $quantity,
            'unit' => 'шт',
            'unit_price' => $unitPrice,
            'line_total' => round($quantity * $unitPrice, 2),
        ];
    }
}
