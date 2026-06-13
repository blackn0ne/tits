<?php

namespace Database\Factories;

use App\Enums\SalesOrderStatus;
use App\Models\SalesClient;
use App\Models\SalesOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SalesOrder>
 */
class SalesOrderFactory extends Factory
{
    protected $model = SalesOrder::class;

    public function definition(): array
    {
        return [
            'sales_client_id' => SalesClient::factory(),
            'user_id' => User::factory()->admin(),
            'status' => SalesOrderStatus::Completed,
            'total' => 0,
            'ordered_at' => fake()->dateTimeBetween('-3 months'),
            'notes' => null,
        ];
    }
}
