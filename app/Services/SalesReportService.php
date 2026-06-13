<?php

namespace App\Services;

use App\Enums\SalesOrderItemType;
use App\Enums\SalesOrderStatus;
use App\Models\SalesOrder;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * @return array{from: string, to: string, start: CarbonInterface, end: CarbonInterface}
     */
    public function resolveRange(string $period, ?string $from = null, ?string $to = null): array
    {
        $now = now();

        [$start, $end] = match ($period) {
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'quarter' => [$now->copy()->firstOfQuarter(), $now->copy()->lastOfQuarter()],
            'half_year' => $now->month <= 6
                ? [$now->copy()->startOfYear(), $now->copy()->month(6)->endOfMonth()]
                : [$now->copy()->month(7)->startOfMonth(), $now->copy()->endOfYear()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                Carbon::parse($from ?? $now)->startOfDay(),
                Carbon::parse($to ?? $now)->endOfDay(),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };

        return [
            'from' => $start->toDateString(),
            'to' => $end->toDateString(),
            'start' => $start,
            'end' => $end,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function summary(string $period, ?string $from = null, ?string $to = null): array
    {
        $range = $this->resolveRange($period, $from, $to);

        $ordersQuery = SalesOrder::query()
            ->where('status', SalesOrderStatus::Completed)
            ->whereBetween('ordered_at', [$range['start'], $range['end']]);

        $ordersCount = (clone $ordersQuery)->count();
        $revenue = (float) (clone $ordersQuery)->sum('total');

        $productsRevenue = (float) DB::table('sales_order_items')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->where('sales_orders.status', SalesOrderStatus::Completed->value)
            ->whereBetween('sales_orders.ordered_at', [$range['start'], $range['end']])
            ->where('sales_order_items.item_type', SalesOrderItemType::Product->value)
            ->sum('sales_order_items.line_total');

        $servicesRevenue = (float) DB::table('sales_order_items')
            ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
            ->where('sales_orders.status', SalesOrderStatus::Completed->value)
            ->whereBetween('sales_orders.ordered_at', [$range['start'], $range['end']])
            ->where('sales_order_items.item_type', SalesOrderItemType::Service->value)
            ->sum('sales_order_items.line_total');

        $chart = $this->chartData($range['start'], $range['end']);

        return [
            'period' => $period,
            'from' => $range['from'],
            'to' => $range['to'],
            'orders_count' => $ordersCount,
            'revenue' => $revenue,
            'products_revenue' => $productsRevenue,
            'services_revenue' => $servicesRevenue,
            'chart' => $chart,
        ];
    }

    /**
     * @return list<array{date: string, revenue: float, orders: int}>
     */
    private function chartData(CarbonInterface $start, CarbonInterface $end): array
    {
        /** @var Collection<int, object{date: string, revenue: string|null, orders: int}> $rows */
        $rows = DB::table('sales_orders')
            ->selectRaw('DATE(ordered_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->where('status', SalesOrderStatus::Completed->value)
            ->whereBetween('ordered_at', [$start, $end])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $rows->map(fn ($row) => [
            'date' => (string) $row->date,
            'revenue' => (float) $row->revenue,
            'orders' => (int) $row->orders,
        ])->values()->all();
    }
}
