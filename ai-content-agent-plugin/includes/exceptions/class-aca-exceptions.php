<?php
/**
 * Custom Exception System for AI Content Agent
 * 
 * Provides comprehensive exception handling with custom exception types,
 * error reporting, logging, and recovery mechanisms.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Base ACA Exception
 */
abstract class ACA_Exception extends Exception {
    
    /**
     * Error context data
     */
    protected $context = [];
    
    /**
     * Error severity level
     */
    protected $severity = 'error';
    
    /**
     * Whether error should be logged
     */
    protected $should_log = true;
    
    /**
     * Whether error should be reported to external services
     */
    protected $should_report = false;
    
    /**
     * Constructor
     */
    public function __construct($message = '', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
        
        if ($this->should_log) {
            $this->logError();
        }
        
        if ($this->should_report) {
            $this->reportError();
        }
    }
    
    /**
     * Get error context
     */
    public function getContext(): array {
        return $this->context;
    }
    
    /**
     * Get error severity
     */
    public function getSeverity(): string {
        return $this->severity;
    }
    
    /**
     * Set error context
     */
    public function setContext(array $context): self {
        $this->context = array_merge($this->context, $context);
        return $this;
    }
    
    /**
     * Log error to database
     */
    protected function logError(): void {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_error_logs';
        
        $wpdb->insert($table_name, [
            'error_type' => get_class($this),
            'error_message' => $this->getMessage(),
            'error_data' => json_encode([
                'code' => $this->getCode(),
                'file' => $this->getFile(),
                'line' => $this->getLine(),
                'trace' => $this->getTraceAsString(),
                'context' => $this->context,
                'severity' => $this->severity
            ]),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'url' => $_SERVER['REQUEST_URI'] ?? '',
            'user_id' => get_current_user_id() ?: null,
            'ip_address' => $this->getClientIP()
        ]);
    }
    
