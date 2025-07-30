<?php
/**
 * Google Search Console API Integration
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if vendor directory exists
if (file_exists(ACA_PLUGIN_PATH . 'vendor/autoload.php')) {
    require_once ACA_PLUGIN_PATH . 'vendor/autoload.php';

    use Google\Client as Google_Client;
    use Google\Service\Webmasters as Google_Service_Webmasters;
    use Google\Service\Webmasters\SearchAnalyticsQueryRequest as Google_Service_Webmasters_SearchAnalyticsQueryRequest;
    use Google\Service\Oauth2;

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
            $this->client = new Google_Client();
            $this->client->setApplicationName('AI Content Agent (ACA)');
            $this->client->setScopes([
                'https://www.googleapis.com/auth/webmasters.readonly'
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
                    $this->service = new Google_Service_Webmasters($this->client);
                }
            }
        } catch (Exception $e) {
            error_log('ACA GSC Init Error: ' . $e->getMessage());
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
            $this->service = new Google_Service_Webmasters($this->client);
            
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
            error_log('GSC OAuth Error: ' . $e->getMessage());
            return new WP_Error('oauth_error', $e->getMessage());
        }
    }
    
    /**
     * Refresh access token
     */
    private function refresh_token() {
        try {
            $refresh_token = $this->client->getRefreshToken();
            if ($refresh_token) {
                $new_tokens = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
                
                // Preserve refresh token if not returned in new token response
                if (!isset($new_tokens['refresh_token']) && $refresh_token) {
                    $new_tokens['refresh_token'] = $refresh_token;
                }
                
                update_option('aca_gsc_tokens', $new_tokens);
                error_log('ACA GSC: Successfully refreshed access token');
            } else {
                error_log('ACA GSC: No refresh token available');
            }
        } catch (Exception $e) {
            error_log('ACA GSC Token Refresh Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Get user information
     */
    private function get_user_info() {
        if (!$this->client || !$this->client->getAccessToken()) {
            return new WP_Error('not_authenticated', 'Not authenticated');
        }
        
        try {
            // Use OAuth2 service to get user info
            $oauth2 = new Oauth2($this->client);
            $user_info = $oauth2->userinfo->get();
            
            return array(
                'email' => $user_info->getEmail(),
                'name' => $user_info->getName()
            );
            
        } catch (Exception $e) {
            error_log('GSC User Info Error: ' . $e->getMessage());
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
            $start_date = date('Y-m-d', strtotime('-30 days'));
        }
        if (!$end_date) {
            $end_date = date('Y-m-d', strtotime('-1 day')); // Yesterday (GSC data has 1-day delay)
        }
        
        try {
            // Create search analytics query
            $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
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
            error_log('GSC Search Analytics Error: ' . $e->getMessage());
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
            error_log('GSC Sites List Error: ' . $e->getMessage());
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
     * Get data formatted for AI content generation
     */
    public function get_data_for_ai() {
        // Ensure we have proper authentication
        if (!$this->service) {
            error_log('ACA GSC: Service not initialized for AI data');
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
            error_log('ACA GSC: Error fetching data - Queries: ' . (is_wp_error($top_queries) ? $top_queries->get_error_message() : 'OK') . 
                     ', Pages: ' . (is_wp_error($underperforming_pages) ? $underperforming_pages->get_error_message() : 'OK'));
            return false;
        }
        
        // Ensure we have meaningful data
        if (empty($top_queries) && empty($underperforming_pages)) {
            error_log('ACA GSC: No meaningful data found for site: ' . $site_url);
            return false;
        }
        
        return array(
            'topQueries' => $top_queries ?: array(),
            'underperformingPages' => $underperforming_pages ?: array(),
            'site_url' => $site_url,
            'data_date' => date('Y-m-d H:i:s')
        );
    }
} else {
    // Create a dummy class when dependencies are not available
    class ACA_Google_Search_Console {
        public function __construct() {
            error_log('ACA GSC Error: Google API client library not installed. Run: composer install');
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
    }
}