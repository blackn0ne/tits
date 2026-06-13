<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Enums\SalesOrderItemType;
use App\Enums\SalesOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sales\StoreSalesOrderRequest;
use App\Http\Requests\Admin\Sales\UpdateSalesOrderRequest;
use App\Models\SalesClient;
use App\Models\SalesOrder;
use App\Models\SalesProduct;
use App\Models\SalesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SalesOrderController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', SalesOrder::class);

        $orders = SalesOrder::query()
            ->with('client')
            ->withCount('items')
            ->latest('ordered_at')
            ->latest('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(fn (SalesOrder $order) => [
                'id' => $order->id,
                'client_name' => $order->client?->full_name ?? '—',
                'client_phone' => $order->client?->phone,
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'total' => (float) $order->total,
                'ordered_at' => $order->ordered_at->toDateTimeString(),
                'items_count' => $order->items_count,
            ]);

        return Inertia::render('admin/Sales/Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', SalesOrder::class);

        return Inertia::render('admin/Sales/Orders/Form', $this->formPayload());
    }

    public function store(StoreSalesOrderRequest $request): RedirectResponse
    {
        $this->authorize('create', SalesOrder::class);

        $validated = $request->validated();

        DB::transaction(function () use ($validated, $request): void {
            $order = SalesOrder::query()->create([
                'sales_client_id' => $validated['sales_client_id'],
                'user_id' => $request->user()->id,
                'status' => $validated['status'],
                'ordered_at' => $validated['ordered_at'],
                'notes' => $validated['notes'] ?? null,
                'total' => 0,
            ]);

            $this->syncItems($order, $validated['items']);
        });

        return redirect()
            ->route('admin.sales.orders.index')
            ->with('status', 'sales-order-saved');
    }

    public function edit(SalesOrder $order): Response
    {
        $this->authorize('update', $order);

        $order->load(['client', 'items']);

        return Inertia::render('admin/Sales/Orders/Form', [
            ...$this->formPayload(),
            'order' => [
                'id' => $order->id,
                'sales_client_id' => $order->sales_client_id,
                'status' => $order->status->value,
                'ordered_at' => $order->ordered_at->format('Y-m-d\TH:i'),
                'notes' => $order->notes,
                'total' => (float) $order->total,
                'items' => $order->items->map(fn ($item) => [
                    'item_type' => $item->item_type->value,
                    'sales_product_id' => $item->sales_product_id,
                    'sales_service_id' => $item->sales_service_id,
                    'name' => $item->name,
                    'quantity' => (float) $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => (float) $item->unit_price,
                    'line_total' => (float) $item->line_total,
                ])->values()->all(),
            ],
        ]);
    }

    public function update(UpdateSalesOrderRequest $request, SalesOrder $order): RedirectResponse
    {
        $this->authorize('update', $order);

        $validated = $request->validated();

        DB::transaction(function () use ($order, $validated): void {
            $order->update([
                'sales_client_id' => $validated['sales_client_id'],
                'status' => $validated['status'],
                'ordered_at' => $validated['ordered_at'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $order->items()->delete();
            $this->syncItems($order, $validated['items']);
        });

        return redirect()
            ->route('admin.sales.orders.index')
            ->with('status', 'sales-order-saved');
    }

    public function destroy(SalesOrder $order): RedirectResponse
    {
        $this->authorize('delete', $order);

        $order->delete();

        return redirect()
            ->route('admin.sales.orders.index')
            ->with('status', 'sales-order-deleted');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     */
    private function syncItems(SalesOrder $order, array $items): void
    {
        $total = 0;

        foreach ($items as $item) {
            $lineTotal = round((float) $item['quantity'] * (float) $item['unit_price'], 2);

            $order->items()->create([
                'item_type' => $item['item_type'],
                'sales_product_id' => $item['item_type'] === SalesOrderItemType::Product->value
                    ? ($item['sales_product_id'] ?? null)
                    : null,
                'sales_service_id' => $item['item_type'] === SalesOrderItemType::Service->value
                    ? ($item['sales_service_id'] ?? null)
                    : null,
                'name' => $item['name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'],
                'line_total' => $lineTotal,
            ]);

            $total += $lineTotal;
        }

        $order->update(['total' => round($total, 2)]);
    }

    /**
     * @return array<string, mixed>
     */
    private function formPayload(): array
    {
        $storeClient = SalesClient::storeClient();

        return [
            'order' => null,
            'default_client_id' => $storeClient->id,
            'clients' => $this->clientOptions(),
            'products' => SalesProduct::query()->orderBy('name')->get()->map(fn (SalesProduct $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'unit' => $product->unit,
                'status' => $product->status->value,
            ]),
            'services' => SalesService::query()->orderBy('name')->get()->map(fn (SalesService $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'status' => $service->status->value,
            ]),
            'statuses' => collect(SalesOrderStatus::values())
                ->map(fn (SalesOrderStatus $status) => [
                    'value' => $status->value,
                    'label' => $status->label(),
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return list<array{id: int, full_name: string, phone: string, is_store: bool}>
     */
    private function clientOptions(): array
    {
        return SalesClient::query()
            ->orderByRaw('CASE WHEN full_name = ? THEN 0 ELSE 1 END', [SalesClient::STORE_NAME])
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'phone'])
            ->map(fn (SalesClient $client) => [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'phone' => $client->phone,
                'is_store' => $client->isStore(),
            ])
            ->values()
            ->all();
    }
}
