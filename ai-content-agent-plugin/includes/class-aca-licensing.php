<?php
/**
 * ACA Licensing System
 * Handles license validation, activation, and Pro feature access
 * Replaces demo mode with proper licensing from v2.3.0 approach
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Licensing {
    
    private $license_server_url = 'https://api.ai-content-agent.com/v1/';
    private $product_id = 'aca-pro';
    private $license_key_option = 'aca_license_key';
    private $license_status_option = 'aca_license_status';
    private $license_data_option = 'aca_license_data';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_aca_activate_license', array($this, 'activate_license'));
        add_action('wp_ajax_aca_deactivate_license', array($this, 'deactivate_license'));
        add_action('wp_ajax_aca_check_license', array($this, 'check_license'));
        add_action('wp_ajax_aca_get_license_status', array($this, 'ajax_get_license_status'));
        add_action('wp_ajax_aca_dismiss_migration_notice', array($this, 'dismiss_migration_notice'));
        
        // Daily license check
        add_action('aca_daily_license_check', array($this, 'daily_license_check'));
        if (!wp_next_scheduled('aca_daily_license_check')) {
            wp_schedule_event(time(), 'daily', 'aca_daily_license_check');
        }
    }
    
    public function init() {
        // Initialize licensing system
        $this->maybe_migrate_from_demo_mode();
    }
    
    /**
     * Migrate from demo mode to proper licensing
     */
    private function maybe_migrate_from_demo_mode() {
        $current_status = get_option($this->license_status_option);
        $license_key = get_option($this->license_key_option);
        
        // If currently in demo mode (active status but no license key), reset to proper licensing
        if ($current_status === 'active' && empty($license_key)) {
            update_option($this->license_status_option, 'inactive');
            update_option($this->license_data_option, array(
                'status' => 'inactive',
                'message' => 'Demo mode disabled. Please enter a valid license key to access Pro features.',
                'migrated_from_demo' => true,
                'migration_date' => current_time('mysql')
            ));
            
            // Add admin notice about migration
            add_option('aca_demo_migration_notice', array(
                'message' => 'AI Content Agent has been migrated from demo mode. Please enter a valid Pro license key to access premium features.',
                'timestamp' => current_time('mysql'),
                'dismissed' => false
            ));
            
            error_log('ACA: Migrated from demo mode to proper licensing system');
        }
    }
    
    /**
     * Check if Pro license is active - PROPER VALIDATION
     */
    public function is_pro_active() {
        $status = get_option($this->license_status_option, 'inactive');
        $license_key = get_option($this->license_key_option, '');
        
        // Must have both valid status and license key
        return ($status === 'active' && !empty($license_key));
    }
    
    /**
     * Get license status with details
     */
    public function get_license_status() {
        $status = get_option($this->license_status_option, 'inactive');
        $license_key = get_option($this->license_key_option, '');
        $license_data = get_option($this->license_data_option, array());
        
        return array(
            'status' => $status,
            'license_key' => $license_key ? substr($license_key, 0, 8) . '...' : '',
            'license_key_masked' => $license_key ? $this->mask_license_key($license_key) : '',
            'is_active' => $this->is_pro_active(),
            'data' => $license_data,
            'features' => $this->get_available_features(),
            'migration_notice' => get_option('aca_demo_migration_notice', false)
        );
    }
    
    /**
     * Mask license key for display
     */
    private function mask_license_key($license_key) {
        if (strlen($license_key) <= 8) {
            return str_repeat('*', strlen($license_key));
        }
        
        return substr($license_key, 0, 4) . str_repeat('*', strlen($license_key) - 8) . substr($license_key, -4);
    }
    
    /**
     * Get available features based on license
     */
    public function get_available_features() {
        if ($this->is_pro_active()) {
            return array(
                'automation' => true,
                'content_freshness' => true,
                'advanced_seo' => true,
                'bulk_operations' => true,
                'api_integrations' => true,
                'priority_support' => true,
                'custom_templates' => true,
                'analytics' => true,
                'gsc_integration' => true,
                'advanced_scheduling' => true
            );
        }
        
        return array(
            'automation' => false,
            'content_freshness' => false,
            'advanced_seo' => false,
            'bulk_operations' => false,
            'api_integrations' => false,
            'priority_support' => false,
            'custom_templates' => false,
            'analytics' => false,
            'gsc_integration' => false,
            'advanced_scheduling' => false
        );
    }
    
    /**
     * AJAX: Get license status
     */
    public function ajax_get_license_status() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        wp_send_json_success($this->get_license_status());
    }
    
    /**
     * Activate license
     */
    public function activate_license() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $license_key = sanitize_text_field($_POST['license_key'] ?? '');
        
        if (empty($license_key)) {
            wp_send_json_error('License key is required');
        }
        
        $result = $this->validate_license($license_key, 'activate');
        
        if ($result['success']) {
            update_option($this->license_key_option, $license_key);
            update_option($this->license_status_option, 'active');
            update_option($this->license_data_option, $result['data']);
            
            // Clear migration notice
            delete_option('aca_demo_migration_notice');
            
            wp_send_json_success(array(
                'message' => 'License activated successfully',
                'status' => $this->get_license_status()
            ));
        } else {
            wp_send_json_error($result['message']);
        }
    }
    
    /**
     * Deactivate license
     */
    public function deactivate_license() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $license_key = get_option($this->license_key_option);
        
        if ($license_key) {
            $this->validate_license($license_key, 'deactivate');
        }
        
        update_option($this->license_status_option, 'inactive');
        delete_option($this->license_key_option);
        delete_option($this->license_data_option);
        
        wp_send_json_success(array(
            'message' => 'License deactivated successfully',
            'status' => $this->get_license_status()
        ));
    }
    
    /**
     * Check license status
     */
    public function check_license() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $license_key = get_option($this->license_key_option);
        
        if (!$license_key) {
            wp_send_json_error('No license key found');
        }
        
        $result = $this->validate_license($license_key, 'check');
        
        if ($result['success']) {
            update_option($this->license_status_option, 'active');
            update_option($this->license_data_option, $result['data']);
        } else {
            update_option($this->license_status_option, 'inactive');
        }
        
        wp_send_json_success($this->get_license_status());
    }
    
    /**
     * Daily license check
     */
    public function daily_license_check() {
        $license_key = get_option($this->license_key_option);
        
        if ($license_key) {
            $result = $this->validate_license($license_key, 'check');
            
            if ($result['success']) {
                update_option($this->license_status_option, 'active');
                update_option($this->license_data_option, $result['data']);
            } else {
                update_option($this->license_status_option, 'inactive');
                
                // Notify admin of license issues
                $this->notify_license_issue($result['message']);
            }
        }
    }
    
    /**
     * Validate license with server
     */
    private function validate_license($license_key, $action = 'check') {
        $api_params = array(
            'action' => 'check_license',
            'license' => $license_key,
            'product_id' => $this->product_id,
            'url' => home_url(),
            'request_action' => $action,
            'version' => defined('ACA_VERSION') ? ACA_VERSION : '2.3.14'
        );
        
        $response = wp_remote_post($this->license_server_url . 'licenses', array(
            'timeout' => 15,
            'sslverify' => true,
            'body' => $api_params,
            'headers' => array(
                'User-Agent' => 'ACA-Plugin/' . (defined('ACA_VERSION') ? ACA_VERSION : '2.3.14') . '; ' . home_url()
            )
        ));
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => 'Connection error: ' . $response->get_error_message()
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return array(
                'success' => false,
                'message' => 'Server error: HTTP ' . $response_code
            );
        }
        
        $license_data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (empty($license_data)) {
            return array(
                'success' => false,
                'message' => 'Invalid response from license server'
            );
        }
        
        if (isset($license_data['license']) && $license_data['license'] === 'valid') {
            return array(
                'success' => true,
                'data' => $license_data
            );
        } else {
            return array(
                'success' => false,
                'message' => $license_data['message'] ?? 'License validation failed'
            );
        }
    }
    
    /**
     * Notify admin of license issues
     */
    private function notify_license_issue($message) {
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        
        $subject = sprintf('[%s] AI Content Agent - License Issue', $site_name);
        $body = sprintf(
            "Hello,\n\nYour AI Content Agent Pro license on %s has an issue:\n\n%s\n\nPlease check your license status in the plugin settings at:\n%s\n\nBest regards,\nAI Content Agent Team",
            home_url(),
            $message,
            admin_url('admin.php?page=ai-content-agent&view=settings')
        );
        
        wp_mail($admin_email, $subject, $body);
    }
    
    /**
     * Check Pro permissions for API endpoints
     */
    public function check_pro_permissions($request) {
        if (!$this->is_pro_active()) {
            return new WP_Error(
                'license_required', 
                'Pro license required for this feature. Please activate your license to access premium features.', 
                array('status' => 403)
            );
        }
        
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'insufficient_permissions', 
                'Insufficient permissions', 
                array('status' => 403)
            );
        }
        
        return true;
    }
    
    /**
     * Check admin permissions (for free features)
     */
    public function check_admin_permissions($request) {
        if (!current_user_can('manage_options')) {
            return new WP_Error(
                'insufficient_permissions', 
                'Insufficient permissions', 
                array('status' => 403)
            );
        }
        
        return true;
    }
    
    /**
     * Dismiss migration notice
     */
    public function dismiss_migration_notice() {
        check_ajax_referer('aca_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        delete_option('aca_demo_migration_notice');
        wp_send_json_success('Notice dismissed');
    }
}

// Initialize licensing system
new ACA_Licensing();

/**
 * Global function to check Pro status (backward compatibility)
 */
if (!function_exists('is_aca_pro_active')) {
    function is_aca_pro_active() {
        static $licensing = null;
        
        if ($licensing === null) {
            $licensing = new ACA_Licensing();
        }
        
        return $licensing->is_pro_active();
    }
}

/**
 * Get licensing instance
 */
if (!function_exists('aca_get_licensing')) {
    function aca_get_licensing() {
        static $licensing = null;
        
        if ($licensing === null) {
            $licensing = new ACA_Licensing();
        }
        
        return $licensing;
    }
}