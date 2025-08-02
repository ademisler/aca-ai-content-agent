<?php
/**
 * REST API functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Rest_Api {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
        
        // Ensure proper charset handling for special characters
        add_action('init', array($this, 'setup_charset_handling'));
    }
    
    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Settings endpoints
        register_rest_route('aca/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_settings'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/settings', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_settings'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // Debug endpoint for automation testing
        register_rest_route('aca/v1', '/debug/automation', array(
            'methods' => 'GET',
            'callback' => array($this, 'debug_automation'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // Manual cron trigger endpoints for testing
        register_rest_route('aca/v1', '/debug/cron/semi-auto', array(
            'methods' => 'POST',
            'callback' => array($this, 'debug_trigger_semi_auto'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/debug/cron/full-auto', array(
            'methods' => 'POST',
            'callback' => array($this, 'debug_trigger_full_auto'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // SEO Plugin Detection endpoint
        register_rest_route('aca/v1', '/seo-plugins', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_seo_plugins'),
            'permission_callback' => array($this, 'check_seo_permissions')
        ));
        
        // Google Search Console endpoints
        register_rest_route('aca/v1', '/gsc/auth-status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_gsc_auth_status'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/connect', array(
            'methods' => 'POST',
            'callback' => array($this, 'gsc_connect'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/disconnect', array(
            'methods' => 'POST',
            'callback' => array($this, 'gsc_disconnect'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/sites', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_gsc_sites'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // Style guide endpoints
        register_rest_route('aca/v1', '/style-guide', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_style_guide'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/style-guide/analyze', array(
            'methods' => 'POST',
            'callback' => array($this, 'analyze_style_guide'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/style-guide', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_style_guide'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Ideas endpoints
        register_rest_route('aca/v1', '/ideas', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_ideas'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/ideas/generate', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_ideas'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/ideas/similar', array(
            'methods' => 'POST',
            'callback' => array($this, 'generate_similar_ideas'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/ideas/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_idea'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/ideas/(?P<id>\d+)', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'delete_idea'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/ideas', array(
            'methods' => 'POST',
            'callback' => array($this, 'add_idea'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Restore archived idea
        register_rest_route('aca/v1', '/ideas/(?P<id>\d+)/restore', array(
            'methods' => 'POST',
            'callback' => array($this, 'restore_idea'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Permanently delete idea
        register_rest_route('aca/v1', '/ideas/(?P<id>\d+)/permanent-delete', array(
            'methods' => 'DELETE',
            'callback' => array($this, 'permanent_delete_idea'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Drafts endpoints
        register_rest_route('aca/v1', '/drafts', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_drafts'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/drafts/create', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_draft'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/drafts/(?P<id>\d+)', array(
            'methods' => 'PUT',
            'callback' => array($this, 'update_draft'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/drafts/(?P<id>\d+)/publish', array(
            'methods' => 'POST',
            'callback' => array($this, 'publish_draft'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/drafts/(?P<id>\d+)/schedule', array(
            'methods' => 'POST',
            'callback' => array($this, 'schedule_draft'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Published posts endpoint
        register_rest_route('aca/v1', '/published', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_published_posts'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Update published post date
        register_rest_route('aca/v1', '/published/(?P<id>\d+)/update-date', array(
            'methods' => 'POST',
            'callback' => array($this, 'update_published_post_date'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Activity logs endpoint
        register_rest_route('aca/v1', '/activity-logs', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_activity_logs'),
                'permission_callback' => array($this, 'check_permissions')
            ),
            array(
                'methods' => 'POST',
                'callback' => array($this, 'add_activity_log_endpoint'),
                'permission_callback' => array($this, 'check_permissions')
            )
        ));
        
        // Google Search Console endpoints
        register_rest_route('aca/v1', '/gsc/auth-status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_gsc_auth_status'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/connect', array(
            'methods' => 'POST',
            'callback' => array($this, 'gsc_connect'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/disconnect', array(
            'methods' => 'POST',
            'callback' => array($this, 'gsc_disconnect'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/sites', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_gsc_sites'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/data', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_gsc_data'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        register_rest_route('aca/v1', '/gsc/status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_gsc_status'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // Content Freshness endpoints (Pro feature)
        register_rest_route('aca/v1', '/content-freshness/analyze', array(
            'methods' => 'POST',
            'callback' => array($this, 'analyze_content_freshness'),
            'permission_callback' => array($this, 'check_pro_permissions')
        ));
        
        register_rest_route('aca/v1', '/content-freshness/analyze/(?P<id>\d+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'analyze_single_post_freshness'),
            'permission_callback' => array($this, 'check_pro_permissions')
        ));
        
        register_rest_route('aca/v1', '/content-freshness/update/(?P<id>\d+)', array(
            'methods' => 'POST', 
            'callback' => array($this, 'update_content_with_ai'),
            'permission_callback' => array($this, 'check_pro_permissions')
        ));
        
        register_rest_route('aca/v1', '/content-freshness/settings', array(
            'methods' => 'GET,POST',
            'callback' => array($this, 'manage_freshness_settings'),
            'permission_callback' => array($this, 'check_pro_permissions')
        ));
        
        try {
            register_rest_route('aca/v1', '/content-freshness/posts', array(
                'methods' => 'GET',
                'callback' => array($this, 'get_posts_freshness_data'),
                'permission_callback' => array($this, 'check_pro_permissions')
            ));

        } catch (Exception $e) {
            aca_debug_log('Error registering /content-freshness/posts endpoint: ' . $e->getMessage());
        }
        
        register_rest_route('aca/v1', '/content-freshness/posts/needing-updates', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_posts_needing_updates'),
            'permission_callback' => array($this, 'check_pro_permissions')
        ));
        

        
        // Test endpoint to verify registration works
        register_rest_route('aca/v1', '/test-endpoint', array(
            'methods' => 'GET',
            'callback' => array($this, 'test_endpoint_callback'),
            'permission_callback' => '__return_true'
        ));
        
        // WordPress REST API routes list endpoint
        register_rest_route('aca/v1', '/debug/routes', array(
            'methods' => 'GET',
            'callback' => array($this, 'debug_routes_callback'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));

        // License verification endpoint
        register_rest_route('aca/v1', '/license/verify', array(
            'methods' => 'POST',
            'callback' => array($this, 'verify_license_key'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // License status endpoint
        register_rest_route('aca/v1', '/license/status', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_license_status'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // License deactivation endpoint
        register_rest_route('aca/v1', '/license/deactivate', array(
            'methods' => 'POST',
            'callback' => array($this, 'deactivate_license'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
        
        // Debug endpoint for Pro license status
        register_rest_route('aca/v1', '/debug/pro-status', array(
            'methods' => 'GET',
            'callback' => array($this, 'debug_pro_status'),
            'permission_callback' => array($this, 'check_admin_permissions')
        ));
    }
    
    /**
     * Check permissions
     */
    public function check_permissions() {
        return current_user_can('manage_options');
    }
    
    /**
     * Check admin permissions (same as check_permissions but with explicit name)
     */
    public function check_admin_permissions() {
        return current_user_can('manage_options');
    }
    
    /**
     * Check SEO permissions - more flexible for SEO plugin detection
     */
    public function check_seo_permissions() {
        // Allow access for users who can edit posts or manage options
        return current_user_can('edit_posts') || current_user_can('manage_options');
    }
    
    /**
     * Check Pro permissions - requires Pro license and admin permissions
     */
    public function check_pro_permissions() {
        // Check admin permissions first
        if (!current_user_can('manage_options')) {
            return false;
        }
        
        // Check if Pro license is active - return false instead of WP_Error
        if (!is_aca_pro_active()) {
            $license_status = get_option('aca_license_status', 'not_set');
            $license_timestamp = get_option('aca_license_timestamp', 0);
            $age_hours = $license_timestamp > 0 ? round((time() - $license_timestamp) / 3600, 2) : 0;
            
            $error_message = 'This feature requires an active Pro license. ';
            
            if ($license_status !== 'active') {
                $error_message .= 'License status: ' . $license_status;
            } elseif ($age_hours > 168) { // 168 hours = 7 days
                $error_message .= 'License expired (age: ' . $age_hours . ' hours). Please refresh license.';
            } else {
                $error_message .= 'License validation failed.';
            }
            
            aca_debug_log('Pro Permission Denied: ' . $error_message);
            
            // Return false instead of WP_Error to get proper 403 instead of 404
            return false;
        }
        
        return true;
    }
    
    /**
     * Verify nonce for security
     */
    private function verify_nonce($request) {
        $nonce = $request->get_header('X-WP-Nonce');
        if (!wp_verify_nonce($nonce, 'wp_rest')) {
            return new WP_Error('invalid_nonce', 'Invalid nonce', array('status' => 403));
        }
        return true;
    }
    
    /**
     * Get settings
     */
    public function get_settings($request) {
        $settings = get_option('aca_settings', array());
        
        // Add pro status to settings response
        $settings['is_pro'] = is_aca_pro_active();
        
        return rest_ensure_response($settings);
    }
    
    /**
     * Save settings
     */
    public function save_settings($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $settings = $request->get_json_params();
        
        // Save main settings
        update_option('aca_settings', $settings);
        
        // Save Google Cloud settings separately for easy access
        if (isset($settings['googleCloudProjectId'])) {
            update_option('aca_google_cloud_project_id', sanitize_text_field($settings['googleCloudProjectId']));
        }
        if (isset($settings['googleCloudLocation'])) {
            update_option('aca_google_cloud_location', sanitize_text_field($settings['googleCloudLocation']));
        }
        
        // Clear Google access token cache when settings change (especially API keys)
        delete_transient('aca_google_access_token');
        
        $this->add_activity_log('settings_updated', 'Application settings were updated.', 'Settings');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Get style guide
     */
    public function get_style_guide($request) {
        $style_guide = get_option('aca_style_guide');
        return rest_ensure_response($style_guide);
    }
    
    /**
     * Analyze style guide using AI
     */
    public function analyze_style_guide($request = null, $is_auto = false) {
        if (!$is_auto) {
            $nonce_check = $this->verify_nonce($request);
            if (is_wp_error($nonce_check)) {
                return $nonce_check;
            }
        }
        
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['geminiApiKey'])) {
            return new WP_Error('no_api_key', 'Google AI API Key is not set', array('status' => 400));
        }
        
        try {
            // Fetch recent posts from WordPress to analyze
            $recent_posts = $this->fetch_recent_posts_for_analysis();
            $posts_content = $this->prepare_posts_content_for_analysis($recent_posts);
            
            $analysis = $this->call_gemini_analyze_style($settings['geminiApiKey'], $posts_content);
            $parsed_analysis = json_decode($analysis, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            $parsed_analysis['lastAnalyzed'] = current_time('c');
            update_option('aca_style_guide', $parsed_analysis);
            
            $message = $is_auto ? 'Style Guide automatically refreshed.' : 'Style Guide was successfully updated.';
            $this->add_activity_log('style_analyzed', $message, 'BookOpen');
            
            return rest_ensure_response($parsed_analysis);
            
        } catch (Exception $e) {
            return new WP_Error('analysis_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Fetch recent posts for style analysis
     */
    private function fetch_recent_posts_for_analysis() {
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 20,
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_wp_page_template',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        
        return get_posts($args);
    }
    
    /**
     * Prepare posts content for AI analysis
     */
    private function prepare_posts_content_for_analysis($posts) {
        if (empty($posts)) {
            return "No recent posts found. Please analyze based on a generic professional blog style.";
        }
        
        $content_samples = array();
        
        foreach ($posts as $post) {
            // Get post content and clean it
            $content = wp_strip_all_tags($post->post_content);
            $content = wp_trim_words($content, 150, '...');
            
            $content_samples[] = array(
                'title' => $post->post_title,
                'content' => $content,
                'date' => $post->post_date
            );
        }
        
        $analysis_prompt = "Here are the 20 most recent blog posts from this website:\n\n";
        
        foreach ($content_samples as $sample) {
            $analysis_prompt .= "Title: {$sample['title']}\n";
            $analysis_prompt .= "Content: {$sample['content']}\n";
            $analysis_prompt .= "Date: {$sample['date']}\n\n---\n\n";
        }
        
        return $analysis_prompt;
    }
    
    private function call_gemini_analyze_style($api_key, $posts_content = '') {
        $prompt = "
            Analyze the writing style of the following blog content and generate a JSON object that describes it.
            This JSON object will be used as a \"Style Guide\" for generating new content.
            
            {$posts_content}
            
            Based on the content above, create a JSON object that strictly follows this schema:
            {
              \"tone\": \"string (e.g., 'Friendly and conversational', 'Formal and professional', 'Technical and informative', 'Witty and humorous')\",
              \"sentenceStructure\": \"string (e.g., 'Mix of short, punchy sentences and longer, more descriptive ones', 'Primarily short and direct sentences', 'Complex sentences with multiple clauses')\",
              \"paragraphLength\": \"string (e.g., 'Short, 2-3 sentences per paragraph', 'Medium, 4-6 sentences per paragraph')\",
              \"formattingStyle\": \"string (e.g., 'Uses bullet points, bold text for emphasis, and subheadings (H2, H3)', 'Minimal formatting, relies on plain text paragraphs')\"
            }
            
            Return ONLY the JSON object, nothing else.
        ";
        
        return $this->call_gemini_api($api_key, $prompt);
    }
    
    /**
     * Save style guide
     */
    public function save_style_guide($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $style_guide = $request->get_json_params();
        update_option('aca_style_guide', $style_guide);
        
        $this->add_activity_log('style_updated', 'Style Guide was manually edited and saved.', 'BookOpen');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Get ideas
     */
    public function get_ideas($request) {
        global $wpdb;
        
        // Get all ideas including archived ones - frontend will filter them
        $ideas = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}aca_ideas ORDER BY created_at DESC"
        );
        
        // Map database status to frontend status
        foreach ($ideas as $idea) {
            if ($idea->status === 'new') {
                $idea->status = 'active';
            } elseif ($idea->status === 'archived') {
                $idea->status = 'archived';
            }
        }
        
        return rest_ensure_response($ideas);
    }
    
    /**
     * Generate ideas using AI
     */
    public function generate_ideas($request) {
        $params = $request->get_json_params();
        $count = isset($params['count']) ? intval($params['count']) : 5;
        $is_auto = isset($params['auto']) ? $params['auto'] : false;
        
        if (!$is_auto) {
            $nonce_check = $this->verify_nonce($request);
            if (is_wp_error($nonce_check)) {
                return $nonce_check;
            }
        }
        
        $settings = get_option('aca_settings', array());
        $style_guide = get_option('aca_style_guide');
        
        if (empty($settings['geminiApiKey'])) {
            return new WP_Error('no_api_key', 'Google AI API Key is not set', array('status' => 400));
        }
        
        if (empty($style_guide)) {
            return new WP_Error('no_style_guide', 'Style Guide is required', array('status' => 400));
        }
        
        try {
            // Get existing titles to avoid duplicates
            $existing_titles = $this->get_existing_titles();
            
            // Get search console data if user is connected
            $search_console_data = null;
            if (!empty($settings['searchConsoleUser'])) {
                require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
                
                $gsc = new ACA_Google_Search_Console();
                $search_console_data = $gsc->get_data_for_ai();
                
                // Fallback to mock data if GSC fails
                if (!$search_console_data) {
                    $search_console_data = array(
                        'topQueries' => array('AI for content marketing', 'how to write blog posts faster', 'wordpress automation tools'),
                        'underperformingPages' => array('/blog/old-seo-tips', '/blog/2022-social-media-trends')
                    );
                }
            }
            
            $new_ideas = $this->call_gemini_generate_ideas(
                $settings['geminiApiKey'],
                json_encode($style_guide),
                $existing_titles,
                $count,
                $search_console_data
            );
            
            $parsed_ideas = json_decode($new_ideas, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || empty($parsed_ideas)) {
                throw new Exception('Invalid response from AI service');
            }
            
            // Save ideas to database
            global $wpdb;
            $source = $search_console_data ? 'search-console' : 'ai';
            $saved_ideas = array();
            
            foreach ($parsed_ideas as $title) {
                $result = $wpdb->insert(
                    $wpdb->prefix . 'aca_ideas',
                    array(
                        'title' => $title,
                        'status' => 'new',
                        'source' => $source,
                        'created_at' => current_time('mysql')
                    )
                );
                
                if ($result) {
                    $saved_ideas[] = array(
                        'id' => $wpdb->insert_id,
                        'title' => $title,
                        'status' => 'active',
                        'source' => $source,
                        'createdAt' => current_time('c'),
                        'description' => '',
                        'tags' => array()
                    );
                }
            }
            
            $this->add_activity_log('ideas_generated', "Generated " . count($saved_ideas) . " new content ideas.", 'Lightbulb');
            
            return rest_ensure_response($saved_ideas);
            
        } catch (Exception $e) {
            return new WP_Error('generation_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Generate similar ideas
     */
    public function generate_similar_ideas($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $params = $request->get_json_params();
        $idea_id = isset($params['ideaId']) ? $params['ideaId'] : $params['baseTitle'];
        
        // If we received an idea ID, get the title from database
        if (is_numeric($idea_id)) {
            global $wpdb;
            $idea = $wpdb->get_row($wpdb->prepare(
                "SELECT title FROM {$wpdb->prefix}aca_ideas WHERE id = %d",
                $idea_id
            ));
            
            if (!$idea) {
                return new WP_Error('idea_not_found', 'Idea not found', array('status' => 404));
            }
            
            $base_title = $idea->title;
        } else {
            $base_title = $idea_id; // It's actually a title string
        }
        
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['geminiApiKey'])) {
            return new WP_Error('no_api_key', 'Google AI API Key is not set', array('status' => 400));
        }
        
        try {
            $existing_titles = $this->get_existing_titles();
            
            $similar_ideas = $this->call_gemini_generate_similar_ideas(
                $settings['geminiApiKey'],
                $base_title,
                $existing_titles
            );
            
            $parsed_ideas = json_decode($similar_ideas, true);
            
            if (json_last_error() !== JSON_ERROR_NONE || empty($parsed_ideas)) {
                throw new Exception('Invalid response from AI service');
            }
            
            // Save ideas to database
            global $wpdb;
            $saved_ideas = array();
            
            foreach ($parsed_ideas as $title) {
                $result = $wpdb->insert(
                    $wpdb->prefix . 'aca_ideas',
                    array(
                        'title' => $title,
                        'status' => 'new',
                        'source' => 'similar',
                        'created_at' => current_time('mysql')
                    )
                );
                
                if ($result) {
                    $saved_ideas[] = array(
                        'id' => $wpdb->insert_id,
                        'title' => $title,
                        'status' => 'active',
                        'source' => 'similar',
                        'createdAt' => current_time('c'),
                        'description' => '',
                        'tags' => array()
                    );
                }
            }
            
            $this->add_activity_log('similar_ideas_generated', "Generated " . count($saved_ideas) . " similar ideas.", 'Sparkles');
            
            return rest_ensure_response($saved_ideas);
            
        } catch (Exception $e) {
            return new WP_Error('generation_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Add manual idea
     */
    public function add_idea($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $params = $request->get_json_params();
        $title = trim($params['title']);
        
        if (empty($title)) {
            return new WP_Error('empty_title', 'Idea title cannot be empty', array('status' => 400));
        }
        
        // Check for duplicates
        $existing_titles = $this->get_existing_titles();
        if (in_array(strtolower($title), array_map('strtolower', $existing_titles))) {
            return new WP_Error('duplicate_title', 'This idea title already exists', array('status' => 400));
        }
        
        global $wpdb;
        $result = $wpdb->insert(
            $wpdb->prefix . 'aca_ideas',
            array(
                'title' => $title,
                'status' => 'new',
                'source' => 'manual',
                'created_at' => current_time('mysql')
            )
        );
        
        if ($result) {
            $idea = array(
                'id' => $wpdb->insert_id,
                'title' => $title,
                'status' => 'active',
                'source' => 'manual',
                'createdAt' => current_time('c'),
                'description' => '',
                'tags' => array()
            );
            
            $this->add_activity_log('idea_added', "Manually added idea: \"$title\"", 'PlusCircle');
            
            return rest_ensure_response($idea);
        }
        
        return new WP_Error('save_failed', 'Failed to save idea', array('status' => 500));
    }
    
    /**
     * Update idea
     */
    public function update_idea($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $id = $request['id'];
        $params = $request->get_json_params();
        $new_title = trim($params['title']);
        
        global $wpdb;
        $result = $wpdb->update(
            $wpdb->prefix . 'aca_ideas',
            array('title' => $new_title),
            array('id' => $id)
        );
        
        if ($result !== false) {
            $this->add_activity_log('idea_updated', "Updated idea title to \"$new_title\"", 'Edit');
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('update_failed', 'Failed to update idea', array('status' => 500));
    }
    
    /**
     * Delete idea (archive)
     */
    public function delete_idea($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $id = $request['id'];
        
        global $wpdb;
        
        // Get idea title for logging
        $idea = $wpdb->get_row($wpdb->prepare(
            "SELECT title FROM {$wpdb->prefix}aca_ideas WHERE id = %d",
            $id
        ));
        
        // Archive the idea instead of deleting it
        $result = $wpdb->update(
            $wpdb->prefix . 'aca_ideas',
            array('status' => 'archived'),
            array('id' => $id)
        );
        
        if ($result !== false) {
            if ($idea) {
                $this->add_activity_log('idea_archived', "Archived idea: \"{$idea->title}\"", 'Archive');
            }
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('archive_failed', 'Failed to archive idea', array('status' => 500));
    }
    
    /**
     * Restore archived idea
     */
    public function restore_idea($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $id = $request['id'];
        
        global $wpdb;
        
        // Get idea title for logging
        $idea = $wpdb->get_row($wpdb->prepare(
            "SELECT title FROM {$wpdb->prefix}aca_ideas WHERE id = %d",
            $id
        ));
        
        // Restore the idea by setting status to 'new' (which maps to 'active' in frontend)
        $result = $wpdb->update(
            $wpdb->prefix . 'aca_ideas',
            array('status' => 'new'),
            array('id' => $id)
        );
        
        if ($result !== false) {
            if ($idea) {
                $this->add_activity_log('idea_updated', "Restored idea: \"{$idea->title}\"", 'Edit');
            }
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('restore_failed', 'Failed to restore idea', array('status' => 500));
    }
    
    /**
     * Permanently delete idea
     */
    public function permanent_delete_idea($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $id = $request['id'];
        
        global $wpdb;
        
        // Get idea title for logging
        $idea = $wpdb->get_row($wpdb->prepare(
            "SELECT title FROM {$wpdb->prefix}aca_ideas WHERE id = %d",
            $id
        ));
        
        // Permanently delete the idea from database
        $result = $wpdb->delete(
            $wpdb->prefix . 'aca_ideas',
            array('id' => $id)
        );
        
        if ($result) {
            if ($idea) {
                $this->add_activity_log('idea_updated', "Permanently deleted idea: \"{$idea->title}\"", 'Trash');
            }
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('delete_failed', 'Failed to permanently delete idea', array('status' => 500));
    }
    
    /**
     * Get drafts
     */
    public function get_drafts($request) {
        $drafts = get_posts(array(
            'post_type' => 'post',
            'post_status' => array('draft', 'future'),
            'meta_query' => array(
                array(
                    'key' => '_aca_meta_title',
                    'compare' => 'EXISTS'
                )
            ),
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        $formatted_drafts = array();
        foreach ($drafts as $draft) {
            $formatted_drafts[] = $this->format_post_for_api($draft);
        }
        
        return rest_ensure_response($formatted_drafts);
    }
    
    /**
     * Get published posts
     */
    public function get_published_posts($request) {
        // Get all published posts, not just ACA-created ones
        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ));
        
        $formatted_posts = array();
        foreach ($posts as $post) {
            $formatted_posts[] = $this->format_post_for_api($post);
        }
        
        return rest_ensure_response($formatted_posts);
    }
    
    /**
     * Update published post date
     */
    public function update_published_post_date($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $post_id = (int) $request['id'];
        $params = $request->get_json_params();
        
        if (!isset($params['newDate'])) {
            return new WP_Error('missing_date', 'New date is required', array('status' => 400));
        }
        
        $new_date = $params['newDate'];
        $should_convert_to_draft = isset($params['shouldConvertToDraft']) ? $params['shouldConvertToDraft'] : false;
        
        // Get the post
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('post_not_found', 'Post not found', array('status' => 404));
        }
        
        // Prepare update data
        $update_data = array(
            'ID' => $post_id,
            'post_date' => gmdate('Y-m-d H:i:s', strtotime($new_date)),
            'post_date_gmt' => get_gmt_from_date(gmdate('Y-m-d H:i:s', strtotime($new_date))),
            'edit_date' => true
        );
        
        // If converting to draft (future date)
        if ($should_convert_to_draft) {
            $update_data['post_status'] = 'future';
        }
        
        // Update the post
        $result = wp_update_post($update_data, true);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // Log the activity
        $action = $should_convert_to_draft ? 'converted to scheduled draft' : 'date updated';
        $this->add_activity_log('draft_updated', "Post \"{$post->post_title}\" {$action}", 'Calendar');
        
        // Return the updated post
        $updated_post = get_post($post_id);
        return rest_ensure_response($this->format_post_for_api($updated_post));
    }
    
    /**
     * Create draft from idea
     */
    public function create_draft($request) {
        // Set up error handling to catch fatal errors
        $old_error_handler = null;
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $old_error_handler = set_error_handler(function($severity, $message, $file, $line) {
                error_log("ACA PHP Error: " . esc_html($message) . " in " . esc_html($file) . " on line " . intval($line));
                throw new ErrorException(esc_html($message), 0, intval($severity), esc_html($file), intval($line));
            });
        }
        
        try {
            $nonce_check = $this->verify_nonce($request);
            if (is_wp_error($nonce_check)) {
                return $nonce_check;
            }
            
            $params = $request->get_json_params();
            if (!isset($params['ideaId'])) {
                return new WP_Error('missing_idea_id', 'Idea ID is required', array('status' => 400));
            }
            
            $idea_id = (int) $params['ideaId'];
            
            // Log the attempt
            aca_debug_log('Creating draft for idea ID: ' . $idea_id);
            
            $result = $this->create_draft_from_idea($idea_id);
            
            // Log the result
            if (is_wp_error($result)) {
                aca_debug_log('Draft creation failed for idea ' . $idea_id . ': ' . $result->get_error_message());
            } else {
                aca_debug_log('Draft creation successful for idea ' . $idea_id);
            }
            
            return $result;
            
        } catch (Throwable $e) {
            aca_debug_log('FATAL ERROR in create_draft: ' . $e->getMessage());
            aca_debug_log('FATAL ERROR stack trace: ' . $e->getTraceAsString());
            return new WP_Error('fatal_error', 'A fatal error occurred during draft creation: ' . $e->getMessage(), array('status' => 500));
        } finally {
            // Restore previous error handler
            if ($old_error_handler) {
                set_error_handler($old_error_handler);
            } else {
                restore_error_handler();
            }
        }
    }
    
    /**
     * Create draft from idea (internal method)
     */
    public function create_draft_from_idea($idea_id, $is_auto = false) {
        global $wpdb;
        
        // Get the idea
        $idea = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}aca_ideas WHERE id = %d",
            $idea_id
        ));
        
        if (!$idea) {
            return new WP_Error('idea_not_found', 'Idea not found', array('status' => 404));
        }
        
        $settings = get_option('aca_settings', array());
        $style_guide = get_option('aca_style_guide');
        
        if (empty($settings['geminiApiKey'])) {
            return new WP_Error('no_api_key', 'Google AI API Key is not set', array('status' => 400));
        }
        
        if (empty($style_guide)) {
            return new WP_Error('no_style_guide', 'Style Guide is required', array('status' => 400));
        }
        
        try {
            // Get existing published posts for internal linking
            $published_posts = get_posts(array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'numberposts' => 10,
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            $existing_posts_context = array();
            foreach ($published_posts as $post) {
                $permalink = get_permalink($post->ID);
                $existing_posts_context[] = array(
                    'title' => $post->post_title,
                    'url' => $permalink ? $permalink : home_url("/?p={$post->ID}"),
                    'content' => wp_strip_all_tags(substr($post->post_content, 0, 500))
                );
            }
            
            // Get site language for content generation
            $site_locale = get_locale();
            $site_language = $this->get_language_from_locale($site_locale);
            
            // Get existing categories with hierarchy for AI to choose from
            $existing_categories = get_categories(array(
                'hide_empty' => false,
                'number' => 50, // Increased to get more categories
                'hierarchical' => true,
                'orderby' => 'parent'
            ));
            
            $categories_context = array();
            foreach ($existing_categories as $category) {
                $parent_info = '';
                if ($category->parent > 0) {
                    $parent_category = get_category($category->parent);
                    if ($parent_category && !is_wp_error($parent_category)) {
                        $parent_info = $parent_category->name;
                    }
                }
                
                $categories_context[] = array(
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => $category->count,
                    'parent_id' => $category->parent,
                    'parent_name' => $parent_info,
                    'hierarchy_level' => $this->get_category_level($category->term_id)
                );
            }
            
            // Generate content using AI
            try {
                $draft_content = $this->call_gemini_create_draft(
                    $settings['geminiApiKey'],
                    $idea->title,
                    json_encode($style_guide),
                    $existing_posts_context,
                    $categories_context
                );
                
                if (empty($draft_content)) {
                    throw new Exception('Empty response from AI service');
                }
                
                $draft_data = json_decode($draft_content, true);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                            // Try to clean and fix common JSON issues
                            $cleaned_content = $this->clean_ai_json_response($draft_content);
                            if ($cleaned_content !== $draft_content) {
                                $draft_data = json_decode($cleaned_content, true);
                                if (json_last_error() !== JSON_ERROR_NONE) {
                                    throw new Exception('Invalid JSON response from AI service after cleaning: ' . json_last_error_msg());
                                }
                            } else {
                                throw new Exception('Invalid JSON response from AI service: ' . json_last_error_msg());
                            }
                        }
                
                // Validate required fields
                $missing_fields = array();
                if (empty($draft_data['content'])) $missing_fields[] = 'content';
                if (empty($draft_data['metaTitle'])) $missing_fields[] = 'metaTitle';
                if (empty($draft_data['metaDescription'])) $missing_fields[] = 'metaDescription';
                
                if (!empty($missing_fields)) {
                    throw new Exception('AI response missing required fields: ' . implode(', ', $missing_fields));
                }
                
                // Validate data types
                if (!is_string($draft_data['content'])) {
                    throw new Exception('AI response content field must be string');
                }
                if (!is_string($draft_data['metaTitle'])) {
                    throw new Exception('AI response metaTitle field must be string');
                }
                if (!is_string($draft_data['metaDescription'])) {
                    throw new Exception('AI response metaDescription field must be string');
                }
                
                // Convert Markdown to HTML if needed
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: Content before conversion (first 200 chars): ' . substr($draft_data['content'], 0, 200));
                }
                if (strpos($draft_data['content'], '**') !== false || 
                    strpos($draft_data['content'], '*') !== false || 
                    strpos($draft_data['content'], '[') !== false ||
                    strpos($draft_data['content'], '##') !== false) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: Markdown detected, converting to HTML');
                    }
                    $draft_data['content'] = $this->markdown_to_html($draft_data['content']);
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: Content after conversion (first 200 chars): ' . substr($draft_data['content'], 0, 200));
                    }
                } else {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: No Markdown detected, using content as-is');
                    }
                }
                
                // Log what we received from AI
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: AI response keys: ' . implode(', ', array_keys($draft_data)));
                    if (isset($draft_data['categoryIds'])) {
                        error_log('ACA DEBUG: AI selected category IDs: ' . implode(', ', $draft_data['categoryIds']));
                    }
                    if (isset($draft_data['tags'])) {
                        error_log('ACA DEBUG: AI selected tags: ' . implode(', ', $draft_data['tags']));
                    }
                }
                
            } catch (Exception $ai_error) {
                throw new Exception('AI content generation failed: ' . $ai_error->getMessage());
            }
            
            // Generate or fetch image
            $image_data = $this->get_featured_image($idea->title, $settings);
            
            // Safely prepare meta data
            $focus_keywords = '';
            if (isset($draft_data['focusKeywords'])) {
                if (is_array($draft_data['focusKeywords'])) {
                    $focus_keywords = implode(', ', $draft_data['focusKeywords']);
                } else {
                    $focus_keywords = (string) $draft_data['focusKeywords'];
                }
            }
            
            // Create WordPress post with enhanced content
            $post_data = array(
                'post_title' => sanitize_text_field($idea->title),
                'post_content' => wp_kses_post($draft_data['content']),
                'post_excerpt' => isset($draft_data['excerpt']) ? sanitize_text_field($draft_data['excerpt']) : '',
                'post_status' => 'draft',
                'post_type' => 'post',
                'meta_input' => array(
                    '_aca_meta_title' => sanitize_text_field($draft_data['metaTitle']),
                    '_aca_meta_description' => sanitize_text_field($draft_data['metaDescription']),
                    '_aca_focus_keywords' => sanitize_text_field($focus_keywords),
                    '_aca_created_from_idea' => (int) $idea_id,
                    '_aca_ai_generated' => true,
                    '_aca_generation_date' => current_time('mysql')
                )
            );
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Creating WordPress post');
            }
            $post_id = wp_insert_post($post_data);
            
            if (is_wp_error($post_id)) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: wp_insert_post failed: ' . $post_id->get_error_message());
                }
                throw new Exception('Failed to create WordPress post: ' . $post_id->get_error_message());
            }
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: WordPress post created with ID: ' . $post_id);
            }
            
            // Add categories safely using AI-selected IDs
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Processing categories');
            }
            if (isset($draft_data['categoryIds']) && is_array($draft_data['categoryIds'])) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: Found ' . count($draft_data['categoryIds']) . ' category IDs to process');
                }
                $category_ids = array();
                foreach ($draft_data['categoryIds'] as $category_id) {
                    if (is_numeric($category_id)) {
                        $category_id = (int) $category_id;
                        // Verify category exists
                        $category = get_category($category_id);
                        if ($category && !is_wp_error($category)) {
                            $category_ids[] = $category_id;
                            if (defined('WP_DEBUG') && WP_DEBUG) {
                                error_log('ACA DEBUG: Valid category ID: ' . $category_id . ' (' . $category->name . ')');
                            }
                        } else {
                            if (defined('WP_DEBUG') && WP_DEBUG) {
                                error_log('ACA DEBUG: Invalid category ID: ' . $category_id);
                            }
                        }
                    }
                }
                if (!empty($category_ids)) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: Setting ' . count($category_ids) . ' categories');
                    }
                    wp_set_post_categories($post_id, $category_ids);
                } else {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: No valid categories to set');
                    }
                }
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: No categoryIds in draft_data');
                }
            }
            
            // Add tags safely
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Processing tags');
            }
            if (isset($draft_data['tags']) && is_array($draft_data['tags'])) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: Found ' . count($draft_data['tags']) . ' tags to process');
                }
                $clean_tags = array();
                foreach ($draft_data['tags'] as $tag) {
                    if (is_string($tag) && !empty(trim($tag))) {
                        $clean_tags[] = sanitize_text_field($tag);
                    }
                }
                if (!empty($clean_tags)) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: Setting ' . count($clean_tags) . ' tags: ' . implode(', ', $clean_tags));
                    }
                    wp_set_post_tags($post_id, $clean_tags);
                } else {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: No valid tags to set');
                    }
                }
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: No tags in draft_data');
                }
            }
            
            // Set featured image if we have one
            if ($image_data) {
                $attachment_id = $this->save_image_to_media_library($image_data, $idea->title, $post_id);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                    aca_debug_log('Successfully set featured image for post ' . $post_id . ' with attachment ' . $attachment_id);
                } else {
                    aca_debug_log('Failed to create attachment for featured image');
                }
            }
            
            // Send SEO data to detected SEO plugins
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Sending SEO data to detected SEO plugins');
            }
            try {
                $focus_keywords_array = !empty($focus_keywords) ? explode(',', $focus_keywords) : array();
                $focus_keywords_array = array_map('trim', $focus_keywords_array);
                
                $seo_results = $this->send_seo_data_to_plugins(
                    $post_id,
                    $draft_data['metaTitle'],
                    $draft_data['metaDescription'],
                    $focus_keywords_array
                );
                
                if (!empty($seo_results)) {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: SEO data sent successfully: ' . json_encode($seo_results));
                    }
                } else {
                    if (defined('WP_DEBUG') && WP_DEBUG) {
                        error_log('ACA DEBUG: No SEO plugins detected or no data sent');
                    }
                }
            } catch (Exception $e) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: SEO data sending failed (non-blocking): ' . $e->getMessage());
                }
            }
            
            // Update idea status instead of deleting (safer approach)
            $wpdb->update(
                $wpdb->prefix . 'aca_ideas',
                array('status' => 'archived'),
                array('id' => $idea_id)
            );
            
            // Add activity log with error handling (non-blocking)
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Adding activity log');
            }
            $log_result = $this->add_activity_log('draft_created', "Created draft: \"{$idea->title}\"", 'FileText');
            if (!$log_result) {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: Activity log failed but continuing with draft creation');
                }
            } else {
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    error_log('ACA DEBUG: Activity log added successfully');
                }
            }
            
            // Return the created post - simplified approach to avoid formatting errors
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Getting created post for response');
            }
            $created_post = get_post($post_id);
            
            if (!$created_post) {
                // Even if we can't retrieve the post, it was created successfully
                aca_debug_log('Post created but could not retrieve - Post ID: ' . $post_id);
                return rest_ensure_response(array(
                    'id' => $post_id,
                    'title' => $idea->title,
                    'content' => isset($draft_data['content']) ? $draft_data['content'] : '',
                    'status' => 'draft',
                    'createdAt' => current_time('mysql'),
                    'categories' => isset($draft_data['categories']) ? $draft_data['categories'] : array(),
                    'tags' => isset($draft_data['tags']) ? $draft_data['tags'] : array(),
                    'message' => 'Draft created successfully'
                ));
            }
            
            // Use simplified response format to avoid complex formatting errors
            $safe_response = array(
                'id' => (int) $post_id,
                'title' => $created_post->post_title ?: '',
                'content' => $created_post->post_content ?: '',
                'excerpt' => $created_post->post_excerpt ?: '',
                'status' => $created_post->post_status === 'publish' ? 'published' : ($created_post->post_status === 'future' ? 'draft' : ($created_post->post_status ?: 'draft')),
                'createdAt' => $created_post->post_date ?: current_time('mysql'),
                'categories' => array(), // Will be populated from actual post categories
                'tags' => isset($draft_data['tags']) && is_array($draft_data['tags']) ? $draft_data['tags'] : array(),
                'metaTitle' => isset($draft_data['metaTitle']) ? $draft_data['metaTitle'] : '',
                'metaDescription' => isset($draft_data['metaDescription']) ? $draft_data['metaDescription'] : '',
                'focusKeywords' => $focus_keywords,
                'featuredImage' => '',
                'publishedAt' => null,
                'url' => null,
                'scheduledFor' => '',
                'aiGenerated' => true,
                'generationDate' => current_time('mysql'),
                'message' => 'Draft created successfully'
            );
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Returning successful response');
            }
            return rest_ensure_response($safe_response);
            
        } catch (Exception $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA DEBUG: Exception caught in create_draft_from_idea');
            }
            aca_debug_log('Draft Creation Error: ' . $e->getMessage());
            aca_debug_log('Draft Creation Stack Trace: ' . $e->getTraceAsString());
            aca_debug_log('Draft Creation Context - Idea ID: ' . $idea_id);
            
            // If post was created but we got an error later, try to return success anyway
            if (isset($post_id) && $post_id && !is_wp_error($post_id)) {
                aca_debug_log('Post was created successfully (ID: ' . $post_id . ') but error occurred in processing');
                
                // Try to get basic post info and return success
                $created_post = get_post($post_id);
                if ($created_post) {
                    return rest_ensure_response(array(
                        'id' => $post_id,
                        'title' => $created_post->post_title,
                        'content' => $created_post->post_content,
                        'status' => $created_post->post_status === 'publish' ? 'published' : ($created_post->post_status === 'future' ? 'draft' : ($created_post->post_status ?: 'draft')),
                        'createdAt' => $created_post->post_date,
                        'categories' => array(),
                        'tags' => array(),
                        'message' => 'Draft created successfully'
                    ));
                }
            }
            
            // Return the actual error message for debugging
            return new WP_Error('creation_failed', 'DETAILED ERROR: ' . $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Update draft
     */
    public function update_draft($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $post_id = $request['id'];
        $params = $request->get_json_params();
        
        $update_data = array(
            'ID' => $post_id
        );
        
        if (isset($params['title'])) {
            $update_data['post_title'] = $params['title'];
        }
        
        if (isset($params['content'])) {
            $update_data['post_content'] = $params['content'];
        }
        
        $result = wp_update_post($update_data);
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        // Update meta fields
        if (isset($params['metaTitle'])) {
            update_post_meta($post_id, '_aca_meta_title', $params['metaTitle']);
        }
        
        if (isset($params['metaDescription'])) {
            update_post_meta($post_id, '_aca_meta_description', $params['metaDescription']);
        }
        
        if (isset($params['focusKeywords'])) {
            update_post_meta($post_id, '_aca_focus_keywords', $params['focusKeywords']);
        }
        
        $this->add_activity_log('draft_updated', "Updated draft: \"{$params['title']}\"", 'Edit');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Publish draft
     */
    public function publish_draft($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $post_id = $request['id'];
        
        $result = wp_update_post(array(
            'ID' => $post_id,
            'post_status' => 'publish'
        ));
        
        if (is_wp_error($result)) {
            return $result;
        }
        
        $post = get_post($post_id);
        $this->add_activity_log('post_published', "Published post: \"{$post->post_title}\"", 'Send');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Schedule draft
     */
    public function schedule_draft($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $post_id = $request['id'];
        $params = $request->get_json_params();
        
        // Debug logging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Schedule Draft: Post ID = ' . $post_id);
            error_log('ACA Schedule Draft: Params = ' . json_encode($params));
        }
        
        // Handle both 'date' and 'scheduledDate' parameters for compatibility
        $scheduled_date = isset($params['scheduledDate']) ? $params['scheduledDate'] : (isset($params['date']) ? $params['date'] : null);
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Schedule Draft: Scheduled Date = ' . $scheduled_date);
        }
        
        if (empty($scheduled_date)) {
            return new WP_Error('missing_date', 'Scheduled date is required', array('status' => 400));
        }
        
        // Get current post to check its status
        $current_post = get_post($post_id);
        if (!$current_post) {
            return new WP_Error('post_not_found', 'Post not found', array('status' => 404));
        }
        
        // Parse the incoming date (usually in ISO format from JavaScript)
        $parsed_date = date_create($scheduled_date);
        if (!$parsed_date) {
            return new WP_Error('invalid_date', 'Invalid date format', array('status' => 400));
        }
        
        // Get current WordPress time for comparison
        $current_wp_time = current_time('timestamp');
        $current_wp_date = current_time('Y-m-d H:i:s');
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Schedule Draft: Current WP Time = ' . $current_wp_date);
            error_log('ACA Schedule Draft: Received Date = ' . $parsed_date->format('Y-m-d H:i:s'));
        }
        
        // If the date doesn't include a time (just date from calendar), set it to a future time
        $time_part = $parsed_date->format('H:i:s');
        
        // If time is 00:00:00 (midnight), it means we got just a date from calendar drag-drop
        if ($time_part === '00:00:00') {
            // Set to 9:00 AM of that date to ensure it's in the future for scheduling
            $parsed_date->setTime(9, 0, 0);
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA Schedule Draft: Set time to 9:00 AM for calendar date');
            }
        }
        
        // Convert to WordPress local time format
        $local_date = $parsed_date->format('Y-m-d H:i:s');
        $target_timestamp = $parsed_date->getTimestamp();
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Schedule Draft: Target Local Date = ' . $local_date);
            error_log('ACA Schedule Draft: Target Timestamp = ' . $target_timestamp);
            error_log('ACA Schedule Draft: Current Timestamp = ' . $current_wp_time);
        }
        
        // Update post meta for our plugin
        update_post_meta($post_id, '_aca_scheduled_for', $scheduled_date);
        
        // Prepare update data
        $update_data = array(
            'ID' => $post_id,
            'post_date' => $local_date,
            'post_date_gmt' => get_gmt_from_date($local_date),
            'edit_date' => true  // This is crucial for WordPress to accept date changes on drafts
        );
        
        // Determine post status based on timing
        if ($target_timestamp > $current_wp_time) {
            // Future date - schedule it
            $update_data['post_status'] = 'future';
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA Schedule Draft: Setting post status to FUTURE');
            }
        } else {
            // Past or current date - keep as draft but update the date
            $update_data['post_status'] = 'draft';
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA Schedule Draft: Past/current date - keeping as draft');
            }
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Schedule Draft: Update Data = ' . json_encode($update_data));
        }
        
        // Update the post
        $update_result = wp_update_post($update_data);
        
        if (is_wp_error($update_result)) {
            aca_debug_log('Schedule Draft: wp_update_post failed: ' . $update_result->get_error_message());
            return new WP_Error('update_failed', 'Failed to schedule post: ' . $update_result->get_error_message(), array('status' => 500));
        }
        
        if ($update_result === 0) {
            aca_debug_log('Schedule Draft: wp_update_post returned 0');
            return new WP_Error('update_failed', 'Failed to update post - wp_update_post returned 0', array('status' => 500));
        }
        
        // Get the updated post and format it for API response
        $updated_post = get_post($post_id);
        if (!$updated_post) {
            return new WP_Error('post_not_found', 'Post not found after update', array('status' => 404));
        }
        
        $formatted_post = $this->format_post_for_api($updated_post);
        
        // Log the successful scheduling
        $readable_date = $parsed_date->format('M j, Y g:i A');
        $this->add_activity_log('draft_scheduled', "Scheduled draft: \"{$updated_post->post_title}\" for {$readable_date}", 'Calendar');
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Schedule Draft: Successfully updated post. Final status = ' . $updated_post->post_status);
            error_log('ACA Schedule Draft: Final post_date = ' . $updated_post->post_date);
            error_log('ACA Schedule Draft: Final post_date_gmt = ' . $updated_post->post_date_gmt);
        }
        
        return rest_ensure_response($formatted_post);
    }
    
    /**
     * Get activity logs
     */
    public function get_activity_logs($request) {
        global $wpdb;
        
        $logs = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}aca_activity_logs ORDER BY timestamp DESC LIMIT 50"
        );
        
        return rest_ensure_response($logs);
    }
    
    /**
     * Add activity log via REST API
     */
    public function add_activity_log_endpoint($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $params = $request->get_json_params();
        
        if (empty($params['type']) || empty($params['message']) || empty($params['icon'])) {
            return new WP_Error('missing_params', 'Missing required parameters: type, message, icon', array('status' => 400));
        }
        
        $this->add_activity_log($params['type'], $params['message'], $params['icon']);
        
        return rest_ensure_response(array('success' => true));
    }
    
    // Helper methods
    
    /**
     * Get existing titles to avoid duplicates
     */
    private function get_existing_titles() {
        global $wpdb;
        
        $idea_titles = $wpdb->get_col("SELECT title FROM {$wpdb->prefix}aca_ideas");
        $post_titles = $wpdb->get_col("SELECT post_title FROM {$wpdb->prefix}posts WHERE post_status IN ('publish', 'draft')");
        
        return array_merge($idea_titles, $post_titles);
    }
    
    /**
     * Format post for API response
     */
    private function format_post_for_api($post) {
        try {
            // Safely get featured image
            $featured_image = '';
            try {
                $attachment_id = get_post_thumbnail_id($post->ID);
                if ($attachment_id) {
                    $image_url = wp_get_attachment_image_src($attachment_id, 'large');
                    if ($image_url && is_array($image_url)) {
                        $featured_image = $image_url[0];
                    }
                }
            } catch (Exception $img_error) {
                aca_debug_log('Featured Image Error: ' . $img_error->getMessage());
            }
            
            // Safely get categories
            $category_names = array();
            try {
                $categories = get_the_category($post->ID);
                if ($categories && is_array($categories)) {
                    foreach ($categories as $category) {
                        if (isset($category->name)) {
                            $category_names[] = $category->name;
                        }
                    }
                }
            } catch (Exception $cat_error) {
                aca_debug_log('Categories Error: ' . $cat_error->getMessage());
            }
            
            // Safely get tags
            $tag_names = array();
            try {
                $tags = get_the_tags($post->ID);
                if ($tags && is_array($tags)) {
                    foreach ($tags as $tag) {
                        if (isset($tag->name)) {
                            $tag_names[] = $tag->name;
                        }
                    }
                }
            } catch (Exception $tag_error) {
                aca_debug_log('Tags Error: ' . $tag_error->getMessage());
            }
            
            // Safely get meta data
            $meta_title = '';
            $meta_description = '';
            $focus_keywords = '';
            $scheduled_for = '';
            $ai_generated = false;
            $generation_date = '';
            
            try {
                $meta_title = get_post_meta($post->ID, '_aca_meta_title', true) ?: '';
                $meta_description = get_post_meta($post->ID, '_aca_meta_description', true) ?: '';
                $focus_keywords = get_post_meta($post->ID, '_aca_focus_keywords', true) ?: '';
                $scheduled_for = get_post_meta($post->ID, '_aca_scheduled_for', true) ?: '';
                $ai_generated = get_post_meta($post->ID, '_aca_ai_generated', true) ?: false;
                $generation_date = get_post_meta($post->ID, '_aca_generation_date', true) ?: '';
            } catch (Exception $meta_error) {
                aca_debug_log('Meta Data Error: ' . $meta_error->getMessage());
            }
            
            return array(
                'id' => (int) $post->ID,
                'title' => $post->post_title ?: '',
                'content' => $post->post_content ?: '',
                'excerpt' => $post->post_excerpt ?: '',
                'metaTitle' => $meta_title,
                'metaDescription' => $meta_description,
                'focusKeywords' => $focus_keywords,
                'categories' => $category_names,
                'tags' => $tag_names,
                'featuredImage' => $featured_image,
                'createdAt' => $post->post_date ?: current_time('mysql'),
                'status' => $post->post_status === 'publish' ? 'published' : ($post->post_status === 'future' ? 'draft' : ($post->post_status ?: 'draft')),
                'publishedAt' => $post->post_status === 'publish' ? $post->post_date : null,
                'url' => $post->post_status === 'publish' ? get_permalink($post->ID) : null,
                'scheduledFor' => $scheduled_for,
                'aiGenerated' => $ai_generated,
                'generationDate' => $generation_date
            );
            
        } catch (Exception $e) {
            aca_debug_log('Format Post Critical Error: ' . esc_html($e->getMessage()));
            throw new Exception('Failed to format post data: ' . esc_html($e->getMessage()));
        }
    }
    
    /**
     * Add activity log entry
     */
    private function add_activity_log($type, $details, $icon) {
        global $wpdb;
        
        try {
            // Check if table exists first
            $table_name = $wpdb->prefix . 'aca_activity_logs';
            $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;
            
            if (!$table_exists) {
                aca_debug_log('Activity logs table does not exist: ' . $table_name);
                return false;
            }
            
            $result = $wpdb->insert(
                $table_name,
                array(
                    'timestamp' => current_time('mysql'),
                    'type' => sanitize_text_field($type),
                    'details' => sanitize_text_field($details),
                    'icon' => sanitize_text_field($icon)
                )
            );
            
            if ($result === false) {
                aca_debug_log('Failed to insert activity log: ' . $wpdb->last_error);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            aca_debug_log('Activity log exception: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get featured image (AI or stock photo)
     */
    private function get_featured_image($title, $settings) {
        try {
            if ($settings['imageSourceProvider'] === 'ai') {
                return $this->call_gemini_generate_image($settings['geminiApiKey'], $title, $settings['aiImageStyle']);
            } else {
                $api_keys = array(
                    'pexels' => $settings['pexelsApiKey'],
                    'unsplash' => $settings['unsplashApiKey'],
                    'pixabay' => $settings['pixabayApiKey']
                );
                
                $api_key = $api_keys[$settings['imageSourceProvider']];
                if (empty($api_key)) {
                    return null;
                }
                
                return $this->fetch_stock_photo($title, $settings['imageSourceProvider'], $api_key);
            }
        } catch (Exception $e) {
            aca_debug_log('Image Generation Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Save image to WordPress media library and properly attach to post
     */
    private function save_image_to_media_library($image_data, $title, $post_id = 0) {
        if (!function_exists('media_handle_sideload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        
        // Create temporary file
        $temp_file = wp_tempnam();
        
        // Decode and save image data
        $image_content = base64_decode($image_data);
        if ($image_content === false) {
            aca_debug_log('Failed to decode base64 image data');
            return false;
        }
        
        file_put_contents($temp_file, $image_content);
        
        // Verify file was created successfully
        if (!file_exists($temp_file) || filesize($temp_file) === 0) {
            aca_debug_log('Failed to create temporary image file');
            if (file_exists($temp_file)) {
                wp_delete_file($temp_file);
            }
            return false;
        }
        
        $file_array = array(
            'name' => sanitize_file_name($title) . '.jpg',
            'tmp_name' => $temp_file
        );
        
        // Attach image to specific post if post_id provided
        $attachment_id = media_handle_sideload($file_array, $post_id);
        
        if (is_wp_error($attachment_id)) {
            aca_debug_log('Failed to create media attachment: ' . $attachment_id->get_error_message());
            if (file_exists($temp_file)) {
                wp_delete_file($temp_file);
            }
            return false;
        }
        
        // Set alt text for accessibility
        if ($attachment_id) {
            update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($title));
        }
        
        return $attachment_id;
    }
    
    /**
     * Extract key concepts from a title for better image relevance
     */
    private function extract_key_concepts($title) {
        // Remove common stop words and extract meaningful concepts
        $stop_words = array('the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'up', 'about', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'between', 'among', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'should', 'could', 'can', 'may', 'might', 'must', 'shall', 'this', 'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they', 'me', 'him', 'her', 'us', 'them', 'my', 'your', 'his', 'her', 'its', 'our', 'their', 'how', 'what', 'when', 'where', 'why', 'which', 'who', 'whom');
        
        // Clean and split the title
        $words = preg_split('/[\s\-_:;,.!?]+/', strtolower($title));
        $key_words = array();
        
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $stop_words) && !is_numeric($word)) {
                $key_words[] = $word;
            }
        }
        
        // Return the first 5 key concepts or all if less than 5
        $concepts = array_slice($key_words, 0, 5);
        return implode(', ', $concepts);
    }
    
    // AI Service calls - Real Gemini API integration
    
    private function call_gemini_generate_ideas($api_key, $style_guide, $existing_titles, $count, $search_console_data) {
        // Get site language for content generation
        $site_locale = get_locale();
        $site_language = $this->get_language_from_locale($site_locale);
        
        $prompt = "
            Based on this style guide: {$style_guide}
            
            IMPORTANT: Generate ALL titles in {$site_language} language. This is the primary language of the website based on the WordPress locale: {$site_locale}.
            
            Generate {$count} unique, engaging blog post titles that match the style and tone described in the guide.
            
            Avoid these existing titles: " . json_encode($existing_titles) . "
            
            Return ONLY a JSON array of strings (the titles in {$site_language}), nothing else.
            Example format: [\"Title 1\", \"Title 2\", \"Title 3\"]
        ";

        if ($search_console_data) {
            $prompt .= "
            
            Also consider these SEO insights:
            - Top performing queries: " . json_encode($search_console_data['topQueries']) . "
            - Underperforming pages that could be improved: " . json_encode($search_console_data['underperformingPages']) . "
            
            Incorporate relevant keywords from the top queries and create content that could improve upon the underperforming topics.
            ";
        }
        
        return $this->call_gemini_api($api_key, $prompt);
    }
    
    private function call_gemini_generate_similar_ideas($api_key, $base_title, $existing_titles) {
        // Get site language for content generation
        $site_locale = get_locale();
        $site_language = $this->get_language_from_locale($site_locale);
        
        $prompt = "
            Generate 3-5 blog post titles that are similar to this idea: \"{$base_title}\"
            
            IMPORTANT: Generate ALL titles in {$site_language} language. This is the primary language of the website based on the WordPress locale: {$site_locale}.
            
            The similar titles should:
            - Cover the same general topic but from different angles
            - Be unique and engaging
            - Be written in {$site_language}
            - Not duplicate any of these existing titles: " . json_encode($existing_titles) . "
            
            Return ONLY a JSON array of strings (the titles in {$site_language}), nothing else.
            Example format: [\"Similar Title 1\", \"Similar Title 2\", \"Similar Title 3\"]
        ";
        
        return $this->call_gemini_api($api_key, $prompt);
    }
    
    private function call_gemini_create_draft($api_key, $title, $style_guide, $existing_posts, $existing_categories = array()) {
        // Get site language for content generation
        $site_locale = get_locale();
        $site_language = $this->get_language_from_locale($site_locale);
        
        // Safely build context string
        $context_string = '';
        if (!empty($existing_posts) && is_array($existing_posts)) {
            $context_string = "Here are some recently published posts for context and internal linking:\n";
            foreach ($existing_posts as $post) {
                if (is_array($post) && isset($post['title'], $post['url'], $post['content'])) {
                    $safe_title = wp_strip_all_tags($post['title']);
                    $safe_url = esc_url($post['url']);
                    $safe_content = wp_strip_all_tags(substr($post['content'], 0, 200));
                    $context_string .= "Title: {$safe_title}\nURL: {$safe_url}\nContent snippet: {$safe_content}...\n\n";
                }
            }
        }

        // Build hierarchical categories context string
        $categories_string = '';
        if (!empty($existing_categories) && is_array($existing_categories)) {
            $categories_string = "Available categories with hierarchy (select the most appropriate ones):\n";
            
            // Group categories by hierarchy level
            $root_categories = array();
            $child_categories = array();
            
            foreach ($existing_categories as $category) {
                if (isset($category['id'], $category['name'])) {
                    if ($category['parent_id'] == 0) {
                        $root_categories[] = $category;
                    } else {
                        $child_categories[] = $category;
                    }
                }
            }
            
            // Display root categories first
            foreach ($root_categories as $category) {
                $categories_string .= "- ID: {$category['id']}, Name: \"{$category['name']}\", Posts: {$category['count']} (ROOT CATEGORY)\n";
                
                // Display child categories under their parent
                foreach ($child_categories as $child) {
                    if ($child['parent_id'] == $category['id']) {
                        $indent = str_repeat('  ', $child['hierarchy_level']);
                        $categories_string .= "{$indent}└─ ID: {$child['id']}, Name: \"{$child['name']}\", Posts: {$child['count']} (SUBCATEGORY of \"{$child['parent_name']}\")\n";
                    }
                }
            }
            
            $categories_string .= "\nIMPORTANT: When selecting categories, consider the hierarchy. If content is about a specific subcategory topic, choose the subcategory rather than the parent category.\n\n";
        }

        // Clean inputs safely
        $safe_title = sanitize_text_field($title);
        
        // Handle style guide - don't strip tags if it's JSON
        $safe_style_guide = '';
        if (is_string($style_guide)) {
            // Check if it's JSON first
            $decoded = json_decode($style_guide, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // It's valid JSON, keep it as is
                $safe_style_guide = $style_guide;
            } else {
                // It's not JSON, sanitize it
                $safe_style_guide = sanitize_text_field($style_guide);
            }
        }

        $prompt = "Create a comprehensive blog post based on this idea: \"{$safe_title}\"

IMPORTANT: Write the entire content in {$site_language}. This is the primary language of the website based on the WordPress locale: {$site_locale}.

Use this style guide: {$safe_style_guide}

{$context_string}

{$categories_string}

Requirements:
- Write EVERYTHING in {$site_language} language (title, content, headings, etc.)
- Write a well-structured blog post with clear H2 and H3 headings
- 800-1500 words in length
- Engaging introduction and compelling conclusion
- SEO-optimized content matching the style guide
- Include 2-3 internal links to the provided existing posts where contextually relevant
- For categories: ONLY use category IDs from the provided list above. Select 1-2 most relevant ones based on content topic and hierarchy
- Consider category hierarchy: if content is specific to a subcategory, choose the subcategory rather than parent category
- For tags: Create new relevant tags as strings in {$site_language}

CONTENT FORMAT REQUIREMENTS:
- Use ONLY HTML formatting, NOT Markdown
- For headings: <h2>Heading</h2>, <h3>Subheading</h3>
- For paragraphs: <p>Text content</p>
- For bold text: <strong>Bold text</strong>
- For italic text: <em>Italic text</em>
- For lists: <ul><li>Item 1</li><li>Item 2</li></ul>
- For links: <a href=\"URL\">Link text</a>
- NO Markdown syntax like *, **, [text](url), ##, etc.

IMPORTANT: Return ONLY a valid JSON object with this exact structure. Do not include any text before or after the JSON:

{
  \"content\": \"The full blog post content in proper HTML format with <h2>, <h3>, <p>, <strong>, <ul>, <li>, <a> tags. NO Markdown syntax.\",
  \"metaTitle\": \"SEO-optimized title (50-60 characters)\",
  \"metaDescription\": \"Compelling meta description (150-160 characters)\",
  \"focusKeywords\": [\"keyword1\", \"keyword2\", \"keyword3\", \"keyword4\", \"keyword5\"],
  \"tags\": [\"tag1\", \"tag2\", \"tag3\", \"tag4\", \"tag5\"],
  \"categoryIds\": [1, 5],
  \"excerpt\": \"Brief excerpt for the post (150 characters)\"
}";
        
        return $this->call_gemini_api($api_key, $prompt);
    }
    
    private function call_gemini_generate_image($api_key, $title, $style) {
        $style_prompts = array(
            'photorealistic' => 'photorealistic, high quality, professional photography, 4K, HDR, studio lighting',
            'digital_art' => 'digital art, illustration, creative, artistic, detailed, professional'
        );
        
        $style_prompt = isset($style_prompts[$style]) ? $style_prompts[$style] : $style_prompts['digital_art'];
        
        // Create a descriptive prompt for the blog post title - EXPLICITLY PREVENT TEXT
        // Extract key concepts from the title for better relevance
        $clean_title = wp_strip_all_tags($title);
        $key_concepts = $this->extract_key_concepts($clean_title);
        
        $prompt = "Create a {$style_prompt} image that represents the concept of \"{$clean_title}\". Focus on the main themes: {$key_concepts}. The image should be relevant to the topic, visually appealing, suitable for use as a featured image on a professional blog, and capture the essence of the subject matter. IMPORTANT: Do not include any text, words, letters, numbers, signs, or written content in the image. The image should be purely visual without any textual elements, logos, or readable content.";
        
        try {
            // Use Google's Imagen API for actual image generation
            $imagen_response = $this->call_imagen_api($api_key, $prompt);
            
            if (is_wp_error($imagen_response)) {
                aca_debug_log('Imagen API Error: ' . $imagen_response->get_error_message());
                throw new Exception('Imagen API error: ' . $imagen_response->get_error_message());
            }
            
            return $imagen_response;
            
        } catch (Exception $e) {
            aca_debug_log('AI Image Generation Error: ' . $e->getMessage());
            
            // Provide more specific error messages for common issues
            $error_message = $e->getMessage();
            if (strpos($error_message, 'Google Cloud Project ID not configured') !== false) {
                $error_message = 'Please configure Google Cloud Project ID in plugin settings';
            } elseif (strpos($error_message, 'wrong_api_type') !== false) {
                $error_message = 'Please use a Google Cloud Vertex AI access token, not a Google AI Studio API key';
            } elseif (strpos($error_message, 'service_account_auth') !== false) {
                $error_message = 'Service account authentication not yet implemented. Please use an access token';
            } elseif (strpos($error_message, 'authentication') !== false || strpos($error_message, 'Unauthorized') !== false) {
                $error_message = 'Authentication failed. Please check your Google Cloud access token';
            }
            
            // Fallback: Return a more informative placeholder
            $fallback_data = array(
                'error' => true,
                'message' => $error_message,
                'title' => $title,
                'style' => $style,
                'timestamp' => current_time('mysql'),
                'help' => 'Check AI_IMAGE_GENERATION_SETUP.md for setup instructions'
            );
            
            return base64_encode(json_encode($fallback_data));
        }
    }
    
    private function call_imagen_api($api_key, $prompt) {
        // Google Cloud Vertex AI Imagen API endpoint
        $project_id = get_option('aca_google_cloud_project_id', '');
        $location = get_option('aca_google_cloud_location', 'us-central1');
        
        if (empty($project_id)) {
            return new WP_Error('missing_project_id', 'Google Cloud Project ID not configured. Please set it in plugin settings.');
        }
        
        // Check if API key looks like a proper Google Cloud credential
        if (empty($api_key) || strlen($api_key) < 20) {
            return new WP_Error('invalid_api_key', 'Invalid Google Cloud API key. Please provide a valid service account key or access token.');
        }
        
        // Use Imagen 3.0 Generate 002 model (latest stable version)
        $model = 'imagen-3.0-generate-002';
        $url = "https://{$location}-aiplatform.googleapis.com/v1/projects/{$project_id}/locations/{$location}/publishers/google/models/{$model}:predict";
        
        $request_body = array(
            'instances' => array(
                array(
                    'prompt' => $prompt,
                    'negativePrompt' => 'text, words, letters, numbers, signs, writing, typography, captions, labels, watermarks, logos, banners, advertisements, titles, subtitles, quotes, speech bubbles, signage, readable content'
                )
            ),
            'parameters' => array(
                'sampleCount' => 1,
                'aspectRatio' => '16:9', // Good for featured images
                'safetyFilterLevel' => 'block_some',
                'personGeneration' => 'allow_adult'
            )
        );
        
        // Try to get a proper access token
        $access_token = $this->get_google_access_token($api_key);
        if (is_wp_error($access_token)) {
            return $access_token;
        }
        
        $headers = array(
            'Authorization' => 'Bearer ' . $access_token,
            'Content-Type' => 'application/json'
        );
        
        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($request_body),
            'timeout' => 60
        ));
        
        if (is_wp_error($response)) {
            aca_debug_log('Imagen API network error: ' . $response->get_error_message());
            return new WP_Error('network_error', 'Failed to connect to Google Imagen API: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            $error_message = "Imagen API returned status code {$response_code}";
            if (!empty($response_body)) {
                $error_data = json_decode($response_body, true);
                if (isset($error_data['error']['message'])) {
                    $error_message .= ': ' . $error_data['error']['message'];
                }
            }
            aca_debug_log('Imagen API error: ' . $error_message);
            return new WP_Error('imagen_api_error', $error_message);
        }
        
        $data = json_decode($response_body, true);
        
        if (!isset($data['predictions'][0]['bytesBase64Encoded'])) {
            aca_debug_log('Imagen API invalid response: ' . $response_body);
            return new WP_Error('invalid_response', 'Invalid response from Imagen API - missing image data');
        }
        
        // Return the base64 encoded image
        return $data['predictions'][0]['bytesBase64Encoded'];
    }
    
    private function get_google_access_token($api_key) {
        // Check if we have a cached access token
        $cached_token = get_transient('aca_google_access_token');
        if ($cached_token) {
            return $cached_token;
        }
        
        // For proper Vertex AI authentication, we need to handle different credential types
        
        // If the API key looks like a JSON service account key
        if (strpos($api_key, '{') === 0 && strpos($api_key, 'private_key') !== false) {
            // This is a service account JSON - we need to generate a JWT token
            // For now, return an error asking user to use proper authentication
            return new WP_Error('service_account_auth', 'Service account JSON authentication is not yet implemented. Please use an access token or set up Application Default Credentials.');
        }
        
        // If it looks like an access token (starts with ya29. or similar)
        if (preg_match('/^[a-zA-Z0-9\.\-_]{100,}$/', $api_key)) {
            // Cache the token for 30 minutes (Google tokens typically last 1 hour)
            set_transient('aca_google_access_token', $api_key, 30 * MINUTE_IN_SECONDS);
            return $api_key;
        }
        
        // If it's a shorter API key, it might be for AI Studio (not Vertex AI)
        if (strlen($api_key) < 100) {
            return new WP_Error('wrong_api_type', 'This appears to be a Google AI Studio API key. For Imagen API, you need a Google Cloud Vertex AI access token or service account credentials.');
        }
        
        // Default case - try to use it as-is but warn about potential issues
                    aca_debug_log('Using API key as access token - this may not work properly');
        return $api_key;
    }
    
    private function fetch_stock_photo($query, $provider, $api_key) {
        $url = '';
        $headers = array();
        
        switch ($provider) {
            case 'pexels':
                $url = 'https://api.pexels.com/v1/search?query=' . urlencode($query) . '&per_page=1&orientation=landscape';
                $headers = array('Authorization' => $api_key);
                break;
            case 'unsplash':
                $url = 'https://api.unsplash.com/search/photos?query=' . urlencode($query) . '&per_page=1&orientation=landscape';
                $headers = array('Authorization' => 'Client-ID ' . $api_key);
                break;
            case 'pixabay':
                $url = 'https://pixabay.com/api/?key=' . $api_key . '&q=' . urlencode($query) . '&image_type=photo&orientation=horizontal&per_page=3&safesearch=true';
                break;
        }
        
        if (empty($url)) {
            throw new Exception('Unsupported stock photo provider');
        }
        
        $response = wp_remote_get($url, array(
            'headers' => $headers,
            'timeout' => 15,
            'user-agent' => 'AI Content Agent/2.4.0'
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to fetch from ' . esc_html($provider) . ': ' . esc_html($response->get_error_message()));
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            throw new Exception('API request failed with status ' . intval($status_code) . ' for provider: ' . esc_html($provider));
        }
        
        $body = wp_remote_retrieve_body($response);
        if (empty($body)) {
            throw new Exception('Empty response from ' . esc_html($provider) . ' API');
        }
        
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from ' . esc_html($provider) . ': ' . esc_html(json_last_error_msg()));
        }
        
        $image_url = '';
        switch ($provider) {
            case 'pexels':
                if (!empty($data['photos'][0]['src']['large'])) {
                    $image_url = $data['photos'][0]['src']['large'];
                }
                break;
            case 'unsplash':
                if (!empty($data['results'][0]['urls']['regular'])) {
                    $image_url = $data['results'][0]['urls']['regular'];
                }
                break;
            case 'pixabay':
                if (!empty($data['hits'][0]['webformatURL'])) {
                    $image_url = $data['hits'][0]['webformatURL'];
                }
                break;
        }
        
        if (empty($image_url)) {
            throw new Exception('No images found for query: ' . esc_html($query));
        }
        
        // Download and convert to base64
        $image_response = wp_remote_get($image_url);
        if (is_wp_error($image_response)) {
            throw new Exception('Failed to download image');
        }
        
        return base64_encode(wp_remote_retrieve_body($image_response));
    }
    
    /**
     * Make actual API call to Gemini with retry logic and model fallback
     */
    private function call_gemini_api($api_key, $prompt, $model = 'gemini-2.0-flash', $retry_count = 0) {
        $max_retries = 3;
        $retry_delay = 2; // seconds
        $fallback_model = 'gemini-1.5-pro';
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        
        // Clean and validate prompt
        $clean_prompt = is_string($prompt) ? trim($prompt) : '';
        if (empty($clean_prompt)) {
            throw new Exception('Empty or invalid prompt provided');
        }
        
        // Ensure prompt is valid UTF-8
        if (!mb_check_encoding($clean_prompt, 'UTF-8')) {
            $clean_prompt = mb_convert_encoding($clean_prompt, 'UTF-8', 'UTF-8');
        }
        
        $request_data = array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $clean_prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'temperature' => 0.7,
                'maxOutputTokens' => 4096, // Increased from 2048
                'responseMimeType' => 'application/json'
            )
        );
        
        $body = json_encode($request_data);
        
        // Check if json_encode failed
        if ($body === false) {
            aca_debug_log('JSON Encode Error: ' . esc_html(json_last_error_msg()));
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA Request Data: ' . print_r($request_data, true));
            }
            throw new Exception('Failed to encode request data: ' . esc_html(json_last_error_msg()));
        }
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $api_key
            ),
            'body' => $body,
            'timeout' => 120, // Increased timeout to 2 minutes
            'blocking' => true,
            'sslverify' => true
        ));

        if (is_wp_error($response)) {
            aca_debug_log('Gemini API WP Error: ' . esc_html($response->get_error_message()));
            throw new Exception('Gemini API request failed: ' . esc_html($response->get_error_message()));
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        
        // Handle 503 and other overload errors with retry logic
        if ($response_code === 503 || $response_code === 429) {
            $error_body = wp_remote_retrieve_body($response);
                            aca_debug_log("Gemini API Overload Error (Code {$response_code}): " . substr($error_body, 0, 500));
            
            // Check if we should retry
            if ($retry_count < $max_retries) {
                // Try fallback model on first retry
                if ($retry_count === 0 && $model === 'gemini-2.0-flash') {
                    aca_debug_log("Trying fallback model {$fallback_model}");
                    sleep($retry_delay);
                    return $this->call_gemini_api($api_key, $prompt, $fallback_model, $retry_count + 1);
                }
                
                // Exponential backoff
                $delay = $retry_delay * pow(2, $retry_count);
                aca_debug_log("Retrying in {$delay} seconds... (attempt " . ($retry_count + 1) . "/{$max_retries})");
                sleep($delay);
                return $this->call_gemini_api($api_key, $prompt, $model, $retry_count + 1);
            }
            
            throw new Exception("Gemini API service unavailable after " . intval($max_retries) . " attempts. Error code: " . intval($response_code) . " - " . esc_html(substr($error_body, 0, 200)));
        }
        
        if ($response_code !== 200) {
            $error_body = wp_remote_retrieve_body($response);
            aca_debug_log('Gemini API HTTP Error: Code ' . intval($response_code) . ', Body: ' . esc_html(substr($error_body, 0, 500)));
            throw new Exception('Gemini API returned error code: ' . intval($response_code) . ' - ' . esc_html(substr($error_body, 0, 200)));
        }
        
        $response_body = wp_remote_retrieve_body($response);
        if (empty($response_body)) {
            aca_debug_log('Gemini API Empty Response Body');
            throw new Exception('Empty response from Gemini API');
        }
        
        // Only log response in debug mode to prevent sensitive data exposure
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA Gemini API Response (DEBUG): ' . substr($response_body, 0, 200));
        }
        
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            aca_debug_log('Gemini API JSON Error: ' . esc_html(json_last_error_msg()) . ', Response: ' . esc_html(substr($response_body, 0, 300)));
            throw new Exception('Invalid JSON response from Gemini API: ' . esc_html(json_last_error_msg()));
        }
        
        if (empty($data['candidates'][0]['content']['parts'][0]['text'])) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA Gemini API No Content: ' . print_r($data, true));
            }
            throw new Exception('No content returned from Gemini API. Response structure: ' . json_encode(array_keys($data)));
        }
        
        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
    
    /**
     * Clean AI JSON response to fix common formatting issues
     */
    private function clean_ai_json_response($response) {
        // Remove any text before the first {
        $start = strpos($response, '{');
        if ($start !== false) {
            $response = substr($response, $start);
        }
        
        // Remove any text after the last }
        $end = strrpos($response, '}');
        if ($end !== false) {
            $response = substr($response, 0, $end + 1);
        }
        
        // Remove markdown code blocks if present
        $response = preg_replace('/^```json\s*/m', '', $response);
        $response = preg_replace('/\s*```$/m', '', $response);
        
        // Fix common JSON issues
        $response = preg_replace('/,\s*}/', '}', $response); // Remove trailing commas in objects
        $response = preg_replace('/,\s*]/', ']', $response); // Remove trailing commas in arrays
        
        // Fix unescaped newlines and tabs in string values
        $response = preg_replace_callback('/"([^"\\\\]*(\\\\.[^"\\\\]*)*)"/', function($matches) {
            $string = $matches[1];
            $string = str_replace(["\n", "\r", "\t"], ['\\n', '\\r', '\\t'], $string);
            return '"' . $string . '"';
        }, $response);
        
        return trim($response);
    }
    
    /**
     * Convert Markdown content to HTML
     */
    private function markdown_to_html($content) {
        // Convert headings
        $content = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $content);
        $content = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $content);
        
        // Convert bold text
        $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
        
        // Convert italic text
        $content = preg_replace('/\*(.+?)\*/', '<em>$1</em>', $content);
        
        // Convert links
        $content = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $content);
        
        // Convert unordered lists
        $content = preg_replace_callback('/(?:^|\n)(\* .+(?:\n\* .+)*)/m', function($matches) {
            $list_items = explode("\n", trim($matches[1]));
            $html_items = '';
            foreach ($list_items as $item) {
                $item = preg_replace('/^\* /', '', $item);
                $html_items .= '<li>' . trim($item) . '</li>';
            }
            return '<ul>' . $html_items . '</ul>';
        }, $content);
        
        // Convert paragraphs (split by double newlines)
        $paragraphs = preg_split('/\n\s*\n/', trim($content));
        $html_content = '';
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                // Skip if already wrapped in HTML tags
                if (!preg_match('/^<(h[1-6]|ul|ol|li|div|p)/', $paragraph)) {
                    $paragraph = '<p>' . $paragraph . '</p>';
                }
                $html_content .= $paragraph . "\n\n";
            }
        }
        
        return trim($html_content);
    }
    
    /**
     * Debug automation status
     */
    public function debug_automation($request) {
        $settings = get_option('aca_settings', array());
        
        $debug_info = array(
            'current_mode' => isset($settings['mode']) ? $settings['mode'] : 'manual',
            'auto_publish' => isset($settings['autoPublish']) ? $settings['autoPublish'] : false,
            'gemini_api_key_set' => !empty($settings['geminiApiKey']),
            'style_guide_exists' => !empty(get_option('aca_style_guide')),
            'cron_schedules' => array(
                'thirty_minute' => wp_next_scheduled('aca_thirty_minute_event'),
                'fifteen_minute' => wp_next_scheduled('aca_fifteen_minute_event')
            ),
            'last_cron_run' => get_option('aca_last_cron_run', 'Never'),
            'wordpress_cron_enabled' => !defined('DISABLE_WP_CRON') || !DISABLE_WP_CRON,
            'server_time' => current_time('mysql'),
            'gmt_time' => current_time('mysql', true)
        );
        
        return rest_ensure_response($debug_info);
    }
    
    /**
     * Debug trigger semi-automatic cron
     */
    public function debug_trigger_semi_auto($request) {
        $cron = new ACA_Cron();
        
        try {
            ACA_Cron::fifteen_minute_task();
            $this->add_activity_log('debug_cron', 'Semi-automatic cron manually triggered', 'Settings');
            update_option('aca_last_cron_run', current_time('mysql') . ' (Semi-Auto Manual)');
            
            return rest_ensure_response(array(
                'success' => true,
                'message' => 'Semi-automatic cron task executed successfully',
                'timestamp' => current_time('mysql')
            ));
        } catch (Exception $e) {
            return new WP_Error('cron_error', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Debug trigger full-automatic cron
     */
    public function debug_trigger_full_auto($request) {
        try {
            ACA_Cron::thirty_minute_task();
            $this->add_activity_log('debug_cron', 'Full-automatic cron manually triggered', 'Settings');
            update_option('aca_last_cron_run', current_time('mysql') . ' (Full-Auto Manual)');
            
            return rest_ensure_response(array(
                'success' => true,
                'message' => 'Full-automatic cron task executed successfully',
                'timestamp' => current_time('mysql')
            ));
        } catch (Exception $e) {
            return new WP_Error('cron_error', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Get Google Search Console authentication status
     */
    public function get_gsc_auth_status($request) {
        try {
            if (!file_exists(ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php')) {
                return rest_ensure_response(array(
                    'connected' => false, 
                    'error' => 'Google Search Console class file not found'
                ));
            }
            
            require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
            
            if (!class_exists('ACA_Google_Search_Console')) {
                return rest_ensure_response(array(
                    'connected' => false, 
                    'error' => 'Google Search Console class not loaded'
                ));
            }
            
            $gsc = new ACA_Google_Search_Console();
            $status = $gsc->get_auth_status();
            
            return rest_ensure_response($status);
        } catch (Exception $e) {
            aca_debug_log('GSC Auth Status Error: ' . $e->getMessage());
            return rest_ensure_response(array(
                'connected' => false, 
                'error' => 'Failed to check GSC auth status: ' . $e->getMessage()
            ));
        } catch (Error $e) {
            aca_debug_log('GSC Auth Status Fatal Error: ' . $e->getMessage());
            return rest_ensure_response(array(
                'connected' => false, 
                'error' => 'Fatal error checking GSC auth status'
            ));
        }
    }
    
    /**
     * Connect to Google Search Console
     */
    public function gsc_connect($request) {
        try {
            require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
            
            if (!class_exists('ACA_Google_Search_Console')) {
                return new WP_Error('gsc_error', 'Google Search Console class not available');
            }
            
            $gsc = new ACA_Google_Search_Console();
            
            // Check if this is an OAuth callback
            if (isset($_GET['code'])) {
                $code = sanitize_text_field(wp_unslash($_GET['code']));
                $result = $gsc->handle_oauth_callback($code);
                
                if (is_wp_error($result)) {
                    return $result;
                }
                
                $this->add_activity_log('gsc_connected', 'Connected to Google Search Console', 'Settings');
                
                return rest_ensure_response(array(
                    'success' => true,
                    'message' => 'Successfully connected to Google Search Console'
                ));
            } else {
                // Return authorization URL
                $auth_status = $gsc->get_auth_status();
                
                if (isset($auth_status['auth_url'])) {
                    return rest_ensure_response(array(
                        'auth_url' => $auth_status['auth_url']
                    ));
                } else {
                    return new WP_Error('auth_error', 'Unable to generate authorization URL', array('status' => 500));
                }
            }
        } catch (Exception $e) {
            aca_debug_log('GSC Connect Error: ' . $e->getMessage());
            return new WP_Error('gsc_error', 'Failed to connect to GSC: ' . $e->getMessage());
        } catch (Error $e) {
            aca_debug_log('GSC Connect Fatal Error: ' . $e->getMessage());
            return new WP_Error('gsc_error', 'Fatal error connecting to GSC');
        }
    }
    
    /**
     * Disconnect from Google Search Console
     */
    public function gsc_disconnect($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
        
        $gsc = new ACA_Google_Search_Console();
        $result = $gsc->disconnect();
        
        if ($result) {
            $this->add_activity_log('gsc_disconnected', 'Disconnected from Google Search Console', 'Settings');
            
            return rest_ensure_response(array(
                'success' => true,
                'message' => 'Successfully disconnected from Google Search Console'
            ));
        } else {
            return new WP_Error('disconnect_error', 'Failed to disconnect from Google Search Console', array('status' => 500));
        }
    }
    
    /**
     * Get Google Search Console sites
     */
    public function get_gsc_sites($request) {
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
        
        $gsc = new ACA_Google_Search_Console();
        $sites = $gsc->get_sites();
        
        if (is_wp_error($sites)) {
            return $sites;
        }
        
        return rest_ensure_response($sites);
    }
    
    /**
     * Get Google Search Console connection status
     */
    public function get_gsc_status($request) {
        try {
            // Check if GSC is configured
            $settings = get_option('aca_settings', array());
            $gsc_tokens = get_option('aca_gsc_tokens', array());
            
            $is_configured = !empty($settings['gscClientId']) && !empty($settings['gscClientSecret']);
            $is_connected = !empty($gsc_tokens['access_token']) && !empty($gsc_tokens['refresh_token']);
            
            // Get selected site if connected
            $selected_site = '';
            if ($is_connected) {
                $selected_site = isset($settings['gscSiteUrl']) ? $settings['gscSiteUrl'] : '';
            }
            
            return rest_ensure_response(array(
                'configured' => $is_configured,
                'connected' => $is_connected,
                'selected_site' => $selected_site,
                'last_sync' => get_option('aca_gsc_last_sync', null),
                'status' => $is_connected ? 'connected' : ($is_configured ? 'configured' : 'not_configured')
            ));
            
        } catch (Exception $e) {
            aca_debug_log('GSC Status Error: ' . $e->getMessage());
            return new WP_Error('gsc_status_error', 'Failed to get GSC status', array('status' => 500));
        }
    }
    
    /**
     * Get SEO plugins status endpoint
     */
    public function get_seo_plugins($request) {
        try {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA: get_seo_plugins called');
            }
            
            $detected_plugins = $this->detect_seo_plugin();
            
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('ACA: Detected SEO plugins: ' . print_r($detected_plugins, true));
            }
            
            return rest_ensure_response(array(
                'success' => true,
                'detected_plugins' => $detected_plugins,
                'count' => count($detected_plugins),
                'auto_detection_enabled' => true,
                'timestamp' => current_time('mysql')
            ));
        } catch (Exception $e) {
            aca_debug_log('Error in get_seo_plugins: ' . $e->getMessage());
            return new WP_Error('seo_detection_failed', 'Failed to detect SEO plugins: ' . $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Detect which SEO plugin is active and return plugin info
     */
    private function detect_seo_plugin() {
        $detected_plugins = array();
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: Starting SEO plugin detection...');
        }
        
        // Check for RankMath - Enhanced detection
        $rankmath_detected = false;
        if (is_plugin_active('seo-by-rank-math/rank-math.php') || 
            class_exists('RankMath') || 
            class_exists('\RankMath\Helper') ||
            defined('RANK_MATH_FILE')) {
            $rankmath_detected = true;
            $detected_plugins[] = array(
                'plugin' => 'rank_math',
                'name' => 'Rank Math',
                'version' => defined('RANK_MATH_VERSION') ? RANK_MATH_VERSION : 'unknown',
                'active' => true,
                'pro' => class_exists('\RankMath\Pro\Admin\Admin_Menu')
            );
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: RankMath detection result: ' . ($rankmath_detected ? 'found' : 'not found'));
        }
        
        // Check for Yoast SEO - Enhanced detection  
        $yoast_detected = false;
        if (is_plugin_active('wordpress-seo/wp-seo.php') || 
            class_exists('WPSEO_Options') ||
            class_exists('WPSEO_Frontend') ||
            defined('WPSEO_VERSION')) {
            $yoast_detected = true;
            $detected_plugins[] = array(
                'plugin' => 'yoast',
                'name' => 'Yoast SEO',
                'version' => defined('WPSEO_VERSION') ? WPSEO_VERSION : 'unknown',
                'active' => true,
                'premium' => defined('WPSEO_PREMIUM_PLUGIN_FILE')
            );
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: Yoast SEO detection result: ' . ($yoast_detected ? 'found' : 'not found'));
        }
        
        // Check for All in One SEO (AIOSEO) - Enhanced detection
        $aioseo_detected = false;
        if (is_plugin_active('all-in-one-seo-pack/all_in_one_seo_pack.php') ||
            is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php') ||
            class_exists('AIOSEO\Plugin\AIOSEO') ||
            class_exists('All_in_One_SEO_Pack') ||
            defined('AIOSEO_VERSION')) {
            $aioseo_detected = true;
            $detected_plugins[] = array(
                'plugin' => 'aioseo',
                'name' => 'All in One SEO',
                'version' => defined('AIOSEO_VERSION') ? AIOSEO_VERSION : (defined('AIOSEOP_VERSION') ? AIOSEOP_VERSION : 'unknown'),
                'active' => true,
                'pro' => is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php') || defined('AIOSEO_PRO')
            );
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: AIOSEO detection result: ' . ($aioseo_detected ? 'found' : 'not found'));
        }
        
        // Log all active plugins for debugging
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $active_plugins = get_option('active_plugins', array());
            error_log('ACA: Active plugins: ' . print_r($active_plugins, true));
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: Total detected SEO plugins: ' . count($detected_plugins));
        }
        
        return $detected_plugins;
    }
    
    /**
     * Send SEO data to detected SEO plugins with conflict prevention
     */
    private function send_seo_data_to_plugins($post_id, $meta_title, $meta_description, $focus_keywords) {
        // Get user's preferred SEO plugin from settings
        $settings = get_option('aca_settings', array());
        $preferred_plugin = isset($settings['seoPlugin']) ? $settings['seoPlugin'] : 'none';
        
        $detected_plugins = $this->detect_seo_plugin();
        $results = array();
        
        // Prevent meta data conflicts by only writing to user's selected plugin
        if ($preferred_plugin !== 'none') {
            // Check if preferred plugin is actually installed and active
            $preferred_plugin_active = false;
            foreach ($detected_plugins as $plugin_info) {
                if ($plugin_info['plugin'] === $preferred_plugin && $plugin_info['active']) {
                    $preferred_plugin_active = true;
                    break;
                }
            }
            
            if ($preferred_plugin_active) {
                // Only send to the preferred plugin to prevent conflicts
                switch ($preferred_plugin) {
                    case 'rank_math':
                        $result = $this->send_to_rankmath($post_id, $meta_title, $meta_description, $focus_keywords);
                        $results['rank_math'] = $result;
                        aca_debug_log("Meta data sent only to preferred plugin: RankMath");
                        break;
                        
                    case 'yoast':
                        $result = $this->send_to_yoast($post_id, $meta_title, $meta_description, $focus_keywords);
                        $results['yoast'] = $result;
                        aca_debug_log("Meta data sent only to preferred plugin: Yoast");
                        break;
                        
                    case 'aioseo':
                        $result = $this->send_to_aioseo($post_id, $meta_title, $meta_description, $focus_keywords);
                        $results['aioseo'] = $result;
                        aca_debug_log("Meta data sent only to preferred plugin: AIOSEO");
                        break;
                }
                
                // Log conflict prevention
                $this->log_meta_conflict_prevention($post_id, $preferred_plugin, $detected_plugins);
                
            } else {
                error_log("ACA: Preferred SEO plugin ($preferred_plugin) not active, falling back to auto-detection");
                $results = $this->send_to_auto_detected_plugins($post_id, $meta_title, $meta_description, $focus_keywords, $detected_plugins);
            }
        } else {
            // No preference set, use auto-detection but prevent conflicts
            $results = $this->send_to_auto_detected_plugins($post_id, $meta_title, $meta_description, $focus_keywords, $detected_plugins);
        }
        
        return $results;
    }
    
    /**
     * Send to auto-detected plugins with priority-based conflict prevention
     */
    private function send_to_auto_detected_plugins($post_id, $meta_title, $meta_description, $focus_keywords, $detected_plugins) {
        $results = array();
        
        if (empty($detected_plugins)) {
            error_log("ACA: No SEO plugins detected, skipping meta data writing");
            return $results;
        }
        
        // Priority order: RankMath > Yoast > AIOSEO (based on market usage and reliability)
        $priority_order = array('rank_math', 'yoast', 'aioseo');
        $selected_plugin = null;
        
        // Find the highest priority active plugin
        foreach ($priority_order as $plugin_name) {
            foreach ($detected_plugins as $plugin_info) {
                if ($plugin_info['plugin'] === $plugin_name && $plugin_info['active']) {
                    $selected_plugin = $plugin_info;
                    break 2; // Break both loops
                }
            }
        }
        
        // If no priority plugin found, use the first active one
        if (!$selected_plugin) {
            foreach ($detected_plugins as $plugin_info) {
                if ($plugin_info['active']) {
                    $selected_plugin = $plugin_info;
                    break;
                }
            }
        }
        
        if ($selected_plugin) {
            switch ($selected_plugin['plugin']) {
                case 'rank_math':
                    $result = $this->send_to_rankmath($post_id, $meta_title, $meta_description, $focus_keywords);
                    $results['rank_math'] = $result;
                    break;
                    
                case 'yoast':
                    $result = $this->send_to_yoast($post_id, $meta_title, $meta_description, $focus_keywords);
                    $results['yoast'] = $result;
                    break;
                    
                case 'aioseo':
                    $result = $this->send_to_aioseo($post_id, $meta_title, $meta_description, $focus_keywords);
                    $results['aioseo'] = $result;
                    break;
            }
            
            error_log("ACA: Meta data sent to auto-selected plugin: " . $selected_plugin['plugin']);
            $this->log_meta_conflict_prevention($post_id, $selected_plugin['plugin'], $detected_plugins);
        }
        
        return $results;
    }
    
    /**
     * Log meta data conflict prevention for debugging and transparency
     */
    private function log_meta_conflict_prevention($post_id, $selected_plugin, $all_detected_plugins) {
        $skipped_plugins = array();
        
        foreach ($all_detected_plugins as $plugin_info) {
            if ($plugin_info['plugin'] !== $selected_plugin && $plugin_info['active']) {
                $skipped_plugins[] = $plugin_info['plugin'];
            }
        }
        
        if (!empty($skipped_plugins)) {
            $skipped_list = implode(', ', $skipped_plugins);
            error_log("ACA: Meta conflict prevention - Post ID: $post_id, Used: $selected_plugin, Skipped: $skipped_list");
            
            // Store conflict prevention log in post meta for transparency
            $conflict_log = array(
                'timestamp' => current_time('mysql'),
                'selected_plugin' => $selected_plugin,
                'skipped_plugins' => $skipped_plugins,
                'reason' => 'conflict_prevention'
            );
            
            update_post_meta($post_id, '_aca_seo_conflict_log', $conflict_log);
        }
    }
    
    /**
     * Send SEO data to RankMath
     */
    private function send_to_rankmath($post_id, $meta_title, $meta_description, $focus_keywords) {
        try {
            // RankMath stores data in post meta with rank_math_ prefix
            if (!empty($meta_title)) {
                update_post_meta($post_id, 'rank_math_title', $this->sanitize_unicode_text($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, 'rank_math_description', $this->sanitize_unicode_textarea($meta_description));
            }
            
            if (!empty($focus_keywords) && is_array($focus_keywords)) {
                // RankMath stores focus keyword as a single string (primary keyword)
                $primary_keyword = sanitize_text_field($focus_keywords[0]);
                update_post_meta($post_id, 'rank_math_focus_keyword', $primary_keyword);
                
                // For RankMath Pro, additional keywords can be stored
                if (count($focus_keywords) > 1 && class_exists('\RankMath\Pro\Admin\Admin_Menu')) {
                    // Store additional keywords as JSON array for Pro version
                    $additional_keywords = array();
                    for ($i = 1; $i < count($focus_keywords); $i++) {
                        $additional_keywords[] = sanitize_text_field($focus_keywords[$i]);
                    }
                    update_post_meta($post_id, 'rank_math_keywords', implode(',', $additional_keywords));
                }
                
                // Store all keywords in a custom meta for reference
                update_post_meta($post_id, 'aca_focus_keywords', $focus_keywords);
            }
            
            // Set additional RankMath meta for better integration
            // Set good content score for AI-generated content (0-100 scale)
            update_post_meta($post_id, 'rank_math_seo_score', 85);
            
            // Set readability score (simulate good readability for AI content)
            update_post_meta($post_id, 'rank_math_contentai_score', 75);
            
            // Set robots meta to index and follow
            update_post_meta($post_id, 'rank_math_robots', array('index', 'follow'));
            
            // Set canonical URL to self to avoid duplicate content
            $post_url = get_permalink($post_id);
            if ($post_url) {
                update_post_meta($post_id, 'rank_math_canonical_url', $post_url);
            }
            
            // Set primary category if post has categories
            $post_type = get_post_type($post_id);
            $categories = array(); // Initialize to avoid undefined variable
            if ($post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    update_post_meta($post_id, 'rank_math_primary_category', $categories[0]->term_id);
                }
            }
            
            // Social Media Integration - OpenGraph
            if (!empty($meta_title)) {
                update_post_meta($post_id, 'rank_math_facebook_title', $this->sanitize_unicode_text($meta_title));
                update_post_meta($post_id, 'rank_math_twitter_title', $this->sanitize_unicode_text($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, 'rank_math_facebook_description', $this->sanitize_unicode_textarea($meta_description));
                update_post_meta($post_id, 'rank_math_twitter_description', $this->sanitize_unicode_textarea($meta_description));
            }
            
            // Set featured image for social media if available
            $featured_image_id = get_post_thumbnail_id($post_id);
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                if ($featured_image_url) {
                    update_post_meta($post_id, 'rank_math_facebook_image', $featured_image_url);
                    update_post_meta($post_id, 'rank_math_facebook_image_id', $featured_image_id);
                    update_post_meta($post_id, 'rank_math_twitter_image', $featured_image_url);
                    update_post_meta($post_id, 'rank_math_twitter_image_id', $featured_image_id);
                }
            }
            
            // Set schema type based on post type
            if ($post_type === 'post') {
                update_post_meta($post_id, 'rank_math_rich_snippet', 'article');
                update_post_meta($post_id, 'rank_math_snippet_article_type', 'BlogPosting');
            } elseif ($post_type === 'page') {
                update_post_meta($post_id, 'rank_math_rich_snippet', 'webpage');
            }
            
            // Advanced RankMath Pro features if available
            if (class_exists('\RankMath\Pro\Admin\Admin_Menu')) {
                // Set advanced schema markup for Pro
                update_post_meta($post_id, 'rank_math_enable_schema', 'on');
                
                // Set pillar content if multiple keywords (indicates important content)
                if (!empty($focus_keywords) && is_array($focus_keywords) && count($focus_keywords) > 2) {
                    update_post_meta($post_id, 'rank_math_pillar_content', 'on');
                }
                
                // Enable Content AI features if available
                if (class_exists('\RankMath\ContentAI\ContentAI')) {
                    update_post_meta($post_id, 'rank_math_contentai_enabled', 'on');
                }
            }
            
            // Set breadcrumb title
            $breadcrumb_title = get_the_title($post_id);
            if (!empty($breadcrumb_title)) {
                update_post_meta($post_id, 'rank_math_breadcrumb_title', sanitize_text_field($breadcrumb_title));
            }
            
            // Set advanced meta for better SEO
            update_post_meta($post_id, 'rank_math_advanced_robots', array());
            
            // Set internal linking suggestions
            if (!empty($focus_keywords) && is_array($focus_keywords)) {
                $internal_links = array();
                foreach ($focus_keywords as $keyword) {
                    $internal_links[] = array(
                        'keyword' => $keyword,
                        'url' => $post_url,
                        'title' => get_the_title($post_id)
                    );
                }
                update_post_meta($post_id, 'rank_math_internal_links', $internal_links);
            }
            
            aca_debug_log('Successfully sent SEO data to RankMath for post ' . $post_id);
            
            return array(
                'success' => true,
                'message' => 'SEO data successfully sent to RankMath',
                'plugin' => 'RankMath',
                'data_sent' => array(
                    'title' => !empty($meta_title),
                    'description' => !empty($meta_description),
                    'focus_keyword' => !empty($focus_keywords),
                    'seo_score' => 85,
                    'content_score' => 75,
                    'social_media' => !empty($meta_title) || !empty($meta_description),
                    'primary_category' => ($post_type === 'post' && !empty($categories)),
                    'schema' => ($post_type === 'post' || $post_type === 'page') ? 'enabled' : 'none',
                    'pro_features' => class_exists('\RankMath\Pro\Admin\Admin_Menu'),
                    'pillar_content' => (!empty($focus_keywords) && count($focus_keywords) > 2),
                    'internal_links' => !empty($focus_keywords)
                )
            );
            
        } catch (Exception $e) {
            aca_debug_log('Error sending to RankMath: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Error sending to RankMath: ' . $e->getMessage(),
                'plugin' => 'RankMath'
            );
        }
    }
    
    /**
     * Send SEO data to Yoast SEO
     */
    private function send_to_yoast($post_id, $meta_title, $meta_description, $focus_keywords) {
        try {
                    // Yoast stores data in post meta with _yoast_wpseo_ prefix
        if (!empty($meta_title)) {
            update_post_meta($post_id, '_yoast_wpseo_title', $this->sanitize_unicode_text($meta_title));
        }
        
        if (!empty($meta_description)) {
            update_post_meta($post_id, '_yoast_wpseo_metadesc', $this->sanitize_unicode_textarea($meta_description));
        }
            
            if (!empty($focus_keywords) && is_array($focus_keywords)) {
                // Yoast stores the primary focus keyword
                $primary_keyword = sanitize_text_field($focus_keywords[0]);
                update_post_meta($post_id, '_yoast_wpseo_focuskw', $primary_keyword);
                
                // For Yoast Premium, additional keywords can be stored
                if (count($focus_keywords) > 1 && defined('WPSEO_PREMIUM_PLUGIN_FILE')) {
                    $additional_keywords = array();
                    for ($i = 1; $i < count($focus_keywords); $i++) {
                        $additional_keywords[] = array(
                            'keyword' => sanitize_text_field($focus_keywords[$i]),
                            'score' => 'good' // Simulate good score for AI content
                        );
                    }
                    update_post_meta($post_id, '_yoast_wpseo_focuskeywords', json_encode($additional_keywords));
                }
                
                // Store all keywords in a custom meta for reference
                update_post_meta($post_id, 'aca_focus_keywords', $focus_keywords);
            }
            
            // Set additional Yoast meta for better integration
            // Set good content score for AI-generated content (0-100 scale)
            update_post_meta($post_id, '_yoast_wpseo_content_score', 75);
            
            // Estimate reading time based on content length
            $post = get_post($post_id);
            if ($post && !empty($post->post_content)) {
                $word_count = str_word_count(wp_strip_all_tags($post->post_content));
                $reading_time = max(1, ceil($word_count / 200)); // 200 words per minute
                update_post_meta($post_id, '_yoast_wpseo_estimated-reading-time-minutes', $reading_time);
            }
            
            // Set readability score (simulate good readability for AI content)
            update_post_meta($post_id, '_yoast_wpseo_readability-score', 60); // Good readability
            
            // Set robots meta to index and follow
            update_post_meta($post_id, '_yoast_wpseo_meta-robots-noindex', '0');
            update_post_meta($post_id, '_yoast_wpseo_meta-robots-nofollow', '0');
            update_post_meta($post_id, '_yoast_wpseo_meta-robots-noarchive', '0');
            update_post_meta($post_id, '_yoast_wpseo_meta-robots-nosnippet', '0');
            
            // Set cornerstone content if multiple keywords (indicates important content)
            if (!empty($focus_keywords) && is_array($focus_keywords) && count($focus_keywords) > 2) {
                update_post_meta($post_id, '_yoast_wpseo_is_cornerstone', '1');
            }
            
            // Set primary category if post has categories
            $post_type = get_post_type($post_id);
            $categories = array(); // Initialize to avoid undefined variable
            if ($post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    update_post_meta($post_id, '_yoast_wpseo_primary_category', $categories[0]->term_id);
                }
            }
            
            // Social Media Integration - OpenGraph
            if (!empty($meta_title)) {
                update_post_meta($post_id, '_yoast_wpseo_opengraph-title', $this->sanitize_unicode_text($meta_title));
                update_post_meta($post_id, '_yoast_wpseo_twitter-title', $this->sanitize_unicode_text($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_yoast_wpseo_opengraph-description', $this->sanitize_unicode_textarea($meta_description));
                update_post_meta($post_id, '_yoast_wpseo_twitter-description', $this->sanitize_unicode_textarea($meta_description));
            }
            
            // Set featured image for social media if available
            $featured_image_id = get_post_thumbnail_id($post_id);
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                if ($featured_image_url) {
                    update_post_meta($post_id, '_yoast_wpseo_opengraph-image', $featured_image_url);
                    update_post_meta($post_id, '_yoast_wpseo_opengraph-image-id', $featured_image_id);
                    update_post_meta($post_id, '_yoast_wpseo_twitter-image', $featured_image_url);
                    update_post_meta($post_id, '_yoast_wpseo_twitter-image-id', $featured_image_id);
                }
            }
            
            // Enhanced Premium features
            if (defined('WPSEO_PREMIUM_PLUGIN_FILE')) {
                // Set SEO score equivalent (linkdex)
                update_post_meta($post_id, '_yoast_wpseo_linkdex', 75);
                
                // Set word form recognition for Premium
                if (!empty($focus_keywords) && is_array($focus_keywords)) {
                    update_post_meta($post_id, '_yoast_wpseo_keywordsynonyms', implode(',', array_slice($focus_keywords, 1)));
                }
                
                // Enable redirect notifications for Premium
                update_post_meta($post_id, '_yoast_wpseo_redirect', '');
            }
            
            // Set breadcrumb title if different from post title
            $breadcrumb_title = get_the_title($post_id);
            if (!empty($breadcrumb_title)) {
                update_post_meta($post_id, '_yoast_wpseo_bctitle', sanitize_text_field($breadcrumb_title));
            }
            
            // Advanced indexing controls
            update_post_meta($post_id, '_yoast_wpseo_meta-robots-adv', 'none');
            
            // Set canonical URL to self to avoid duplicate content
            $post_url = get_permalink($post_id);
            if ($post_url) {
                update_post_meta($post_id, '_yoast_wpseo_canonical', $post_url);
            }
            
            aca_debug_log('Successfully sent SEO data to Yoast SEO for post ' . $post_id);
            
            return array(
                'success' => true,
                'message' => 'SEO data successfully sent to Yoast SEO',
                'plugin' => 'Yoast SEO',
                'data_sent' => array(
                    'title' => !empty($meta_title),
                    'description' => !empty($meta_description),
                    'focus_keyword' => !empty($focus_keywords),
                    'content_score' => 75,
                    'readability_score' => 60,
                    'reading_time' => isset($reading_time) ? $reading_time : 'estimated',
                    'cornerstone' => (!empty($focus_keywords) && count($focus_keywords) > 2),
                    'social_media' => !empty($meta_title) || !empty($meta_description),
                    'primary_category' => ($post_type === 'post' && !empty($categories)),
                    'premium_features' => defined('WPSEO_PREMIUM_PLUGIN_FILE')
                )
            );
            
        } catch (Exception $e) {
            aca_debug_log('Error sending to Yoast SEO: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Error sending to Yoast SEO: ' . $e->getMessage(),
                'plugin' => 'Yoast SEO'
            );
        }
    }
    
    /**
     * Send SEO data to All in One SEO (AIOSEO)
     */
    private function send_to_aioseo($post_id, $meta_title, $meta_description, $focus_keywords) {
        try {
            // AIOSEO v4+ uses a different data structure - JSON-based post meta
            $aioseo_data = array();
            
            // Get existing AIOSEO data if any
            $existing_data = get_post_meta($post_id, '_aioseo_posts', true);
            if (is_string($existing_data)) {
                $existing_data = json_decode($existing_data, true);
            }
            if (!is_array($existing_data)) {
                $existing_data = array();
            }
            
            // Update title
            if (!empty($meta_title)) {
                $existing_data['title'] = $this->sanitize_unicode_text($meta_title);
                // Also set legacy field for backward compatibility
                update_post_meta($post_id, '_aioseo_title', $this->sanitize_unicode_text($meta_title));
            }
            
            // Update description
            if (!empty($meta_description)) {
                $existing_data['description'] = $this->sanitize_unicode_textarea($meta_description);
                // Also set legacy field for backward compatibility
                update_post_meta($post_id, '_aioseo_description', $this->sanitize_unicode_textarea($meta_description));
            }
            
            // Update keywords
            if (!empty($focus_keywords) && is_array($focus_keywords)) {
                // AIOSEO v4+ stores keywords as comma-separated string in the main data
                $keywords_string = implode(', ', array_map('sanitize_text_field', $focus_keywords));
                $existing_data['keywords'] = $keywords_string;
                
                // Set focus keyphrase (primary keyword)
                $existing_data['keyphrases'] = array(
                    'focus' => array(
                        'keyphrase' => sanitize_text_field($focus_keywords[0]),
                        'score' => 80, // Good score for AI content
                        'analysis' => array()
                    )
                );
                
                // Legacy fields for compatibility
                update_post_meta($post_id, '_aioseo_keywords', $keywords_string);
                update_post_meta($post_id, '_aioseo_focus_keyphrase', sanitize_text_field($focus_keywords[0]));
                
                // Store all keywords in a custom meta for reference
                update_post_meta($post_id, 'aca_focus_keywords', $focus_keywords);
            }
            
            // Set robots meta
            $existing_data['robots'] = array(
                'default' => true,
                'noindex' => false,
                'nofollow' => false,
                'noarchive' => false,
                'nosnippet' => false,
                'noimageindex' => false
            );
            
            // Social Media Integration - OpenGraph
            if (!empty($meta_title) || !empty($meta_description)) {
                $existing_data['og'] = array();
                if (!empty($meta_title)) {
                    $existing_data['og']['title'] = $this->sanitize_unicode_text($meta_title);
                }
                if (!empty($meta_description)) {
                    $existing_data['og']['description'] = $this->sanitize_unicode_textarea($meta_description);
                }
                
                // Set featured image for social media if available
                $featured_image_id = get_post_thumbnail_id($post_id);
                if ($featured_image_id) {
                    $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                    if ($featured_image_url) {
                        $existing_data['og']['image'] = $featured_image_url;
                        $existing_data['og']['imageType'] = 'default';
                    }
                }
            }
            
            // Twitter Card data
            if (!empty($meta_title) || !empty($meta_description)) {
                $existing_data['twitter'] = array();
                if (!empty($meta_title)) {
                    $existing_data['twitter']['title'] = $this->sanitize_unicode_text($meta_title);
                }
                if (!empty($meta_description)) {
                    $existing_data['twitter']['description'] = $this->sanitize_unicode_textarea($meta_description);
                }
                
                // Set featured image for Twitter if available
                $featured_image_id = get_post_thumbnail_id($post_id);
                if ($featured_image_id) {
                    $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                    if ($featured_image_url) {
                        $existing_data['twitter']['image'] = $featured_image_url;
                        $existing_data['twitter']['imageType'] = 'default';
                    }
                }
            }
            
            // Set primary category if post has categories
            $post_type = get_post_type($post_id);
            $categories = array(); // Initialize to avoid undefined variable
            if ($post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    $existing_data['primary_term'] = array(
                        'category' => $categories[0]->term_id
                    );
                }
            }
            
            // Set canonical URL to self
            $post_url = get_permalink($post_id);
            if ($post_url) {
                $existing_data['canonical_url'] = $post_url;
            }
            
            // Set schema type based on post type
            if ($post_type === 'post') {
                $existing_data['schema'] = array(
                    'default' => 'Article',
                    'article' => array(
                        'articleType' => 'BlogPosting'
                    )
                );
            } elseif ($post_type === 'page') {
                $existing_data['schema'] = array(
                    'default' => 'WebPage'
                );
            }
            
            // AIOSEO Pro features if available
            if (is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php') || defined('AIOSEO_PRO')) {
                // Set SEO score for Pro version
                $existing_data['seo_score'] = 85;
                
                // Enable advanced features
                $existing_data['priority'] = 'default';
                $existing_data['frequency'] = 'default';
            }
            
            // Save the updated AIOSEO data
            update_post_meta($post_id, '_aioseo_posts', json_encode($existing_data));
            
            // Also maintain legacy meta fields for older versions
            if (!empty($meta_title)) {
                update_post_meta($post_id, '_aioseo_title', $this->sanitize_unicode_text($meta_title));
            }
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_aioseo_description', $this->sanitize_unicode_textarea($meta_description));
            }
            
            aca_debug_log('Successfully sent SEO data to All in One SEO for post ' . $post_id);
            
            return array(
                'success' => true,
                'message' => 'SEO data successfully sent to All in One SEO',
                'plugin' => 'All in One SEO',
                'data_sent' => array(
                    'title' => !empty($meta_title),
                    'description' => !empty($meta_description),
                    'focus_keyword' => !empty($focus_keywords),
                    'robots' => 'index,follow',
                    'social_media' => !empty($meta_title) || !empty($meta_description),
                    'primary_category' => ($post_type === 'post' && !empty($categories)),
                    'schema' => ($post_type === 'post' || $post_type === 'page') ? 'enabled' : 'none',
                    'pro_features' => (is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php') || defined('AIOSEO_PRO')),
                    'data_structure' => 'v4_compatible'
                )
            );
            
        } catch (Exception $e) {
            aca_debug_log('Error sending to All in One SEO: ' . $e->getMessage());
            return array(
                'success' => false,
                'message' => 'Error sending to All in One SEO: ' . $e->getMessage(),
                'plugin' => 'All in One SEO'
            );
        }
    }
    
    /**
     * Verify license key with Gumroad API
     */
    public function verify_license_key($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $params = $request->get_json_params();
        $license_key = sanitize_text_field($params['license_key'] ?? '');
        
        if (empty($license_key)) {
            return new WP_Error(
                'missing_license_key', 
                'License key is required', 
                array('status' => 400)
            );
        }
        
        // Gumroad product ID - get this from your product's content page by expanding the license key module
        // ⚠️ Important: For products created on or after Jan 9, 2023, use product_id instead of product_permalink
        // You can find this ID by going to your product's content page and expanding the license key module
        $product_id = 'Q2Mhx923crYSQP19FBbYsg==';
        
        // Log the product ID being used for debugging
        aca_debug_log('Using product_id: ' . $product_id . ' for license verification');
        
        try {
            $verification_result = $this->call_gumroad_api($product_id, $license_key);
            
            if ($verification_result['success']) {
                // Check if license is already bound to another site
                $stored_site_hash = get_option('aca_license_site_hash', '');
                $current_site_hash = hash('sha256', get_site_url() . NONCE_SALT);
                
                if (!empty($stored_site_hash) && $stored_site_hash !== $current_site_hash) {
                    aca_debug_log('License already bound to another site');
                    return rest_ensure_response(array(
                        'success' => false,
                        'message' => 'This license is already active on another website. Each license can only be used on one site at a time. Please deactivate it from the other site first.'
                    ));
                }
                
                // Store license status and bind to current site with enhanced security
                update_option('aca_license_status', 'active');
                update_option('aca_license_data', $verification_result);
                update_option('aca_license_site_hash', $current_site_hash);
                
                // Additional security fields for multi-point validation
                update_option('aca_license_verified', wp_hash('verified'));
                update_option('aca_license_timestamp', time());
                update_option('aca_license_key', $license_key);
                
                // Add success message to response
                $verification_result['message'] = 'License verified successfully! Pro features are now active.';
            } else {
                // Remove license status
                delete_option('aca_license_status');
                delete_option('aca_license_data');
                delete_option('aca_license_site_hash');
                delete_option('aca_license_key');
                delete_option('aca_license_verified');
                delete_option('aca_license_timestamp');
                
                // Add failure message to response
                $verification_result['message'] = 'License verification failed. Please check your license key and try again.';
            }
            
            return rest_ensure_response($verification_result);
            
        } catch (Exception $e) {
            aca_debug_log('License verification error: ' . $e->getMessage());
            
            // Clean up any partial data
            delete_option('aca_license_status');
            delete_option('aca_license_data');
            delete_option('aca_license_site_hash');
            
            // Return JSON error response instead of WP_Error to prevent WordPress critical error
            return rest_ensure_response(array(
                'success' => false,
                'message' => 'License verification failed: ' . $e->getMessage(),
                'error_code' => 'license_verification_failed'
            ));
        }
    }
    
    /**
     * Get license status
     */
    public function get_license_status($request) {
        $license_status = get_option('aca_license_status', 'inactive');
        $license_data = get_option('aca_license_data', array());
        
        return rest_ensure_response(array(
            'status' => $license_status,
            'is_active' => $license_status === 'active',
            'data' => $license_data,
            'verified_at' => isset($license_data['verified_at']) ? $license_data['verified_at'] : null
        ));
    }
    
    /**
     * Deactivate license
     */
    public function deactivate_license($request) {
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        try {
            // Remove license status and data
            delete_option('aca_license_status');
            delete_option('aca_license_data');
            delete_option('aca_license_site_hash');
            delete_option('aca_license_key');
            delete_option('aca_license_verified');
            delete_option('aca_license_timestamp');
            
            aca_debug_log('License deactivated successfully');
            
            return rest_ensure_response(array(
                'success' => true,
                'message' => 'License deactivated successfully. You can now use this license on another site.'
            ));
            
        } catch (Exception $e) {
            aca_debug_log('License deactivation error: ' . $e->getMessage());
            
            // Return JSON error response instead of WP_Error
            return rest_ensure_response(array(
                'success' => false,
                'message' => 'License deactivation failed: ' . $e->getMessage(),
                'error_code' => 'license_deactivation_failed'
            ));
        }
    }
    
    /**
     * Call Gumroad License Verification API
     */
    private function call_gumroad_api($product_id, $license_key) {
        $url = 'https://api.gumroad.com/v2/licenses/verify';
        
        // Log API call details
        aca_debug_log('Calling Gumroad API - URL: ' . $url . ', Product ID: ' . $product_id);
        
        // Generate site-specific hash for license binding
        $site_url = get_site_url();
        $site_hash = hash('sha256', $site_url . NONCE_SALT);
        
        // Use product_id for products created on or after Jan 9, 2023
        $body_data = array(
            'product_id' => $product_id,           // Required for products after Jan 9, 2023
            'license_key' => $license_key,
            'increment_uses_count' => 'true'       // Track usage for analytics
        );
        
        // Store site info locally instead of sending to Gumroad
        // (Gumroad API doesn't support custom metadata in license verification)
        aca_debug_log('Site binding info - URL: ' . $site_url . ', Hash: ' . substr($site_hash, 0, 16) . '...');
        
        // Log request body (without showing full license key for security)
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $log_body = $body_data;
            $log_body['license_key'] = substr($license_key, 0, 8) . '...';
            error_log('ACA: Request body: ' . print_r($log_body, true));
        }
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'body' => $body_data,
            'timeout' => 30,
            'blocking' => true,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            aca_debug_log('Gumroad API request failed: ' . $response->get_error_message());
            return array(
                'success' => false,
                'message' => 'Gumroad API request failed: ' . $response->get_error_message(),
                'purchase_data' => null,
                'verified_at' => current_time('mysql'),
                'error_code' => 'network_error'
            );
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            aca_debug_log('Gumroad API error - Code: ' . $response_code . ', Body: ' . $body);
            return array(
                'success' => false,
                'message' => 'Gumroad API returned error code: ' . $response_code,
                'purchase_data' => null,
                'verified_at' => current_time('mysql'),
                'error_code' => 'api_error'
            );
        }
        
        $data = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            aca_debug_log('JSON decode error - Body: ' . $body);
            return array(
                'success' => false,
                'message' => 'Invalid JSON response from Gumroad API',
                'purchase_data' => null,
                'verified_at' => current_time('mysql'),
                'error_code' => 'json_error'
            );
        }
        
        // Log the full response for debugging
        // Only log detailed response in debug mode to prevent sensitive data exposure
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: Gumroad API response (DEBUG): ' . print_r($data, true));
        }
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('ACA: Response analysis - success field: ' . (isset($data['success']) ? ($data['success'] ? 'true' : 'false') : 'missing'));
            error_log('ACA: Response analysis - success type: ' . (isset($data['success']) ? gettype($data['success']) : 'N/A'));
        }
        
        // Validate license according to Gumroad documentation
        // Check if response has success field and purchase data
        if (!isset($data['success'])) {
            aca_debug_log('Missing success field in Gumroad response');
            return array(
                'success' => false,
                'message' => 'Invalid Gumroad API response format',
                'purchase_data' => null,
                'verified_at' => current_time('mysql'),
                'error_code' => 'invalid_response'
            );
        }
        
        // Handle different success field formats from Gumroad
        $is_valid = false;
        if (isset($data['success'])) {
            if (is_bool($data['success'])) {
                $is_valid = $data['success'] === true;
            } elseif (is_string($data['success'])) {
                $is_valid = strtolower($data['success']) === 'true' || $data['success'] === '1';
            } elseif (is_numeric($data['success'])) {
                $is_valid = (int)$data['success'] === 1;
            }
        }
        
        aca_debug_log('License validation result: ' . ($is_valid ? 'VALID' : 'INVALID'));
        
        // Additional validation if purchase data exists
        if (isset($data['purchase'])) {
            aca_debug_log('Purchase data found, validating...');
            
            // If we have purchase data, assume license is valid unless proven otherwise
            $purchase_valid = true;
            
            // Check for refunds
            if (isset($data['purchase']['refunded'])) {
                $refunded = $data['purchase']['refunded'];
                if (is_bool($refunded) && $refunded === true) {
                    $purchase_valid = false;
                    aca_debug_log('License invalid - refunded');
                } elseif (is_string($refunded) && strtolower($refunded) === 'true') {
                    $purchase_valid = false;
                    aca_debug_log('License invalid - refunded (string)');
                }
            }
            
            // Check for chargebacks
            if (isset($data['purchase']['chargebacked'])) {
                $chargebacked = $data['purchase']['chargebacked'];
                if (is_bool($chargebacked) && $chargebacked === true) {
                    $purchase_valid = false;
                    aca_debug_log('License invalid - chargebacked');
                } elseif (is_string($chargebacked) && strtolower($chargebacked) === 'true') {
                    $purchase_valid = false;
                    aca_debug_log('License invalid - chargebacked (string)');
                }
            }
            
            // If we have purchase data and it's not refunded/chargebacked, consider it valid
            // even if the success field is ambiguous
            if ($purchase_valid) {
                $is_valid = true;
                aca_debug_log('License valid based on purchase data');
            } else {
                $is_valid = false;
            }
                       
            // Check if subscription has ended, cancelled, or failed (for subscription products)
            if (isset($data['purchase']['subscription_ended_at']) && $data['purchase']['subscription_ended_at'] !== null) {
                $is_valid = false;
                aca_debug_log('License invalid - subscription ended at: ' . $data['purchase']['subscription_ended_at']);
            }
            if (isset($data['purchase']['subscription_cancelled_at']) && $data['purchase']['subscription_cancelled_at'] !== null) {
                $is_valid = false;
                aca_debug_log('License invalid - subscription cancelled at: ' . $data['purchase']['subscription_cancelled_at']);
            }
            if (isset($data['purchase']['subscription_failed_at']) && $data['purchase']['subscription_failed_at'] !== null) {
                $is_valid = false;
                aca_debug_log('License invalid - subscription failed at: ' . $data['purchase']['subscription_failed_at']);
            }
        }
        
        return array(
            'success' => $is_valid,
            'message' => $is_valid ? 'License verified successfully' : 'License verification failed',
            'purchase_data' => $data['purchase'] ?? null,
            'verified_at' => current_time('mysql')
        );
    }
    
    /**
     * Debug Pro license status
     */
    public function debug_pro_status($request) {
        $license_status = get_option('aca_license_status', 'not_set');
        $license_key = get_option('aca_license_key', '');
        $license_verified = get_option('aca_license_verified', 'not_set');
        $license_timestamp = get_option('aca_license_timestamp', 0);
        
        $debug_info = array(
            'is_pro_active' => is_aca_pro_active(),
            'license_status' => $license_status,
            'license_key_exists' => !empty($license_key),
            'license_key_length' => strlen($license_key),
            'license_verified' => $license_verified,
            'license_verified_expected' => wp_hash('verified'),
            'license_timestamp' => $license_timestamp,
            'license_age_hours' => $license_timestamp > 0 ? round((time() - $license_timestamp) / 3600, 2) : 'N/A',
            'current_time' => time(),
            'checks' => array(
                'status_active' => $license_status === 'active',
                'verified_match' => $license_verified === wp_hash('verified'),
                'timestamp_valid' => (time() - $license_timestamp) < 604800, // 7 days
                'key_exists' => !empty($license_key)
            )
        );
        
        return rest_ensure_response($debug_info);
    }
    
    // ============================================================================
    // CONTENT FRESHNESS API METHODS (PRO FEATURE)
    // ============================================================================
    
    /**
     * Analyze content freshness for multiple posts
     */
    public function analyze_content_freshness($request) {
        // Verify nonce for security
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';
        
        $freshness_manager = new ACA_Content_Freshness();
        $limit = $request->get_param('limit') ?: 10;
        
        // Get posts that need analysis
        $posts_to_analyze = $freshness_manager->get_posts_needing_analysis($limit);
        
        $results = array();
        $analyzed_count = 0;
        
        foreach ($posts_to_analyze as $post_id) {
            $analysis = $freshness_manager->analyze_post_freshness($post_id);
            
            if (!is_wp_error($analysis)) {
                $post = get_post($post_id);
                $results[] = array(
                    'post_id' => $post_id,
                    'post_title' => $post->post_title,
                    'analysis' => $analysis
                );
                $analyzed_count++;
            }
        }
        
        // Add activity log entry
        $this->add_activity_log('content_freshness_analysis', "Analyzed $analyzed_count posts for content freshness", 'Sparkles');
        
        return array(
            'success' => true,
            'analyzed_count' => $analyzed_count,
            'results' => $results,
            'message' => "Successfully analyzed $analyzed_count posts for content freshness"
        );
    }
    
    /**
     * Analyze content freshness for a single post
     */
    public function analyze_single_post_freshness($request) {
        // Verify nonce for security
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';
        
        $post_id = $request->get_param('id');
        $freshness_manager = new ACA_Content_Freshness();
        
        $analysis = $freshness_manager->analyze_post_freshness($post_id);
        
        if (is_wp_error($analysis)) {
            return $analysis;
        }
        
        $post = get_post($post_id);
        
        return array(
            'success' => true,
            'post_id' => $post_id,
            'post_title' => $post->post_title,
            'analysis' => $analysis,
            'message' => 'Content freshness analysis completed'
        );
    }
    
    /**
     * Update content with AI suggestions
     */
    public function update_content_with_ai($request) {
        // Verify nonce for security
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $post_id = $request->get_param('id');
        $update_type = $request->get_param('update_type') ?: 'suggestions';
        
        // This would implement AI-powered content updates
        // For now, return a placeholder response
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_content_updates';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'last_updated' => current_time('mysql'),
                'update_type' => $update_type,
                'ai_suggestions' => json_encode(array('AI update suggestions will be implemented here')),
                'status' => 'pending'
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
        
        if ($result === false) {
            return new WP_Error('update_failed', 'Failed to queue content update', array('status' => 500));
        }
        
        return array(
            'success' => true,
            'post_id' => $post_id,
            'message' => 'Content update queued successfully'
        );
    }
    
    /**
     * Manage freshness settings (GET and POST)
     */
    public function manage_freshness_settings($request) {
        // Verify nonce for security (only for POST requests)
        if ($request->get_method() === 'POST') {
            $nonce_check = $this->verify_nonce($request);
            if (is_wp_error($nonce_check)) {
                return $nonce_check;
            }
        }
        
        $method = $request->get_method();
        
        if ($method === 'GET') {
            $settings = get_option('aca_freshness_settings', array(
                'analysisFrequency' => 'weekly',
                'autoUpdate' => false,
                'updateThreshold' => 70,
                'enabled' => true
            ));
            
            return array(
                'success' => true,
                'settings' => $settings
            );
        } 
        
        if ($method === 'POST') {
            $settings = $request->get_json_params();
            
            if (empty($settings)) {
                return new WP_Error('invalid_data', 'No settings data provided', array('status' => 400));
            }
            
            // Key name transformation for frontend/backend compatibility
            if (isset($settings['analyzeContentFrequency'])) {
                $settings['analysisFrequency'] = $settings['analyzeContentFrequency'];
                unset($settings['analyzeContentFrequency']);
            }
            
            // Validate settings
            $valid_frequencies = array('daily', 'weekly', 'monthly', 'manual');
            if (isset($settings['analysisFrequency']) && !in_array($settings['analysisFrequency'], $valid_frequencies)) {
                return new WP_Error('invalid_frequency', 'Invalid analysis frequency', array('status' => 400));
            }
            
            if (isset($settings['updateThreshold']) && ($settings['updateThreshold'] < 0 || $settings['updateThreshold'] > 100)) {
                return new WP_Error('invalid_threshold', 'Update threshold must be between 0 and 100', array('status' => 400));
            }
            
            // Save settings
            update_option('aca_freshness_settings', $settings);
            
            // Add activity log entry
            $this->add_activity_log('settings_updated', 'Content freshness settings updated', 'Settings');
            
            return array(
                'success' => true,
                'settings' => $settings,
                'message' => 'Freshness settings saved successfully'
            );
        }
        
        return new WP_Error('invalid_method', 'Method not allowed', array('status' => 405));
    }
    
    /**
     * Get posts with freshness data
     */
    public function get_posts_freshness_data($request) {
        
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';
        
        $freshness_manager = new ACA_Content_Freshness();
        $limit = $request->get_param('limit') ?: 20;
        $status = $request->get_param('status'); // 'needs_update', 'fresh', 'all'
        
        global $wpdb;
        $freshness_table = $wpdb->prefix . 'aca_content_freshness';
        
        // Build WHERE clause safely with prepared statements
        $where_conditions = array("p.post_status = %s", "p.post_type = %s");
        $where_values = array('publish', 'post');
        
        if ($status === 'needs_update') {
            $where_conditions[] = "f.needs_update = %d";
            $where_values[] = 1;
        } elseif ($status === 'fresh') {
            $where_conditions[] = "f.needs_update = %d";
            $where_values[] = 0;
        }
        
        $where_clause = "WHERE " . implode(" AND ", $where_conditions);
        
        // Use properly prepared SQL
        $sql = "SELECT p.ID, p.post_title, p.post_date, p.post_modified,
                       f.freshness_score, f.last_analyzed, f.needs_update, f.update_priority
                FROM {$wpdb->posts} p
                LEFT JOIN {$freshness_table} f ON p.ID = f.post_id
                {$where_clause}
                ORDER BY f.update_priority DESC, f.freshness_score ASC, p.post_date DESC
                LIMIT %d";
        
        // Add limit to values array
        $where_values[] = $limit;
        
        $results = $wpdb->get_results($wpdb->prepare($sql, $where_values), ARRAY_A);
        
        // Check for database errors
        if ($wpdb->last_error) {
            aca_debug_log('Database error in get_posts_freshness_data: ' . $wpdb->last_error);
            return new WP_Error('database_error', 'Database query failed: ' . $wpdb->last_error, array('status' => 500));
        }
        
        if ($results === null) {
            aca_debug_log('NULL result from database query in get_posts_freshness_data');
            return new WP_Error('query_failed', 'Database query returned null', array('status' => 500));
        }
        
        return array(
            'success' => true,
            'posts' => $results,
            'total_count' => count($results)
        );
    }
    
    /**
     * Get posts needing updates
     */
    public function get_posts_needing_updates($request) {
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-content-freshness.php';
        
        $freshness_manager = new ACA_Content_Freshness();
        $limit = $request->get_param('limit') ?: 20;
        
        $posts = $freshness_manager->get_posts_needing_updates($limit);
        
        return array(
            'success' => true,
            'posts' => $posts,
            'count' => count($posts),
            'message' => 'Retrieved posts needing content updates'
        );
    }

    /**
     * Get language from WordPress locale
     */
    private function get_language_from_locale($locale) {
        $language_map = array(
            'en_US' => 'English',
            'en_GB' => 'English',
            'tr_TR' => 'Turkish',
            'de_DE' => 'German',
            'fr_FR' => 'French',
            'es_ES' => 'Spanish',
            'it_IT' => 'Italian',
            'pt_PT' => 'Portuguese',
            'pt_BR' => 'Portuguese',
            'ru_RU' => 'Russian',
            'ja' => 'Japanese',
            'ko_KR' => 'Korean',
            'zh_CN' => 'Chinese',
            'zh_TW' => 'Chinese',
            'ar' => 'Arabic',
            'nl_NL' => 'Dutch',
            'sv_SE' => 'Swedish',
            'da_DK' => 'Danish',
            'no' => 'Norwegian',
            'fi' => 'Finnish',
            'pl_PL' => 'Polish',
            'cs_CZ' => 'Czech',
            'hu_HU' => 'Hungarian',
            'ro_RO' => 'Romanian',
            'bg_BG' => 'Bulgarian',
            'hr' => 'Croatian',
            'sk_SK' => 'Slovak',
            'sl_SI' => 'Slovenian',
            'et' => 'Estonian',
            'lv' => 'Latvian',
            'lt_LT' => 'Lithuanian',
            'el' => 'Greek',
            'he_IL' => 'Hebrew',
            'th' => 'Thai',
            'vi' => 'Vietnamese',
            'hi_IN' => 'Hindi',
            'bn_BD' => 'Bengali',
            'ur' => 'Urdu',
            'fa_IR' => 'Persian',
            'uk' => 'Ukrainian',
            'be_BY' => 'Belarusian',
            'ka_GE' => 'Georgian',
            'hy' => 'Armenian',
            'az' => 'Azerbaijani',
            'kk' => 'Kazakh',
            'ky_KY' => 'Kyrgyz',
            'uz_UZ' => 'Uzbek',
            'tg' => 'Tajik',
            'mn' => 'Mongolian'
        );

        // Check direct match first
        if (isset($language_map[$locale])) {
            return $language_map[$locale];
        }

        // Try language code only (e.g., 'en' from 'en_US')
        $lang_code = substr($locale, 0, 2);
        foreach ($language_map as $loc => $lang) {
            if (substr($loc, 0, 2) === $lang_code) {
                return $lang;
            }
        }

        // Default to English if not found
        return 'English';
    }

    /**
     * Get category hierarchy level (depth)
     */
    private function get_category_level($category_id, $level = 0) {
        $category = get_category($category_id);
        if (!$category || is_wp_error($category)) {
            return $level;
        }

        if ($category->parent == 0) {
            return $level;
        }

        return $this->get_category_level($category->parent, $level + 1);
    }

    /**
     * Get full category hierarchy path
     */
    private function get_category_hierarchy_path($category_id) {
        $path = array();
        $category = get_category($category_id);
        
        while ($category && !is_wp_error($category) && $category->parent != 0) {
            array_unshift($path, $category->name);
            $category = get_category($category->parent);
        }
        
        if ($category && !is_wp_error($category)) {
            array_unshift($path, $category->name);
        }
        
        return implode(' > ', $path);
    }
    
    /**
     * Setup proper charset handling for Unicode and special characters
     */
    public function setup_charset_handling() {
        // Ensure UTF-8 encoding is used throughout
        if (function_exists('mb_internal_encoding')) {
            mb_internal_encoding('UTF-8');
        }
        
        // Set proper headers for Unicode support
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }
    }
    
    /**
     * Unicode-safe text sanitization
     */
    private function sanitize_unicode_text($text) {
        if (empty($text)) {
            return '';
        }
        
        // Convert to UTF-8 if not already
        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }
        
        // Remove control characters but preserve Unicode
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Normalize Unicode characters
        if (class_exists('Normalizer')) {
            $text = Normalizer::normalize($text, Normalizer::FORM_C);
        }
        
        // Standard WordPress sanitization that preserves Unicode
        return sanitize_text_field($text);
    }
    
    /**
     * Unicode-safe textarea sanitization
     */
    private function sanitize_unicode_textarea($text) {
        if (empty($text)) {
            return '';
        }
        
        // Convert to UTF-8 if not already
        if (function_exists('mb_convert_encoding')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'auto');
        }
        
        // Remove dangerous control characters but preserve line breaks
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        // Normalize Unicode characters
        if (class_exists('Normalizer')) {
            $text = Normalizer::normalize($text, Normalizer::FORM_C);
        }
        
        // Standard WordPress sanitization that preserves Unicode
        return sanitize_textarea_field($text);
    }
    
    /**
     * Safe JSON encoding with Unicode support
     */
    private function safe_json_encode($data) {
        // Use JSON_UNESCAPED_UNICODE to preserve Unicode characters
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            aca_debug_log('JSON encoding error: ' . json_last_error_msg());
            
            // Fallback: try with escaped Unicode
            $json = json_encode($data);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return false;
            }
        }
        
        return $json;
    }
    
    /**
     * Safe JSON decoding with Unicode support
     */
    private function safe_json_decode($json, $assoc = true) {
        if (empty($json)) {
            return $assoc ? array() : null;
        }
        
        $data = json_decode($json, $assoc);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            aca_debug_log('JSON decoding error: ' . json_last_error_msg());
            return $assoc ? array() : null;
        }
        
        return $data;
    }
    
    /**
     * Test endpoint callback
     */
    public function test_endpoint_callback($request) {
        return array(
            'success' => true,
            'message' => 'Test endpoint working',
            'timestamp' => current_time('mysql')
        );
    }
    
    /**
     * Debug routes callback - shows all registered routes
     */
    public function debug_routes_callback($request) {
        $server = rest_get_server();
        $routes = array();
        
        foreach ($server->get_routes() as $route => $handlers) {
            if (strpos($route, '/aca/v1') === 0) {
                $routes[$route] = array(
                    'methods' => array(),
                    'callbacks' => array()
                );
                
                foreach ($handlers as $handler) {
                    if (isset($handler['methods'])) {
                        $routes[$route]['methods'] = array_merge($routes[$route]['methods'], array_keys($handler['methods']));
                    }
                    if (isset($handler['callback'])) {
                        if (is_array($handler['callback']) && count($handler['callback']) == 2) {
                            $routes[$route]['callbacks'][] = get_class($handler['callback'][0]) . '::' . $handler['callback'][1];
                        } else {
                            $routes[$route]['callbacks'][] = 'Unknown callback';
                        }
                    }
                }
            }
        }
        
        return array(
            'success' => true,
            'routes' => $routes,
            'total_routes' => count($routes)
        );
    }
}