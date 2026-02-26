<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        $availableLocales = ['es', 'pt', 'en'];

        if (auth()->check()) {
            $locale = auth()->user()->locale;
        } elseif (session()->has('locale')) {
            $locale = session('locale');
        } else {
            $locale = $request->getPreferredLanguage($availableLocales)
                ?? config('app.locale');
        }

        App::setLocale($locale);

        session(['locale' => $locale]);

        return $next($request);
    }
}