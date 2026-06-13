<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Sales\StoreSalesClientRequest;
use App\Http\Requests\Admin\Sales\UpdateSalesClientRequest;
use App\Models\SalesClient;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SalesClientController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', SalesClient::class);

        $clients = SalesClient::query()
            ->withCount('orders')
            ->latest('id')
            ->paginate($this->adminPerPage())
            ->withQueryString()
            ->through(fn (SalesClient $client) => [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'phone' => $client->phone,
                'orders_count' => $client->orders_count,
            ]);

        return Inertia::render('admin/Sales/Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', SalesClient::class);

        return Inertia::render('admin/Sales/Clients/Form', [
            'client' => null,
        ]);
    }

    public function store(StoreSalesClientRequest $request): RedirectResponse
    {
        $this->authorize('create', SalesClient::class);

        SalesClient::query()->create($request->validated());

        return redirect()
            ->route('admin.sales.clients.index')
            ->with('status', 'sales-client-saved');
    }

    public function edit(SalesClient $client): Response
    {
        $this->authorize('update', $client);

        return Inertia::render('admin/Sales/Clients/Form', [
            'client' => [
                'id' => $client->id,
                'full_name' => $client->full_name,
                'phone' => $client->phone,
            ],
        ]);
    }

    public function update(UpdateSalesClientRequest $request, SalesClient $client): RedirectResponse
    {
        $this->authorize('update', $client);

        $client->update($request->validated());

        return redirect()
            ->route('admin.sales.clients.index')
            ->with('status', 'sales-client-saved');
    }

    public function destroy(SalesClient $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        if ($client->isStore()) {
            abort(403, 'Системного клиента «Магазин» нельзя удалить');
        }

        $client->delete();

        return redirect()
            ->route('admin.sales.clients.index')
            ->with('status', 'sales-client-deleted');
    }
}
