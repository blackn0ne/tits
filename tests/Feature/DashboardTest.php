<?php

use App\Enums\SalesCatalogStatus;
use App\Enums\SalesOrderItemType;
use App\Enums\SalesOrderStatus;
use App\Models\SalesClient;
use App\Models\SalesOrder;
use App\Models\SalesProduct;
use App\Models\SalesService;
use App\Models\User;
use Database\Seeders\LanguageSeeder;

beforeEach(function () {
    $this->seed(LanguageSeeder::class);
    $this->seed(\Database\Seeders\SalesClientSeeder::class);
});

test('guests are redirected to the login page', function () {
    $response = $this->get('/dashboard');
    $response->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/dashboard');
    $response->assertSuccessful();
});

test('admin dashboard returns summary metrics', function () {
    $admin = User::factory()->admin()->create();

    $product = SalesProduct::factory()->create([
        'status' => SalesCatalogStatus::Active,
    ]);

    $service = SalesService::factory()->create([
        'status' => SalesCatalogStatus::Active,
    ]);

    $client = SalesClient::query()
        ->where('full_name', '!=', SalesClient::STORE_NAME)
        ->firstOrFail();

    $this->actingAs($admin)->post(route('admin.sales.orders.store'), [
        'sales_client_id' => $client->id,
        'status' => SalesOrderStatus::Completed->value,
        'ordered_at' => now()->toDateTimeString(),
        'items' => [
            [
                'item_type' => SalesOrderItemType::Product->value,
                'sales_product_id' => $product->id,
                'sales_service_id' => null,
                'name' => $product->name,
                'quantity' => 1,
                'unit' => 'шт',
                'unit_price' => 10000,
            ],
            [
                'item_type' => SalesOrderItemType::Service->value,
                'sales_product_id' => null,
                'sales_service_id' => $service->id,
                'name' => $service->name,
                'quantity' => 1,
                'unit' => 'усл',
                'unit_price' => 5000,
            ],
        ],
    ])->assertRedirect(route('admin.sales.orders.index'));

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('is_admin', true)
            ->has('summary.sales.revenue')
            ->has('summary.sales.orders_count')
            ->has('summary.catalog.active_products')
            ->has('summary.catalog.active_services')
            ->has('summary.recent_orders', 1)
        );
});

test('regular user dashboard does not include admin summary', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertSuccessful()
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->where('is_admin', false)
            ->where('summary', null)
        );
});
