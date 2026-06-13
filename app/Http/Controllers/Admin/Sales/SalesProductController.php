<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Enums\SalesCatalogStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sales\StoreSalesProductRequest;
use App\Http\Requests\Admin\Sales\UpdateSalesProductRequest;
use App\Models\SalesProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SalesProductController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', SalesProduct::class);

        $products = SalesProduct::query()
            ->latest('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(fn (SalesProduct $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => $product->quantity,
                'unit' => $product->unit,
                'status' => $product->status->value,
                'status_label' => $product->status->label(),
                'kaspi_link' => $product->kaspi_link,
                'image_url' => $product->image_path ? Storage::disk('public')->url($product->image_path) : null,
            ]);

        return Inertia::render('admin/Sales/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', SalesProduct::class);

        return Inertia::render('admin/Sales/Products/Form', [
            'product' => null,
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function store(StoreSalesProductRequest $request): RedirectResponse
    {
        $this->authorize('create', SalesProduct::class);

        $validated = $request->validated();

        $product = SalesProduct::query()->create([
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'kaspi_link' => $validated['kaspi_link'] ?? null,
        ]);

        if ($request->hasFile('image')) {
            $product->update([
                'image_path' => $request->file('image')->store('sales/products', 'public'),
            ]);
        }

        return redirect()
            ->route('admin.sales.products.index')
            ->with('status', 'sales-product-saved');
    }

    public function edit(SalesProduct $product): Response
    {
        $this->authorize('update', $product);

        return Inertia::render('admin/Sales/Products/Form', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'short_description' => $product->short_description,
                'price' => (float) $product->price,
                'quantity' => $product->quantity,
                'unit' => $product->unit,
                'status' => $product->status->value,
                'kaspi_link' => $product->kaspi_link,
                'image_url' => $product->image_path ? Storage::disk('public')->url($product->image_path) : null,
            ],
            'statuses' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateSalesProductRequest $request, SalesProduct $product): RedirectResponse
    {
        $this->authorize('update', $product);

        $validated = $request->validated();

        if ($request->boolean('remove_image') && $product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $product->image_path = null;
        }

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->image_path = $request->file('image')->store('sales/products', 'public');
        }

        $product->fill([
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'price' => $validated['price'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'status' => $validated['status'],
            'kaspi_link' => $validated['kaspi_link'] ?? null,
        ]);
        $product->save();

        return redirect()
            ->route('admin.sales.products.index')
            ->with('status', 'sales-product-saved');
    }

    public function destroy(SalesProduct $product): RedirectResponse
    {
        $this->authorize('delete', $product);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()
            ->route('admin.sales.products.index')
            ->with('status', 'sales-product-deleted');
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
