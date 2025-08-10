<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequestValidationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate request size
        if ($request->header('Content-Length') > 10 * 1024 * 1024) { // 10MB limit
            Log::warning('Request size exceeded', [
                'ip' => $request->ip(),
                'user_id' => $request->user()?->id,
                'size' => $request->header('Content-Length'),
                'url' => $request->fullUrl(),
            ]);
            
            return response()->json(['error' => 'Request too large'], 413);
        }

        // Validate content type for POST/PUT/PATCH requests
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $contentType = $request->header('Content-Type');
            
            if (str_contains($request->path(), 'api/') && 
                !str_contains($contentType, 'application/json') && 
                !str_contains($contentType, 'multipart/form-data')) {
                
                Log::warning('Invalid content type for API request', [
                    'ip' => $request->ip(),
                    'user_id' => $request->user()?->id,
                    'content_type' => $contentType,
                    'url' => $request->fullUrl(),
                ]);
                
                return response()->json(['error' => 'Invalid content type'], 400);
            }
        }

        // Sanitize input data
        $this->sanitizeInput($request);

        $response = $next($request);

        return $response;
    }

    /**
     * Sanitize request input to prevent XSS and injection attacks
     */
    protected function sanitizeInput(Request $request): void
    {
        $input = $request->all();
        $sanitized = [];

        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Remove potentially dangerous content
                $value = $this->removeDangerousContent($value);
                
                // Limit string length
                if (strlen($value) > 10000) { // 10KB limit per field
                    $value = substr($value, 0, 10000);
                }
            }
            
            $sanitized[$key] = $value;
        }

        $request->merge($sanitized);
    }

    /**
     * Remove potentially dangerous content from input
     */
    protected function removeDangerousContent(string $input): string
    {
        // Remove script tags and event handlers
        $input = preg_replace('/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/mi', '', $input);
        $input = preg_replace('/on\w+\s*=\s*["\'][^"\']*["\']/i', '', $input);
        
        // Remove javascript: and data: URLs
        $input = preg_replace('/javascript:/i', '', $input);
        $input = preg_replace('/data:text\/html/i', '', $input);
        
        // Remove SQL injection patterns
        $sqlPatterns = [
            '/union\s+select/i',
            '/drop\s+table/i',
            '/delete\s+from/i',
            '/insert\s+into/i',
            '/update\s+set/i',
            '/alter\s+table/i',
            '/exec\s*\(/i',
            '/eval\s*\(/i',
        ];
        
        foreach ($sqlPatterns as $pattern) {
            $input = preg_replace($pattern, '', $input);
        }

        return $input;
    }
}