<?php
/**
 * Google Search Console API Integration
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once ACA_PLUGIN_PATH . 'vendor/autoload.php';

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
        $this->client = new Google_Client();
        $this->client->setApplicationName('AI Content Agent');
        $this->client->setScopes([
            'https://www.googleapis.com/auth/webmasters.readonly'
        ]);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
        
        // Get credentials from settings
        $settings = get_option('aca_settings', array());
        if (!empty($settings['gscClientId']) && !empty($settings['gscClientSecret'])) {
            $this->client->setClientId($settings['gscClientId']);
            $this->client->setClientSecret($settings['gscClientSecret']);
        }
        
        // Set redirect URI
        $this->client->setRedirectUri($this->get_redirect_uri());
        
        // Set access token if available
        $access_token = get_option('aca_gsc_access_token');
        if ($access_token) {
            $this->client->setAccessToken($access_token);
            
            // Check if token is expired and refresh if needed
            if ($this->client->isAccessTokenExpired()) {
                $this->refresh_token();
            }
        }
        
        // Initialize Search Console service
        if ($this->client->getAccessToken()) {
            $this->service = new Google_Service_SearchConsole($this->client);
        }
    }
    
    /**
     * Get OAuth2 authorization URL
     */
    public function get_auth_url() {
        return $this->client->createAuthUrl();
    }
    
    /**
     * Get redirect URI for OAuth2
     */
    public function get_redirect_uri() {
        return admin_url('admin.php?page=ai-content-agent&gsc_auth=callback');
    }
    
    /**
     * Handle OAuth2 callback
     */
    public function handle_oauth_callback($auth_code) {
        try {
            $access_token = $this->client->fetchAccessTokenWithAuthCode($auth_code);
            
            if (isset($access_token['error'])) {
                throw new Exception('OAuth error: ' . $access_token['error_description']);
            }
            
            // Store access token
            update_option('aca_gsc_access_token', $access_token);
            
            // Store refresh token separately for security
            if (isset($access_token['refresh_token'])) {
                update_option('aca_gsc_refresh_token', $access_token['refresh_token']);
            }
            
            // Get user email and store it
            $this->service = new Google_Service_SearchConsole($this->client);
            $user_info = $this->get_user_info();
            
            if ($user_info) {
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
        $refresh_token = get_option('aca_gsc_refresh_token');
        if (!$refresh_token) {
            return false;
        }
        
        try {
            $this->client->refreshToken($refresh_token);
            $new_access_token = $this->client->getAccessToken();
            
            // Store the new access token
            update_option('aca_gsc_access_token', $new_access_token);
            
            return true;
            
        } catch (Exception $e) {
            error_log('GSC Token Refresh Error: ' . $e->getMessage());
            // Clear stored tokens if refresh fails
            $this->disconnect();
            return false;
        }
    }
    
    /**
     * Get user information
     */
    private function get_user_info() {
        try {
            // Get sites to extract user email from permissions
            $sites = $this->service->sites->listSites();
            if (!empty($sites->getSiteEntry())) {
                // For now, we'll use a placeholder email
                // In a real implementation, you'd get this from the OAuth user info
                return array('email' => 'authenticated.user@gmail.com');
            }
            return null;
        } catch (Exception $e) {
            error_log('GSC User Info Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get list of sites from Search Console
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
            error_log('GSC Get Sites Error: ' . $e->getMessage());
            return new WP_Error('api_error', $e->getMessage());
        }
    }
    
    /**
     * Get search analytics data
     */
    public function get_search_analytics($site_url, $start_date = null, $end_date = null, $dimensions = array('query'), $row_limit = 1000) {
        if (!$this->service) {
            return new WP_Error('not_authenticated', 'Not authenticated with Google Search Console');
        }
        
        try {
            // Set default date range if not provided
            if (!$start_date) {
                $start_date = date('Y-m-d', strtotime('-30 days'));
            }
            if (!$end_date) {
                $end_date = date('Y-m-d', strtotime('-1 day'));
            }
            
            // Create search analytics query
            $request = new Google_Service_SearchConsole_SearchAnalyticsQueryRequest();
            $request->setStartDate($start_date);
            $request->setEndDate($end_date);
            $request->setDimensions($dimensions);
            $request->setRowLimit($row_limit);
            
            // Execute query
            $response = $this->service->searchanalytics->query($site_url, $request);
            
            $results = array();
            if ($response->getRows()) {
                foreach ($response->getRows() as $row) {
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
     * Get top queries
     */
    public function get_top_queries($site_url, $limit = 10) {
        $analytics = $this->get_search_analytics($site_url, null, null, array('query'), $limit);
        
        if (is_wp_error($analytics)) {
            return $analytics;
        }
        
        $top_queries = array();
        foreach ($analytics as $row) {
            if (!empty($row['keys'][0])) {
                $top_queries[] = $row['keys'][0];
            }
        }
        
        return $top_queries;
    }
    
    /**
     * Get underperforming pages
     */
    public function get_underperforming_pages($site_url, $limit = 10) {
        $analytics = $this->get_search_analytics($site_url, null, null, array('page'), $limit);
        
        if (is_wp_error($analytics)) {
            return $analytics;
        }
        
        $underperforming = array();
        foreach ($analytics as $row) {
            // Consider pages with high impressions but low CTR as underperforming
            if (!empty($row['keys'][0]) && $row['impressions'] > 100 && $row['ctr'] < 0.02) {
                $underperforming[] = $row['keys'][0];
            }
        }
        
        return array_slice($underperforming, 0, $limit);
    }
    
    /**
     * Get Search Console data for AI content generation
     */
    public function get_data_for_ai() {
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['searchConsoleUser']) || !$this->service) {
            return null;
        }
        
        // Try to get the first available site
        $sites = $this->get_sites();
        if (is_wp_error($sites) || empty($sites)) {
            return null;
        }
        
        $site_url = $sites[0]['siteUrl'];
        
        // Get top queries and underperforming pages
        $top_queries = $this->get_top_queries($site_url, 10);
        $underperforming_pages = $this->get_underperforming_pages($site_url, 5);
        
        if (is_wp_error($top_queries)) {
            $top_queries = array();
        }
        if (is_wp_error($underperforming_pages)) {
            $underperforming_pages = array();
        }
        
        return array(
            'topQueries' => $top_queries,
            'underperformingPages' => $underperforming_pages,
            'siteUrl' => $site_url
        );
    }
    
    /**
     * Disconnect from Google Search Console
     */
    public function disconnect() {
        // Revoke token if possible
        if ($this->client && $this->client->getAccessToken()) {
            try {
                $this->client->revokeToken();
            } catch (Exception $e) {
                // Ignore revoke errors
            }
        }
        
        // Clear stored tokens and settings
        delete_option('aca_gsc_access_token');
        delete_option('aca_gsc_refresh_token');
        
        $settings = get_option('aca_settings', array());
        $settings['searchConsoleUser'] = null;
        update_option('aca_settings', $settings);
        
        return true;
    }
    
    /**
     * Check if user is authenticated
     */
    public function is_authenticated() {
        return !empty($this->service) && !$this->client->isAccessTokenExpired();
    }
    
    /**
     * Get authentication status
     */
    public function get_auth_status() {
        if (!$this->is_authenticated()) {
            return array(
                'authenticated' => false,
                'auth_url' => $this->get_auth_url()
            );
        }
        
        $settings = get_option('aca_settings', array());
        return array(
            'authenticated' => true,
            'user_email' => isset($settings['searchConsoleUser']['email']) ? $settings['searchConsoleUser']['email'] : 'Unknown'
        );
    }
}