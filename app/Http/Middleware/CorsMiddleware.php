<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class CorsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Add CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*'); // Change '*' to specific origins if needed
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');

        // Handle preflight requests
        if ($request->getMethod() === 'OPTIONS') {
            $response->setStatusCode(200);
        }

        return $response;
    }
}
