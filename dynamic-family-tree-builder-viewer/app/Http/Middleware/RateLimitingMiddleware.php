<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->getMaxAttempts($request);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $this->logRateLimitExceeded($request, $key);
            
            return response()->json([
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => RateLimiter::availableIn($key)
            ], 429);
        }

        RateLimiter::hit($key, $this->getDecayMinutes($request) * 60);

        $response = $next($request);

        $response->headers->add([
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
            'X-RateLimit-Reset' => RateLimiter::availableAt($key),
        ]);

        return $response;
    }

    /**
     * Resolve the request signature for rate limiting
     */
    protected function resolveRequestSignature(Request $request): string
    {
        $userId = $request->user()?->id ?? $request->ip();
        $route = $request->route()?->getName() ?? $request->path();
        
        return sha1($userId . '|' . $route);
    }

    /**
     * Get the maximum number of attempts for the given request
     */
    protected function getMaxAttempts(Request $request): int
    {
        if ($request->is('api/family-tree/*')) {
            return (int) env('RATE_LIMIT_FAMILY_TREE', 100);
        }

        if ($request->is('api/*')) {
            return (int) env('RATE_LIMIT_API', 60);
        }

        return (int) env('RATE_LIMIT_DEFAULT', 1000);
    }

    /**
     * Get the decay minutes for the given request
     */
    protected function getDecayMinutes(Request $request): int
    {
        if ($request->is('api/*')) {
            return 1; // 1 minute for API requests
        }

        return 1; // 1 minute default
    }

    /**
     * Log rate limit exceeded attempts
     */
    protected function logRateLimitExceeded(Request $request, string $key): void
    {
        Log::warning('Rate limit exceeded', [
            'key' => $key,
            'ip' => $request->ip(),
            'user_id' => $request->user()?->id,
            'user_agent' => $request->userAgent(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);
    }
}