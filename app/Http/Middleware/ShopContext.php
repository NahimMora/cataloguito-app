<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ShopContext
{
    public function handle(Request $request, Closure $next)
    {
        $shop = $request->user()->shop;
        if (! $shop) {
            return redirect()->route('onboarding');
        }
        $request->attributes->set('shop', $shop);
        View::share('currentShop', $shop);

        return $next($request);
    }
}
