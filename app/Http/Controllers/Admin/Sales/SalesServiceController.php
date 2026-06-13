<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Enums\SalesCatalogStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sales\StoreSalesServiceRequest;
use App\Http\Requests\Admin\Sales\UpdateSalesServiceRequest;
use App\Models\SalesService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SalesServiceController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', SalesService::class);

        $services = SalesService::query()
            ->latest('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(fn (SalesService $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'status' => $service->status->value,
                'status_label' => $service->status->label(),
            ]);

        return Inertia::render('admin/Sales/Services/Index', [
            'services' => $services,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', SalesService::class);

        return Inertia::render('admin/Sales/Services/Form', [
            'service' => null,
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(StoreSalesServiceRequest $request): RedirectResponse
    {
        $this->authorize('create', SalesService::class);

        $validated = $request->validated();

        SalesService::query()->create($validated);

        return redirect()
            ->route('admin.sales.services.index')
            ->with('status', 'sales-service-saved');
    }

    public function edit(SalesService $service): Response
    {
        $this->authorize('update', $service);

        return Inertia::render('admin/Sales/Services/Form', [
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'price' => (float) $service->price,
                'status' => $service->status->value,
            ],
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateSalesServiceRequest $request, SalesService $service): RedirectResponse
    {
        $this->authorize('update', $service);

        $service->update($request->validated());

        return redirect()
            ->route('admin.sales.services.index')
            ->with('status', 'sales-service-saved');
    }

    public function destroy(SalesService $service): RedirectResponse
    {
        $this->authorize('delete', $service);

        $service->delete();

        return redirect()
            ->route('admin.sales.services.index')
            ->with('status', 'sales-service-deleted');
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    private function statusOptions(): array
    {
        return collect(SalesCatalogStatus::values())
            ->map(fn (SalesCatalogStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
            ])
            ->values()
            ->all();
    }
}
