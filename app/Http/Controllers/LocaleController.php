<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /**
     * Update the user's preferred locale.
     */
    public function update(Request $request, string $locale): RedirectResponse
    {
        if (! Language::isValidCode($locale)) {
            abort(400);
        }

        $request->session()->put('locale', $locale);
        app()->setLocale($locale);

        return back();
    }
}
