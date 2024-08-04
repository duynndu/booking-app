<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DelayResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        sleep(0);
        return $response;
    }
}
