<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKeyFromHeader = $request->header('X-Api-Key');
        if (!$apiKeyFromHeader) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $apiKey = config('api.api_key');

        if ($apiKeyFromHeader !== $apiKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
