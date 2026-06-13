<?php

namespace App\Services;

use App\Enums\BlogPostStatus;
use App\Enums\ProjectStatus;
use App\Enums\SalesCatalogStatus;
use App\Models\BlogPost;
use App\Models\Project;
use App\Models\SalesClient;
use App\Models\SalesOrder;
use App\Models\SalesProduct;
use App\Models\SalesService;
use App\Models\SiteSetting;

class DashboardSummaryService
{
    public function __construct(
        private SalesReportService $salesReportService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function forAdmin(): array
    {
        $salesThisMonth = $this->salesReportService->summary('month');

        $lastMonth = now()->subMonth();
        $salesLastMonth = $this->salesReportService->summary(
            'custom',
            $lastMonth->copy()->startOfMonth()->toDateString(),
            $lastMonth->copy()->endOfMonth()->toDateString(),
        );

        $chartStart = now()->subDays(29)->startOfDay();
        $salesChart = $this->salesReportService->summary(
            'custom',
            $chartStart->toDateString(),
            now()->toDateString(),
        );

        $avgOrderThisMonth = $salesThisMonth['orders_count'] > 0
            ? round($salesThisMonth['revenue'] / $salesThisMonth['orders_count'], 2)
            : 0.0;
        $avgOrderLastMonth = $salesLastMonth['orders_count'] > 0
            ? round($salesLastMonth['revenue'] / $salesLastMonth['orders_count'], 2)
            : 0.0;

        return [
            'period' => [
                'from' => $salesThisMonth['from'],
                'to' => $salesThisMonth['to'],
            ],
            'sales' => [
                'revenue' => $salesThisMonth['revenue'],
                'revenue_trend' => $this->trend($salesThisMonth['revenue'], $salesLastMonth['revenue']),
                'orders_count' => $salesThisMonth['orders_count'],
                'orders_trend' => $this->trend((float) $salesThisMonth['orders_count'], (float) $salesLastMonth['orders_count']),
                'average_order' => $avgOrderThisMonth,
                'average_order_trend' => $this->trend($avgOrderThisMonth, $avgOrderLastMonth),
                'products_revenue' => $salesThisMonth['products_revenue'],
                'services_revenue' => $salesThisMonth['services_revenue'],
            ],
            'catalog' => [
                'active_products' => SalesProduct::query()->where('status', SalesCatalogStatus::Active)->count(),
                'active_services' => SalesService::query()->where('status', SalesCatalogStatus::Active)->count(),
                'clients' => SalesClient::query()
                    ->where('full_name', '!=', SalesClient::STORE_NAME)
                    ->count(),
            ],
            'content' => [
                'blog_published' => BlogPost::query()->published()->count(),
                'blog_total' => BlogPost::query()->count(),
                'blog_drafts' => BlogPost::query()->where('status', BlogPostStatus::Draft)->count(),
                'projects_published' => Project::query()->published()->count(),
                'projects_total' => Project::query()->count(),
                'projects_drafts' => Project::query()->where('status', ProjectStatus::Draft)->count(),
            ],
            'site' => [
                'maintenance_mode' => SiteSetting::instance()->maintenance_mode,
            ],
            'chart' => $salesChart['chart'],
            'recent_orders' => $this->recentOrders(),
        ];
    }

    /**
     * @return list<array{id: int, client_name: string, status: string, status_label: string, total: float, ordered_at: string}>
     */
    private function recentOrders(): array
    {
        return SalesOrder::query()
            ->with('client')
            ->latest('ordered_at')
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (SalesOrder $order) => [
                'id' => $order->id,
                'client_name' => $order->client?->full_name ?? '—',
                'status' => $order->status->value,
                'status_label' => $order->status->label(),
                'total' => (float) $order->total,
                'ordered_at' => $order->ordered_at->format('d.m.Y H:i'),
            ])
            ->all();
    }

    /**
     * @return array{direction: string, percent: float|null}|null
     */
    private function trend(float $current, float $previous): ?array
    {
        if ($current === 0.0 && $previous === 0.0) {
            return null;
        }

        if ($previous === 0.0) {
            return [
                'direction' => 'up',
                'percent' => null,
            ];
        }

        $percent = round((($current - $previous) / $previous) * 100, 1);

        return [
            'direction' => $percent >= 0 ? 'up' : 'down',
            'percent' => abs($percent),
        ];
    }
}
