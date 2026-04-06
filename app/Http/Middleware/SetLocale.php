<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = 'pt';

        if (auth()->check() && auth()->user()->locale) {
            $locale = auth()->user()->locale;
        }

        app()->setLocale($locale);

        Carbon::setLocale(match ($locale) {
            'pt' => 'pt_BR',
            'en' => 'en',
            'es' => 'es',
            default => 'pt_BR',
        });

        return $next($request);
    }
}
