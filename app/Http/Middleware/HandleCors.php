<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleCors
{
    public function handle(Request $request, Closure $next)
    {
        // Define the allowed origin(s)
        $allowedOrigins = ['http://localhost:3000', 'http://localhost:8000'];

        // Get the request origin
        $origin = $request->header('Origin');

        // Allow requests without an Origin header (e.g., same-origin requests)
        if (!$origin) {
            return $next($request);
        }
        // Check if the request origin is allowed
        if (in_array($origin, $allowedOrigins)) {
            // Add CORS headers to the response
            $response = $next($request);

            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
            $response->headers->set('Access-Control-Allow-Credentials', 'true'); // Allow credentials if needed

            // Handle preflight requests
            if ($request->isMethod('OPTIONS')) {
                // For preflight requests, return an empty response with the CORS headers
                return response('', 204)
                    ->header('Access-Control-Allow-Origin', $origin)
                    ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                    ->header('Access-Control-Allow-Credentials', 'true');
            }

            return $response;
        }

        // If the origin is not allowed, return a 403 response
        return $next($request);
    }
}
