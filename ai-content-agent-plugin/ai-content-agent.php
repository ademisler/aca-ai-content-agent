<?php
/**
 * Plugin Name: AI Content Agent (ACA)
 * Plugin URI: https://ademisler.gumroad.com/l/ai-content-agent-pro
 * Description: AI-powered content creation and management plugin that generates blog posts, ideas, and manages your content workflow automatically with Google Search Console integration and Pro features.
 * Version: 2.4.11
 * Author: Adem Isler
 * Author URI: https://ademisler.com/en
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-content-agent-plugin
 * Requires at least: 5.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ACA_VERSION', '2.4.11');
define('ACA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ACA_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Check if ACA Pro is active with multi-point validation
 * 
 * @return bool True if pro license is active, false otherwise
 */
function is_aca_pro_active() {
    // Multi-point validation to prevent bypass attempts
    $license_status = get_option('aca_license_status');
    $license_key = get_option('aca_license_key');
    $license_verified = get_option('aca_license_verified');
    $license_timestamp = get_option('aca_license_timestamp', 0);
    
    $checks = array(
        $license_status === 'active',
        $license_verified === wp_hash('verified'),
        (time() - $license_timestamp) < 604800, // Weekly verification (7 days instead of 1 day)
        !empty($license_key)
    );
    
    return count(array_filter($checks)) === 4;
}

/**
 * Debug logging helper - only logs when WP_DEBUG is enabled
 * 
 * @param string $message Log message
 * @return void
 */
function aca_debug_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('ACA: ' . $message); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Centralized debug logging function
    }
}

// Include required files
require_once ACA_PLUGIN_PATH . 'includes/class-aca-activator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-deactivator.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-rest-api.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-cron.php';
require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';

