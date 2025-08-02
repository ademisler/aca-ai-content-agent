<?php
/**
 * Google Search Console API Integration
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if vendor directory exists and Google classes are available
if (file_exists(ACA_PLUGIN_PATH . 'vendor/autoload.php')) {
    require_once ACA_PLUGIN_PATH . 'vendor/autoload.php';
    
    // Double check that Google classes are actually loaded
    if (class_exists('\Google\Client') && class_exists('\Google\Service\Webmasters')) {
        class ACA_Google_Search_Console {
    
        private $client;
        private $service;
        
        public function __construct() {
            $this->init_google_client();
        }
        
        /**
         * Initialize Google Client
         */
        private function init_google_client() {
            try {
                $this->client = new \Google\Client();
                $this->client->setApplicationName('AI Content Agent (ACA)');
                $this->client->setScopes([
                    'https://www.googleapis.com/auth/webmasters.readonly',
                    'https://www.googleapis.com/auth/webmasters',
                    'https://www.googleapis.com/auth/siteverification.verify_only',
                    'https://www.googleapis.com/auth/siteverification'
                ]);
                $this->client->setAccessType('offline');
                $this->client->setPrompt('select_account consent'); // Force consent screen for proper refresh token
                $this->client->setApprovalPrompt('force'); // Ensure refresh token is always returned
                
                // Get credentials from settings
                $settings = get_option('aca_settings', array());
                if (!empty($settings['gscClientId']) && !empty($settings['gscClientSecret'])) {
                    $this->client->setClientId($settings['gscClientId']);
                    $this->client->setClientSecret($settings['gscClientSecret']);
                }
                
                // Set redirect URI
                $this->client->setRedirectUri($this->get_redirect_uri());
                
                // Set access token if available
                $tokens = get_option('aca_gsc_tokens');
                if ($tokens && is_array($tokens)) {
                    $this->client->setAccessToken($tokens);
                    
                    // Check if token is expired and refresh if needed
                    if ($this->client->isAccessTokenExpired()) {
                        $this->refresh_token();
                    }
                    
                    // Initialize Search Console service
                    if ($this->client->getAccessToken()) {
                        $this->service = new \Google\Service\Webmasters($this->client);
                    }
                }
            } catch (Exception $e) {
                aca_debug_log('GSC Init Error: ' . $e->getMessage());
                $this->client = null;
                $this->service = null;
            }
        }
        
        /**
         * Get redirect URI for OAuth
         */
        private function get_redirect_uri() {
            return admin_url('admin.php?page=ai-content-agent&gsc_auth=callback');
        }
        
        /**
         * Get authorization URL
         */
        public function get_auth_url() {
            if (!$this->client) {
                return new WP_Error('client_error', 'Google client not initialized');
            }
            
            return $this->client->createAuthUrl();
        }
        
        /**
         * Handle OAuth callback
         */
        public function handle_oauth_callback($auth_code) {
            if (!$this->client) {
                return new WP_Error('client_error', 'Google client not initialized');
            }
            
            try {
                $token = $this->client->fetchAccessTokenWithAuthCode($auth_code);
                
                if (isset($token['error'])) {
                    return new WP_Error('oauth_error', $token['error_description']);
                }
                
                // Store complete token array including refresh token
                update_option('aca_gsc_tokens', $token);
                
                // Initialize service
                $this->service = new \Google\Service\Webmasters($this->client);
                
                // Get user info
                $user_info = $this->get_user_info();
                if (!is_wp_error($user_info)) {
                    // Update settings with user info
                    $settings = get_option('aca_settings', array());
                    $settings['searchConsoleUser'] = array('email' => $user_info['email']);
                    update_option('aca_settings', $settings);
                }
                
                return true;
                
            } catch (Exception $e) {
                aca_debug_log('GSC OAuth Error: ' . $e->getMessage());
                return new WP_Error('oauth_error', $e->getMessage());
            }
        }
        
        /**
         * Enhanced token refresh with retry mechanism
         * Note: Maintains original method signature (no parameters, no return value)
         */
        private function refresh_token() {
            try {
                // Get current stored tokens to preserve all data
                $current_tokens = get_option('aca_gsc_tokens', array());
                
                // Check if proactive refresh is needed (only if forced or near expiry)
                if (isset($current_tokens['expires_in'], $current_tokens['created'])) {
                    $expires_at = $current_tokens['created'] + $current_tokens['expires_in'] - 300; // 5 min buffer
                    if (time() < $expires_at) {
                        // Token still valid, no need to refresh
                        return;
                    }
                }
                
                $refresh_token = $this->client->getRefreshToken();
                
                if (!$refresh_token && isset($current_tokens['refresh_token'])) {
                    // Fallback to stored refresh token if client doesn't have it
                    $refresh_token = $current_tokens['refresh_token'];
                    $this->client->setAccessToken($current_tokens);
                }
                
                if ($refresh_token) {
                    // Retry mechanism with exponential backoff
                    $max_retries = 3;
                    $retry_delay = 1;
                    $last_error = null;
                    
                    for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
                        try {
                            $new_tokens = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
                            
                            if (isset($new_tokens['error'])) {
                                throw new Exception($new_tokens['error_description'] ?? $new_tokens['error']);
                            }
                            
                            // Success - preserve refresh token and add metadata
                            if (!isset($new_tokens['refresh_token'])) {
                                $new_tokens['refresh_token'] = $refresh_token;
                            }
                            
                            $new_tokens['created'] = time();
                            $new_tokens['last_refresh'] = time();
                            
                            // Preserve other token data that might exist
                            $merged_tokens = array_merge($current_tokens, $new_tokens);
                            
                            update_option('aca_gsc_tokens', $merged_tokens);
                            
                            // Reset failure count on success
                            delete_option('aca_gsc_refresh_failures');
                            
                            // Clear validation cache since we have new tokens
                            $this->clear_validation_cache();
                            
                            aca_debug_log("GSC: Successfully refreshed access token on attempt $attempt");
                            return; // Success - exit method
                            
                        } catch (Exception $e) {
                            $last_error = $e->getMessage();
                            aca_debug_log("GSC: Token refresh attempt $attempt failed: " . $last_error);
                            
                            if ($attempt < $max_retries) {
                                sleep($retry_delay);
                                $retry_delay *= 2;
                            }
                        }
                    }
                    
                    // All retries failed
                    $this->handle_refresh_failure($last_error);
                    
                } else {
                    aca_debug_log('GSC: No refresh token available in client or stored tokens');
                    $this->handle_refresh_failure('No refresh token available');
                }
                
            } catch (Exception $e) {
                aca_debug_log('GSC Token Refresh Error: ' . $e->getMessage());
                $this->handle_refresh_failure($e->getMessage());
            }
        }
        
        /**
         * Handle token refresh failures (new method - add after refresh_token method)
         */
        private function handle_refresh_failure($error_message) {
            $failure_count = get_option('aca_gsc_refresh_failures', 0) + 1;
            update_option('aca_gsc_refresh_failures', $failure_count);
            
            aca_debug_log("GSC: Token refresh failure #$failure_count - $error_message");
            
            // After 3 consecutive failures, trigger re-authentication notice
            if ($failure_count >= 3) {
                $this->trigger_reauth_notice($error_message);
            }
        }
        
        /**
         * Trigger re-authentication notice (new method - add after handle_refresh_failure)
         */
        private function trigger_reauth_notice($error_message) {
            set_transient('aca_gsc_reauth_required', array(
                'error_message' => $error_message,
                'timestamp' => time()
            ), DAY_IN_SECONDS);
            
                            aca_debug_log("GSC: Re-authentication notice set due to: $error_message");
        }
        
        /**
         * Get user information
         */
        private function get_user_info() {
            if (!$this->client || !$this->client->getAccessToken()) {
                return new WP_Error('not_authenticated', 'Not authenticated');
            }
            
            // Ensure token is valid before making API calls
            $token_check = $this->ensure_valid_token();
            if (is_wp_error($token_check) || $token_check === false) {
                return is_wp_error($token_check) ? $token_check : new WP_Error('token_invalid', 'Token validation failed');
            }
            
            try {
                // Use OAuth2 service to get user info
                $oauth2 = new \Google\Service\Oauth2($this->client);
                $user_info = $oauth2->userinfo->get();
                
                return array(
                    'email' => $user_info->getEmail(),
                    'name' => $user_info->getName()
                );
                
            } catch (Exception $e) {
                aca_debug_log('GSC User Info Error: ' . $e->getMessage());
                return new WP_Error('api_error', $e->getMessage());
            }
        }
        
        /**
         * Get authentication status
         */
        public function get_auth_status() {
            $tokens = get_option('aca_gsc_tokens');
            $settings = get_option('aca_settings', array());
            
            return array(
                'is_authenticated' => !empty($tokens) && !empty($settings['searchConsoleUser']),
                'user' => isset($settings['searchConsoleUser']) ? $settings['searchConsoleUser'] : null,
                'has_credentials' => !empty($settings['gscClientId']) && !empty($settings['gscClientSecret'])
            );
        }
        
        /**
         * Disconnect from Google Search Console
         */
        public function disconnect() {
            delete_option('aca_gsc_tokens');
            
            $settings = get_option('aca_settings', array());
            $settings['searchConsoleUser'] = null;
            update_option('aca_settings', $settings);
            
            $this->service = null;
            
            return true;
        }
        
        /**
         * Get search analytics data
         */
        public function get_search_analytics($site_url, $start_date = null, $end_date = null, $dimensions = array('query'), $row_limit = 25) {
            if (!$this->service) {
                return new WP_Error('not_authenticated', 'Not authenticated with Google Search Console');
            }
            
            // Set default dates (last 30 days)
            if (!$start_date) {
                $start_date = gmdate('Y-m-d', strtotime('-30 days'));
            }
            if (!$end_date) {
                $end_date = gmdate('Y-m-d', strtotime('-1 day')); // Yesterday (GSC data has 1-day delay)
            }
            
            try {
                // Create search analytics query
                $request = new \Google\Service\Webmasters\SearchAnalyticsQueryRequest();
                $request->setStartDate($start_date);
                $request->setEndDate($end_date);
                $request->setDimensions($dimensions);
                $request->setRowLimit($row_limit);
                
                // Execute query
                $response = $this->service->searchanalytics->query($site_url, $request);
                $rows = $response->getRows();
                
                $results = array();
                if ($rows) {
                    foreach ($rows as $row) {
                        $results[] = array(
                            'keys' => $row->getKeys(),
                            'clicks' => $row->getClicks(),
                            'impressions' => $row->getImpressions(),
                            'ctr' => $row->getCtr(),
                            'position' => $row->getPosition()
                        );
                    }
                }
                
                return $results;
                
            } catch (Exception $e) {
                aca_debug_log('GSC Search Analytics Error: ' . $e->getMessage());
                return new WP_Error('api_error', $e->getMessage());
            }
        }
        
        /**
         * Get sites list
         */
        public function get_sites() {
            if (!$this->service) {
                return new WP_Error('not_authenticated', 'Not authenticated with Google Search Console');
            }
            
            // Ensure token is valid before making API calls
            $token_check = $this->ensure_valid_token();
            if (is_wp_error($token_check) || $token_check === false) {
                return is_wp_error($token_check) ? $token_check : new WP_Error('token_invalid', 'Token validation failed');
            }
            
            try {
                $sites_list = $this->service->sites->listSites();
                $sites = array();
                
                foreach ($sites_list->getSiteEntry() as $site) {
                    $sites[] = array(
                        'siteUrl' => $site->getSiteUrl(),
                        'permissionLevel' => $site->getPermissionLevel()
                    );
                }
                
                return $sites;
                
            } catch (Exception $e) {
                aca_debug_log('GSC Sites List Error: ' . $e->getMessage());
                return new WP_Error('api_error', $e->getMessage());
            }
        }
        
        /**
         * Get top queries for AI content generation
         */
        public function get_top_queries($site_url = null, $limit = 10) {
            // Auto-detect site URL if not provided
            if (!$site_url) {
                $site_url = home_url();
                // Add trailing slash if not present (GSC requirement)
                if (substr($site_url, -1) !== '/') {
                    $site_url .= '/';
                }
            }
            
            $analytics_data = $this->get_search_analytics($site_url, null, null, array('query'), $limit);
            
            if (is_wp_error($analytics_data)) {
                return $analytics_data;
            }
            
            $queries = array();
            foreach ($analytics_data as $row) {
                if (!empty($row['keys'][0]) && $row['clicks'] > 0) { // Only include queries with actual clicks
                    $queries[] = $row['keys'][0];
                }
            }
            
            return $queries;
        }
        
        /**
         * Get underperforming pages for AI content optimization
         */
        public function get_underperforming_pages($site_url = null, $limit = 10) {
            // Auto-detect site URL if not provided
            if (!$site_url) {
                $site_url = home_url();
                // Add trailing slash if not present (GSC requirement)
                if (substr($site_url, -1) !== '/') {
                    $site_url .= '/';
                }
            }
            
            $analytics_data = $this->get_search_analytics($site_url, null, null, array('page'), $limit * 2); // Get more to filter
            
            if (is_wp_error($analytics_data)) {
                return $analytics_data;
            }
            
            $pages = array();
            foreach ($analytics_data as $row) {
                if (!empty($row['keys'][0]) && $row['position'] > 10 && $row['impressions'] > 10) { // Pages ranking below position 10 with decent impressions
                    $pages[] = $row['keys'][0];
                    if (count($pages) >= $limit) {
                        break;
                    }
                }
            }
            
            return $pages;
        }
        
        /**
         * Get page performance data for a specific URL
         */
        public function get_page_performance($page_url) {
            if (!$this->service) {
                return new WP_Error('not_authenticated', 'Not authenticated with Google Search Console');
            }
            
            // Ensure token is valid before making API calls
            $token_check = $this->ensure_valid_token();
            if (is_wp_error($token_check) || $token_check === false) {
                return is_wp_error($token_check) ? $token_check : new WP_Error('token_invalid', 'Token validation failed');
            }
            
            // Get site URL
            $site_url = home_url();
            if (substr($site_url, -1) !== '/') {
                $site_url .= '/';
            }
            
            try {
                // Get analytics data for the specific page
                $analytics_data = $this->get_search_analytics($site_url, null, null, array('page'), 1000);
                
                if (is_wp_error($analytics_data)) {
                    return $analytics_data;
                }
                
                // Find data for the specific page
                foreach ($analytics_data as $row) {
                    if (!empty($row['keys'][0]) && $row['keys'][0] === $page_url) {
                        return array(
                            'url' => $row['keys'][0],
                            'clicks' => $row['clicks'],
                            'impressions' => $row['impressions'],
                            'ctr' => $row['ctr'],
                            'position' => $row['position'],
                            'performance_score' => $this->calculate_performance_score($row)
                        );
                    }
                }
                
                // Page not found in GSC data
                return array(
                    'url' => $page_url,
                    'clicks' => 0,
                    'impressions' => 0,
                    'ctr' => 0,
                    'position' => 0,
                    'performance_score' => 0
                );
                
            } catch (Exception $e) {
                aca_debug_log('GSC Page Performance Error: ' . $e->getMessage());
                return new WP_Error('api_error', $e->getMessage());
            }
        }
        
        /**
         * Calculate performance score based on GSC metrics
         */
        private function calculate_performance_score($row) {
            $score = 0;
            
            // CTR score (0-40 points)
            $ctr = $row['ctr'] * 100; // Convert to percentage
            if ($ctr >= 5) {
                $score += 40;
            } elseif ($ctr >= 2) {
                $score += 30;
            } elseif ($ctr >= 1) {
                $score += 20;
            } elseif ($ctr > 0) {
                $score += 10;
            }
            
            // Position score (0-40 points)
            if ($row['position'] <= 3) {
                $score += 40;
            } elseif ($row['position'] <= 10) {
                $score += 30;
            } elseif ($row['position'] <= 20) {
                $score += 20;
            } elseif ($row['position'] <= 50) {
                $score += 10;
            }
            
            // Impressions score (0-20 points)
            if ($row['impressions'] >= 1000) {
                $score += 20;
            } elseif ($row['impressions'] >= 100) {
                $score += 15;
            } elseif ($row['impressions'] >= 10) {
                $score += 10;
            } elseif ($row['impressions'] > 0) {
                $score += 5;
            }
            
            return min($score, 100); // Cap at 100
        }

        /**
         * Get data formatted for AI content generation
         */
        public function get_data_for_ai() {
            // Ensure we have proper authentication
            if (!$this->service) {
                aca_debug_log('GSC: Service not initialized for AI data');
                return false;
            }
            
            // Ensure token is valid before making API calls
            $token_check = $this->ensure_valid_token();
            if (is_wp_error($token_check) || $token_check === false) {
                $error_msg = is_wp_error($token_check) ? $token_check->get_error_message() : 'Token validation failed';
                aca_debug_log('GSC: Token validation failed for AI data: ' . $error_msg);
                return false;
            }
            
            $site_url = home_url();
            // Add trailing slash if not present (GSC requirement)
            if (substr($site_url, -1) !== '/') {
                $site_url .= '/';
            }
            
            $top_queries = $this->get_top_queries($site_url, 20);
            $underperforming_pages = $this->get_underperforming_pages($site_url, 10);
            
            if (is_wp_error($top_queries) || is_wp_error($underperforming_pages)) {
                aca_debug_log('GSC: Error fetching data - Queries: ' . (is_wp_error($top_queries) ? $top_queries->get_error_message() : 'OK') . 
                         ', Pages: ' . (is_wp_error($underperforming_pages) ? $underperforming_pages->get_error_message() : 'OK'));
                return false;
            }
            
            // Ensure we have meaningful data
            if (empty($top_queries) && empty($underperforming_pages)) {
                aca_debug_log('GSC: No meaningful data found for site: ' . $site_url);
                return false;
            }
            
            return array(
                'topQueries' => $top_queries ?: array(),
                'underperformingPages' => $underperforming_pages ?: array(),
                'site_url' => $site_url,
                'data_date' => gmdate('Y-m-d H:i:s')
            );
        }

        /**
         * Proactive token refresh - call before API requests
         * This is a new method that returns boolean for success/failure
         */
        public function ensure_valid_token() {
            $current_tokens = get_option('aca_gsc_tokens');
            
            if (!$current_tokens) {
                return false;
            }
            
            // Check if token expires within next 10 minutes
            if (isset($current_tokens['expires_in'], $current_tokens['created'])) {
                $expires_at = $current_tokens['created'] + $current_tokens['expires_in'] - 600; // 10 min buffer
                
                if (time() >= $expires_at) {
                    // Use a lock mechanism to prevent race conditions during token refresh
                    $lock_key = 'aca_token_refresh_lock';
                    $lock_timeout = 30; // 30 seconds max lock
                    
                    if (get_transient($lock_key)) {
                        // Another process is already refreshing, wait and return current status
                        sleep(1);
                        $updated_tokens = get_option('aca_gsc_tokens');
                        return isset($updated_tokens['access_token']) && !empty($updated_tokens['access_token']);
                    }
                    
                    // Set lock before refresh
                    set_transient($lock_key, time(), $lock_timeout);
                    
                    try {
                        $this->refresh_token();
                        
                        // Check if refresh was successful
                        $updated_tokens = get_option('aca_gsc_tokens');
                        return isset($updated_tokens['access_token']) && !empty($updated_tokens['access_token']);
                    } finally {
                        // Always release lock
                        delete_transient($lock_key);
                    }
                }
            }
            
            return true;
        }
        
        /**
         * Validate OAuth scopes for current token with caching
         */
        public function validate_token_scopes() {
            $current_tokens = get_option('aca_gsc_tokens');
            
            if (!$current_tokens || !isset($current_tokens['access_token'])) {
                return new WP_Error('no_token', 'No access token available');
            }
            
            // Check cache first to avoid excessive HTTP requests
            $cache_key = 'aca_gsc_scope_validation_' . md5($current_tokens['access_token']);
            $cached_result = get_transient($cache_key);
            
            if ($cached_result !== false) {
                return $cached_result === 'valid' ? true : new WP_Error('cached_invalid', 'Token validation failed (cached)');
            }
            
            try {
                // Set the access token
                $this->client->setAccessToken($current_tokens);
                
                // Try to get token info to check scopes
                $token_info_url = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' . $current_tokens['access_token'];
                
                $response = wp_remote_get($token_info_url, array(
                    'timeout' => 10, // Reduced timeout for better performance
                    'headers' => array(
                        'User-Agent' => 'AI Content Agent (ACA)'
                    )
                ));
                
                if (is_wp_error($response)) {
                    return new WP_Error('scope_check_failed', 'Failed to validate token scopes: ' . $response->get_error_message());
                }
                
                $status_code = wp_remote_retrieve_response_code($response);
                if ($status_code !== 200) {
                    return new WP_Error('token_validation_failed', 'Token validation API returned status: ' . $status_code);
                }
                
                $body = wp_remote_retrieve_body($response);
                if (empty($body)) {
                    return new WP_Error('empty_response', 'Empty response from token validation API');
                }
                
                $token_data = json_decode($body, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return new WP_Error('json_error', 'Invalid JSON in token validation response: ' . json_last_error_msg());
                }
                
                if (!$token_data || isset($token_data['error'])) {
                    return new WP_Error('invalid_token', 'Token validation failed: ' . ($token_data['error_description'] ?? 'Unknown error'));
                }
                
                // Check if we have the required scopes
                $required_scopes = array(
                    'https://www.googleapis.com/auth/webmasters.readonly',
                    'https://www.googleapis.com/auth/webmasters'
                );
                
                $granted_scopes = isset($token_data['scope']) ? explode(' ', $token_data['scope']) : array();
                $missing_scopes = array();
                
                foreach ($required_scopes as $required_scope) {
                    if (!in_array($required_scope, $granted_scopes)) {
                        $missing_scopes[] = $required_scope;
                    }
                }
                
                if (!empty($missing_scopes)) {
                    aca_debug_log('GSC: Missing OAuth scopes: ' . implode(', ', $missing_scopes));
                    
                    // Trigger re-authentication with expanded scopes
                    $this->trigger_scope_reauth_notice($missing_scopes);
                    
                    return new WP_Error('insufficient_scopes', 'Token missing required scopes', array(
                        'missing_scopes' => $missing_scopes,
                        'granted_scopes' => $granted_scopes
                    ));
                }
                
                aca_debug_log('GSC: Token scope validation successful');
                
                // Cache successful validation for 10 minutes
                set_transient($cache_key, 'valid', 600);
                
                return true;
                
            } catch (Exception $e) {
                aca_debug_log('GSC: Scope validation error: ' . $e->getMessage());
                
                // Cache failed validation for 1 minute to prevent spam
                set_transient($cache_key, 'invalid', 60);
                
                return new WP_Error('scope_validation_error', $e->getMessage());
            }
        }
        
        /**
         * Trigger re-authentication notice for insufficient scopes
         */
        private function trigger_scope_reauth_notice($missing_scopes) {
            set_transient('aca_gsc_scope_reauth_required', array(
                'missing_scopes' => $missing_scopes,
                'timestamp' => time(),
                'reason' => 'insufficient_oauth_scopes'
            ), DAY_IN_SECONDS);
            
            aca_debug_log("GSC: Scope re-authentication notice set for missing scopes: " . implode(', ', $missing_scopes));
        }
        
        /**
         * Check if operation requires specific scopes
         */
        public function check_operation_permissions($operation) {
            $scope_requirements = array(
                'read_data' => array('https://www.googleapis.com/auth/webmasters.readonly'),
                'submit_sitemap' => array('https://www.googleapis.com/auth/webmasters'),
                'site_verification' => array('https://www.googleapis.com/auth/siteverification'),
                'manage_sites' => array('https://www.googleapis.com/auth/webmasters')
            );
            
            if (!isset($scope_requirements[$operation])) {
                return true; // Unknown operation, assume it's allowed
            }
            
            $validation_result = $this->validate_token_scopes();
            
            if (is_wp_error($validation_result)) {
                return $validation_result;
            }
            
            return true;
        }
        
        /**
         * Clear validation cache when tokens are refreshed
         */
        private function clear_validation_cache() {
            global $wpdb;
            
            // Clear all validation cache transients
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_aca_gsc_scope_validation_%'");
            $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_aca_gsc_scope_validation_%'");
            
            aca_debug_log('GSC: Cleared validation cache after token refresh');
        }
    }
    } else {
        // Create a dummy class when Google classes are not available
        class ACA_Google_Search_Console {
            public function __construct() {
                aca_debug_log('GSC Error: Google API client library not installed. Run: composer install');
            }
            
            public function get_auth_status() {
                return array('connected' => false, 'error' => 'Dependencies not installed');
            }
            
            public function get_auth_url() {
                return new WP_Error('gsc_error', 'Google API client library not installed.');
            }
            
            public function handle_oauth_callback($code) {
                return new WP_Error('gsc_error', 'Google API client library not installed.');
            }
            
            public function disconnect() {
                return new WP_Error('gsc_error', 'Google API client library not installed.');
            }
            
            public function get_sites() {
                return new WP_Error('gsc_error', 'Google API client library not installed.');
            }
            
            public function get_data_for_ai() {
                return false;
            }
            
                    public function get_page_performance($page_url) {
            // Return empty performance data instead of WP_Error to maintain compatibility  
            return array(
                'url' => $page_url,
                'clicks' => 0,
                'impressions' => 0,
                'ctr' => 0,
                'position' => 0,
                'performance_score' => 0
            );
        }
        }
    }
} else {
    // Create a dummy class when vendor directory doesn't exist
    class ACA_Google_Search_Console {
        public function __construct() {
            aca_debug_log('GSC Error: Google API client library not installed. Run: composer install');
        }
        
        public function get_auth_status() {
            return array('connected' => false, 'error' => 'Dependencies not installed');
        }
        
        public function get_auth_url() {
            return new WP_Error('gsc_error', 'Google API client library not installed.');
        }
        
        public function handle_oauth_callback($code) {
            return new WP_Error('gsc_error', 'Google API client library not installed.');
        }
        
        public function disconnect() {
            return new WP_Error('gsc_error', 'Google API client library not installed.');
        }
        
        public function get_sites() {
            return new WP_Error('gsc_error', 'Google API client library not installed.');
        }
        
        public function get_data_for_ai() {
            return false;
        }
        
        public function get_page_performance($page_url) {
            // Return empty performance data instead of WP_Error to maintain compatibility
            return array(
                'url' => $page_url,
                'clicks' => 0,
                'impressions' => 0,
                'ctr' => 0,
                'position' => 0,
                'performance_score' => 0
            );
        }
    }
}