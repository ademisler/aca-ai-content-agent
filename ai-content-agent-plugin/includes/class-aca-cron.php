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
        
        // Hook cron events
        add_action('aca_thirty_minute_event', array($this, 'thirty_minute_task'));
        add_action('aca_fifteen_minute_event', array($this, 'fifteen_minute_task'));
    }
    
    /**
     * Add custom cron intervals
     */
    public function add_cron_intervals($schedules) {
        $schedules['aca_thirty_minutes'] = array(
            'interval' => 30 * 60, // 30 minutes in seconds
            'display' => __('Every 30 Minutes', 'ai-content-agent')
        );
        
        $schedules['aca_fifteen_minutes'] = array(
            'interval' => 15 * 60, // 15 minutes in seconds
            'display' => __('Every 15 Minutes', 'ai-content-agent')
        );
        
        return $schedules;
    }
    
    /**
     * Task that runs every 30 minutes
     * Handles full-automatic mode operations
     */
    public function thirty_minute_task() {
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['geminiApiKey'])) {
            return;
        }
        
        // Auto-analyze style guide
        $this->auto_analyze_style_guide();
        
        // Run full content cycle if in full-automatic mode
        if (isset($settings['mode']) && $settings['mode'] === 'full-automatic') {
            $this->run_full_automatic_cycle();
        }
    }
    
    /**
     * Task that runs every 15 minutes
     * Handles semi-automatic mode operations
     */
    public function fifteen_minute_task() {
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['geminiApiKey'])) {
            return;
        }
        
        // Generate ideas in semi-automatic mode
        if (isset($settings['mode']) && $settings['mode'] === 'semi-automatic') {
            $this->generate_ideas_semi_auto();
        }
    }
    
    /**
     * Auto-analyze style guide
     */
    private function auto_analyze_style_guide() {
        $rest_api = new ACA_Rest_Api();
        $rest_api->analyze_style_guide(true); // true = auto mode
    }
    
    /**
     * Generate ideas in semi-automatic mode
     */
    private function generate_ideas_semi_auto() {
        $rest_api = new ACA_Rest_Api();
        $rest_api->generate_ideas(array('count' => 5, 'auto' => true));
    }
    
    /**
     * Run full automatic content cycle
     */
    private function run_full_automatic_cycle() {
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
                    'meta_key' => '_aca_created_from_idea',
                    'meta_value' => $latest_idea->id,
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
                    $this->add_activity_log('post_published', "Auto-published post: \"{$post[0]->post_title}\"", 'Send');
                }
            }
            
        } catch (Exception $e) {
            error_log('ACA Full Auto Cycle Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Add activity log entry
     */
    private function add_activity_log($type, $details, $icon) {
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