    /**
     * Report error to external services
     */
    protected function reportError(): void {
        // Can be extended to report to Sentry, Bugsnag, etc.
        do_action('aca_error_reported', $this);
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP(): string {
        $ip_keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
    
    /**
     * Convert exception to array
     */
    public function toArray(): array {
        return [
            'type' => get_class($this),
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'context' => $this->context,
            'severity' => $this->severity,
            'timestamp' => current_time('mysql')
        ];
    }
}

/**
 * API Related Exceptions
 */
class ACA_API_Exception extends ACA_Exception {
    protected $severity = 'error';
    protected $should_report = true;
}

class ACA_API_Rate_Limit_Exception extends ACA_API_Exception {
    protected $severity = 'warning';
    
    public function __construct($message = 'API rate limit exceeded', $code = 429, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_API_Authentication_Exception extends ACA_API_Exception {
    protected $severity = 'critical';
    
    public function __construct($message = 'API authentication failed', $code = 401, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_API_Quota_Exceeded_Exception extends ACA_API_Exception {
    protected $severity = 'warning';
    
    public function __construct($message = 'API quota exceeded', $code = 402, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_API_Service_Unavailable_Exception extends ACA_API_Exception {
    protected $severity = 'error';
    
    public function __construct($message = 'API service unavailable', $code = 503, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Content Generation Exceptions
 */
class ACA_Content_Generation_Exception extends ACA_Exception {
    protected $severity = 'error';
    protected $should_report = true;
}

class ACA_Content_Quality_Exception extends ACA_Content_Generation_Exception {
    protected $severity = 'warning';
    
    public function __construct($message = 'Generated content quality below threshold', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Content_Moderation_Exception extends ACA_Content_Generation_Exception {
    protected $severity = 'critical';
    
    public function __construct($message = 'Content failed moderation check', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Content_Length_Exception extends ACA_Content_Generation_Exception {
    protected $severity = 'warning';
    
    public function __construct($message = 'Generated content length outside acceptable range', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Database Exceptions
 */
class ACA_Database_Exception extends ACA_Exception {
    protected $severity = 'critical';
    protected $should_report = true;
}

class ACA_Database_Connection_Exception extends ACA_Database_Exception {
    public function __construct($message = 'Database connection failed', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Database_Query_Exception extends ACA_Database_Exception {
    public function __construct($message = 'Database query failed', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Database_Migration_Exception extends ACA_Database_Exception {
    public function __construct($message = 'Database migration failed', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Validation Exceptions
 */
class ACA_Validation_Exception extends ACA_Exception {
    protected $severity = 'warning';
    protected $should_log = false; // Usually not critical enough to log
    
    /**
     * Validation errors
     */
    private $errors = [];
    
    public function __construct($message = 'Validation failed', $code = 422, Exception $previous = null, array $errors = []) {
        $this->errors = $errors;
        parent::__construct($message, $code, $previous, ['validation_errors' => $errors]);
    }
    
    public function getErrors(): array {
        return $this->errors;
    }
    
    public function hasError(string $field): bool {
        return isset($this->errors[$field]);
    }
    
    public function getError(string $field): ?string {
        return $this->errors[$field] ?? null;
    }
}

/**
 * License Exceptions
 */
class ACA_License_Exception extends ACA_Exception {
    protected $severity = 'warning';
    protected $should_report = true;
}

class ACA_License_Expired_Exception extends ACA_License_Exception {
    public function __construct($message = 'License has expired', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_License_Invalid_Exception extends ACA_License_Exception {
    public function __construct($message = 'Invalid license key', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_License_Quota_Exceeded_Exception extends ACA_License_Exception {
    public function __construct($message = 'License quota exceeded', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * File System Exceptions
 */
class ACA_File_System_Exception extends ACA_Exception {
    protected $severity = 'error';
    protected $should_report = true;
}

class ACA_File_Not_Found_Exception extends ACA_File_System_Exception {
    public function __construct($message = 'File not found', $code = 404, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_File_Permission_Exception extends ACA_File_System_Exception {
    public function __construct($message = 'File permission denied', $code = 403, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_File_Upload_Exception extends ACA_File_System_Exception {
    public function __construct($message = 'File upload failed', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Security Exceptions
 */
class ACA_Security_Exception extends ACA_Exception {
    protected $severity = 'critical';
    protected $should_report = true;
    protected $should_log = true;
}

class ACA_CSRF_Exception extends ACA_Security_Exception {
    public function __construct($message = 'CSRF token validation failed', $code = 403, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Unauthorized_Exception extends ACA_Security_Exception {
    public function __construct($message = 'Unauthorized access attempt', $code = 401, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Forbidden_Exception extends ACA_Security_Exception {
    public function __construct($message = 'Access forbidden', $code = 403, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Configuration Exceptions
 */
class ACA_Configuration_Exception extends ACA_Exception {
    protected $severity = 'error';
    protected $should_report = true;
}

class ACA_Missing_Configuration_Exception extends ACA_Configuration_Exception {
    public function __construct($message = 'Required configuration missing', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

class ACA_Invalid_Configuration_Exception extends ACA_Configuration_Exception {
    public function __construct($message = 'Invalid configuration value', $code = 0, Exception $previous = null, array $context = []) {
        parent::__construct($message, $code, $previous, $context);
    }
}

/**
 * Exception Handler
 */
class ACA_Exception_Handler {
    
    /**
     * Handle exception
     */
    public static function handle(Exception $exception): void {
        // Log to WordPress error log
        error_log(sprintf(
            '[ACA Exception] %s: %s in %s:%d',
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        ));
        
        // Handle based on exception type
        if ($exception instanceof ACA_Exception) {
            self::handleACAException($exception);
        } else {
            self::handleGenericException($exception);
        }
        
        // Trigger action for custom handling
        do_action('aca_exception_handled', $exception);
    }
    
    /**
     * Handle ACA-specific exceptions
     */
    private static function handleACAException(ACA_Exception $exception): void {
        $severity = $exception->getSeverity();
        $context = $exception->getContext();
        
        // Send appropriate HTTP status code
        if (!headers_sent()) {
            http_response_code($exception->getCode() ?: 500);
        }
        
        // Handle different severity levels
        switch ($severity) {
            case 'critical':
                self::handleCriticalError($exception);
                break;
            case 'error':
                self::handleError($exception);
                break;
            case 'warning':
                self::handleWarning($exception);
                break;
            default:
                self::handleInfo($exception);
        }
    }
    
    /**
     * Handle generic exceptions
     */
    private static function handleGenericException(Exception $exception): void {
        // Convert to ACA exception format
        $aca_exception = new class($exception->getMessage(), $exception->getCode(), $exception) extends ACA_Exception {
            protected $severity = 'error';
            protected $should_report = true;
        };
        
        self::handleACAException($aca_exception);
    }
    
    /**
     * Handle critical errors
     */
    private static function handleCriticalError(ACA_Exception $exception): void {
        // Notify administrators
        self::notifyAdministrators($exception);
        
        // Attempt graceful degradation
        self::attemptGracefulDegradation($exception);
        
        // Log to external monitoring service
        self::logToExternalService($exception);
    }
    
    /**
     * Handle regular errors
     */
    private static function handleError(ACA_Exception $exception): void {
        // Log for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Error: ' . $exception->getMessage());
        }
        
        // Attempt recovery
        self::attemptRecovery($exception);
    }
    
    /**
     * Handle warnings
     */
    private static function handleWarning(ACA_Exception $exception): void {
        // Log warning
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Warning: ' . $exception->getMessage());
        }
        
        // Continue execution with fallback
        self::provideFallback($exception);
    }
    
    /**
     * Handle info messages
     */
    private static function handleInfo(ACA_Exception $exception): void {
        // Just log for information
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Info: ' . $exception->getMessage());
        }
    }
    
    /**
     * Notify administrators of critical errors
     */
    private static function notifyAdministrators(ACA_Exception $exception): void {
        $admin_email = get_option('admin_email');
        if (!$admin_email) {
            return;
        }
        
        $subject = sprintf('[%s] Critical Error in AI Content Agent', get_bloginfo('name'));
        $message = sprintf(
            "A critical error occurred in AI Content Agent:\n\n" .
            "Error: %s\n" .
            "Type: %s\n" .
            "File: %s:%d\n" .
            "Time: %s\n" .
            "URL: %s\n" .
            "User: %s\n\n" .
            "Context: %s\n\n" .
            "Please check the error logs for more details.",
            $exception->getMessage(),
            get_class($exception),
            $exception->getFile(),
            $exception->getLine(),
            current_time('mysql'),
            $_SERVER['REQUEST_URI'] ?? 'N/A',
            wp_get_current_user()->user_login ?? 'Guest',
            json_encode($exception->getContext(), JSON_PRETTY_PRINT)
        );
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Attempt graceful degradation
     */
    private static function attemptGracefulDegradation(ACA_Exception $exception): void {
        // Disable AI features temporarily
        update_option('aca_emergency_mode', true, false);
        
        // Set expiration for emergency mode (1 hour)
        wp_schedule_single_event(time() + 3600, 'aca_disable_emergency_mode');
    }
    
    /**
     * Attempt error recovery
     */
    private static function attemptRecovery(ACA_Exception $exception): void {
        // Implement recovery strategies based on exception type
        if ($exception instanceof ACA_API_Exception) {
            self::recoverFromAPIError($exception);
        } elseif ($exception instanceof ACA_Database_Exception) {
            self::recoverFromDatabaseError($exception);
        }
    }
    
    /**
     * Recover from API errors
     */
    private static function recoverFromAPIError(ACA_API_Exception $exception): void {
        // Switch to fallback API if available
        $fallback_api = get_option('aca_fallback_api');
        if ($fallback_api) {
            update_option('aca_current_api', $fallback_api, false);
        }
        
        // Implement exponential backoff
        $retry_count = get_transient('aca_api_retry_count') ?: 0;
        $backoff_time = min(300, pow(2, $retry_count) * 10); // Max 5 minutes
        set_transient('aca_api_retry_count', $retry_count + 1, $backoff_time);
        set_transient('aca_api_backoff', true, $backoff_time);
    }
    
    /**
     * Recover from database errors
     */
    private static function recoverFromDatabaseError(ACA_Database_Exception $exception): void {
        // Attempt to repair tables
        global $wpdb;
        $tables = [
            $wpdb->prefix . 'aca_ideas',
            $wpdb->prefix . 'aca_activity_logs',
            $wpdb->prefix . 'aca_content_updates',
            $wpdb->prefix . 'aca_content_freshness'
        ];
        
        foreach ($tables as $table) {
            $result = $wpdb->query("REPAIR TABLE {$table}");
            if ($result === false) {
                error_log("ACA Plugin: Failed to repair table $table. Error: " . $wpdb->last_error);
            }
        }
    }
    
    /**
     * Provide fallback functionality
     */
    private static function provideFallback(ACA_Exception $exception): void {
        // Set fallback data in cache
        if ($exception instanceof ACA_Content_Generation_Exception) {
            wp_cache_set('aca_fallback_content', true, '', 300);
        }
    }
    
    /**
     * Log to external monitoring service
     */
    private static function logToExternalService(ACA_Exception $exception): void {
        // This can be extended to integrate with services like Sentry, Rollbar, etc.
        $external_logging_url = get_option('aca_external_logging_url');
        if (!$external_logging_url) {
            return;
        }
        
        $data = [
            'error' => $exception->toArray(),
            'environment' => defined('WP_DEBUG') && WP_DEBUG ? 'development' : 'production',
            'site_url' => home_url(),
            'wp_version' => get_bloginfo('version'),
            'plugin_version' => ACA_VERSION
        ];
        
        wp_remote_post($external_logging_url, [
            'body' => json_encode($data),
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 5,
            'blocking' => false // Don't wait for response
        ]);
    }
}

// Register exception handler
set_exception_handler([ACA_Exception_Handler::class, 'handle']);

// Hook to disable emergency mode
add_action('aca_disable_emergency_mode', function() {
    delete_option('aca_emergency_mode');
});

/**
 * Helper functions for throwing exceptions
 */

function aca_throw_api_exception(string $message, int $code = 0, array $context = []): void {
    throw new ACA_API_Exception($message, $code, null, $context);
}

function aca_throw_validation_exception(string $message, array $errors = []): void {
    throw new ACA_Validation_Exception($message, 422, null, $errors);
}

function aca_throw_security_exception(string $message, int $code = 403, array $context = []): void {
    throw new ACA_Security_Exception($message, $code, null, $context);
}

function aca_throw_database_exception(string $message, array $context = []): void {
    throw new ACA_Database_Exception($message, 0, null, $context);
}

function aca_throw_configuration_exception(string $message, array $context = []): void {
    throw new ACA_Configuration_Exception($message, 0, null, $context);
}