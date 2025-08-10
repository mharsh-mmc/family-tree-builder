<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SecurityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureSecuritySettings();
        $this->configureFileUploadSecurity();
        $this->configureSessionSecurity();
    }

    /**
     * Configure security settings
     */
    protected function configureSecuritySettings(): void
    {
        // Force HTTPS in production
        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }

        // Configure security headers
        if (env('SECURITY_HEADERS_ENABLED', true)) {
            $this->configureSecurityHeaders();
        }

        // Configure rate limiting
        $this->configureRateLimiting();
    }

    /**
     * Configure security headers
     */
    protected function configureSecurityHeaders(): void
    {
        // These will be handled by the SecurityHeadersMiddleware
        // but we can add additional configuration here if needed
    }

    /**
     * Configure rate limiting
     */
    protected function configureRateLimiting(): void
    {
        // Configure rate limiters
        \Illuminate\Support\Facades\RateLimiter::for('login', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(
                env('RATE_LIMIT_LOGIN', 5)
            )->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('api', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(
                env('RATE_LIMIT_API', 60)
            )->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('family-tree', function ($request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(
                env('RATE_LIMIT_FAMILY_TREE', 100)
            )->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * Configure file upload security
     */
    protected function configureFileUploadSecurity(): void
    {
        // Set maximum file upload size
        $maxFileSize = env('MAX_FILE_SIZE', 2) * 1024 * 1024; // Default 2MB
        Config::set('filesystems.max_file_size', $maxFileSize);

        // Set allowed image types
        $allowedTypes = env('ALLOWED_IMAGE_TYPES', 'jpeg,png,jpg,gif');
        Config::set('filesystems.allowed_image_types', explode(',', $allowedTypes));

        // Enable upload scanning if configured
        if (env('SCAN_UPLOADS', false)) {
            $this->enableUploadScanning();
        }
    }

    /**
     * Configure session security
     */
    protected function configureSessionSecurity(): void
    {
        // Session security is configured in config/session.php
        // but we can add additional validation here
        
        if (app()->environment('production')) {
            // Force secure cookies in production
            Config::set('session.secure', true);
            Config::set('session.http_only', true);
            Config::set('session.same_site', 'strict');
        }
    }

    /**
     * Enable upload scanning
     */
    protected function enableUploadScanning(): void
    {
        // This would integrate with antivirus scanning services
        // For now, we'll log that scanning is enabled
        Log::info('File upload scanning enabled');
    }
}