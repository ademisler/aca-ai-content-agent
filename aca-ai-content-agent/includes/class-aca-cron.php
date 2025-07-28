<?php
/**
 * ACA Cron Job Handler
 * Handles background automation tasks
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Cron {
    
    /**
     * Schedule all cron jobs
     */
    public static function schedule_jobs() {
        try {
            // Clear existing jobs first
            self::clear_jobs();
            
            $settings = get_option('aca_settings', array());
            
            // Add custom cron schedules first
            add_filter('cron_schedules', array(__CLASS__, 'add_custom_schedules'));
            
            // Schedule style analysis every 30 minutes
            if (!wp_next_scheduled('aca_style_analysis')) {
                wp_schedule_event(time(), 'aca_30min', 'aca_style_analysis');
            }
            
            // Schedule based on automation mode
            $mode = isset($settings['mode']) ? $settings['mode'] : 'manual';
            
            if ($mode === 'semi-automatic') {
                // Generate ideas every 15 minutes
                if (!wp_next_scheduled('aca_semi_auto_ideas')) {
                    wp_schedule_event(time(), 'aca_15min', 'aca_semi_auto_ideas');
                }
            } elseif ($mode === 'full-automatic') {
                // Full automation cycle every 30 minutes
                if (!wp_next_scheduled('aca_full_auto_cycle')) {
                    wp_schedule_event(time(), 'aca_30min', 'aca_full_auto_cycle');
                }
            }
            
            // Hook the cron functions
            add_action('aca_style_analysis', array(__CLASS__, 'run_style_analysis'));
            add_action('aca_semi_auto_ideas', array(__CLASS__, 'run_semi_auto_ideas'));
            add_action('aca_full_auto_cycle', array(__CLASS__, 'run_full_auto_cycle'));
            
        } catch (Exception $e) {
            error_log('ACA Plugin Exception in schedule_jobs: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear all scheduled jobs
     */
    public static function clear_jobs() {
        try {
            wp_clear_scheduled_hook('aca_style_analysis');
            wp_clear_scheduled_hook('aca_semi_auto_ideas');
            wp_clear_scheduled_hook('aca_full_auto_cycle');
        } catch (Exception $e) {
            error_log('ACA Plugin Exception in clear_jobs: ' . $e->getMessage());
        }
    }
    
    /**
     * Reschedule jobs based on current settings
     */
    public static function reschedule_jobs() {
        self::schedule_jobs();
    }
    
    /**
     * Add custom cron schedules
     */
    public static function add_custom_schedules($schedules) {
        $schedules['aca_15min'] = array(
            'interval' => 15 * 60, // 15 minutes
            'display' => __('Every 15 minutes')
        );
        
        $schedules['aca_30min'] = array(
            'interval' => 30 * 60, // 30 minutes
            'display' => __('Every 30 minutes')
        );
        
        return $schedules;
    }
    
    /**
     * Run automatic style analysis
     */
    public static function run_style_analysis() {
        try {
            $gemini_service = new ACA_Gemini_Service();
            $analysis = $gemini_service->analyze_style();
            $parsed_analysis = json_decode($analysis, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                $parsed_analysis['lastAnalyzed'] = current_time('c');
                update_option('aca_style_guide', $parsed_analysis);
                
                ACA_Database::add_activity_log(
                    'style_updated',
                    'Style Guide was automatically refreshed.',
                    'BookOpen'
                );
            }
        } catch (Exception $e) {
            error_log('ACA Style Analysis Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Run semi-automatic idea generation
     */
    public static function run_semi_auto_ideas() {
        try {
            $settings = get_option('aca_settings', array());
            
            // Only run if mode is semi-automatic
            if (!isset($settings['mode']) || $settings['mode'] !== 'semi-automatic') {
                return;
            }
            
            $style_guide = get_option('aca_style_guide', null);
            if (!$style_guide) {
                return; // Can't generate ideas without style guide
            }
            
            // Get existing titles
            $ideas = ACA_Database::get_ideas();
            $posts = array_merge(
                ACA_Database::get_posts_by_status('draft'),
                ACA_Database::get_posts_by_status('publish')
            );
            $existing_titles = array_merge(
                array_column($ideas, 'title'),
                array_column($posts, 'title')
            );
            
            // Don't generate if we already have too many ideas
            if (count($ideas) >= 20) {
                return;
            }
            
            // Get search console data if available
            $search_console_data = null;
            if (isset($settings['searchConsoleUser']) && $settings['searchConsoleUser']) {
                $search_console_data = array(
                    'topQueries' => array('AI for content marketing', 'how to write blog posts faster', 'wordpress automation tools'),
                    'underperformingPages' => array('/blog/old-seo-tips', '/blog/2022-social-media-trends')
                );
            }
            
            $gemini_service = new ACA_Gemini_Service();
            $new_ideas_json = $gemini_service->generate_ideas(
                json_encode($style_guide),
                $existing_titles,
                3, // Generate 3 ideas at a time
                $search_console_data
            );
            
            $new_ideas = json_decode($new_ideas_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Save new ideas to database
                foreach ($new_ideas as $title) {
                    ACA_Database::create_idea($title, 'ai');
                }
                
                ACA_Database::add_activity_log(
                    'ideas_generated',
                    'Ideas were automatically generated (semi-auto mode).',
                    'Lightbulb'
                );
            }
            
        } catch (Exception $e) {
            error_log('ACA Semi-Auto Ideas Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Run full automation cycle
     */
    public static function run_full_auto_cycle() {
        try {
            $settings = get_option('aca_settings', array());
            
            // Only run if mode is full-automatic
            if (!isset($settings['mode']) || $settings['mode'] !== 'full-automatic') {
                return;
            }
            
            $style_guide = get_option('aca_style_guide', null);
            if (!$style_guide) {
                return; // Can't proceed without style guide
            }
            
            // Step 1: Generate ideas if we don't have enough
            $ideas = ACA_Database::get_ideas();
            if (count($ideas) < 5) {
                self::generate_ideas_for_full_auto();
            }
            
            // Step 2: Create drafts from ideas
            $ideas = ACA_Database::get_ideas(); // Refresh ideas list
            if (!empty($ideas)) {
                $idea = $ideas[0]; // Take the first idea
                self::create_draft_for_full_auto($idea['title']);
                
                // Remove the used idea
                ACA_Database::delete_idea($idea['id']);
            }
            
            // Step 3: Auto-publish if enabled
            if (isset($settings['autoPublish']) && $settings['autoPublish']) {
                self::auto_publish_draft();
            }
            
        } catch (Exception $e) {
            error_log('ACA Full Auto Cycle Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate ideas for full automation
     */
    private static function generate_ideas_for_full_auto() {
        try {
            $style_guide = get_option('aca_style_guide', null);
            $settings = get_option('aca_settings', array());
            
            // Get existing titles
            $ideas = ACA_Database::get_ideas();
            $posts = array_merge(
                ACA_Database::get_posts_by_status('draft'),
                ACA_Database::get_posts_by_status('publish')
            );
            $existing_titles = array_merge(
                array_column($ideas, 'title'),
                array_column($posts, 'title')
            );
            
            // Get search console data if available
            $search_console_data = null;
            if (isset($settings['searchConsoleUser']) && $settings['searchConsoleUser']) {
                $search_console_data = array(
                    'topQueries' => array('AI for content marketing', 'how to write blog posts faster', 'wordpress automation tools'),
                    'underperformingPages' => array('/blog/old-seo-tips', '/blog/2022-social-media-trends')
                );
            }
            
            $gemini_service = new ACA_Gemini_Service();
            $new_ideas_json = $gemini_service->generate_ideas(
                json_encode($style_guide),
                $existing_titles,
                5,
                $search_console_data
            );
            
            $new_ideas = json_decode($new_ideas_json, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                foreach ($new_ideas as $title) {
                    ACA_Database::create_idea($title, 'ai');
                }
                
                ACA_Database::add_activity_log(
                    'ideas_generated',
                    'Ideas were automatically generated (full-auto mode).',
                    'Lightbulb'
                );
            }
            
        } catch (Exception $e) {
            error_log('ACA Full Auto Ideas Generation Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Create draft for full automation
     */
    private static function create_draft_for_full_auto($title) {
        try {
            $style_guide = get_option('aca_style_guide', null);
            $settings = get_option('aca_settings', array());
            
            // Get existing posts for internal linking
            $published_posts = ACA_Database::get_posts_by_status('publish');
            $existing_posts_for_linking = array();
            foreach ($published_posts as $post) {
                $existing_posts_for_linking[] = array(
                    'title' => $post['title'],
                    'url' => $post['url'],
                    'content' => wp_strip_all_tags($post['content'])
                );
            }
            
            // Create draft content
            $gemini_service = new ACA_Gemini_Service();
            $draft_json = $gemini_service->create_draft(
                $title,
                json_encode($style_guide),
                $existing_posts_for_linking
            );
            
            $draft_data = json_decode($draft_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            // Get image
            $image_provider = isset($settings['imageSourceProvider']) ? $settings['imageSourceProvider'] : 'ai';
            $featured_image_id = null;
            
            if ($image_provider === 'ai') {
                $ai_style = isset($settings['aiImageStyle']) ? $settings['aiImageStyle'] : 'digital_art';
                $image_base64 = $gemini_service->generate_image($title, $ai_style);
                
                // Convert and upload image
                $image_data = base64_decode($image_base64);
                $filename = 'aca-ai-image-' . time() . '.jpg';
                $temp_file = wp_tempnam($filename);
                file_put_contents($temp_file, $image_data);
                
                if (!function_exists('media_handle_sideload')) {
                    require_once(ABSPATH . 'wp-admin/includes/media.php');
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                }
                
                $file_array = array(
                    'name' => $filename,
                    'tmp_name' => $temp_file,
                    'type' => 'image/jpeg'
                );
                
                $featured_image_id = media_handle_sideload($file_array, 0);
                @unlink($temp_file);
                
            } else {
                $stock_service = new ACA_Stock_Photo_Service();
                $photo_data = $stock_service->fetch_stock_photo($title, $image_provider);
                $featured_image_id = $stock_service->sideload_image($photo_data['url']);
            }
            
            // Create the post
            $post_id = ACA_Database::create_draft(
                $title,
                $draft_data['content'],
                $draft_data,
                is_wp_error($featured_image_id) ? null : $featured_image_id
            );
            
            if ($post_id) {
                ACA_Database::add_activity_log(
                    'draft_created',
                    "Draft automatically created: $title",
                    'FileText'
                );
            }
            
        } catch (Exception $e) {
            error_log('ACA Full Auto Draft Creation Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Auto-publish oldest draft
     */
    private static function auto_publish_draft() {
        try {
            $drafts = ACA_Database::get_posts_by_status('draft');
            
            if (!empty($drafts)) {
                // Sort by creation date and get the oldest
                usort($drafts, function($a, $b) {
                    return strtotime($a['createdAt']) - strtotime($b['createdAt']);
                });
                
                $oldest_draft = $drafts[0];
                $result = ACA_Database::publish_post($oldest_draft['id']);
                
                if ($result && !is_wp_error($result)) {
                    ACA_Database::add_activity_log(
                        'post_published',
                        "Post automatically published: {$oldest_draft['title']}",
                        'Send'
                    );
                }
            }
            
        } catch (Exception $e) {
            error_log('ACA Auto Publish Error: ' . $e->getMessage());
        }
    }
}