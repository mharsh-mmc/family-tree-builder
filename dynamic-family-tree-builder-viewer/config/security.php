<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | application including headers, rate limiting, and file upload security.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Configure security headers that will be applied to all responses.
    |
    */

    'headers' => [
        'enabled' => env('SECURITY_HEADERS_ENABLED', true),
        
        'x_content_type_options' => 'nosniff',
        'x_frame_options' => 'DENY',
        'x_xss_protection' => '1; mode=block',
        'referrer_policy' => 'strict-origin-when-cross-origin',
        'permissions_policy' => 'geolocation=(), microphone=(), camera=()',
        
        'hsts' => [
            'enabled' => env('HSTS_ENABLED', true),
            'max_age' => env('HSTS_MAX_AGE', 31536000), // 1 year
            'include_subdomains' => true,
            'preload' => true,
        ],
        
        'csp' => [
            'enabled' => env('CSP_ENABLED', true),
            'default_src' => ["'self'"],
            'script_src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
            'style_src' => ["'self'", "'unsafe-inline'"],
            'img_src' => ["'self'", 'data:', 'blob:'],
            'font_src' => ["'self'"],
            'connect_src' => ["'self'"],
            'frame_ancestors' => ["'none'"],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for different types of requests.
    |
    */

    'rate_limiting' => [
        'login' => [
            'attempts' => env('RATE_LIMIT_LOGIN', 5),
            'decay_minutes' => 1,
        ],
        
        'api' => [
            'attempts' => env('RATE_LIMIT_API', 60),
            'decay_minutes' => 1,
        ],
        
        'family_tree' => [
            'attempts' => env('RATE_LIMIT_FAMILY_TREE', 100),
            'decay_minutes' => 1,
        ],
        
        'default' => [
            'attempts' => env('RATE_LIMIT_DEFAULT', 1000),
            'decay_minutes' => 1,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    |
    | Configure security settings for file uploads.
    |
    */

    'file_uploads' => [
        'max_size' => env('MAX_FILE_SIZE', 2) * 1024 * 1024, // 2MB default
        
        'allowed_types' => [
            'images' => explode(',', env('ALLOWED_IMAGE_TYPES', 'jpeg,png,jpg,gif')),
        ],
        
        'scan_uploads' => env('SCAN_UPLOADS', false),
        
        'validation' => [
            'check_mime_type' => true,
            'check_file_content' => true,
            'max_dimensions' => [
                'width' => 5000,
                'height' => 5000,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    |
    | Configure session security settings.
    |
    */

    'session' => [
        'encrypt' => env('SESSION_ENCRYPT', false),
        'secure_cookie' => env('SESSION_SECURE_COOKIE', false),
        'http_only' => env('SESSION_HTTP_ONLY', true),
        'same_site' => env('SESSION_SAME_SITE', 'lax'),
        'lifetime' => env('SESSION_LIFETIME', 120),
        'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    |
    | Configure audit logging for security events.
    |
    */

    'audit_logging' => [
        'enabled' => env('AUDIT_LOG_ENABLED', true),
        'retention_days' => env('AUDIT_LOG_RETENTION_DAYS', 365),
        
        'events' => [
            'authentication' => true,
            'authorization' => true,
            'data_access' => true,
            'data_modification' => true,
            'file_uploads' => true,
            'rate_limit_exceeded' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | CSRF Protection
    |--------------------------------------------------------------------------
    |
    | Configure CSRF protection settings.
    |
    */

    'csrf' => [
        'enabled' => true,
        'except' => [
            // Add routes that should be excluded from CSRF protection
            // 'api/*', // Uncomment if you want to exclude all API routes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    |
    | Configure input validation and sanitization.
    |
    */

    'input_validation' => [
        'max_request_size' => 10 * 1024 * 1024, // 10MB
        
        'sanitization' => [
            'remove_scripts' => true,
            'remove_event_handlers' => true,
            'remove_dangerous_urls' => true,
            'remove_sql_patterns' => true,
        ],
        
        'field_limits' => [
            'max_string_length' => 10000, // 10KB per field
        ],
    ],

];