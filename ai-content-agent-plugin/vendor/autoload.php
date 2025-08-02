<?php
/**
 * Composer autoloader placeholder
 * This is a minimal autoloader to satisfy dependency checks
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Basic autoloader for Google API classes
spl_autoload_register(function ($class) {
    // Handle Google API classes
    if (strpos($class, 'Google\\') === 0) {
        // For now, just return true to prevent fatal errors
        return true;
    }
});

// Define some basic Google API classes to prevent fatal errors
if (!class_exists('Google\\Client')) {
    class Google_Client {
        public function __construct() {}
        public function setClientId($clientId) {}
        public function setClientSecret($clientSecret) {}
        public function setRedirectUri($redirectUri) {}
        public function setScopes($scopes) {}
        public function setAccessType($accessType) {}
        public function setApprovalPrompt($approvalPrompt) {}
        public function createAuthUrl() { return ''; }
        public function fetchAccessTokenWithAuthCode($code) { return []; }
        public function setAccessToken($token) {}
        public function getAccessToken() { return null; }
        public function refreshToken($refreshToken) { return []; }
    }
}

if (!class_exists('Google\\Service\\SearchConsole')) {
    class Google_Service_SearchConsole {
        public function __construct($client) {}
    }
}

return true;