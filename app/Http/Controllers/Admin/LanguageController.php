<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LanguageController extends Controller
{
    /**
     * Display a listing of languages.
     */
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->isAdmin(), 403);

        return Inertia::render('admin/Languages/Index', [
            'languages' => Language::query()
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
        ]);
    }
}
