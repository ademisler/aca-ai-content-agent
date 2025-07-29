<?php
/**
 * Google Search Console API Integration
 */

if (!defined('ABSPATH')) {
    exit;
}

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
            $this->service = new Google_Service_Webmasters($this->client);
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
            $this->service = new Google_Service_Webmasters($this->client);
            $user_info = $this->get_user_info();
            
            if ($user_info) {
                $settings = get_option('aca_settings', array());
                $settings['searchConsoleUser'] = array('email' => $user_info['email']);
                update_option('aca_settings', $settings);
            }
            
            return array('success' => true, 'user_info' => $user_info);
            
        } catch (Exception $e) {
            error_log('GSC OAuth Error: ' . $e->getMessage());
            return array('success' => false, 'error' => $e->getMessage());
        }
    }
    
    /**
     * Refresh access token
     */
    private function refresh_token() {
        $refresh_token = get_option('aca_gsc_refresh_token');
        if ($refresh_token) {
            try {
                $this->client->refreshToken($refresh_token);
                $new_access_token = $this->client->getAccessToken();
                update_option('aca_gsc_access_token', $new_access_token);
                
                // Update refresh token if a new one is provided
                if (isset($new_access_token['refresh_token'])) {
                    update_option('aca_gsc_refresh_token', $new_access_token['refresh_token']);
                }
                
                return true;
            } catch (Exception $e) {
                error_log('GSC Token Refresh Error: ' . $e->getMessage());
                return false;
            }
        }
        return false;
    }
    
    /**
     * Get user info from Google
     */
    private function get_user_info() {
        try {
            // Use OAuth2 service to get user info
            $oauth2 = new Oauth2($this->client);
            $user_info = $oauth2->userinfo->get();
            
            return array(
                'email' => $user_info->getEmail(),
                'name' => $user_info->getName(),
                'picture' => $user_info->getPicture()
            );
        } catch (Exception $e) {
            error_log('GSC User Info Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get authentication status
     */
    public function get_auth_status() {
        $access_token = get_option('aca_gsc_access_token');
        $settings = get_option('aca_settings', array());
        
        if ($access_token && !empty($settings['searchConsoleUser'])) {
            return array(
                'authenticated' => true,
                'user_email' => $settings['searchConsoleUser']['email']
            );
        }
        
        return array('authenticated' => false);
    }
    
    /**
     * Disconnect from Google Search Console
     */
    public function disconnect() {
        // Revoke token
        try {
            if ($this->client && $this->client->getAccessToken()) {
                $this->client->revokeToken();
            }
        } catch (Exception $e) {
            error_log('GSC Token Revoke Error: ' . $e->getMessage());
        }
        
        // Clear stored tokens and user info
        delete_option('aca_gsc_access_token');
        delete_option('aca_gsc_refresh_token');
        
        // Clear user info from settings
        $settings = get_option('aca_settings', array());
        $settings['searchConsoleUser'] = null;
        update_option('aca_settings', $settings);
        
        return array('success' => true);
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
            $request = new Google_Service_Webmasters_SearchAnalyticsQueryRequest();
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
        }
        
        $analytics_data = $this->get_search_analytics($site_url, null, null, array('query'), $limit);
        
        if (is_wp_error($analytics_data)) {
            return $analytics_data;
        }
        
        $queries = array();
        foreach ($analytics_data as $row) {
            if (!empty($row['keys'][0])) {
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
        }
        
        $analytics_data = $this->get_search_analytics($site_url, null, null, array('page'), $limit);
        
        if (is_wp_error($analytics_data)) {
            return $analytics_data;
        }
        
        $pages = array();
        foreach ($analytics_data as $row) {
            if (!empty($row['keys'][0]) && $row['position'] > 10) { // Pages ranking below position 10
                $pages[] = $row['keys'][0];
            }
        }
        
        return $pages;
    }
    
    /**
     * Get data formatted for AI content generation
     */
    public function get_data_for_ai() {
        $site_url = home_url();
        
        $top_queries = $this->get_top_queries($site_url, 20);
        $underperforming_pages = $this->get_underperforming_pages($site_url, 10);
        
        if (is_wp_error($top_queries) || is_wp_error($underperforming_pages)) {
            return false;
        }
        
        return array(
            'topQueries' => $top_queries,
            'underperformingPages' => $underperforming_pages
        );
    }
}