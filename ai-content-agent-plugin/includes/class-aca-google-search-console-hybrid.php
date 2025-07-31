<?php
/**
 * Hybrid Google Search Console Client
 * Uses real API when available, falls back to demo data
 * Replaces demo-only GSC implementation
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Google_Search_Console_Hybrid {
    
    private $client_id = '';
    private $client_secret = '';
    private $redirect_uri = '';
    private $scopes = array(
        'https://www.googleapis.com/auth/webmasters.readonly'
    );
    
    public function __construct() {
        $this->redirect_uri = admin_url('admin.php?page=ai-content-agent&gsc_auth=callback');
        
        // Load credentials from options or constants
        $this->client_id = defined('ACA_GOOGLE_CLIENT_ID') ? ACA_GOOGLE_CLIENT_ID : get_option('aca_google_client_id', '');
        $this->client_secret = defined('ACA_GOOGLE_CLIENT_SECRET') ? ACA_GOOGLE_CLIENT_SECRET : get_option('aca_google_client_secret', '');
        
        add_action('wp_ajax_aca_gsc_connect', array($this, 'ajax_connect'));
        add_action('wp_ajax_aca_gsc_disconnect', array($this, 'ajax_disconnect'));
        add_action('wp_ajax_aca_gsc_get_data', array($this, 'ajax_get_search_data'));
        add_action('wp_ajax_aca_gsc_get_sites', array($this, 'ajax_get_sites'));
        add_action('wp_ajax_aca_gsc_get_status', array($this, 'ajax_get_status'));
    }
    
    /**
     * Get GSC data - hybrid approach
     */
    public function get_search_analytics($site_url, $start_date, $end_date, $dimensions = array('query'), $row_limit = 100) {
        // Check if Pro license is active
        if (!is_aca_pro_active()) {
            return $this->get_demo_search_analytics($site_url, $start_date, $end_date, 'Pro license required for real GSC data');
        }
        
        // Try real API first if credentials are available
        if ($this->has_valid_credentials()) {
            try {
                $real_data = $this->get_real_search_analytics($site_url, $start_date, $end_date, $dimensions, $row_limit);
                if ($real_data && !empty($real_data['data'])) {
                    return $real_data;
                }
            } catch (Exception $e) {
                error_log('GSC Real API failed: ' . $e->getMessage());
                // Continue to demo data fallback
            }
        }
        
        // Fall back to demo data
        return $this->get_demo_search_analytics($site_url, $start_date, $end_date, 'Using demo data - Connect GSC for real analytics');
    }
    
    /**
     * Check if we have valid GSC credentials
     */
    private function has_valid_credentials() {
        $access_token = get_option('aca_gsc_access_token');
        $refresh_token = get_option('aca_gsc_refresh_token');
        
        return !empty($this->client_id) && !empty($this->client_secret) && 
               !empty($access_token) && !empty($refresh_token);
    }
    
    /**
     * Get real GSC data using Google API
     */
    private function get_real_search_analytics($site_url, $start_date, $end_date, $dimensions, $row_limit) {
        $access_token = $this->get_valid_access_token();
        
        if (!$access_token) {
            throw new Exception('No valid access token');
        }
        
        // Ensure site URL is properly formatted
        $site_url = $this->format_site_url($site_url);
        
        $api_url = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . 
                   urlencode($site_url) . '/searchAnalytics/query';
        
        $request_body = array(
            'startDate' => $start_date,
            'endDate' => $end_date,
            'dimensions' => $dimensions,
            'rowLimit' => min($row_limit, 25000), // GSC API limit
            'startRow' => 0
        );
        
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($request_body),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('API request failed: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            $body = wp_remote_retrieve_body($response);
            $error_data = json_decode($body, true);
            
            // Check for JSON decode errors in error response
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('GSC API: JSON decode error in error response - ' . json_last_error_msg());
                $error_message = 'HTTP ' . $response_code;
            } else {
                $error_message = isset($error_data['error']['message']) ? $error_data['error']['message'] : 'HTTP ' . $response_code;
            }
            throw new Exception('GSC API error: ' . $error_message);
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Check for JSON decode errors in success response
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('GSC API: JSON decode error in success response - ' . json_last_error_msg());
            throw new Exception('GSC API response parsing failed: ' . json_last_error_msg());
        }
        
        if (empty($data['rows'])) {
            // No data is not necessarily an error
            return array(
                'data' => array(),
                'is_demo' => false,
                'message' => 'No data available for the selected period',
                'total_rows' => 0,
                'site_url' => $site_url
            );
        }
        
        return $this->format_gsc_data($data['rows'], false, $site_url);
    }
    
    /**
     * Get demo GSC data for fallback
     */
    private function get_demo_search_analytics($site_url, $start_date, $end_date, $message = 'Demo data') {
        // Generate realistic demo data based on date range
        $start_timestamp = strtotime($start_date);
        $end_timestamp = strtotime($end_date);
        $days_diff = ($end_timestamp - $start_timestamp) / (24 * 60 * 60);
        
        $demo_data = array();
        
        $sample_queries = array(
            'wordpress tutorial' => array('clicks' => 45, 'impressions' => 1200, 'position' => 3.2),
            'seo tips' => array('clicks' => 38, 'impressions' => 980, 'position' => 4.1),
            'content marketing' => array('clicks' => 52, 'impressions' => 1450, 'position' => 2.8),
            'blog writing guide' => array('clicks' => 29, 'impressions' => 750, 'position' => 5.3),
            'digital marketing strategy' => array('clicks' => 34, 'impressions' => 890, 'position' => 4.7),
            'web development' => array('clicks' => 41, 'impressions' => 1100, 'position' => 3.9),
            'wordpress plugins' => array('clicks' => 27, 'impressions' => 680, 'position' => 6.1),
            'seo optimization' => array('clicks' => 33, 'impressions' => 820, 'position' => 4.4),
            'content creation' => array('clicks' => 36, 'impressions' => 950, 'position' => 3.6),
            'online marketing' => array('clicks' => 25, 'impressions' => 640, 'position' => 5.8)
        );
        
        foreach ($sample_queries as $query => $stats) {
            // Scale stats based on date range
            $scale_factor = max(0.3, min(2.0, $days_diff / 30));
            
            $clicks = round($stats['clicks'] * $scale_factor * (0.8 + rand(0, 40) / 100));
            $impressions = round($stats['impressions'] * $scale_factor * (0.8 + rand(0, 40) / 100));
            $ctr = $impressions > 0 ? $clicks / $impressions : 0;
            
            $demo_data[] = array(
                'keys' => array($query),
                'clicks' => $clicks,
                'impressions' => $impressions,
                'ctr' => round($ctr, 4),
                'position' => round($stats['position'] + (rand(-10, 10) / 10), 1)
            );
        }
        
        // Sort by clicks descending
        usort($demo_data, function($a, $b) {
            return $b['clicks'] - $a['clicks'];
        });
        
        return $this->format_gsc_data($demo_data, true, $site_url, $message);
    }
    
    /**
     * Format site URL for GSC API
     */
    private function format_site_url($site_url) {
        // Remove trailing slash
        $site_url = rtrim($site_url, '/');
        
        // Add protocol if missing
        if (!preg_match('/^https?:\/\//', $site_url)) {
            $site_url = 'https://' . $site_url;
        }
        
        return $site_url;
    }
    
    /**
     * Get valid access token (refresh if needed)
     */
    private function get_valid_access_token() {
        $access_token = get_option('aca_gsc_access_token');
        $token_expires = get_option('aca_gsc_token_expires');
        
        // Check if token is still valid (with 5 minute buffer)
        if ($access_token && $token_expires && time() < ($token_expires - 300)) {
            return $access_token;
        }
        
        // Try to refresh token
        $refresh_token = get_option('aca_gsc_refresh_token');
        if (!$refresh_token) {
            return false;
        }
        
        return $this->refresh_access_token($refresh_token);
    }
    
    /**
     * Refresh access token using refresh token
     */
    private function refresh_access_token($refresh_token) {
        $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
            'body' => array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            ),
            'timeout' => 15
        ));
        
        if (is_wp_error($response)) {
            error_log('GSC token refresh failed: ' . $response->get_error_message());
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log('GSC token refresh HTTP error: ' . $response_code);
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Check for JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('GSC token refresh: JSON decode error - ' . json_last_error_msg());
            return false;
        }
        
        if (empty($data['access_token'])) {
            error_log('GSC token refresh: No access token in response');
            return false;
        }
        
        // Save new token
        update_option('aca_gsc_access_token', $data['access_token']);
        update_option('aca_gsc_token_expires', time() + ($data['expires_in'] ?? 3600));
        
        return $data['access_token'];
    }
    
    /**
     * Format GSC data for consistency
     */
    private function format_gsc_data($rows, $is_demo = false, $site_url = '', $message = '') {
        $formatted = array();
        $total_clicks = 0;
        $total_impressions = 0;
        
        foreach ($rows as $row) {
            $clicks = $row['clicks'] ?? 0;
            $impressions = $row['impressions'] ?? 0;
            $ctr = $row['ctr'] ?? 0;
            $position = $row['position'] ?? 0;
            
            $total_clicks += $clicks;
            $total_impressions += $impressions;
            
            $formatted[] = array(
                'query' => isset($row['keys'][0]) ? $row['keys'][0] : '',
                'page' => isset($row['keys'][1]) ? $row['keys'][1] : '',
                'clicks' => $clicks,
                'impressions' => $impressions,
                'ctr' => round($ctr, 4),
                'position' => round($position, 1)
            );
        }
        
        return array(
            'data' => $formatted,
            'is_demo' => $is_demo,
            'message' => $message ?: ($is_demo ? 'Demo data' : 'Real GSC data'),
            'total_rows' => count($formatted),
            'total_clicks' => $total_clicks,
            'total_impressions' => $total_impressions,
            'average_ctr' => $total_impressions > 0 ? round($total_clicks / $total_impressions, 4) : 0,
            'site_url' => $site_url,
            'timestamp' => current_time('mysql')
        );
    }
    
    /**
     * Get authorization URL for OAuth
     */
    public function get_auth_url() {
        if (empty($this->client_id)) {
            return false;
        }
        
        $params = array(
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'scope' => implode(' ', $this->scopes),
            'response_type' => 'code',
            'access_type' => 'offline',
            'prompt' => 'consent'
        );
        
        return 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($params);
    }
    
    /**
     * Handle OAuth callback
     */
    public function handle_oauth_callback($code) {
        if (empty($this->client_id) || empty($this->client_secret)) {
            return new WP_Error('missing_credentials', 'Google API credentials not configured');
        }
        
        $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
            'body' => array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirect_uri
            ),
            'timeout' => 15
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        // Check for JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('GSC OAuth: JSON decode error - ' . json_last_error_msg());
            return new WP_Error('oauth_json_error', 'Failed to parse OAuth response: ' . json_last_error_msg());
        }
        
        if (empty($data['access_token'])) {
            return new WP_Error('oauth_failed', 'Failed to get access token');
        }
        
        // Save tokens
        update_option('aca_gsc_access_token', $data['access_token']);
        update_option('aca_gsc_refresh_token', $data['refresh_token'] ?? '');
        update_option('aca_gsc_token_expires', time() + ($data['expires_in'] ?? 3600));
        update_option('aca_gsc_connected', true);
        update_option('aca_gsc_connected_date', current_time('mysql'));
        
        return true;
    }
    
    /**
     * AJAX: Connect to GSC
     */
    public function ajax_connect() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $auth_url = $this->get_auth_url();
        
        if (!$auth_url) {
            wp_send_json_error('Google API credentials not configured');
        }
        
        wp_send_json_success(array(
            'auth_url' => $auth_url,
            'message' => 'Redirecting to Google for authentication...'
        ));
    }
    
    /**
     * AJAX: Disconnect from GSC
     */
    public function ajax_disconnect() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        // Clear all GSC data
        delete_option('aca_gsc_access_token');
        delete_option('aca_gsc_refresh_token');
        delete_option('aca_gsc_token_expires');
        delete_option('aca_gsc_connected');
        delete_option('aca_gsc_connected_date');
        
        wp_send_json_success('Disconnected from Google Search Console');
    }
    
    /**
     * AJAX: Get search data
     */
    public function ajax_get_search_data() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $site_url = sanitize_text_field($_POST['site_url'] ?? home_url());
        $start_date = sanitize_text_field($_POST['start_date'] ?? date('Y-m-d', strtotime('-30 days')));
        $end_date = sanitize_text_field($_POST['end_date'] ?? date('Y-m-d'));
        $dimensions = array_map('sanitize_text_field', $_POST['dimensions'] ?? array('query'));
        $row_limit = intval(sanitize_text_field($_POST['row_limit'] ?? '100'));
        
        $data = $this->get_search_analytics($site_url, $start_date, $end_date, $dimensions, $row_limit);
        
        wp_send_json_success($data);
    }
    
    /**
     * AJAX: Get GSC status
     */
    public function ajax_get_status() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        wp_send_json_success($this->get_connection_status());
    }
    
    /**
     * Get connection status
     */
    public function get_connection_status() {
        return array(
            'connected' => get_option('aca_gsc_connected', false),
            'connected_date' => get_option('aca_gsc_connected_date', ''),
            'has_credentials' => !empty($this->client_id) && !empty($this->client_secret),
            'credentials_source' => defined('ACA_GOOGLE_CLIENT_ID') ? 'constants' : 'options',
            'has_tokens' => !empty(get_option('aca_gsc_access_token')) && !empty(get_option('aca_gsc_refresh_token')),
            'token_expires' => get_option('aca_gsc_token_expires', 0),
            'is_token_valid' => $this->is_token_valid()
        );
    }
    
    /**
     * Check if current token is valid
     */
    private function is_token_valid() {
        $token_expires = get_option('aca_gsc_token_expires', 0);
        return $token_expires > time();
    }
}

// Initialize GSC Hybrid client
new ACA_Google_Search_Console_Hybrid();