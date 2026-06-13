<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->hasSession()
            ? (string) $request->session()->get('locale', Language::defaultCode())
            : Language::defaultCode();

        if (! Language::isValidCode($code)) {
            $code = Language::defaultCode();
        }

        app()->setLocale($code);

        return $next($request);
    }
}
