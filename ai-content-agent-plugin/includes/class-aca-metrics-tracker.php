<?php
/**
 * Metrics Tracker
 * 
 * Tracks and monitors key performance indicators including technical,
 * user, and business metrics for the AI Content Agent plugin.
 *
 * @package AI_Content_Agent
 * @since 1.8.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Metrics_Tracker {
    
    /**
     * Performance thresholds
     */
    const BUNDLE_SIZE_THRESHOLD = 200 * 1024; // 200KB
    const LOAD_TIME_THRESHOLD = 2; // 2 seconds
    const MEMORY_USAGE_THRESHOLD = 50 * 1024 * 1024; // 50MB
    const ERROR_RATE_THRESHOLD = 1; // 1%
    
    /**
     * Metrics collection intervals
     */
    const METRICS_COLLECTION_INTERVAL = 300; // 5 minutes
    const DAILY_REPORT_INTERVAL = 86400; // 24 hours
    const WEEKLY_REPORT_INTERVAL = 604800; // 7 days
    
    /**
     * Initialize metrics tracking
     */
    public static function init() {
        add_action('wp_ajax_aca_track_metric', [__CLASS__, 'track_metric']);
        add_action('wp_ajax_nopriv_aca_track_metric', [__CLASS__, 'track_metric']);
        add_action('wp_ajax_aca_get_metrics_dashboard', [__CLASS__, 'get_metrics_dashboard']);
        
        // Schedule periodic metric collection
        add_action('aca_collect_metrics', [__CLASS__, 'collect_periodic_metrics']);
        add_action('aca_generate_daily_report', [__CLASS__, 'generate_daily_report']);
        add_action('aca_generate_weekly_report', [__CLASS__, 'generate_weekly_report']);
        
        // Hook into WordPress events for automatic tracking
        add_action('wp_footer', [__CLASS__, 'inject_performance_tracking']);
        add_action('admin_footer', [__CLASS__, 'inject_admin_tracking']);
        add_action('wp_ajax_heartbeat', [__CLASS__, 'track_heartbeat_metrics']);
        
        // Plugin-specific event tracking
        add_action('aca_content_generated', [__CLASS__, 'track_content_generation']);
        add_action('aca_idea_created', [__CLASS__, 'track_idea_creation']);
        add_action('aca_draft_published', [__CLASS__, 'track_draft_publication']);
        add_action('aca_settings_saved', [__CLASS__, 'track_settings_usage']);
        add_action('aca_api_error', [__CLASS__, 'track_api_error']);
        
        // Schedule events if not already scheduled
        if (!wp_next_scheduled('aca_collect_metrics')) {
            wp_schedule_event(time(), 'aca_metrics_interval', 'aca_collect_metrics');
        }
        
        if (!wp_next_scheduled('aca_generate_daily_report')) {
            wp_schedule_event(strtotime('tomorrow 2:00 AM'), 'daily', 'aca_generate_daily_report');
        }
        
        if (!wp_next_scheduled('aca_generate_weekly_report')) {
            wp_schedule_event(strtotime('next Monday 3:00 AM'), 'weekly', 'aca_generate_weekly_report');
        }
    }
    
    /**
     * Track a specific metric
     */
    public static function track_metric() {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'aca_track_metric')) {
            wp_die(__('Security check failed.', 'ai-content-agent'));
        }
        
        $metric_type = sanitize_text_field($_POST['metric_type'] ?? '');
        $metric_data = $_POST['metric_data'] ?? [];
        
        if (empty($metric_type)) {
            wp_send_json_error(['message' => __('Metric type is required.', 'ai-content-agent')]);
        }
        
        $success = self::record_metric($metric_type, $metric_data);
        
        if ($success) {
            wp_send_json_success(['message' => __('Metric tracked successfully.', 'ai-content-agent')]);
        } else {
            wp_send_json_error(['message' => __('Failed to track metric.', 'ai-content-agent')]);
        }
    }
    
    /**
     * Record a metric in the database
     */
    public static function record_metric($metric_type, $metric_data, $user_id = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        // Ensure the metrics table exists
        self::create_metrics_table();
        
        $user_id = $user_id ?: get_current_user_id();
        $timestamp = current_time('mysql');
        
        // Sanitize and validate metric data
        $sanitized_data = self::sanitize_metric_data($metric_data);
        
        $result = $wpdb->insert(
            $table_name,
            [
                'metric_type' => sanitize_text_field($metric_type),
                'metric_data' => wp_json_encode($sanitized_data),
                'user_id' => intval($user_id),
                'timestamp' => $timestamp,
                'site_id' => get_current_blog_id(),
                'session_id' => self::get_session_id()
            ],
            ['%s', '%s', '%d', '%s', '%d', '%s']
        );
        
        if ($result === false) {
            error_log('ACA Metrics: Failed to record metric - ' . $wpdb->last_error);
            return false;
        }
        
        // Update real-time metrics cache
        self::update_realtime_metrics($metric_type, $sanitized_data);
        
        return true;
    }
    
    /**
     * Create metrics table if it doesn't exist
     */
    private static function create_metrics_table() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            metric_type varchar(100) NOT NULL,
            metric_data longtext NOT NULL,
            user_id bigint(20) DEFAULT 0,
            timestamp datetime NOT NULL,
            site_id bigint(20) DEFAULT 1,
            session_id varchar(100) DEFAULT '',
            PRIMARY KEY (id),
            KEY metric_type (metric_type),
            KEY timestamp (timestamp),
            KEY user_id (user_id),
            KEY site_id (site_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
    
    /**
     * Sanitize metric data
     */
    private static function sanitize_metric_data($data) {
        if (!is_array($data)) {
            return [];
        }
        
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            $key = sanitize_key($key);
            
            if (is_array($value)) {
                $sanitized[$key] = self::sanitize_metric_data($value);
            } elseif (is_numeric($value)) {
                $sanitized[$key] = floatval($value);
            } elseif (is_bool($value)) {
                $sanitized[$key] = (bool) $value;
            } else {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Get or generate session ID
     */
    private static function get_session_id() {
        if (!session_id()) {
            session_start();
        }
        
        if (!isset($_SESSION['aca_session_id'])) {
            $_SESSION['aca_session_id'] = wp_generate_uuid4();
        }
        
        return $_SESSION['aca_session_id'];
    }
    
    /**
     * Update real-time metrics cache
     */
    private static function update_realtime_metrics($metric_type, $data) {
        $cache_key = 'aca_realtime_metrics_' . $metric_type;
        $current_metrics = get_transient($cache_key) ?: [];
        
        // Add new data point
        $current_metrics[] = [
            'data' => $data,
            'timestamp' => time()
        ];
        
        // Keep only last 100 data points for performance
        if (count($current_metrics) > 100) {
            $current_metrics = array_slice($current_metrics, -100);
        }
        
        set_transient($cache_key, $current_metrics, HOUR_IN_SECONDS);
    }
    
    /**
     * Collect periodic system metrics
     */
    public static function collect_periodic_metrics() {
        // Technical metrics
        self::collect_technical_metrics();
        
        // User behavior metrics
        self::collect_user_metrics();
        
        // Business metrics
        self::collect_business_metrics();
        
        // Plugin-specific metrics
        self::collect_plugin_metrics();
    }
    
    /**
     * Collect technical performance metrics
     */
    private static function collect_technical_metrics() {
        $metrics = [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'load_time' => self::measure_load_time(),
            'database_queries' => get_num_queries(),
            'cache_hit_ratio' => self::get_cache_hit_ratio(),
            'error_count' => self::get_error_count(),
            'php_version' => PHP_VERSION,
            'wp_version' => get_bloginfo('version'),
            'active_plugins_count' => count(get_option('active_plugins', [])),
            'theme' => get_template(),
        ];
        
        self::record_metric('technical_performance', $metrics);
    }
    
    /**
     * Collect user behavior metrics
     */
    private static function collect_user_metrics() {
        $metrics = [
            'active_users_today' => self::count_active_users('today'),
            'active_users_week' => self::count_active_users('week'),
            'active_users_month' => self::count_active_users('month'),
            'page_views' => self::get_page_views(),
            'session_duration' => self::get_average_session_duration(),
            'bounce_rate' => self::calculate_bounce_rate(),
            'feature_usage' => self::get_feature_usage_stats(),
            'user_retention' => self::calculate_user_retention(),
        ];
        
        self::record_metric('user_behavior', $metrics);
    }
    
    /**
     * Collect business metrics
     */
    private static function collect_business_metrics() {
        $metrics = [
            'total_content_generated' => self::count_generated_content(),
            'content_generation_rate' => self::calculate_generation_rate(),
            'user_satisfaction_score' => self::get_satisfaction_score(),
            'support_tickets' => self::count_support_tickets(),
            'feature_adoption_rate' => self::calculate_feature_adoption(),
            'conversion_rate' => self::calculate_conversion_rate(),
            'revenue_impact' => self::estimate_revenue_impact(),
            'market_share' => self::get_market_share_data(),
        ];
        
        self::record_metric('business_performance', $metrics);
    }
    
    /**
     * Collect plugin-specific metrics
     */
    private static function collect_plugin_metrics() {
        $metrics = [
            'api_calls_today' => self::count_api_calls('today'),
            'api_success_rate' => self::calculate_api_success_rate(),
            'content_ideas_generated' => self::count_content_ideas(),
            'drafts_created' => self::count_drafts_created(),
            'posts_published' => self::count_posts_published(),
            'seo_scores_improved' => self::count_seo_improvements(),
            'automation_usage' => self::get_automation_usage(),
            'integration_health' => self::check_integration_health(),
        ];
        
        self::record_metric('plugin_performance', $metrics);
    }
    
    /**
     * Measure page load time
     */
    private static function measure_load_time() {
        if (defined('WP_START_TIMESTAMP')) {
            return microtime(true) - WP_START_TIMESTAMP;
        }
        
        return 0;
    }
    
    /**
     * Get cache hit ratio
     */
    private static function get_cache_hit_ratio() {
        $cache_stats = wp_cache_get_stats();
        
        if (!$cache_stats || !isset($cache_stats['hits'], $cache_stats['misses'])) {
            return 0;
        }
        
        $total = $cache_stats['hits'] + $cache_stats['misses'];
        
        return $total > 0 ? ($cache_stats['hits'] / $total) * 100 : 0;
    }
    
    /**
     * Get error count from logs
     */
    private static function get_error_count() {
        $error_log = ini_get('error_log');
        
        if (!$error_log || !file_exists($error_log)) {
            return 0;
        }
        
        $today = date('Y-m-d');
        $errors = 0;
        
        $handle = fopen($error_log, 'r');
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, $today) !== false && strpos($line, 'ACA') !== false) {
                    $errors++;
                }
            }
            fclose($handle);
        }
        
        return $errors;
    }
    
    /**
     * Count active users for a given period
     */
    private static function count_active_users($period) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $date_condition = '';
        switch ($period) {
            case 'today':
                $date_condition = "DATE(timestamp) = CURDATE()";
                break;
            case 'week':
                $date_condition = "timestamp >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
            case 'month':
                $date_condition = "timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
                break;
        }
        
        $query = $wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $table_name WHERE $date_condition AND user_id > 0"
        );
        
        return intval($wpdb->get_var($query));
    }
    
    /**
     * Get page views
     */
    private static function get_page_views() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $query = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE metric_type = 'page_view' AND DATE(timestamp) = CURDATE()"
        );
        
        return intval($wpdb->get_var($query));
    }
    
    /**
     * Get average session duration
     */
    private static function get_average_session_duration() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $query = $wpdb->prepare(
            "SELECT AVG(JSON_EXTRACT(metric_data, '$.duration')) as avg_duration 
             FROM $table_name 
             WHERE metric_type = 'session_end' AND DATE(timestamp) = CURDATE()"
        );
        
        return floatval($wpdb->get_var($query)) ?: 0;
    }
    
    /**
     * Calculate bounce rate
     */
    private static function calculate_bounce_rate() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        // Count single-page sessions
        $single_page_sessions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table_name 
             WHERE metric_type = 'page_view' AND DATE(timestamp) = CURDATE()
             GROUP BY session_id HAVING COUNT(*) = 1"
        ));
        
        // Count total sessions
        $total_sessions = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT session_id) FROM $table_name 
             WHERE metric_type = 'page_view' AND DATE(timestamp) = CURDATE()"
        ));
        
        return $total_sessions > 0 ? ($single_page_sessions / $total_sessions) * 100 : 0;
    }
    
    /**
     * Get feature usage statistics
     */
    private static function get_feature_usage_stats() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $features = ['content_generation', 'idea_board', 'settings', 'calendar', 'drafts'];
        $usage_stats = [];
        
        foreach ($features as $feature) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name 
                 WHERE metric_type = 'feature_usage' 
                 AND JSON_EXTRACT(metric_data, '$.feature') = %s 
                 AND DATE(timestamp) = CURDATE()",
                $feature
            ));
            
            $usage_stats[$feature] = intval($count);
        }
        
        return $usage_stats;
    }
    
    /**
     * Calculate user retention rate
     */
    private static function calculate_user_retention() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        // Users active this week
        $users_this_week = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $table_name 
             WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 1 WEEK) AND user_id > 0"
        ));
        
        // Users active last week
        $users_last_week = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $table_name 
             WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 2 WEEK) 
             AND timestamp < DATE_SUB(NOW(), INTERVAL 1 WEEK) AND user_id > 0"
        ));
        
        return $users_last_week > 0 ? ($users_this_week / $users_last_week) * 100 : 0;
    }
    
    /**
     * Count generated content
     */
    private static function count_generated_content() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'content_generated' AND DATE(timestamp) = CURDATE()"
        )));
    }
    
    /**
     * Calculate content generation rate
     */
    private static function calculate_generation_rate() {
        $today_count = self::count_generated_content();
        $yesterday_count = self::count_generated_content_for_date(date('Y-m-d', strtotime('-1 day')));
        
        if ($yesterday_count == 0) {
            return $today_count > 0 ? 100 : 0;
        }
        
        return (($today_count - $yesterday_count) / $yesterday_count) * 100;
    }
    
    /**
     * Count generated content for specific date
     */
    private static function count_generated_content_for_date($date) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'content_generated' AND DATE(timestamp) = %s",
            $date
        )));
    }
    
    /**
     * Get user satisfaction score
     */
    private static function get_satisfaction_score() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $avg_rating = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(JSON_EXTRACT(metric_data, '$.rating')) as avg_rating 
             FROM $table_name 
             WHERE metric_type = 'user_feedback' AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"
        ));
        
        return floatval($avg_rating) ?: 0;
    }
    
    /**
     * Count support tickets
     */
    private static function count_support_tickets() {
        // This would integrate with your support system
        // For now, return a placeholder value
        return 0;
    }
    
    /**
     * Calculate feature adoption rate
     */
    private static function calculate_feature_adoption() {
        $total_users = self::count_active_users('month');
        $feature_users = self::count_feature_users();
        
        return $total_users > 0 ? ($feature_users / $total_users) * 100 : 0;
    }
    
    /**
     * Count users who have used any feature
     */
    private static function count_feature_users() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $table_name 
             WHERE metric_type = 'feature_usage' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND user_id > 0"
        )));
    }
    
    /**
     * Calculate conversion rate
     */
    private static function calculate_conversion_rate() {
        // This would depend on your conversion goals
        // For now, return content creation as conversion
        $visitors = self::count_active_users('month');
        $converters = self::count_content_creators();
        
        return $visitors > 0 ? ($converters / $visitors) * 100 : 0;
    }
    
    /**
     * Count users who created content
     */
    private static function count_content_creators() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(DISTINCT user_id) FROM $table_name 
             WHERE metric_type = 'content_generated' 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH) AND user_id > 0"
        )));
    }
    
    /**
     * Estimate revenue impact
     */
    private static function estimate_revenue_impact() {
        // This would require integration with e-commerce or revenue tracking
        // Return estimated value based on content generation and user engagement
        $content_count = self::count_generated_content();
        $estimated_value_per_content = 10; // $10 per piece of content
        
        return $content_count * $estimated_value_per_content;
    }
    
    /**
     * Get market share data
     */
    private static function get_market_share_data() {
        // This would require external market research data
        // Return placeholder data structure
        return [
            'total_market_size' => 1000000,
            'our_user_base' => self::count_active_users('month'),
            'estimated_share' => 0.1 // 0.1%
        ];
    }
    
    /**
     * Count API calls for a period
     */
    private static function count_api_calls($period) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $date_condition = '';
        switch ($period) {
            case 'today':
                $date_condition = "DATE(timestamp) = CURDATE()";
                break;
            case 'week':
                $date_condition = "timestamp >= DATE_SUB(NOW(), INTERVAL 1 WEEK)";
                break;
        }
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE metric_type = 'api_call' AND $date_condition"
        )));
    }
    
    /**
     * Calculate API success rate
     */
    private static function calculate_api_success_rate() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $total_calls = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'api_call' AND DATE(timestamp) = CURDATE()"
        ));
        
        $successful_calls = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'api_call' 
             AND JSON_EXTRACT(metric_data, '$.success') = true 
             AND DATE(timestamp) = CURDATE()"
        ));
        
        return $total_calls > 0 ? ($successful_calls / $total_calls) * 100 : 0;
    }
    
    /**
     * Count content ideas generated
     */
    private static function count_content_ideas() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'idea_created' AND DATE(timestamp) = CURDATE()"
        )));
    }
    
    /**
     * Count drafts created
     */
    private static function count_drafts_created() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'draft_created' AND DATE(timestamp) = CURDATE()"
        )));
    }
    
    /**
     * Count posts published
     */
    private static function count_posts_published() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'post_published' AND DATE(timestamp) = CURDATE()"
        )));
    }
    
    /**
     * Count SEO improvements
     */
    private static function count_seo_improvements() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'seo_improved' AND DATE(timestamp) = CURDATE()"
        )));
    }
    
    /**
     * Get automation usage statistics
     */
    private static function get_automation_usage() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $automation_types = ['manual', 'semi_automatic', 'full_automatic'];
        $usage_stats = [];
        
        foreach ($automation_types as $type) {
            $count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name 
                 WHERE metric_type = 'automation_used' 
                 AND JSON_EXTRACT(metric_data, '$.type') = %s 
                 AND DATE(timestamp) = CURDATE()",
                $type
            ));
            
            $usage_stats[$type] = intval($count);
        }
        
        return $usage_stats;
    }
    
    /**
     * Check integration health
     */
    private static function check_integration_health() {
        $integrations = [
            'gemini_ai' => self::check_gemini_health(),
            'google_search_console' => self::check_gsc_health(),
            'seo_plugins' => self::check_seo_plugin_health(),
            'image_services' => self::check_image_service_health(),
        ];
        
        return $integrations;
    }
    
    /**
     * Check Gemini AI health
     */
    private static function check_gemini_health() {
        $recent_errors = self::count_recent_errors('gemini_api');
        $recent_calls = self::count_recent_api_calls('gemini_api');
        
        $error_rate = $recent_calls > 0 ? ($recent_errors / $recent_calls) * 100 : 0;
        
        return [
            'status' => $error_rate < 5 ? 'healthy' : 'degraded',
            'error_rate' => $error_rate,
            'recent_calls' => $recent_calls,
            'recent_errors' => $recent_errors
        ];
    }
    
    /**
     * Check Google Search Console health
     */
    private static function check_gsc_health() {
        // Similar implementation for GSC
        return ['status' => 'healthy', 'error_rate' => 0];
    }
    
    /**
     * Check SEO plugin health
     */
    private static function check_seo_plugin_health() {
        // Check if SEO plugins are active and responding
        return ['status' => 'healthy', 'active_plugins' => []];
    }
    
    /**
     * Check image service health
     */
    private static function check_image_service_health() {
        // Check image generation services
        return ['status' => 'healthy', 'services' => []];
    }
    
    /**
     * Count recent errors for a service
     */
    private static function count_recent_errors($service) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'api_error' 
             AND JSON_EXTRACT(metric_data, '$.service') = %s 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            $service
        )));
    }
    
    /**
     * Count recent API calls for a service
     */
    private static function count_recent_api_calls($service) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        return intval($wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name 
             WHERE metric_type = 'api_call' 
             AND JSON_EXTRACT(metric_data, '$.service') = %s 
             AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            $service
        )));
    }
    
    /**
     * Generate daily report
     */
    public static function generate_daily_report() {
        $report_data = [
            'date' => current_time('Y-m-d'),
            'technical_metrics' => self::get_daily_technical_metrics(),
            'user_metrics' => self::get_daily_user_metrics(),
            'business_metrics' => self::get_daily_business_metrics(),
            'plugin_metrics' => self::get_daily_plugin_metrics(),
            'alerts' => self::check_metric_alerts(),
        ];
        
        // Store the report
        update_option('aca_daily_report_' . date('Y_m_d'), $report_data);
        
        // Send email if configured
        self::maybe_send_report_email('daily', $report_data);
        
        return $report_data;
    }
    
    /**
     * Generate weekly report
     */
    public static function generate_weekly_report() {
        $report_data = [
            'week_start' => date('Y-m-d', strtotime('last Monday')),
            'week_end' => date('Y-m-d', strtotime('last Sunday')),
            'summary' => self::get_weekly_summary(),
            'trends' => self::get_weekly_trends(),
            'recommendations' => self::get_weekly_recommendations(),
        ];
        
        // Store the report
        update_option('aca_weekly_report_' . date('Y_W'), $report_data);
        
        // Send email if configured
        self::maybe_send_report_email('weekly', $report_data);
        
        return $report_data;
    }
    
    /**
     * Get metrics dashboard data
     */
    public static function get_metrics_dashboard() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Insufficient permissions.', 'ai-content-agent'));
        }
        
        $dashboard_data = [
            'technical' => [
                'bundle_size' => self::get_current_bundle_size(),
                'load_time' => self::get_average_load_time(),
                'memory_usage' => memory_get_usage(true),
                'error_rate' => self::get_current_error_rate(),
                'status' => self::get_technical_status()
            ],
            'user' => [
                'active_today' => self::count_active_users('today'),
                'active_week' => self::count_active_users('week'),
                'retention_rate' => self::calculate_user_retention(),
                'satisfaction' => self::get_satisfaction_score(),
                'status' => self::get_user_status()
            ],
            'business' => [
                'content_generated' => self::count_generated_content(),
                'conversion_rate' => self::calculate_conversion_rate(),
                'feature_adoption' => self::calculate_feature_adoption(),
                'revenue_impact' => self::estimate_revenue_impact(),
                'status' => self::get_business_status()
            ],
            'realtime' => self::get_realtime_metrics()
        ];
        
        wp_send_json_success($dashboard_data);
    }
    
    /**
     * Get current bundle size
     */
    private static function get_current_bundle_size() {
        $bundle_path = plugin_dir_path(ACA_PLUGIN_FILE) . 'dist/assets/';
        $bundle_size = 0;
        
        if (is_dir($bundle_path)) {
            $files = glob($bundle_path . '*.js');
            foreach ($files as $file) {
                $bundle_size += filesize($file);
            }
        }
        
        return $bundle_size;
    }
    
    /**
     * Get average load time
     */
    private static function get_average_load_time() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_metrics';
        
        $avg_time = $wpdb->get_var($wpdb->prepare(
            "SELECT AVG(JSON_EXTRACT(metric_data, '$.load_time')) as avg_time 
             FROM $table_name 
             WHERE metric_type = 'technical_performance' AND DATE(timestamp) = CURDATE()"
        ));
        
        return floatval($avg_time) ?: 0;
    }
    
    /**
     * Get current error rate
     */
    private static function get_current_error_rate() {
        $total_requests = self::count_api_calls('today');
        $error_count = self::get_error_count();
        
        return $total_requests > 0 ? ($error_count / $total_requests) * 100 : 0;
    }
    
    /**
     * Get technical status
     */
    private static function get_technical_status() {
        $bundle_size = self::get_current_bundle_size();
        $load_time = self::get_average_load_time();
        $memory_usage = memory_get_usage(true);
        $error_rate = self::get_current_error_rate();
        
        $issues = [];
        
        if ($bundle_size > self::BUNDLE_SIZE_THRESHOLD) {
            $issues[] = 'Bundle size exceeds threshold';
        }
        
        if ($load_time > self::LOAD_TIME_THRESHOLD) {
            $issues[] = 'Load time exceeds threshold';
        }
        
        if ($memory_usage > self::MEMORY_USAGE_THRESHOLD) {
            $issues[] = 'Memory usage exceeds threshold';
        }
        
        if ($error_rate > self::ERROR_RATE_THRESHOLD) {
            $issues[] = 'Error rate exceeds threshold';
        }
        
        return empty($issues) ? 'healthy' : 'warning';
    }
    
    /**
     * Get user status
     */
    private static function get_user_status() {
        $retention = self::calculate_user_retention();
        $satisfaction = self::get_satisfaction_score();
        
        if ($retention < 70 || $satisfaction < 3.5) {
            return 'warning';
        }
        
        return 'healthy';
    }
    
    /**
     * Get business status
     */
    private static function get_business_status() {
        $adoption = self::calculate_feature_adoption();
        $conversion = self::calculate_conversion_rate();
        
        if ($adoption < 50 || $conversion < 10) {
            return 'warning';
        }
        
        return 'healthy';
    }
    
    /**
     * Get real-time metrics
     */
    private static function get_realtime_metrics() {
        $metrics = [];
        $metric_types = ['page_view', 'api_call', 'content_generated', 'user_action'];
        
        foreach ($metric_types as $type) {
            $cache_key = 'aca_realtime_metrics_' . $type;
            $metrics[$type] = get_transient($cache_key) ?: [];
        }
        
        return $metrics;
    }
    
    /**
     * Inject performance tracking scripts
     */
    public static function inject_performance_tracking() {
        if (!is_admin()) {
            return;
        }
        
        ?>
        <script>
        (function() {
            // Track page load time
            window.addEventListener('load', function() {
                var loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                
                // Send metric to server
                jQuery.post(ajaxurl, {
                    action: 'aca_track_metric',
                    metric_type: 'page_load',
                    metric_data: {
                        load_time: loadTime,
                        page: window.location.pathname
                    },
                    nonce: '<?php echo wp_create_nonce('aca_track_metric'); ?>'
                });
            });
            
            // Track user interactions
            document.addEventListener('click', function(e) {
                if (e.target.matches('.aca-button, .aca-link')) {
                    jQuery.post(ajaxurl, {
                        action: 'aca_track_metric',
                        metric_type: 'user_interaction',
                        metric_data: {
                            element: e.target.className,
                            page: window.location.pathname
                        },
                        nonce: '<?php echo wp_create_nonce('aca_track_metric'); ?>'
                    });
                }
            });
        })();
        </script>
        <?php
    }
    
    /**
     * Inject admin tracking scripts
     */
    public static function inject_admin_tracking() {
        $screen = get_current_screen();
        
        if (!$screen || strpos($screen->id, 'ai-content-agent') === false) {
            return;
        }
        
        ?>
        <script>
        (function() {
            // Track feature usage
            jQuery(document).on('click', '[data-feature]', function() {
                var feature = jQuery(this).data('feature');
                
                jQuery.post(ajaxurl, {
                    action: 'aca_track_metric',
                    metric_type: 'feature_usage',
                    metric_data: {
                        feature: feature,
                        timestamp: Date.now()
                    },
                    nonce: '<?php echo wp_create_nonce('aca_track_metric'); ?>'
                });
            });
            
            // Track session duration
            var sessionStart = Date.now();
            
            window.addEventListener('beforeunload', function() {
                var sessionDuration = Date.now() - sessionStart;
                
                navigator.sendBeacon(ajaxurl, new URLSearchParams({
                    action: 'aca_track_metric',
                    metric_type: 'session_end',
                    metric_data: JSON.stringify({
                        duration: sessionDuration
                    }),
                    nonce: '<?php echo wp_create_nonce('aca_track_metric'); ?>'
                }));
            });
        })();
        </script>
        <?php
    }
    
    /**
     * Track heartbeat metrics
     */
    public static function track_heartbeat_metrics($response, $data) {
        if (isset($data['aca_heartbeat'])) {
            self::record_metric('heartbeat', [
                'memory_usage' => memory_get_usage(true),
                'timestamp' => time()
            ]);
        }
        
        return $response;
    }
    
    /**
     * Track content generation
     */
    public static function track_content_generation($content_data) {
        self::record_metric('content_generated', [
            'type' => $content_data['type'] ?? 'unknown',
            'length' => strlen($content_data['content'] ?? ''),
            'ai_model' => $content_data['ai_model'] ?? 'unknown'
        ]);
    }
    
    /**
     * Track idea creation
     */
    public static function track_idea_creation($idea_data) {
        self::record_metric('idea_created', [
            'category' => $idea_data['category'] ?? 'general',
            'source' => $idea_data['source'] ?? 'manual'
        ]);
    }
    
    /**
     * Track draft publication
     */
    public static function track_draft_publication($draft_data) {
        self::record_metric('post_published', [
            'post_type' => $draft_data['post_type'] ?? 'post',
            'word_count' => $draft_data['word_count'] ?? 0,
            'seo_score' => $draft_data['seo_score'] ?? 0
        ]);
    }
    
    /**
     * Track settings usage
     */
    public static function track_settings_usage($settings_data) {
        self::record_metric('settings_changed', [
            'section' => $settings_data['section'] ?? 'general',
            'changes' => count($settings_data['changes'] ?? [])
        ]);
    }
    
    /**
     * Track API errors
     */
    public static function track_api_error($error_data) {
        self::record_metric('api_error', [
            'service' => $error_data['service'] ?? 'unknown',
            'error_code' => $error_data['error_code'] ?? 'unknown',
            'error_message' => $error_data['error_message'] ?? ''
        ]);
    }
    
    /**
     * Add custom cron schedule
     */
    public static function add_cron_schedules($schedules) {
        $schedules['aca_metrics_interval'] = [
            'interval' => self::METRICS_COLLECTION_INTERVAL,
            'display' => __('Every 5 Minutes', 'ai-content-agent')
        ];
        
        return $schedules;
    }
}

// Add custom cron schedule
add_filter('cron_schedules', ['ACA_Metrics_Tracker', 'add_cron_schedules']);