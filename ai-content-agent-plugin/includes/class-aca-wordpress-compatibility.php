<?php
/**
 * WordPress Compatibility Manager
 * 
 * Handles WordPress version compatibility, PHP compatibility,
 * multi-site support, and plugin conflict resolution.
 *
 * @package AI_Content_Agent
 * @since 1.8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_WordPress_Compatibility {
    
    /**
     * Minimum WordPress version required
     */
    const MIN_WP_VERSION = '5.0';
    
    /**
     * Recommended WordPress version
     */
    const RECOMMENDED_WP_VERSION = '6.8';
    
    /**
     * Minimum PHP version required
     */
    const MIN_PHP_VERSION = '7.4';
    
    /**
     * Recommended PHP version
     */
    const RECOMMENDED_PHP_VERSION = '8.2';
    
    /**
     * Known conflicting plugins
     */
    private static $conflicting_plugins = [
        'wp-rocket/wp-rocket.php' => 'WP Rocket',
        'autoptimize/autoptimize.php' => 'Autoptimize',
        'w3-total-cache/w3-total-cache.php' => 'W3 Total Cache',
        'wp-super-cache/wp-cache.php' => 'WP Super Cache',
        'litespeed-cache/litespeed-cache.php' => 'LiteSpeed Cache',
    ];
    
    /**
     * Initialize compatibility checks
     */
    public static function init() {
        add_action('admin_init', [__CLASS__, 'check_compatibility']);
        add_action('admin_notices', [__CLASS__, 'display_compatibility_notices']);
        add_action('wp_ajax_aca_dismiss_compatibility_notice', [__CLASS__, 'dismiss_compatibility_notice']);
        
        // Multi-site specific hooks
        if (is_multisite()) {
            add_action('network_admin_notices', [__CLASS__, 'display_network_notices']);
            add_action('wp_ajax_aca_network_compatibility_check', [__CLASS__, 'network_compatibility_check']);
        }
        
        // Plugin conflict detection
        add_action('activated_plugin', [__CLASS__, 'check_plugin_conflicts']);
        add_action('deactivated_plugin', [__CLASS__, 'clear_plugin_conflict_cache']);
    }
    
    /**
     * Check overall system compatibility
     */
    public static function check_compatibility() {
        $checks = [
            'wordpress_version' => self::check_wordpress_version(),
            'php_version' => self::check_php_version(),
            'required_functions' => self::check_required_functions(),
            'memory_limit' => self::check_memory_limit(),
            'multisite_compatibility' => self::check_multisite_compatibility(),
            'plugin_conflicts' => self::check_plugin_conflicts(),
        ];
        
        update_option('aca_compatibility_checks', $checks);
        
        // Log compatibility issues
        $issues = array_filter($checks, function($check) {
            return isset($check['status']) && $check['status'] === 'error';
        });
        
        if (!empty($issues)) {
            error_log('ACA Compatibility Issues: ' . wp_json_encode($issues));
        }
        
        return $checks;
    }
    
    /**
     * Check WordPress version compatibility
     */
    public static function check_wordpress_version() {
        global $wp_version;
        
        $result = [
            'current_version' => $wp_version,
            'min_required' => self::MIN_WP_VERSION,
            'recommended' => self::RECOMMENDED_WP_VERSION,
        ];
        
        if (version_compare($wp_version, self::MIN_WP_VERSION, '<')) {
            $result['status'] = 'error';
            $result['message'] = sprintf(
                __('WordPress %s or higher is required. You are running %s.', 'ai-content-agent'),
                self::MIN_WP_VERSION,
                $wp_version
            );
        } elseif (version_compare($wp_version, self::RECOMMENDED_WP_VERSION, '<')) {
            $result['status'] = 'warning';
            $result['message'] = sprintf(
                __('WordPress %s is recommended for optimal performance. You are running %s.', 'ai-content-agent'),
                self::RECOMMENDED_WP_VERSION,
                $wp_version
            );
        } else {
            $result['status'] = 'success';
            $result['message'] = __('WordPress version is compatible.', 'ai-content-agent');
        }
        
        return $result;
    }
    
    /**
     * Check PHP version compatibility
     */
    public static function check_php_version() {
        $php_version = PHP_VERSION;
        
        $result = [
            'current_version' => $php_version,
            'min_required' => self::MIN_PHP_VERSION,
            'recommended' => self::RECOMMENDED_PHP_VERSION,
        ];
        
        if (version_compare($php_version, self::MIN_PHP_VERSION, '<')) {
            $result['status'] = 'error';
            $result['message'] = sprintf(
                __('PHP %s or higher is required. You are running %s.', 'ai-content-agent'),
                self::MIN_PHP_VERSION,
                $php_version
            );
        } elseif (version_compare($php_version, self::RECOMMENDED_PHP_VERSION, '<')) {
            $result['status'] = 'warning';
            $result['message'] = sprintf(
                __('PHP %s is recommended for optimal performance. You are running %s.', 'ai-content-agent'),
                self::RECOMMENDED_PHP_VERSION,
                $php_version
            );
        } else {
            $result['status'] = 'success';
            $result['message'] = __('PHP version is compatible.', 'ai-content-agent');
            
            // Check for PHP 8.2 specific features
            if (version_compare($php_version, '8.2', '>=')) {
                $result['php82_features'] = self::check_php82_features();
            }
        }
        
        return $result;
    }
    
    /**
     * Check PHP 8.2 specific features
     */
    private static function check_php82_features() {
        $features = [];
        
        // Check for readonly classes (PHP 8.2)
        if (version_compare(PHP_VERSION, '8.2', '>=')) {
            $features['readonly_classes'] = true;
            $features['dynamic_properties'] = class_exists('AllowDynamicProperties');
        }
        
        // Check for deprecated features that might affect the plugin
        $features['deprecated_checks'] = [
            'utf8_encode' => function_exists('utf8_encode'),
            'utf8_decode' => function_exists('utf8_decode'),
        ];
        
        return $features;
    }
    
    /**
     * Check required PHP functions
     */
    public static function check_required_functions() {
        $required_functions = [
            'curl_init' => 'cURL extension',
            'json_encode' => 'JSON extension',
            'json_decode' => 'JSON extension',
            'wp_remote_post' => 'WordPress HTTP API',
            'wp_remote_get' => 'WordPress HTTP API',
            'wp_schedule_event' => 'WordPress Cron',
        ];
        
        $missing_functions = [];
        $available_functions = [];
        
        foreach ($required_functions as $function => $description) {
            if (!function_exists($function)) {
                $missing_functions[] = [
                    'function' => $function,
                    'description' => $description
                ];
            } else {
                $available_functions[] = $function;
            }
        }
        
        return [
            'status' => empty($missing_functions) ? 'success' : 'error',
            'missing' => $missing_functions,
            'available' => $available_functions,
            'message' => empty($missing_functions) 
                ? __('All required functions are available.', 'ai-content-agent')
                : sprintf(__('%d required functions are missing.', 'ai-content-agent'), count($missing_functions))
        ];
    }
    
    /**
     * Check memory limit
     */
    public static function check_memory_limit() {
        $memory_limit = wp_convert_hr_to_bytes(ini_get('memory_limit'));
        $recommended_memory = 128 * 1024 * 1024; // 128MB
        
        $result = [
            'current_limit' => size_format($memory_limit),
            'recommended_limit' => size_format($recommended_memory),
        ];
        
        if ($memory_limit < $recommended_memory) {
            $result['status'] = 'warning';
            $result['message'] = sprintf(
                __('Memory limit is %s. We recommend at least %s for optimal performance.', 'ai-content-agent'),
                size_format($memory_limit),
                size_format($recommended_memory)
            );
        } else {
            $result['status'] = 'success';
            $result['message'] = __('Memory limit is sufficient.', 'ai-content-agent');
        }
        
        return $result;
    }
    
    /**
     * Check multi-site compatibility
     */
    public static function check_multisite_compatibility() {
        if (!is_multisite()) {
            return [
                'status' => 'success',
                'message' => __('Single site installation detected.', 'ai-content-agent'),
                'is_multisite' => false
            ];
        }
        
        $result = [
            'is_multisite' => true,
            'network_admin' => is_network_admin(),
            'main_site' => is_main_site(),
            'blog_id' => get_current_blog_id(),
            'network_id' => get_current_network_id(),
        ];
        
        // Check network-wide activation
        $network_plugins = get_site_option('active_sitewide_plugins', []);
        $plugin_file = plugin_basename(ACA_PLUGIN_FILE);
        $result['network_activated'] = isset($network_plugins[$plugin_file]);
        
        // Check for multisite-specific issues
        $issues = [];
        
        // Check if plugin is activated on individual sites when it should be network-wide
        if (!$result['network_activated'] && is_plugin_active($plugin_file)) {
            $issues[] = __('Plugin is activated on individual sites. Consider network-wide activation for consistency.', 'ai-content-agent');
        }
        
        // Check for shared tables
        global $wpdb;
        $shared_tables = [
            $wpdb->prefix . 'aca_activity_log',
            $wpdb->prefix . 'aca_content_ideas',
            $wpdb->prefix . 'aca_drafts'
        ];
        
        foreach ($shared_tables as $table) {
            if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
                $issues[] = sprintf(__('Required table %s is missing.', 'ai-content-agent'), $table);
            }
        }
        
        $result['status'] = empty($issues) ? 'success' : 'warning';
        $result['issues'] = $issues;
        $result['message'] = empty($issues) 
            ? __('Multi-site configuration is compatible.', 'ai-content-agent')
            : sprintf(__('%d multi-site issues detected.', 'ai-content-agent'), count($issues));
        
        return $result;
    }
    
    /**
     * Check for plugin conflicts
     */
    public static function check_plugin_conflicts() {
        $active_plugins = get_option('active_plugins', []);
        $network_plugins = get_site_option('active_sitewide_plugins', []);
        $all_active = array_merge($active_plugins, array_keys($network_plugins));
        
        $conflicts = [];
        $warnings = [];
        
        foreach (self::$conflicting_plugins as $plugin_file => $plugin_name) {
            if (in_array($plugin_file, $all_active)) {
                $conflict_info = self::get_plugin_conflict_info($plugin_file);
                
                if ($conflict_info['severity'] === 'high') {
                    $conflicts[] = [
                        'plugin' => $plugin_name,
                        'file' => $plugin_file,
                        'issue' => $conflict_info['issue'],
                        'solution' => $conflict_info['solution']
                    ];
                } else {
                    $warnings[] = [
                        'plugin' => $plugin_name,
                        'file' => $plugin_file,
                        'issue' => $conflict_info['issue'],
                        'solution' => $conflict_info['solution']
                    ];
                }
            }
        }
        
        // Cache the results
        set_transient('aca_plugin_conflicts', ['conflicts' => $conflicts, 'warnings' => $warnings], HOUR_IN_SECONDS);
        
        $status = !empty($conflicts) ? 'error' : (!empty($warnings) ? 'warning' : 'success');
        
        return [
            'status' => $status,
            'conflicts' => $conflicts,
            'warnings' => $warnings,
            'message' => $status === 'success' 
                ? __('No plugin conflicts detected.', 'ai-content-agent')
                : sprintf(__('%d conflicts and %d warnings detected.', 'ai-content-agent'), count($conflicts), count($warnings))
        ];
    }
    
    /**
     * Get specific conflict information for a plugin
     */
    private static function get_plugin_conflict_info($plugin_file) {
        $conflict_details = [
            'wp-rocket/wp-rocket.php' => [
                'severity' => 'medium',
                'issue' => __('WP Rocket may cache AJAX requests, affecting real-time features.', 'ai-content-agent'),
                'solution' => __('Exclude AI Content Agent AJAX endpoints from caching.', 'ai-content-agent')
            ],
            'autoptimize/autoptimize.php' => [
                'severity' => 'medium',
                'issue' => __('Autoptimize may minify JavaScript, potentially breaking functionality.', 'ai-content-agent'),
                'solution' => __('Exclude AI Content Agent scripts from optimization.', 'ai-content-agent')
            ],
            'w3-total-cache/w3-total-cache.php' => [
                'severity' => 'low',
                'issue' => __('W3 Total Cache may interfere with dynamic content generation.', 'ai-content-agent'),
                'solution' => __('Configure cache exclusions for AI Content Agent pages.', 'ai-content-agent')
            ],
            'wp-super-cache/wp-cache.php' => [
                'severity' => 'low',
                'issue' => __('WP Super Cache may cache admin pages.', 'ai-content-agent'),
                'solution' => __('Ensure admin pages are excluded from caching.', 'ai-content-agent')
            ],
            'litespeed-cache/litespeed-cache.php' => [
                'severity' => 'medium',
                'issue' => __('LiteSpeed Cache may interfere with AJAX requests.', 'ai-content-agent'),
                'solution' => __('Add AI Content Agent to cache exclusions.', 'ai-content-agent')
            ],
        ];
        
        return $conflict_details[$plugin_file] ?? [
            'severity' => 'low',
            'issue' => __('Potential compatibility issue detected.', 'ai-content-agent'),
            'solution' => __('Monitor for conflicts and report issues.', 'ai-content-agent')
        ];
    }
    
    /**
     * Display compatibility notices
     */
    public static function display_compatibility_notices() {
        $checks = get_option('aca_compatibility_checks', []);
        $dismissed_notices = get_user_meta(get_current_user_id(), 'aca_dismissed_notices', true) ?: [];
        
        foreach ($checks as $check_type => $check_data) {
            if (!isset($check_data['status']) || $check_data['status'] === 'success') {
                continue;
            }
            
            $notice_key = 'aca_' . $check_type . '_' . md5($check_data['message']);
            
            if (in_array($notice_key, $dismissed_notices)) {
                continue;
            }
            
            $class = $check_data['status'] === 'error' ? 'notice-error' : 'notice-warning';
            
            echo '<div class="notice ' . esc_attr($class) . ' is-dismissible" data-notice-key="' . esc_attr($notice_key) . '">';
            echo '<p><strong>' . __('AI Content Agent:', 'ai-content-agent') . '</strong> ' . esc_html($check_data['message']) . '</p>';
            
            if (isset($check_data['conflicts']) && !empty($check_data['conflicts'])) {
                echo '<ul>';
                foreach ($check_data['conflicts'] as $conflict) {
                    echo '<li><strong>' . esc_html($conflict['plugin']) . ':</strong> ' . esc_html($conflict['issue']);
                    if (!empty($conflict['solution'])) {
                        echo ' <em>' . esc_html($conflict['solution']) . '</em>';
                    }
                    echo '</li>';
                }
                echo '</ul>';
            }
            
            echo '</div>';
        }
        
        // Add JavaScript for dismissible notices
        ?>
        <script>
        jQuery(document).ready(function($) {
            $('.notice[data-notice-key]').on('click', '.notice-dismiss', function() {
                var noticeKey = $(this).parent().data('notice-key');
                $.post(ajaxurl, {
                    action: 'aca_dismiss_compatibility_notice',
                    notice_key: noticeKey,
                    nonce: '<?php echo wp_create_nonce('aca_dismiss_notice'); ?>'
                });
            });
        });
        </script>
        <?php
    }
    
    /**
     * Handle notice dismissal
     */
    public static function dismiss_compatibility_notice() {
        if (!wp_verify_nonce($_POST['nonce'], 'aca_dismiss_notice')) {
            wp_die(__('Security check failed.', 'ai-content-agent'));
        }
        
        $notice_key = sanitize_text_field($_POST['notice_key']);
        $dismissed_notices = get_user_meta(get_current_user_id(), 'aca_dismissed_notices', true) ?: [];
        
        if (!in_array($notice_key, $dismissed_notices)) {
            $dismissed_notices[] = $notice_key;
            update_user_meta(get_current_user_id(), 'aca_dismissed_notices', $dismissed_notices);
        }
        
        wp_die();
    }
    
    /**
     * Display network admin notices
     */
    public static function display_network_notices() {
        if (!is_network_admin()) {
            return;
        }
        
        $network_checks = get_site_option('aca_network_compatibility_checks', []);
        
        if (empty($network_checks)) {
            // Run network compatibility check
            $network_checks = self::run_network_compatibility_check();
        }
        
        foreach ($network_checks as $site_id => $site_checks) {
            $site_info = get_blog_details($site_id);
            if (!$site_info) continue;
            
            $has_errors = false;
            foreach ($site_checks as $check) {
                if (isset($check['status']) && $check['status'] === 'error') {
                    $has_errors = true;
                    break;
                }
            }
            
            if ($has_errors) {
                echo '<div class="notice notice-error">';
                echo '<p><strong>' . sprintf(__('AI Content Agent compatibility issues on site: %s', 'ai-content-agent'), esc_html($site_info->blogname)) . '</strong></p>';
                echo '<p><a href="' . esc_url(get_admin_url($site_id, 'admin.php?page=ai-content-agent')) . '">' . __('View Details', 'ai-content-agent') . '</a></p>';
                echo '</div>';
            }
        }
    }
    
    /**
     * Run network-wide compatibility check
     */
    public static function run_network_compatibility_check() {
        if (!is_multisite()) {
            return [];
        }
        
        $sites = get_sites(['number' => 100]); // Limit to 100 sites for performance
        $network_checks = [];
        
        foreach ($sites as $site) {
            switch_to_blog($site->blog_id);
            
            $network_checks[$site->blog_id] = self::check_compatibility();
            
            restore_current_blog();
        }
        
        update_site_option('aca_network_compatibility_checks', $network_checks);
        
        return $network_checks;
    }
    
    /**
     * Handle network compatibility check AJAX
     */
    public static function network_compatibility_check() {
        if (!current_user_can('manage_network')) {
            wp_die(__('Insufficient permissions.', 'ai-content-agent'));
        }
        
        $results = self::run_network_compatibility_check();
        
        wp_send_json_success([
            'message' => __('Network compatibility check completed.', 'ai-content-agent'),
            'results' => $results
        ]);
    }
    
    /**
     * Clear plugin conflict cache when plugins are activated/deactivated
     */
    public static function clear_plugin_conflict_cache() {
        delete_transient('aca_plugin_conflicts');
        // Re-run compatibility check
        self::check_compatibility();
    }
    
    /**
     * Get system information for debugging
     */
    public static function get_system_info() {
        global $wp_version, $wpdb;
        
        $system_info = [
            'wordpress' => [
                'version' => $wp_version,
                'multisite' => is_multisite(),
                'language' => get_locale(),
                'timezone' => wp_timezone_string(),
                'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
                'memory_limit' => wp_convert_hr_to_bytes(ini_get('memory_limit')),
            ],
            'php' => [
                'version' => PHP_VERSION,
                'sapi' => php_sapi_name(),
                'max_execution_time' => ini_get('max_execution_time'),
                'max_input_vars' => ini_get('max_input_vars'),
                'post_max_size' => wp_convert_hr_to_bytes(ini_get('post_max_size')),
                'upload_max_filesize' => wp_convert_hr_to_bytes(ini_get('upload_max_filesize')),
            ],
            'server' => [
                'software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'php_version' => phpversion(),
                'mysql_version' => $wpdb->db_version(),
                'https' => is_ssl(),
            ],
            'plugin' => [
                'version' => defined('ACA_VERSION') ? ACA_VERSION : 'Unknown',
                'file' => plugin_basename(ACA_PLUGIN_FILE),
                'active_plugins' => get_option('active_plugins', []),
                'network_plugins' => get_site_option('active_sitewide_plugins', []),
            ]
        ];
        
        return $system_info;
    }
    
    /**
     * Export compatibility report
     */
    public static function export_compatibility_report() {
        $compatibility_checks = get_option('aca_compatibility_checks', []);
        $system_info = self::get_system_info();
        
        $report = [
            'generated_at' => current_time('mysql'),
            'site_url' => get_site_url(),
            'system_info' => $system_info,
            'compatibility_checks' => $compatibility_checks,
        ];
        
        return $report;
    }
}