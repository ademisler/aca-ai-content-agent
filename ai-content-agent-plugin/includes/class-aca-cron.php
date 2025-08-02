<?php
/**
 * WP-Cron functionality for automated tasks
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Cron {
    
    public function __construct() {
        $this->init();
    }
    
    public function init() {
        // Add custom cron schedules
        add_filter('cron_schedules', array($this, 'add_cron_intervals'));
        
        // Cron events are now hooked in main plugin file as static methods
    }
    
    /**
     * Add custom cron intervals
     */
    public function add_cron_intervals($schedules) {
        $schedules['aca_thirty_minutes'] = array(
            'interval' => 30 * 60, // 30 minutes in seconds
            'display' => __('Every 30 Minutes', 'ai-content-agent-v2.4.6-production-stable')
        );
        
        $schedules['aca_fifteen_minutes'] = array(
            'interval' => 15 * 60, // 15 minutes in seconds
            'display' => __('Every 15 Minutes', 'ai-content-agent-v2.4.6-production-stable')
        );
        
        return $schedules;
    }
    
    /**
     * Task that runs every 30 minutes
     * Handles full-automatic mode operations
     */
    public static function thirty_minute_task() {
        // Prevent overlapping executions
        $lock_key = 'aca_thirty_minute_task_lock';
        if (get_transient($lock_key)) {
            aca_debug_log('Cron: 30-minute task already running, skipping this execution');
            return;
        }
        
        // Set lock for maximum expected execution time
        set_transient($lock_key, time(), 600); // 10 minutes lock
        
        try {
            // Cron context detection and resource optimization
            $is_cron = defined('DOING_CRON') && DOING_CRON;
            $is_manual_trigger = !$is_cron && (defined('WP_CLI') && WP_CLI) || is_admin();
            
            // Store original limits for restoration
            $original_memory_limit = ini_get('memory_limit');
            $original_time_limit = ini_get('max_execution_time');
            
            if ($is_cron) {
                // Optimize for cron environment
                if (function_exists('ini_set')) {
                    ini_set('memory_limit', '512M');
                }
                if (function_exists('set_time_limit')) {
                    set_time_limit(300); // 5 minutes max execution
                }
                aca_debug_log('Cron: 30-minute task started in CRON context (Memory: 512M, Time: 300s)');
            } else if ($is_manual_trigger) {
                aca_debug_log('Cron: 30-minute task started manually');
            }
        
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['geminiApiKey'])) {
            if ($is_cron) {
                aca_debug_log('Cron: Skipping 30-minute task - no Gemini API key');
            }
            return;
        }
        
        // Update last run time
        update_option('aca_last_cron_run', current_time('mysql') . ' (30min - Full-Auto)');
        
        // Auto-analyze style guide
        self::auto_analyze_style_guide();
        
        // Run content freshness analysis
        self::content_freshness_task();
        
        // Run full content cycle if in full-automatic mode
        if (isset($settings['mode']) && $settings['mode'] === 'full-automatic') {
            // Check if pro license is active for full-automatic mode
            if (is_aca_pro_active()) {
                self::run_full_automatic_cycle();
            } else {
                aca_debug_log('Full-automatic mode requires Pro license');
            }
        }
        
        } finally {
            // Restore original resource limits
            if (isset($original_memory_limit)) {
                ini_set('memory_limit', $original_memory_limit);
            }
            if (isset($original_time_limit)) {
                set_time_limit($original_time_limit);
            }
            
            // Always release the lock
            delete_transient($lock_key);
            aca_debug_log('Cron: 30-minute task completed, lock released, limits restored');
        }
    }
    
    /**
     * Task that runs every 15 minutes
     * Handles semi-automatic mode operations
     */
    public static function fifteen_minute_task() {
        // Prevent overlapping executions
        $lock_key = 'aca_fifteen_minute_task_lock';
        if (get_transient($lock_key)) {
            aca_debug_log('Cron: 15-minute task already running, skipping this execution');
            return;
        }
        
        // Set lock for maximum expected execution time
        set_transient($lock_key, time(), 300); // 5 minutes lock
        
        try {
            // Cron context detection and resource optimization
            $is_cron = defined('DOING_CRON') && DOING_CRON;
            $is_manual_trigger = !$is_cron && (defined('WP_CLI') && WP_CLI) || is_admin();
            
            // Store original limits for restoration
            $original_memory_limit = ini_get('memory_limit');
            $original_time_limit = ini_get('max_execution_time');
        
        if ($is_cron) {
            // Optimize for cron environment - lighter resource usage for 15min task
            if (function_exists('ini_set')) {
                ini_set('memory_limit', '256M');
            }
            if (function_exists('set_time_limit')) {
                set_time_limit(180); // 3 minutes max execution
            }
            aca_debug_log('Cron: 15-minute task started in CRON context (Memory: 256M, Time: 180s)');
        } else if ($is_manual_trigger) {
            aca_debug_log('Cron: 15-minute task started manually');
        }
        
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['geminiApiKey'])) {
            if ($is_cron) {
                aca_debug_log('Cron: Skipping 15-minute task - no Gemini API key');
            }
            return;
        }
        
        // Update last run time
        update_option('aca_last_cron_run', current_time('mysql') . ' (15min - Semi-Auto)');
        
        // Generate ideas in semi-automatic mode
        if (isset($settings['mode']) && $settings['mode'] === 'semi-automatic') {
            // Check if pro license is active for semi-automatic mode
            if (is_aca_pro_active()) {
                self::generate_ideas_semi_auto();
            } else {
                aca_debug_log('Semi-automatic mode requires Pro license');
            }
        }
        
        } finally {
            // Restore original resource limits
            if (isset($original_memory_limit) && function_exists('ini_set')) {
                ini_set('memory_limit', $original_memory_limit);
            }
            if (isset($original_time_limit) && function_exists('set_time_limit')) {
                set_time_limit($original_time_limit);
            }
            
            // Always release the lock
            delete_transient($lock_key);
            aca_debug_log('Cron: 15-minute task completed, lock released, limits restored');
        }
    }
    
    /**
     * Auto-analyze style guide
     */
    private static function auto_analyze_style_guide() {
        $rest_api = new ACA_Rest_Api();
        $result = $rest_api->analyze_style_guide(null, true); // null request, true = auto mode
        
        if (is_wp_error($result)) {
            aca_debug_log('Auto Style Guide Analysis Error: ' . $result->get_error_message());
        }
    }
    
    /**
     * Generate ideas in semi-automatic mode
     */
    private static function generate_ideas_semi_auto() {
        $rest_api = new ACA_Rest_Api();
        
        // Create a mock WP_REST_Request for internal API call
        $request = new WP_REST_Request();
        $request->set_header('content-type', 'application/json');
        $request->set_body(json_encode(array('count' => 5, 'auto' => true)));
        
        $result = $rest_api->generate_ideas($request);
        
        if (is_wp_error($result)) {
            aca_debug_log('Semi-Auto Ideas Generation Error: ' . $result->get_error_message());
        }
    }
    
    /**
     * Run full automatic content cycle
     */
    private static function run_full_automatic_cycle() {
        $settings = get_option('aca_settings', array());
        $style_guide = get_option('aca_style_guide');
        
        if (empty($style_guide)) {
            return;
        }
        
        try {
            $rest_api = new ACA_Rest_Api();
            
            // Generate one idea
            $ideas_result = $rest_api->generate_ideas(array('count' => 1, 'auto' => true));
            
            if (is_wp_error($ideas_result) || empty($ideas_result)) {
                return;
            }
            
            // Get the generated idea from database
            global $wpdb;
            $latest_idea = $wpdb->get_row(
                "SELECT * FROM {$wpdb->prefix}aca_ideas ORDER BY created_at DESC LIMIT 1"
            );
            
            if (!$latest_idea) {
                return;
            }
            
            // Create draft from the idea
            $draft_result = $rest_api->create_draft_from_idea($latest_idea->id, true); // true = auto mode
            
            if (is_wp_error($draft_result)) {
                return;
            }
            
            // Auto-publish if enabled
            if (isset($settings['autoPublish']) && $settings['autoPublish']) {
                // Get the created post
                $post = get_posts(array(
                    'post_status' => 'draft',
                    'meta_query' => array(
                        array(
                            'key' => '_aca_created_from_idea',
                            'value' => $latest_idea->id,
                            'compare' => '='
                        )
                    ),
                    'numberposts' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if (!empty($post)) {
                    wp_update_post(array(
                        'ID' => $post[0]->ID,
                        'post_status' => 'publish'
                    ));
                    
                    // Log the auto-publish
                    self::add_activity_log('post_published', "Auto-published post: \"{$post[0]->post_title}\"", 'Send');
                }
            }
            
        } catch (Exception $e) {
            aca_debug_log('Full Auto Cycle Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Content freshness task
     */
    public static function content_freshness_task() {
        if (!is_aca_pro_active()) {
            return; // Pro feature only
        }
        
        $settings = get_option('aca_freshness_settings', array());
        $frequency = $settings['analysisFrequency'] ?? 'weekly';
        
        if (self::should_run_freshness_analysis($frequency)) {
            self::analyze_content_freshness();
        }
    }
    
    /**
     * Check if freshness analysis should run based on frequency
     */
    private static function should_run_freshness_analysis($frequency) {
        if ($frequency === 'manual') {
            return false;
        }
        
        $last_run = get_option('aca_last_freshness_analysis', 0);
        $current_time = time();
        
        // If this is the first run, set last_run to allow immediate execution
        if ($last_run == 0) {
            $last_run = $current_time - (7 * 24 * 60 * 60);
        }
        
        switch ($frequency) {
            case 'daily':
                return ($current_time - $last_run) > (24 * 60 * 60); // 24 hours
            case 'weekly':
                return ($current_time - $last_run) > (7 * 24 * 60 * 60); // 7 days
            case 'monthly':
                return ($current_time - $last_run) > (30 * 24 * 60 * 60); // 30 days
            default:
                return false;
        }
    }
    
    /**
     * Analyze content freshness
     */
    private static function analyze_content_freshness() {
        $freshness_manager = new ACA_Content_Freshness();
        
        // Get posts that need analysis using optimized query
        $posts = get_posts(array(
            'post_status' => 'publish',
            'numberposts' => 10, // Limit to prevent timeout
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_aca_last_freshness_check',
                    'value' => strtotime('-7 days'),
                    'compare' => '<',
                    'type' => 'NUMERIC'
                ),
                array(
                    'key' => '_aca_last_freshness_check',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        
        $analyzed_count = 0;
        foreach ($posts as $post) {
            $analysis = $freshness_manager->analyze_post_freshness($post->ID);
            
            if (!is_wp_error($analysis)) {
                $analyzed_count++;
                
                if ($analysis['needs_update']) {
                    // Queue for manual review or auto-update based on settings
                    self::queue_content_update($post->ID, $analysis);
                }
                
                update_post_meta($post->ID, '_aca_last_freshness_check', current_time('mysql'));
            }
        }
        
        // Update last run time
        update_option('aca_last_freshness_analysis', time());
        
        // Log the activity
        if ($analyzed_count > 0) {
            self::add_activity_log('content_freshness_analysis', "Automatically analyzed $analyzed_count posts for content freshness", 'Sparkles');
        }
    }
    
    /**
     * Queue content update (from documentation)
     */
    private static function queue_content_update($post_id, $analysis) {
        // Get freshness settings
        $freshness_settings = get_option('aca_freshness_settings', array());
        
        // If auto-update is enabled, queue it
        if (isset($freshness_settings['autoUpdate']) && $freshness_settings['autoUpdate']) {
            require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';
            $freshness_manager = new ACA_Content_Freshness();
            $freshness_manager->queue_content_update($post_id, $analysis);
        }
    }
    
    /**
     * Legacy method - kept for backward compatibility
     */
    private static function run_content_freshness_analysis() {
        // Check if Pro license is active (content freshness is a Pro feature)
        if (!is_aca_pro_active()) {
            return;
        }
        
        // Get freshness settings
        $freshness_settings = get_option('aca_freshness_settings', array());
        
        // Check if automatic analysis is enabled
        if (!isset($freshness_settings['enabled']) || !$freshness_settings['enabled']) {
            return;
        }
        
        // Check frequency
        $frequency = isset($freshness_settings['analysisFrequency']) ? $freshness_settings['analysisFrequency'] : 'weekly';
        if ($frequency === 'manual') {
            return;
        }
        
        // Check if it's time to run analysis based on frequency
        $last_run = get_option('aca_last_freshness_analysis', 0);
        $current_time = time();
        $should_run = false;
        
        // If this is the first run, set last_run to a week ago to allow immediate execution
        if ($last_run == 0) {
            $last_run = $current_time - (7 * 24 * 60 * 60);
        }
        
        switch ($frequency) {
            case 'daily':
                $should_run = ($current_time - $last_run) > (24 * 60 * 60); // 24 hours
                break;
            case 'weekly':
                $should_run = ($current_time - $last_run) > (7 * 24 * 60 * 60); // 7 days
                break;
            case 'monthly':
                $should_run = ($current_time - $last_run) > (30 * 24 * 60 * 60); // 30 days
                break;
        }
        
        if (!$should_run) {
            return;
        }
        
        // Load content freshness class
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';
        $freshness_manager = new ACA_Content_Freshness();
        
        // Get posts that need analysis (limit to prevent timeout)
        $posts_to_analyze = $freshness_manager->get_posts_needing_analysis(5);
        
        $analyzed_count = 0;
        foreach ($posts_to_analyze as $post_id) {
            $analysis = $freshness_manager->analyze_post_freshness($post_id);
            
            if (!is_wp_error($analysis)) {
                $analyzed_count++;
                
                // Update meta field to track last analysis
                update_post_meta($post_id, '_aca_last_freshness_check', current_time('mysql'));
                
                // If post needs update and auto-update is enabled, queue it
                if ($analysis['needs_update'] && isset($freshness_settings['autoUpdate']) && $freshness_settings['autoUpdate']) {
                    $freshness_manager->queue_content_update($post_id, $analysis);
                }
            }
        }
        
        // Update last run time
        update_option('aca_last_freshness_analysis', $current_time);
        
        // Log the activity
        if ($analyzed_count > 0) {
            self::add_activity_log('content_freshness_analysis', "Automatically analyzed $analyzed_count posts for content freshness", 'Sparkles');
        }
    }
    
    /**
     * Add activity log entry
     */
    private static function add_activity_log($type, $details, $icon) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'aca_activity_logs',
            array(
                'timestamp' => current_time('mysql'),
                'type' => $type,
                'details' => $details,
                'icon' => $icon
            )
        );
    }
}