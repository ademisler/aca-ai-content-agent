<?php
/**
 * ACA - AI Content Agent
 *
 * Main Plugin Class
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Plugin {

    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Main ACA_Plugin Instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    private function includes() {
        // Core classes
        require_once plugin_dir_path(__FILE__) . 'core/class-aca-activator.php';
        require_once plugin_dir_path(__FILE__) . 'core/class-aca-deactivator.php';

        // Utility classes
        require_once plugin_dir_path(__FILE__) . 'utils/class-aca-encryption-util.php';
        require_once plugin_dir_path(__FILE__) . 'utils/class-aca-helper.php';
        require_once plugin_dir_path(__FILE__) . 'utils/class-aca-log-service.php';
        require_once plugin_dir_path(__FILE__) . 'utils/class-aca-cache-service.php';
        require_once plugin_dir_path(__FILE__) . 'utils/class-aca-error-recovery.php';

        // API classes
        require_once plugin_dir_path(__FILE__) . 'api/class-aca-gemini-api.php';
        require_once plugin_dir_path(__FILE__) . 'api/class-aca-gumroad-api.php';

        // Service classes
        require_once plugin_dir_path(__FILE__) . 'services/class-aca-idea-service.php';
        require_once plugin_dir_path(__FILE__) . 'services/class-aca-draft-service.php';
        require_once plugin_dir_path(__FILE__) . 'services/class-aca-style-guide-service.php';

        // Integration classes
        require_once plugin_dir_path(__FILE__) . 'integrations/class-aca-privacy.php';
        require_once plugin_dir_path(__FILE__) . 'integrations/class-aca-post-hooks.php';

        // Admin classes (only in admin)
        if (is_admin()) {
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-admin.php';
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-admin-menu.php';
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-admin-assets.php';
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-admin-notices.php';
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-ajax-handler.php';
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-dashboard.php';
            require_once plugin_dir_path(__FILE__) . 'admin/class-aca-onboarding.php';
            
            // Settings classes
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-api.php';
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-automation.php';
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-analysis.php';
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-enrichment.php';
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-management.php';
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-prompts.php';
            require_once plugin_dir_path(__FILE__) . 'admin/settings/class-aca-settings-license.php';
        }

        // Cron class
        require_once plugin_dir_path(__FILE__) . 'class-aca-cron.php';
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {
        add_action('plugins_loaded', [$this, 'init']);
        add_action('admin_init', array($this, 'handle_activation_redirect'));
        // Removed duplicate diagnostics page - already handled in ACA_Admin_Menu
        // add_action('admin_menu', array($this, 'add_diagnostics_page'));
        // add_action('admin_notices', array($this, 'diagnostics_admin_notices'));
        add_action('admin_init', array($this, 'check_and_create_tables'));
    }

    /**
     * Init the plugin.
     */
    public function init() {
        // Initialize cron functionality
        new ACA_Cron();

        // Initialize admin functionality
        if (is_admin()) {
            // Initialize the main admin class - this was missing!
            new ACA_Admin();
        }

        // Initialize privacy integration
        if (class_exists('ACA_Privacy')) {
            ACA_Privacy::init();
        }
        
        // Initialize post hooks
        if (class_exists('ACA_Post_Hooks')) {
            new ACA_Post_Hooks();
        }
    }

    public function handle_activation_redirect() {
        if ( get_transient( 'aca_ai_content_agent_activation_redirect' ) ) {
            delete_transient( 'aca_ai_content_agent_activation_redirect' );
            if ( ! is_multisite() ) {
                wp_safe_redirect( admin_url( 'admin.php?page=aca-ai-content-agent-onboarding' ) );
                exit;
            }
        }
    }

    

    /**
     * Add a diagnostics page to the admin menu.
     * NOTE: This method is not used anymore - diagnostics page is handled by ACA_Admin_Menu
     */
    private function add_diagnostics_page() {
        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__('Diagnostics', 'aca-ai-content-agent'),
            esc_html__('Diagnostics', 'aca-ai-content-agent'),
            'manage_options',
            'aca-ai-content-agent-diagnostics',
            array($this, 'render_diagnostics_page')
        );
    }

    /**
     * Render the diagnostics page.
     * NOTE: This method is not used anymore - diagnostics page is handled by ACA_Admin_Menu
     */
    private function render_diagnostics_page() {
        echo '<div class="wrap"><h1>' . esc_html__('ACA Content Agent Diagnostics', 'aca-ai-content-agent') . '</h1>';
        $this->output_diagnostics();
        if (class_exists('ACA_Cron')) {
            ACA_Cron::output_cron_diagnostics();
        }
        echo '</div>';
    }

    /**
     * Output diagnostics information.
     */
    public function output_diagnostics() {
        global $wpdb;
        $required_tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
        ];
        echo '<h2>' . esc_html__('Database Tables', 'aca-ai-content-agent') . '</h2><ul>';
        foreach ($required_tables as $table) {
            $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
            echo '<li>' . esc_html($table) . ': ' . ($exists ? '<span style="color:green">OK</span>' : '<span style="color:red">Missing</span>') . '</li>';
        }
        echo '</ul>';
        
        echo '<h2>' . esc_html__('Keys & Settings', 'aca-ai-content-agent') . '</h2><ul>';
        if (!defined('AUTH_KEY') || AUTH_KEY === 'put your unique phrase here') {
            echo '<li style="color:red">' . esc_html__('AUTH_KEY is missing or default!', 'aca-ai-content-agent') . '</li>';
        } else {
            echo '<li style="color:green">' . esc_html__('AUTH_KEY is set.', 'aca-ai-content-agent') . '</li>';
        }
        $api_key = get_option('aca_ai_content_agent_gemini_api_key');
        if (empty($api_key)) {
            echo '<li style="color:red">' . esc_html__('Gemini API key is missing!', 'aca-ai-content-agent') . '</li>';
        } else {
            echo '<li style="color:green">' . esc_html__('Gemini API key is set.', 'aca-ai-content-agent') . '</li>';
        }
        $license_key = get_option('aca_ai_content_agent_license_key');
        if (empty($license_key)) {
            echo '<li style="color:red">' . esc_html__('License key is missing!', 'aca-ai-content-agent') . '</li>';
        } else {
            echo '<li style="color:green">' . esc_html__('License key is set.', 'aca-ai-content-agent') . '</li>';
        }
        echo '</ul>';

        // HEALTH CHECK: Add comprehensive health check
        echo '<h2>' . esc_html__('Health Check', 'aca-ai-content-agent') . '</h2>';
        $this->output_health_check();
    }

    /**
     * Output comprehensive health check results.
     *
     * @since 1.2.0
     */
    private function output_health_check() {
        global $wpdb;
        
        $health_checks = [];
        
        // Check API connectivity
        $api_key = get_option('aca_ai_content_agent_gemini_api_key');
        if (!empty($api_key)) {
            $test_result = ACA_Gemini_Api::test_api_connectivity();
            $health_checks['api_connectivity'] = is_wp_error($test_result) ? 'error' : 'ok';
        } else {
            $health_checks['api_connectivity'] = 'warning';
        }
        
        // Check database performance
        $start_time = microtime(true);
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->prefix . 'aca_ai_content_agent_ideas'));
        
        if ($table_exists) {
            $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}aca_ai_content_agent_ideas");
            $db_time = (microtime(true) - $start_time) * 1000;
            $health_checks['database_performance'] = $db_time < 100 ? 'ok' : ($db_time < 500 ? 'warning' : 'error');
        } else {
            $health_checks['database_performance'] = 'warning';
        }
        
        // Check memory usage
        $memory_usage = memory_get_usage(true);
        $memory_limit = wp_convert_hr_to_bytes(ini_get('memory_limit'));
        $memory_percentage = ($memory_usage / $memory_limit) * 100;
        $health_checks['memory_usage'] = $memory_percentage < 50 ? 'ok' : ($memory_percentage < 80 ? 'warning' : 'error');
        
        // Check recent errors
        $logs_table = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $table_exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $logs_table));
        
        if ($table_exists) {
            $recent_errors = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$logs_table} WHERE level = %s AND timestamp >= %s",
                'error',
                gmdate('Y-m-d H:i:s', strtotime('-24 hours'))
            ));
            $health_checks['recent_errors'] = $recent_errors == 0 ? 'ok' : ($recent_errors < 10 ? 'warning' : 'error');
        } else {
            $health_checks['recent_errors'] = 'warning';
        }
        
        // Check cron jobs
        $cron_jobs = [
            'aca_ai_content_agent_main_automation' => wp_next_scheduled('aca_ai_content_agent_main_automation'),
            'aca_ai_content_agent_reset_api_usage' => wp_next_scheduled('aca_ai_content_agent_reset_api_usage'),
            'aca_ai_content_agent_verify_license' => wp_next_scheduled('aca_ai_content_agent_verify_license'),
        ];
        
        $cron_status = 'ok';
        foreach ($cron_jobs as $job => $next_run) {
            if (!$next_run) {
                $cron_status = 'error';
                break;
            }
        }
        $health_checks['cron_jobs'] = $cron_status;
        
        // Output health check results
        echo '<table class="widefat">';
        echo '<thead><tr><th>' . esc_html__('Check', 'aca-ai-content-agent') . '</th><th>' . esc_html__('Status', 'aca-ai-content-agent') . '</th><th>' . esc_html__('Details', 'aca-ai-content-agent') . '</th></tr></thead>';
        echo '<tbody>';
        
        foreach ($health_checks as $check => $status) {
            $status_color = $status === 'ok' ? 'green' : ($status === 'warning' ? 'orange' : 'red');
            $status_text = $status === 'ok' ? __('OK', 'aca-ai-content-agent') : ($status === 'warning' ? __('Warning', 'aca-ai-content-agent') : __('Error', 'aca-ai-content-agent'));
            
            echo '<tr>';
            echo '<td>' . esc_html(ucwords(str_replace('_', ' ', $check))) . '</td>';
            echo '<td><span style="color:' . esc_attr($status_color) . '">' . esc_html($status_text) . '</span></td>';
            echo '<td>';
            
            switch ($check) {
                case 'api_connectivity':
                    echo $status === 'ok' ? __('API is accessible', 'aca-ai-content-agent') : __('API connectivity issues detected', 'aca-ai-content-agent');
                    break;
                case 'database_performance':
                    echo sprintf(__('Query time: %sms', 'aca-ai-content-agent'), round($db_time, 2));
                    break;
                case 'memory_usage':
                    echo sprintf(__('Memory usage: %s / %s (%s%%)', 'aca-ai-content-agent'), 
                        size_format($memory_usage), 
                        size_format($memory_limit), 
                        round($memory_percentage, 1)
                    );
                    break;
                case 'recent_errors':
                    echo sprintf(__('%d errors in last 24 hours', 'aca-ai-content-agent'), $recent_errors);
                    break;
                case 'cron_jobs':
                    echo $status === 'ok' ? __('All cron jobs scheduled', 'aca-ai-content-agent') : __('Some cron jobs not scheduled', 'aca-ai-content-agent');
                    break;
            }
            
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody></table>';
    }

    /**
     * Show admin notices for missing AUTH_KEY or API keys.
     */
    public function diagnostics_admin_notices() {
        if (!defined('AUTH_KEY') || AUTH_KEY === 'put your unique phrase here') {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('ACA: AUTH_KEY is not defined or is set to the default in your wp-config.php file. This is a security risk and will break encryption. Please set a unique AUTH_KEY.', 'aca-ai-content-agent') . '</p></div>';
        }
        $api_key = get_option('aca_ai_content_agent_gemini_api_key');
        if (empty($api_key)) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('ACA: Gemini API key is missing. Please set it in the plugin settings.', 'aca-ai-content-agent') . '</p></div>';
        }
    }

    /**
     * Check for missing tables and attempt to create them if needed.
     */
    public function check_and_create_tables() {
        global $wpdb;
        $required_tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
        ];
        $missing = false;
        foreach ($required_tables as $table) {
            $exists = $wpdb->get_var($wpdb->prepare('SHOW TABLES LIKE %s', $table));
            if (!$exists) {
                $missing = true;
                break;
            }
        }
        if ($missing) {
            if (class_exists('ACA_Activator')) {
                ACA_Activator::activate();
            }
        }
    }
}