// Activation and deactivation hooks
register_activation_hook(__FILE__, array('ACA_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('ACA_Deactivator', 'deactivate'));

/**
 * Main plugin class
 */
class AI_Content_Agent {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    public function init() {
        // Initialize dependency installer
        require_once ACA_PLUGIN_PATH . 'install-dependencies.php';
        
        // Initialize REST API
        new ACA_Rest_Api();
        
        // Initialize Cron jobs
        new ACA_Cron();
        
        // Handle Google Search Console OAuth callback - only when needed
        // Note: Nonce verification not required for OAuth callbacks from external services
        if (isset($_GET['code']) && isset($_GET['state']) && sanitize_text_field(wp_unslash($_GET['state'])) === 'aca_gsc_auth') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback from external service
            add_action('admin_init', array($this, 'handle_gsc_oauth_callback'));
        }
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Handle Google Search Console OAuth callback
     */
    public function handle_gsc_oauth_callback() {
        // Note: OAuth callbacks from external services don't require nonce verification
        if (isset($_GET['page']) && sanitize_text_field(wp_unslash($_GET['page'])) === 'ai-content-agent' && // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback parameter
            isset($_GET['gsc_auth']) && sanitize_text_field(wp_unslash($_GET['gsc_auth'])) === 'callback' && // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback parameter
            isset($_GET['code'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback parameter
            
            require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
            
            $gsc = new ACA_Google_Search_Console();
            $code = sanitize_text_field(wp_unslash($_GET['code'])); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- OAuth callback parameter
            $result = $gsc->handle_oauth_callback($code);
            
            if (is_wp_error($result)) {
                wp_die('Google Search Console authentication failed: ' . esc_html($result->get_error_message()));
            } else {
                // Redirect back to settings page
                wp_redirect(admin_url('admin.php?page=ai-content-agent&view=settings&gsc_connected=1'));
                exit;
            }
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'AI Content Agent (ACA)',
            'AI Content Agent (ACA)',
            'manage_options',
            'ai-content-agent',
            array($this, 'admin_page'),
            'dashicons-edit-large',
            30
        );
    }
    
    /**
     * Admin page callback
     */
    public function admin_page() {
        // Display dependency status if needed
        if (!ACA_Dependencies_Installer::are_dependencies_installed()) {
            echo '<div class="wrap">';
            ACA_Dependencies_Installer::display_dependency_status();
            echo '</div>';
        }
        
        echo '<div id="root"></div>';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin page
        if ('toplevel_page_ai-content-agent' != $hook) {
            return;
        }
        
        // Find the latest built JS file in admin/assets
        $assets_dir = ACA_PLUGIN_PATH . 'admin/assets/';
        $js_files = glob($assets_dir . 'index-*.js');
        
        if (!empty($js_files)) {
            // Get the most recent JS file by modification time
            $latest_file = '';
            $latest_time = 0;
            
            foreach ($js_files as $file) {
                $file_time = filemtime($file);
                if ($file_time > $latest_time) {
                    $latest_time = $file_time;
                    $latest_file = $file;
                }
            }
            
            if ($latest_file) {
                $js_filename = basename($latest_file);
                $js_version = ACA_VERSION . '-' . $latest_time;
                $script_handle = 'aca-app-' . md5($js_filename . $latest_time);
                
                // Enqueue the compiled React app (CSS is inlined in JS)
                wp_enqueue_script($script_handle, ACA_PLUGIN_URL . 'admin/assets/' . $js_filename, array(), $js_version, true);
                
                // Pass data to React app
                wp_localize_script($script_handle, 'acaData', array(
                    'nonce' => wp_create_nonce('wp_rest'),
                    'api_url' => rest_url('aca/v1/'),
                    'admin_url' => admin_url(),
                    'plugin_url' => ACA_PLUGIN_URL,
                ));
            }
        } else {
            // Fallback to old files if new build doesn't exist
            $css_file = ACA_PLUGIN_PATH . 'admin/css/index.css';
            $js_file = ACA_PLUGIN_PATH . 'admin/js/index.js';
            
            $css_version = ACA_VERSION . '-' . (file_exists($css_file) ? filemtime($css_file) : time());
            $js_version = ACA_VERSION . '-' . (file_exists($js_file) ? filemtime($js_file) : time());
            $fallback_handle = 'aca-app-fallback-' . md5($js_version);
            
            wp_enqueue_style('aca-styles', ACA_PLUGIN_URL . 'admin/css/index.css', array(), $css_version);
            wp_enqueue_script($fallback_handle, ACA_PLUGIN_URL . 'admin/js/index.js', array(), $js_version, true);
            
            // Pass data to React app
            wp_localize_script($fallback_handle, 'acaData', array(
                'nonce' => wp_create_nonce('wp_rest'),
                'api_url' => rest_url('aca/v1/'),
                'admin_url' => admin_url(),
                'plugin_url' => ACA_PLUGIN_URL,
            ));
        }
    }
}

// Initialize the plugin
new AI_Content_Agent();

// Hook cron events
add_action('aca_thirty_minute_event', array('ACA_Cron', 'thirty_minute_task'));
add_action('aca_fifteen_minute_event', array('ACA_Cron', 'fifteen_minute_task'));

// Consolidated admin initialization to avoid multiple hook calls
add_action('admin_init', 'aca_admin_init_handler');

// Admin notices - only show on admin pages, not during REST API requests
add_action('admin_notices', function() {
    // Don't show notices during REST API requests or AJAX calls
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    aca_show_gsc_reauth_notice();
});

add_action('admin_notices', function() {
    // Don't show notices during REST API requests or AJAX calls
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    aca_show_gsc_scope_reauth_notice();
});

// Post view count tracking for content freshness analysis
function aca_track_post_views() {
    if (is_single() && !is_admin() && !current_user_can('edit_posts')) {
        global $post;
        if ($post && $post->post_type === 'post') {
            $current_views = get_post_meta($post->ID, '_aca_view_count', true) ?: 0;
            update_post_meta($post->ID, '_aca_view_count', $current_views + 1);
        }
    }
}
add_action('wp_head', 'aca_track_post_views');

// Consolidated admin initialization handler
function aca_admin_init_handler() {
    // Handle database updates (has its own frequency control)
    aca_check_database_updates();
    
    // Handle GSC reauth dismissals (only process if relevant GET parameters exist)
    // Note: These are admin dismissal actions with proper nonce verification in the handler
    if (isset($_GET['dismiss_gsc_reauth']) || isset($_GET['dismiss_gsc_scope_reauth'])) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification handled in individual handlers
        aca_handle_gsc_reauth_dismissal();
        aca_handle_gsc_scope_reauth_dismissal();
    }
}

function aca_check_database_updates() {
    // Only run for admins and not during plugin activation or AJAX
    if (!is_admin() || !current_user_can('activate_plugins') || wp_doing_ajax() || defined('DOING_AUTOSAVE')) {
        return;
    }
    
    // Don't run on every admin page load - use transient to limit checks
    if (get_transient('aca_migration_check_done')) {
        return;
    }
    
    // Include migration manager
    $migration_file = ACA_PLUGIN_PATH . 'includes/class-aca-migration-manager.php';
    if (file_exists($migration_file)) {
        require_once $migration_file;
        
        $migration_manager = new ACA_Migration_Manager();
        $result = $migration_manager->run_migrations();
        
        if (is_wp_error($result)) {
            add_action('admin_notices', function() use ($result) {
                // Don't show notices during REST API requests or AJAX calls
                if (defined('REST_REQUEST') && REST_REQUEST) {
                    return;
                }
                if (defined('DOING_AJAX') && DOING_AJAX) {
                    return;
                }
                echo '<div class="notice notice-error"><p>ACA Database Update Failed: ' . 
                     esc_html($result->get_error_message()) . '</p></div>';
            });
        }
    }
    
    // Set transient to prevent running again for 1 hour
    set_transient('aca_migration_check_done', true, HOUR_IN_SECONDS);
}

function aca_show_gsc_reauth_notice() {
    $reauth_data = get_transient('aca_gsc_reauth_required');
    
    if (!$reauth_data || !current_user_can('manage_options')) {
        return;
    }
    
    // Only show on ACA pages to avoid annoying users
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'aca') === false) {
        return;
    }
    
    echo '<div class="notice notice-error">';
    echo '<p><strong>ACA Google Search Console - Re-authentication Required</strong></p>';
    echo '<p>Your Google Search Console connection has been lost. Please re-authenticate to continue using GSC features.</p>';
    
    if (!empty($reauth_data['error_message'])) {
        echo '<p><small>Error: ' . esc_html($reauth_data['error_message']) . '</small></p>';
    }
    
    echo '<p>';
    echo '<a href="' . esc_url(admin_url('admin.php?page=aca-settings')) . '" class="button button-primary">Go to Settings</a> ';
    echo '<a href="' . esc_url(wp_nonce_url(add_query_arg('dismiss_gsc_reauth', '1'), 'aca_dismiss_gsc_reauth')) . '" class="button">Dismiss</a>';
    echo '</p>';
    echo '</div>';
}

function aca_handle_gsc_reauth_dismissal() {
    if (isset($_GET['dismiss_gsc_reauth']) && $_GET['dismiss_gsc_reauth'] == '1' && current_user_can('manage_options')) {
        // Add nonce verification for security
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'aca_dismiss_gsc_reauth')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'ai-content-agent-plugin'));
        }
        
        delete_transient('aca_gsc_reauth_required');
        wp_redirect(remove_query_arg(array('dismiss_gsc_reauth', '_wpnonce')));
        exit;
    }
}

