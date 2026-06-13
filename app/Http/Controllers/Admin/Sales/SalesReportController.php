<?php

namespace App\Http\Controllers\Admin\Sales;

use App\Http\Controllers\Controller;
use App\Models\SalesOrder;
use App\Services\SalesReportService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SalesReportController extends Controller
{
    public function index(Request $request, SalesReportService $salesReportService): Response
    {
        $this->authorize('viewAny', SalesOrder::class);

        $period = (string) $request->query('period', 'month');
        $from = $request->query('from');
        $to = $request->query('to');

        return Inertia::render('admin/Sales/Reports/Index', [
            'period' => $period,
            'from' => $from,
            'to' => $to,
            'report' => $salesReportService->summary($period, is_string($from) ? $from : null, is_string($to) ? $to : null),
            'periods' => [
                ['value' => 'week', 'label_key' => 'admin.sales.reports.periods.week'],
                ['value' => 'month', 'label_key' => 'admin.sales.reports.periods.month'],
                ['value' => 'quarter', 'label_key' => 'admin.sales.reports.periods.quarter'],
                ['value' => 'half_year', 'label_key' => 'admin.sales.reports.periods.half_year'],
                ['value' => 'year', 'label_key' => 'admin.sales.reports.periods.year'],
                ['value' => 'custom', 'label_key' => 'admin.sales.reports.periods.custom'],
            ],
        ]);
    }
}
