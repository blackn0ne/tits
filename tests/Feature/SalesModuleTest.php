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

test('guest cannot access sales products', function () {
    $this->get(route('admin.sales.products.index'))->assertRedirect(route('login'));
});

test('sales products index paginates results', function () {
    $admin = User::factory()->admin()->create();

    SalesProduct::factory()->count(16)->create();

    $this->actingAs($admin)
        ->get(route('admin.sales.products.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/Sales/Products/Index')
            ->where('products.current_page', 1)
            ->where('products.last_page', 2)
            ->where('products.per_page', 15)
            ->has('products.data', 15)
        );

    $this->actingAs($admin)
        ->get(route('admin.sales.products.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('products.current_page', 2)
            ->has('products.data', 1)
        );
});

test('admin can create product service client and order', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.sales.products.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/Sales/Products/Index')
            ->has('products.data')
            ->has('products.links')
            ->has('products.total')
        );

    $productResponse = $this->actingAs($admin)->post(route('admin.sales.products.store'), [
        'name' => 'Ноутбук',
        'short_description' => 'Рабочий ноутбук',
        'price' => 350000,
        'quantity' => 5,
        'unit' => 'шт',
        'status' => SalesCatalogStatus::Active->value,
        'kaspi_link' => 'https://kaspi.kz/shop/product/1',
    ]);
    $productResponse->assertRedirect(route('admin.sales.products.index'));

    $product = SalesProduct::query()->where('name', 'Ноутбук')->firstOrFail();

    $serviceResponse = $this->actingAs($admin)->post(route('admin.sales.services.store'), [
        'name' => 'Настройка ПК',
        'price' => 15000,
        'status' => SalesCatalogStatus::Active->value,
    ]);
    $serviceResponse->assertRedirect(route('admin.sales.services.index'));

    $service = SalesService::query()->where('name', 'Настройка ПК')->firstOrFail();

    $clientResponse = $this->actingAs($admin)->post(route('admin.sales.clients.store'), [
        'full_name' => 'Айдар Нурланов',
        'phone' => '+7 777 111 22 33',
    ]);
    $clientResponse->assertRedirect(route('admin.sales.clients.index'));

    $client = SalesClient::query()->where('full_name', 'Айдар Нурланов')->firstOrFail();

    $orderResponse = $this->actingAs($admin)->post(route('admin.sales.orders.store'), [
        'sales_client_id' => $client->id,
        'status' => SalesOrderStatus::Completed->value,
        'ordered_at' => now()->toDateTimeString(),
        'notes' => 'Тестовый заказ',
        'items' => [
            [
                'item_type' => SalesOrderItemType::Product->value,
                'sales_product_id' => $product->id,
                'sales_service_id' => null,
                'name' => $product->name,
                'quantity' => 2,
                'unit' => 'шт',
                'unit_price' => 340000,
            ],
            [
                'item_type' => SalesOrderItemType::Service->value,
                'sales_product_id' => null,
                'sales_service_id' => $service->id,
                'name' => $service->name,
                'quantity' => 1,
                'unit' => null,
                'unit_price' => 12000,
            ],
        ],
    ]);

    $orderResponse->assertRedirect(route('admin.sales.orders.index'));

    $order = SalesOrder::query()->with('items')->firstOrFail();

    expect($order->sales_client_id)->toBe($client->id)
        ->and($order->items)->toHaveCount(2)
        ->and((float) $order->total)->toBe(692000.0);
});

test('order create form defaults to store client', function () {
    $admin = User::factory()->admin()->create();
    $storeClient = SalesClient::storeClient();

    $this->actingAs($admin)
        ->get(route('admin.sales.orders.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/Sales/Orders/Form')
            ->where('default_client_id', $storeClient->id)
            ->where('clients.0.full_name', SalesClient::STORE_NAME)
            ->where('clients.0.is_store', true)
        );
});

test('admin can view sales reports with statistics', function () {
    $admin = User::factory()->admin()->create();
    $client = SalesClient::factory()->create();

    SalesOrder::factory()->create([
        'sales_client_id' => $client->id,
        'user_id' => $admin->id,
        'status' => SalesOrderStatus::Completed,
        'total' => 50000,
        'ordered_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.sales.reports.index', ['period' => 'month']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/Sales/Reports/Index')
            ->has('report.orders_count')
            ->has('report.revenue')
            ->where('report.orders_count', 1)
        );
});