function aca_show_gsc_scope_reauth_notice() {
    $scope_reauth_data = get_transient('aca_gsc_scope_reauth_required');
    
    if (!$scope_reauth_data || !current_user_can('manage_options')) {
        return;
    }
    
    // Only show on ACA pages to avoid annoying users
    $screen = get_current_screen();
    if (!$screen || strpos($screen->id, 'aca') === false) {
        return;
    }
    
    echo '<div class="notice notice-warning">';
    echo '<p><strong>ACA Google Search Console - Additional Permissions Required</strong></p>';
    echo '<p>Your Google Search Console connection needs additional permissions to access all features. Please re-authenticate to grant the required scopes.</p>';
    
    if (!empty($scope_reauth_data['missing_scopes'])) {
        echo '<p><small>Missing permissions: ' . esc_html(implode(', ', $scope_reauth_data['missing_scopes'])) . '</small></p>';
    }
    
    echo '<p>';
    echo '<a href="' . esc_url(admin_url('admin.php?page=aca-settings')) . '" class="button button-primary">Update Permissions</a> ';
    echo '<a href="' . esc_url(wp_nonce_url(add_query_arg('dismiss_gsc_scope_reauth', '1'), 'aca_dismiss_gsc_scope_reauth')) . '" class="button">Dismiss</a>';
    echo '</p>';
    echo '</div>';
}

function aca_handle_gsc_scope_reauth_dismissal() {
    if (isset($_GET['dismiss_gsc_scope_reauth']) && $_GET['dismiss_gsc_scope_reauth'] == '1' && current_user_can('manage_options')) {
        // Add nonce verification for security
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'aca_dismiss_gsc_scope_reauth')) {
            wp_die(esc_html__('Security check failed. Please try again.', 'ai-content-agent-plugin'));
        }
        
        delete_transient('aca_gsc_scope_reauth_required');
        wp_redirect(remove_query_arg(array('dismiss_gsc_scope_reauth', '_wpnonce')));
        exit;
    }
}