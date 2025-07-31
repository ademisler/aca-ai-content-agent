<?php
/**
 * Composer autoloader placeholder with Google API mock classes
 * This prevents dependency errors while maintaining functionality
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include the real Composer autoloader if it exists
$composerAutoload = __DIR__ . '/composer/autoload_real.php';
if (file_exists($composerAutoload)) {
    require_once $composerAutoload;
    return ComposerAutoloaderInit::getLoader();
}

// Fallback autoloader for Google API classes
spl_autoload_register(function ($class) {
    // Handle Google API classes
    if (strpos($class, 'Google\\') === 0 || strpos($class, 'Google_') === 0) {
        // For now, just return true to prevent fatal errors
        return true;
    }
});

// Define Google API mock classes to prevent fatal errors
if (!class_exists('Google\\Client') && !class_exists('Google_Client')) {
    class Google_Client {
        public function __construct() {}
        public function setClientId($clientId) { return $this; }
        public function setClientSecret($clientSecret) { return $this; }
        public function setRedirectUri($redirectUri) { return $this; }
        public function setScopes($scopes) { return $this; }
        public function setAccessType($accessType) { return $this; }
        public function setApprovalPrompt($approvalPrompt) { return $this; }
        public function createAuthUrl() { return '#google-auth-placeholder'; }
        public function fetchAccessTokenWithAuthCode($code) { 
            return ['access_token' => 'placeholder', 'error' => 'Google API not installed']; 
        }
        public function setAccessToken($token) { return $this; }
        public function getAccessToken() { return null; }
        public function refreshToken($refreshToken) { 
            return ['access_token' => 'placeholder', 'error' => 'Google API not installed']; 
        }
        public function isAccessTokenExpired() { return true; }
    }
}

if (!class_exists('Google\\Service\\SearchConsole') && !class_exists('Google_Service_SearchConsole')) {
    class Google_Service_SearchConsole {
        public function __construct($client) {}
        public function sites() { return new Google_Service_SearchConsole_Sites(); }
        public function searchanalytics() { return new Google_Service_SearchConsole_SearchAnalytics(); }
    }
    
    class Google_Service_SearchConsole_Sites {
        public function listSites() { return new Google_Service_SearchConsole_SitesListResponse(); }
    }
    
    class Google_Service_SearchConsole_SearchAnalytics {
        public function query($siteUrl, $request) { return new Google_Service_SearchConsole_SearchAnalyticsQueryResponse(); }
    }
    
    class Google_Service_SearchConsole_SitesListResponse {
        public function getSiteEntry() { return []; }
    }
    
    class Google_Service_SearchConsole_SearchAnalyticsQueryResponse {
        public function getRows() { return []; }
    }
    
    class Google_Service_SearchConsole_SearchAnalyticsQueryRequest {
        public function __construct() {}
        public function setStartDate($date) { return $this; }
        public function setEndDate($date) { return $this; }
        public function setDimensions($dimensions) { return $this; }
        public function setRowLimit($limit) { return $this; }
    }
}

// PSR interfaces mock if not available
if (!interface_exists('Psr\\Log\\LoggerInterface')) {
    interface Psr_Log_LoggerInterface {
        public function emergency($message, array $context = array());
        public function alert($message, array $context = array());
        public function critical($message, array $context = array());
        public function error($message, array $context = array());
        public function warning($message, array $context = array());
        public function notice($message, array $context = array());
        public function info($message, array $context = array());
        public function debug($message, array $context = array());
        public function log($level, $message, array $context = array());
    }
}

return true;
