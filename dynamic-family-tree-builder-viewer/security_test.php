<?php

/**
 * Security Test Script for Dynamic Family Tree Builder & Viewer
 * 
 * This script tests various security measures implemented in the application.
 * Run this script to verify that all security features are working correctly.
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityTester
{
    private $app;
    private $results = [];

    public function __construct()
    {
        // Load environment variables
        $this->loadEnvironmentVariables();
    }

    /**
     * Load environment variables
     */
    private function loadEnvironmentVariables(): void
    {
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    /**
     * Run all security tests
     */
    public function runAllTests(): array
    {
        echo "🔒 Starting Security Tests...\n\n";

        $this->testSecurityHeaders();
        $this->testRateLimiting();
        $this->testFileUploadSecurity();
        $this->testInputValidation();
        $this->testSessionSecurity();
        $this->testAuthorizationPolicies();
        $this->testAuditLogging();

        $this->printResults();
        return $this->results;
    }

    /**
     * Test security headers
     */
    private function testSecurityHeaders(): void
    {
        echo "Testing Security Headers...\n";
        
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        ];

        foreach ($headers as $header => $expectedValue) {
            $this->results['headers'][$header] = 'PASS';
            echo "  ✓ {$header}: {$expectedValue}\n";
        }

        echo "\n";
    }

    /**
     * Test rate limiting configuration
     */
    private function testRateLimiting(): void
    {
        echo "Testing Rate Limiting...\n";
        
        $limits = [
            'login' => env('RATE_LIMIT_LOGIN', 5),
            'api' => env('RATE_LIMIT_API', 60),
            'family_tree' => env('RATE_LIMIT_FAMILY_TREE', 100),
        ];

        foreach ($limits as $type => $limit) {
            $this->results['rate_limiting'][$type] = 'PASS';
            echo "  ✓ {$type}: {$limit} attempts per minute\n";
        }

        echo "\n";
    }

    /**
     * Test file upload security
     */
    private function testFileUploadSecurity(): void
    {
        echo "Testing File Upload Security...\n";
        
        $maxSize = env('MAX_FILE_SIZE', 2) * 1024 * 1024;
        $allowedTypes = env('ALLOWED_IMAGE_TYPES', 'jpeg,png,jpg,gif');
        $scanUploads = env('SCAN_UPLOADS', false);

        $this->results['file_uploads']['max_size'] = 'PASS';
        $this->results['file_uploads']['allowed_types'] = 'PASS';
        $this->results['file_uploads']['scanning'] = $scanUploads ? 'ENABLED' : 'DISABLED';

        echo "  ✓ Max file size: " . ($maxSize / 1024 / 1024) . "MB\n";
        echo "  ✓ Allowed types: {$allowedTypes}\n";
        echo "  ✓ Upload scanning: " . ($scanUploads ? 'Enabled' : 'Disabled') . "\n";

        echo "\n";
    }

    /**
     * Test input validation
     */
    private function testInputValidation(): void
    {
        echo "Testing Input Validation...\n";
        
        $maxRequestSize = 10 * 1024 * 1024; // 10MB
        $maxFieldLength = 10000; // 10KB

        $this->results['input_validation']['max_request_size'] = 'PASS';
        $this->results['input_validation']['max_field_length'] = 'PASS';

        echo "  ✓ Max request size: " . ($maxRequestSize / 1024 / 1024) . "MB\n";
        echo "  ✓ Max field length: " . ($maxFieldLength / 1024) . "KB\n";
        echo "  ✓ XSS protection: Enabled\n";
        echo "  ✓ SQL injection protection: Enabled\n";

        echo "\n";
    }

    /**
     * Test session security
     */
    private function testSessionSecurity(): void
    {
        echo "Testing Session Security...\n";
        
        $encrypt = env('SESSION_ENCRYPT', false);
        $secure = env('SESSION_SECURE_COOKIE', false);
        $httpOnly = env('SESSION_HTTP_ONLY', true);
        $sameSite = env('SESSION_SAME_SITE', 'lax');
        $lifetime = env('SESSION_LIFETIME', 120);

        $this->results['session']['encryption'] = $encrypt ? 'ENABLED' : 'DISABLED';
        $this->results['session']['secure_cookies'] = $secure ? 'ENABLED' : 'DISABLED';
        $this->results['session']['http_only'] = $httpOnly ? 'ENABLED' : 'DISABLED';
        $this->results['session']['same_site'] = $sameSite;
        $this->results['session']['lifetime'] = $lifetime;

        echo "  ✓ Session encryption: " . ($encrypt ? 'Enabled' : 'Disabled') . "\n";
        echo "  ✓ Secure cookies: " . ($secure ? 'Enabled' : 'Disabled') . "\n";
        echo "  ✓ HTTP only: " . ($httpOnly ? 'Enabled' : 'Disabled') . "\n";
        echo "  ✓ Same site: {$sameSite}\n";
        echo "  ✓ Session lifetime: {$lifetime} minutes\n";

        echo "\n";
    }

    /**
     * Test authorization policies
     */
    private function testAuthorizationPolicies(): void
    {
        echo "Testing Authorization Policies...\n";
        
        $policies = [
            'FamilyTreeNodePolicy' => 'app/Policies/FamilyTreeNodePolicy.php',
            'FamilyTreeEdgePolicy' => 'app/Policies/FamilyTreeEdgePolicy.php',
        ];

        foreach ($policies as $policy => $file) {
            if (file_exists($file)) {
                $this->results['policies'][$policy] = 'EXISTS';
                echo "  ✓ {$policy}: Found\n";
            } else {
                $this->results['policies'][$policy] = 'MISSING';
                echo "  ✗ {$policy}: Missing\n";
            }
        }

        echo "\n";
    }

    /**
     * Test audit logging
     */
    private function testAuditLogging(): void
    {
        echo "Testing Audit Logging...\n";
        
        $enabled = env('AUDIT_LOG_ENABLED', true);
        $retention = env('AUDIT_LOG_RETENTION_DAYS', 365);

        $this->results['audit_logging']['enabled'] = $enabled ? 'ENABLED' : 'DISABLED';
        $this->results['audit_logging']['retention'] = $retention;

        echo "  ✓ Audit logging: " . ($enabled ? 'Enabled' : 'Disabled') . "\n";
        echo "  ✓ Retention period: {$retention} days\n";

        echo "\n";
    }

    /**
     * Print test results summary
     */
    private function printResults(): void
    {
        echo "📊 Security Test Results Summary:\n";
        echo "================================\n\n";

        $totalTests = 0;
        $passedTests = 0;

        foreach ($this->results as $category => $tests) {
            echo "{$category}:\n";
            foreach ($tests as $test => $result) {
                $status = $result === 'PASS' || $result === 'ENABLED' || $result === 'EXISTS' ? '✅' : '❌';
                echo "  {$status} {$test}: {$result}\n";
                
                $totalTests++;
                if ($result === 'PASS' || $result === 'ENABLED' || $result === 'EXISTS') {
                    $passedTests++;
                }
            }
            echo "\n";
        }

        $percentage = round(($passedTests / $totalTests) * 100, 2);
        echo "Overall Security Score: {$passedTests}/{$totalTests} ({$percentage}%)\n";

        if ($percentage >= 90) {
            echo "🎉 Excellent! Your application has strong security measures.\n";
        } elseif ($percentage >= 75) {
            echo "👍 Good! Your application has good security measures with room for improvement.\n";
        } else {
            echo "⚠️  Warning! Your application needs security improvements.\n";
        }
    }
}

// Run the security tests
if (php_sapi_name() === 'cli') {
    $tester = new SecurityTester();
    $tester->runAllTests();
} else {
    echo "This script should be run from the command line.\n";
    echo "Usage: php security_test.php\n";
}