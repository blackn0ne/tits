<?php

namespace App\Http\Controllers;

use App\Services\DashboardSummaryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardSummaryService $dashboardSummaryService): Response
    {
        $user = $request->user();

        return Inertia::render('Dashboard', [
            'is_admin' => $user->isAdmin(),
            'summary' => $user->isAdmin() ? $dashboardSummaryService->forAdmin() : null,
        ]);
    }
}
