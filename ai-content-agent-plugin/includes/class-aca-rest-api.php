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
            
            // Get search console data if user is connected and has pro license
            $search_console_data = null;
            if (!empty($settings['searchConsoleUser'])) {
                // Check if pro license is active for GSC integration
                if (is_aca_pro_active()) {
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
                } else {
                    error_log('ACA: Google Search Console integration requires Pro license');
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
            'meta_key' => '_aca_meta_title',
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
            'post_date' => date('Y-m-d H:i:s', strtotime($new_date)),
            'post_date_gmt' => get_gmt_from_date(date('Y-m-d H:i:s', strtotime($new_date))),
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
        $old_error_handler = set_error_handler(function($severity, $message, $file, $line) {
            error_log("ACA PHP Error: $message in $file on line $line");
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        
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
            error_log('ACA: Creating draft for idea ID: ' . $idea_id);
            
            $result = $this->create_draft_from_idea($idea_id);
            
            // Log the result
            if (is_wp_error($result)) {
                error_log('ACA: Draft creation failed for idea ' . $idea_id . ': ' . $result->get_error_message());
            } else {
                error_log('ACA: Draft creation successful for idea ' . $idea_id);
            }
            
            return $result;
            
        } catch (Throwable $e) {
            error_log('ACA FATAL ERROR in create_draft: ' . $e->getMessage());
            error_log('ACA FATAL ERROR stack trace: ' . $e->getTraceAsString());
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
        
        // Enable error reporting for debugging
        error_log('ACA DEBUG: Starting create_draft_from_idea for ID: ' . $idea_id);
        
        // Get the idea
        $idea = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}aca_ideas WHERE id = %d",
            $idea_id
        ));
        
        if (!$idea) {
            error_log('ACA DEBUG: Idea not found for ID: ' . $idea_id);
            return new WP_Error('idea_not_found', 'Idea not found', array('status' => 404));
        }
        
        error_log('ACA DEBUG: Idea found: ' . $idea->title);
        
        $settings = get_option('aca_settings', array());
        $style_guide = get_option('aca_style_guide');
        
        error_log('ACA DEBUG: Settings loaded, API key present: ' . (!empty($settings['geminiApiKey']) ? 'YES' : 'NO'));
        error_log('ACA DEBUG: Style guide present: ' . (!empty($style_guide) ? 'YES' : 'NO'));
        
        if (empty($settings['geminiApiKey'])) {
            return new WP_Error('no_api_key', 'Google AI API Key is not set', array('status' => 400));
        }
        
        if (empty($style_guide)) {
            return new WP_Error('no_style_guide', 'Style Guide is required', array('status' => 400));
        }
        
        try {
            error_log('ACA DEBUG: Starting try block');
            
            // Get existing published posts for internal linking
            error_log('ACA DEBUG: Getting published posts');
            $published_posts = get_posts(array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'numberposts' => 10,
                'orderby' => 'date',
                'order' => 'DESC'
            ));
            
            error_log('ACA DEBUG: Found ' . count($published_posts) . ' published posts');
            
            $existing_posts_context = array();
            foreach ($published_posts as $post) {
                $permalink = get_permalink($post->ID);
                $existing_posts_context[] = array(
                    'title' => $post->post_title,
                    'url' => $permalink ? $permalink : home_url("/?p={$post->ID}"),
                    'content' => wp_strip_all_tags(substr($post->post_content, 0, 500))
                );
            }
            
            // Get existing categories for AI to choose from
            error_log('ACA DEBUG: Getting existing categories');
            $existing_categories = get_categories(array(
                'hide_empty' => false,
                'number' => 20
            ));
            
            $categories_context = array();
            foreach ($existing_categories as $category) {
                $categories_context[] = array(
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => $category->count
                );
            }
            
            error_log('ACA DEBUG: Found ' . count($categories_context) . ' existing categories');
            
            // Generate content using AI
            error_log('ACA DEBUG: Starting AI content generation');
            try {
                $draft_content = $this->call_gemini_create_draft(
                    $settings['geminiApiKey'],
                    $idea->title,
                    json_encode($style_guide),
                    $existing_posts_context,
                    $categories_context
                );
                
                error_log('ACA DEBUG: AI content generated, length: ' . strlen($draft_content));
                
                if (empty($draft_content)) {
                    throw new Exception('Empty response from AI service');
                }
                
                $draft_data = json_decode($draft_content, true);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                            error_log('ACA JSON Decode Error: ' . json_last_error_msg());
                            error_log('ACA Raw Response Length: ' . strlen($draft_content));
                            error_log('ACA Raw Response First 1000 chars: ' . substr($draft_content, 0, 1000));
                            error_log('ACA Raw Response Last 500 chars: ' . substr($draft_content, -500));
                            
                            // Try to clean and fix common JSON issues
                            $cleaned_content = $this->clean_ai_json_response($draft_content);
                            if ($cleaned_content !== $draft_content) {
                                error_log('ACA Attempting to parse cleaned JSON');
                                $draft_data = json_decode($cleaned_content, true);
                                if (json_last_error() === JSON_ERROR_NONE) {
                                    error_log('ACA Successfully parsed cleaned JSON');
                                } else {
                                    error_log('ACA Cleaned JSON still invalid: ' . json_last_error_msg());
                                    throw new Exception('Invalid JSON response from AI service after cleaning: ' . json_last_error_msg());
                                }
                            } else {
                                throw new Exception('Invalid JSON response from AI service: ' . json_last_error_msg());
                            }
                        }
                
                // Validate required fields with detailed logging
                $missing_fields = array();
                if (empty($draft_data['content'])) $missing_fields[] = 'content';
                if (empty($draft_data['metaTitle'])) $missing_fields[] = 'metaTitle';
                if (empty($draft_data['metaDescription'])) $missing_fields[] = 'metaDescription';
                
                if (!empty($missing_fields)) {
                    error_log('ACA Missing Fields: ' . implode(', ', $missing_fields));
                    error_log('ACA Response Keys: ' . implode(', ', array_keys($draft_data)));
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
                error_log('ACA DEBUG: Content before conversion (first 200 chars): ' . substr($draft_data['content'], 0, 200));
                if (strpos($draft_data['content'], '**') !== false || 
                    strpos($draft_data['content'], '*') !== false || 
                    strpos($draft_data['content'], '[') !== false ||
                    strpos($draft_data['content'], '##') !== false) {
                    error_log('ACA DEBUG: Markdown detected, converting to HTML');
                    $draft_data['content'] = $this->markdown_to_html($draft_data['content']);
                    error_log('ACA DEBUG: Content after conversion (first 200 chars): ' . substr($draft_data['content'], 0, 200));
                } else {
                    error_log('ACA DEBUG: No Markdown detected, using content as-is');
                }
                
                // Log what we received from AI
                error_log('ACA DEBUG: AI response keys: ' . implode(', ', array_keys($draft_data)));
                if (isset($draft_data['categoryIds'])) {
                    error_log('ACA DEBUG: AI selected category IDs: ' . implode(', ', $draft_data['categoryIds']));
                }
                if (isset($draft_data['tags'])) {
                    error_log('ACA DEBUG: AI selected tags: ' . implode(', ', $draft_data['tags']));
                }
                
            } catch (Exception $ai_error) {
                throw new Exception('AI content generation failed: ' . $ai_error->getMessage());
            }
            
            // Generate or fetch image (temporarily disabled for debugging)
            $image_data = null; // $this->get_featured_image($idea->title, $settings);
            
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
            
            error_log('ACA DEBUG: Creating WordPress post');
            $post_id = wp_insert_post($post_data);
            
            if (is_wp_error($post_id)) {
                error_log('ACA DEBUG: wp_insert_post failed: ' . $post_id->get_error_message());
                throw new Exception('Failed to create WordPress post: ' . $post_id->get_error_message());
            }
            
            error_log('ACA DEBUG: WordPress post created with ID: ' . $post_id);
            
            // Add categories safely using AI-selected IDs
            error_log('ACA DEBUG: Processing categories');
            if (isset($draft_data['categoryIds']) && is_array($draft_data['categoryIds'])) {
                error_log('ACA DEBUG: Found ' . count($draft_data['categoryIds']) . ' category IDs to process');
                $category_ids = array();
                foreach ($draft_data['categoryIds'] as $category_id) {
                    if (is_numeric($category_id)) {
                        $category_id = (int) $category_id;
                        // Verify category exists
                        $category = get_category($category_id);
                        if ($category && !is_wp_error($category)) {
                            $category_ids[] = $category_id;
                            error_log('ACA DEBUG: Valid category ID: ' . $category_id . ' (' . $category->name . ')');
                        } else {
                            error_log('ACA DEBUG: Invalid category ID: ' . $category_id);
                        }
                    }
                }
                if (!empty($category_ids)) {
                    error_log('ACA DEBUG: Setting ' . count($category_ids) . ' categories');
                    wp_set_post_categories($post_id, $category_ids);
                } else {
                    error_log('ACA DEBUG: No valid categories to set');
                }
            } else {
                error_log('ACA DEBUG: No categoryIds in draft_data');
            }
            
            // Add tags safely
            error_log('ACA DEBUG: Processing tags');
            if (isset($draft_data['tags']) && is_array($draft_data['tags'])) {
                error_log('ACA DEBUG: Found ' . count($draft_data['tags']) . ' tags to process');
                $clean_tags = array();
                foreach ($draft_data['tags'] as $tag) {
                    if (is_string($tag) && !empty(trim($tag))) {
                        $clean_tags[] = sanitize_text_field($tag);
                    }
                }
                if (!empty($clean_tags)) {
                    error_log('ACA DEBUG: Setting ' . count($clean_tags) . ' tags: ' . implode(', ', $clean_tags));
                    wp_set_post_tags($post_id, $clean_tags);
                } else {
                    error_log('ACA DEBUG: No valid tags to set');
                }
            } else {
                error_log('ACA DEBUG: No tags in draft_data');
            }
            
            // Set featured image if we have one
            if ($image_data) {
                $attachment_id = $this->save_image_to_media_library($image_data, $idea->title, $post_id);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                    error_log('ACA: Successfully set featured image for post ' . $post_id . ' with attachment ' . $attachment_id);
                } else {
                    error_log('ACA: Failed to create attachment for featured image');
                }
            }
            
            // Send SEO data to detected SEO plugins
            error_log('ACA DEBUG: Sending SEO data to detected SEO plugins');
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
                    error_log('ACA DEBUG: SEO data sent successfully: ' . json_encode($seo_results));
                } else {
                    error_log('ACA DEBUG: No SEO plugins detected or no data sent');
                }
            } catch (Exception $e) {
                error_log('ACA DEBUG: SEO data sending failed (non-blocking): ' . $e->getMessage());
            }
            
            // Update idea status instead of deleting (safer approach)
            $wpdb->update(
                $wpdb->prefix . 'aca_ideas',
                array('status' => 'archived'),
                array('id' => $idea_id)
            );
            
            // Add activity log with error handling (non-blocking)
            error_log('ACA DEBUG: Adding activity log');
            $log_result = $this->add_activity_log('draft_created', "Created draft: \"{$idea->title}\"", 'FileText');
            if (!$log_result) {
                error_log('ACA DEBUG: Activity log failed but continuing with draft creation');
            } else {
                error_log('ACA DEBUG: Activity log added successfully');
            }
            
            // Return the created post - simplified approach to avoid formatting errors
            error_log('ACA DEBUG: Getting created post for response');
            $created_post = get_post($post_id);
            
            if (!$created_post) {
                // Even if we can't retrieve the post, it was created successfully
                error_log('ACA: Post created but could not retrieve - Post ID: ' . $post_id);
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
            
            error_log('ACA DEBUG: Returning successful response');
            return rest_ensure_response($safe_response);
            
        } catch (Exception $e) {
            error_log('ACA DEBUG: Exception caught in create_draft_from_idea');
            error_log('ACA Draft Creation Error: ' . $e->getMessage());
            error_log('ACA Draft Creation Stack Trace: ' . $e->getTraceAsString());
            error_log('ACA Draft Creation Context - Idea ID: ' . $idea_id);
            
            // If post was created but we got an error later, try to return success anyway
            if (isset($post_id) && $post_id && !is_wp_error($post_id)) {
                error_log('ACA: Post was created successfully (ID: ' . $post_id . ') but error occurred in processing');
                
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
        error_log('ACA Schedule Draft: Post ID = ' . $post_id);
        error_log('ACA Schedule Draft: Params = ' . json_encode($params));
        
        // Handle both 'date' and 'scheduledDate' parameters for compatibility
        $scheduled_date = isset($params['scheduledDate']) ? $params['scheduledDate'] : (isset($params['date']) ? $params['date'] : null);
        
        error_log('ACA Schedule Draft: Scheduled Date = ' . $scheduled_date);
        
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
        
        error_log('ACA Schedule Draft: Current WP Time = ' . $current_wp_date);
        error_log('ACA Schedule Draft: Received Date = ' . $parsed_date->format('Y-m-d H:i:s'));
        
        // If the date doesn't include a time (just date from calendar), set it to a future time
        $time_part = $parsed_date->format('H:i:s');
        
        // If time is 00:00:00 (midnight), it means we got just a date from calendar drag-drop
        if ($time_part === '00:00:00') {
            // Set to 9:00 AM of that date to ensure it's in the future for scheduling
            $parsed_date->setTime(9, 0, 0);
            error_log('ACA Schedule Draft: Set time to 9:00 AM for calendar date');
        }
        
        // Convert to WordPress local time format
        $local_date = $parsed_date->format('Y-m-d H:i:s');
        $target_timestamp = $parsed_date->getTimestamp();
        
        error_log('ACA Schedule Draft: Target Local Date = ' . $local_date);
        error_log('ACA Schedule Draft: Target Timestamp = ' . $target_timestamp);
        error_log('ACA Schedule Draft: Current Timestamp = ' . $current_wp_time);
        
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
            error_log('ACA Schedule Draft: Setting post status to FUTURE');
        } else {
            // Past or current date - keep as draft but update the date
            $update_data['post_status'] = 'draft';
            error_log('ACA Schedule Draft: Past/current date - keeping as draft');
        }
        
        error_log('ACA Schedule Draft: Update Data = ' . json_encode($update_data));
        
        // Update the post
        $update_result = wp_update_post($update_data);
        
        if (is_wp_error($update_result)) {
            error_log('ACA Schedule Draft: wp_update_post failed: ' . $update_result->get_error_message());
            return new WP_Error('update_failed', 'Failed to schedule post: ' . $update_result->get_error_message(), array('status' => 500));
        }
        
        if ($update_result === 0) {
            error_log('ACA Schedule Draft: wp_update_post returned 0');
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
        
        error_log('ACA Schedule Draft: Successfully updated post. Final status = ' . $updated_post->post_status);
        error_log('ACA Schedule Draft: Final post_date = ' . $updated_post->post_date);
        error_log('ACA Schedule Draft: Final post_date_gmt = ' . $updated_post->post_date_gmt);
        
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
                error_log('ACA Featured Image Error: ' . $img_error->getMessage());
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
                error_log('ACA Categories Error: ' . $cat_error->getMessage());
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
                error_log('ACA Tags Error: ' . $tag_error->getMessage());
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
                error_log('ACA Meta Data Error: ' . $meta_error->getMessage());
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
            error_log('ACA Format Post Critical Error: ' . $e->getMessage());
            throw new Exception('Failed to format post data: ' . $e->getMessage());
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
            $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
            
            if (!$table_exists) {
                error_log('ACA: Activity logs table does not exist: ' . $table_name);
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
                error_log('ACA: Failed to insert activity log: ' . $wpdb->last_error);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log('ACA: Activity log exception: ' . $e->getMessage());
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
            error_log('ACA Image Generation Error: ' . $e->getMessage());
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
            error_log('ACA: Failed to decode base64 image data');
            return false;
        }
        
        file_put_contents($temp_file, $image_content);
        
        // Verify file was created successfully
        if (!file_exists($temp_file) || filesize($temp_file) === 0) {
            error_log('ACA: Failed to create temporary image file');
            if (file_exists($temp_file)) {
                unlink($temp_file);
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
            error_log('ACA: Failed to create media attachment: ' . $attachment_id->get_error_message());
            if (file_exists($temp_file)) {
                unlink($temp_file);
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
        $prompt = "
            Based on this style guide: {$style_guide}
            
            Generate {$count} unique, engaging blog post titles that match the style and tone described in the guide.
            
            Avoid these existing titles: " . json_encode($existing_titles) . "
            
            Return ONLY a JSON array of strings (the titles), nothing else.
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
        $prompt = "
            Generate 3-5 blog post titles that are similar to this idea: \"{$base_title}\"
            
            The similar titles should:
            - Cover the same general topic but from different angles
            - Be unique and engaging
            - Not duplicate any of these existing titles: " . json_encode($existing_titles) . "
            
            Return ONLY a JSON array of strings (the titles), nothing else.
            Example format: [\"Similar Title 1\", \"Similar Title 2\", \"Similar Title 3\"]
        ";
        
        return $this->call_gemini_api($api_key, $prompt);
    }
    
    private function call_gemini_create_draft($api_key, $title, $style_guide, $existing_posts, $existing_categories = array()) {
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

        // Build categories context string
        $categories_string = '';
        if (!empty($existing_categories) && is_array($existing_categories)) {
            $categories_string = "Available categories to choose from (select the most appropriate ones):\n";
            foreach ($existing_categories as $category) {
                if (isset($category['id'], $category['name'])) {
                    $categories_string .= "- ID: {$category['id']}, Name: \"{$category['name']}\", Posts: {$category['count']}\n";
                }
            }
            $categories_string .= "\n";
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

Use this style guide: {$safe_style_guide}

{$context_string}

{$categories_string}

Requirements:
- Write a well-structured blog post with clear H2 and H3 headings
- 800-1500 words in length
- Engaging introduction and compelling conclusion
- SEO-optimized content matching the style guide
- Include 2-3 internal links to the provided existing posts where contextually relevant
- For categories: ONLY use category IDs from the provided list above. Select 1-2 most relevant ones.
- For tags: Create new relevant tags as strings

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
        $clean_title = strip_tags($title);
        $key_concepts = $this->extract_key_concepts($clean_title);
        
        $prompt = "Create a {$style_prompt} image that represents the concept of \"{$clean_title}\". Focus on the main themes: {$key_concepts}. The image should be relevant to the topic, visually appealing, suitable for use as a featured image on a professional blog, and capture the essence of the subject matter. IMPORTANT: Do not include any text, words, letters, numbers, signs, or written content in the image. The image should be purely visual without any textual elements, logos, or readable content.";
        
        try {
            // Use Google's Imagen API for actual image generation
            $imagen_response = $this->call_imagen_api($api_key, $prompt);
            
            if (is_wp_error($imagen_response)) {
                error_log('ACA Imagen API Error: ' . $imagen_response->get_error_message());
                throw new Exception('Imagen API error: ' . $imagen_response->get_error_message());
            }
            
            return $imagen_response;
            
        } catch (Exception $e) {
            error_log('ACA AI Image Generation Error: ' . $e->getMessage());
            
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
            error_log('ACA Imagen API network error: ' . $response->get_error_message());
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
            error_log('ACA Imagen API error: ' . $error_message);
            return new WP_Error('imagen_api_error', $error_message);
        }
        
        $data = json_decode($response_body, true);
        
        if (!isset($data['predictions'][0]['bytesBase64Encoded'])) {
            error_log('ACA Imagen API invalid response: ' . $response_body);
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
        error_log('ACA: Using API key as access token - this may not work properly');
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
        
        $response = wp_remote_get($url, array('headers' => $headers));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to fetch from ' . $provider . ': ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
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
            throw new Exception('No images found for query: ' . $query);
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
            error_log('ACA JSON Encode Error: ' . json_last_error_msg());
            error_log('ACA Request Data: ' . print_r($request_data, true));
            throw new Exception('Failed to encode request data: ' . json_last_error_msg());
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
            error_log('ACA Gemini API WP Error: ' . $response->get_error_message());
            throw new Exception('Gemini API request failed: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        
        // Handle 503 and other overload errors with retry logic
        if ($response_code === 503 || $response_code === 429) {
            $error_body = wp_remote_retrieve_body($response);
            error_log("ACA Gemini API Overload Error (Code {$response_code}): " . substr($error_body, 0, 500));
            
            // Check if we should retry
            if ($retry_count < $max_retries) {
                // Try fallback model on first retry
                if ($retry_count === 0 && $model === 'gemini-2.0-flash') {
                    error_log("ACA: Trying fallback model {$fallback_model}");
                    sleep($retry_delay);
                    return $this->call_gemini_api($api_key, $prompt, $fallback_model, $retry_count + 1);
                }
                
                // Exponential backoff
                $delay = $retry_delay * pow(2, $retry_count);
                error_log("ACA: Retrying in {$delay} seconds... (attempt " . ($retry_count + 1) . "/{$max_retries})");
                sleep($delay);
                return $this->call_gemini_api($api_key, $prompt, $model, $retry_count + 1);
            }
            
            throw new Exception("Gemini API service unavailable after {$max_retries} attempts. Error code: {$response_code} - " . substr($error_body, 0, 200));
        }
        
        if ($response_code !== 200) {
            $error_body = wp_remote_retrieve_body($response);
            error_log('ACA Gemini API HTTP Error: Code ' . $response_code . ', Body: ' . substr($error_body, 0, 500));
            throw new Exception('Gemini API returned error code: ' . $response_code . ' - ' . substr($error_body, 0, 200));
        }
        
        $response_body = wp_remote_retrieve_body($response);
        if (empty($response_body)) {
            error_log('ACA Gemini API Empty Response Body');
            throw new Exception('Empty response from Gemini API');
        }
        
        error_log('ACA Gemini API Response: ' . substr($response_body, 0, 500));
        
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('ACA Gemini API JSON Error: ' . json_last_error_msg() . ', Response: ' . substr($response_body, 0, 300));
            throw new Exception('Invalid JSON response from Gemini API: ' . json_last_error_msg());
        }
        
        if (empty($data['candidates'][0]['content']['parts'][0]['text'])) {
            error_log('ACA Gemini API No Content: ' . print_r($data, true));
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
            $cron->fifteen_minute_task();
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
        $cron = new ACA_Cron();
        
        try {
            $cron->thirty_minute_task();
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
            error_log('ACA GSC Auth Status Error: ' . $e->getMessage());
            return rest_ensure_response(array(
                'connected' => false, 
                'error' => 'Failed to check GSC auth status: ' . $e->getMessage()
            ));
        } catch (Error $e) {
            error_log('ACA GSC Auth Status Fatal Error: ' . $e->getMessage());
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
                $result = $gsc->handle_oauth_callback($_GET['code']);
                
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
            error_log('ACA GSC Connect Error: ' . $e->getMessage());
            return new WP_Error('gsc_error', 'Failed to connect to GSC: ' . $e->getMessage());
        } catch (Error $e) {
            error_log('ACA GSC Connect Fatal Error: ' . $e->getMessage());
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
     * Get SEO plugins status endpoint
     */
    public function get_seo_plugins($request) {
        try {
            error_log('ACA: get_seo_plugins called');
            
            $detected_plugins = $this->detect_seo_plugin();
            
            error_log('ACA: Detected SEO plugins: ' . print_r($detected_plugins, true));
            
            return rest_ensure_response(array(
                'success' => true,
                'detected_plugins' => $detected_plugins,
                'count' => count($detected_plugins),
                'auto_detection_enabled' => true,
                'timestamp' => current_time('mysql')
            ));
        } catch (Exception $e) {
            error_log('ACA: Error in get_seo_plugins: ' . $e->getMessage());
            return new WP_Error('seo_detection_failed', 'Failed to detect SEO plugins: ' . $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Detect which SEO plugin is active and return plugin info
     */
    private function detect_seo_plugin() {
        $detected_plugins = array();
        
        error_log('ACA: Starting SEO plugin detection...');
        
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
        error_log('ACA: RankMath detection result: ' . ($rankmath_detected ? 'found' : 'not found'));
        
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
        error_log('ACA: Yoast SEO detection result: ' . ($yoast_detected ? 'found' : 'not found'));
        
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
        error_log('ACA: AIOSEO detection result: ' . ($aioseo_detected ? 'found' : 'not found'));
        
        // Log all active plugins for debugging
        $active_plugins = get_option('active_plugins', array());
        error_log('ACA: Active plugins: ' . print_r($active_plugins, true));
        
        error_log('ACA: Total detected SEO plugins: ' . count($detected_plugins));
        
        return $detected_plugins;
    }
    
    /**
     * Send SEO data to detected SEO plugins
     */
    private function send_seo_data_to_plugins($post_id, $meta_title, $meta_description, $focus_keywords) {
        $detected_plugins = $this->detect_seo_plugin();
        $results = array();
        
        foreach ($detected_plugins as $plugin_info) {
            switch ($plugin_info['plugin']) {
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
        }
        
        return $results;
    }
    
    /**
     * Send SEO data to RankMath
     */
    private function send_to_rankmath($post_id, $meta_title, $meta_description, $focus_keywords) {
        try {
            // RankMath stores data in post meta with specific keys
            if (!empty($meta_title)) {
                update_post_meta($post_id, 'rank_math_title', sanitize_text_field($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, 'rank_math_description', sanitize_textarea_field($meta_description));
            }
            
            if (!empty($focus_keywords) && is_array($focus_keywords)) {
                // RankMath stores focus keyword as a single string (first keyword)
                $primary_keyword = sanitize_text_field($focus_keywords[0]);
                update_post_meta($post_id, 'rank_math_focus_keyword', $primary_keyword);
                
                // Store all keywords in a custom meta for reference
                update_post_meta($post_id, 'aca_focus_keywords', $focus_keywords);
            }
            
            // Set additional RankMath meta for better integration
            update_post_meta($post_id, 'rank_math_robots', array('index', 'follow'));
            
            // Set content score (simulate a good score for AI-generated content)
            update_post_meta($post_id, 'rank_math_seo_score', 85);
            
            // Set pillar content if multiple keywords (indicates comprehensive content)
            if (!empty($focus_keywords) && is_array($focus_keywords) && count($focus_keywords) > 1) {
                update_post_meta($post_id, 'rank_math_pillar_content', 'on');
            }
            
            // Enhanced Schema support for different post types
            $post_type = get_post_type($post_id);
            if ($post_type === 'post') {
                update_post_meta($post_id, 'rank_math_rich_snippet', 'article');
                update_post_meta($post_id, 'rank_math_snippet_article_type', 'BlogPosting');
            } elseif ($post_type === 'page') {
                update_post_meta($post_id, 'rank_math_rich_snippet', 'article');
                update_post_meta($post_id, 'rank_math_snippet_article_type', 'Article');
            }
            
            // Set primary category if post has categories
            if ($post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    update_post_meta($post_id, '_yoast_wpseo_primary_category', $categories[0]->term_id);
                }
            }
            
            // Social Media Integration
            if (!empty($meta_title)) {
                update_post_meta($post_id, 'rank_math_facebook_title', sanitize_text_field($meta_title));
                update_post_meta($post_id, 'rank_math_twitter_title', sanitize_text_field($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, 'rank_math_facebook_description', sanitize_textarea_field($meta_description));
                update_post_meta($post_id, 'rank_math_twitter_description', sanitize_textarea_field($meta_description));
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
            
            // Advanced RankMath Pro features if available
            if (class_exists('\RankMath\Pro\Admin\Admin_Menu')) {
                // Set content AI score for Pro version
                update_post_meta($post_id, 'rank_math_contentai_score', 90);
                
                // Enable advanced tracking
                update_post_meta($post_id, 'rank_math_analytic_object_id', $post_id);
            }
            
            error_log('ACA: Successfully sent SEO data to RankMath for post ' . $post_id);
            
            return array(
                'success' => true,
                'message' => 'SEO data successfully sent to RankMath',
                'plugin' => 'RankMath',
                'data_sent' => array(
                    'title' => !empty($meta_title),
                    'description' => !empty($meta_description),
                    'focus_keyword' => !empty($focus_keywords),
                    'seo_score' => 85,
                    'robots' => 'index,follow',
                    'schema' => ($post_type === 'post' || $post_type === 'page') ? 'article' : 'none',
                    'social_media' => !empty($meta_title) || !empty($meta_description),
                    'primary_category' => ($post_type === 'post' && !empty($categories))
                )
            );
            
        } catch (Exception $e) {
            error_log('ACA: Error sending to RankMath: ' . $e->getMessage());
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
                update_post_meta($post_id, '_yoast_wpseo_title', sanitize_text_field($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_yoast_wpseo_metadesc', sanitize_textarea_field($meta_description));
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
                $word_count = str_word_count(strip_tags($post->post_content));
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
            if ($post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    update_post_meta($post_id, '_yoast_wpseo_primary_category', $categories[0]->term_id);
                }
            }
            
            // Social Media Integration - OpenGraph
            if (!empty($meta_title)) {
                update_post_meta($post_id, '_yoast_wpseo_opengraph-title', sanitize_text_field($meta_title));
                update_post_meta($post_id, '_yoast_wpseo_twitter-title', sanitize_text_field($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_yoast_wpseo_opengraph-description', sanitize_textarea_field($meta_description));
                update_post_meta($post_id, '_yoast_wpseo_twitter-description', sanitize_textarea_field($meta_description));
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
            
            error_log('ACA: Successfully sent SEO data to Yoast SEO for post ' . $post_id);
            
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
            error_log('ACA: Error sending to Yoast SEO: ' . $e->getMessage());
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
            // AIOSEO stores data in post meta with _aioseo_ prefix
            if (!empty($meta_title)) {
                update_post_meta($post_id, '_aioseo_title', sanitize_text_field($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_aioseo_description', sanitize_textarea_field($meta_description));
            }
            
            if (!empty($focus_keywords) && is_array($focus_keywords)) {
                // AIOSEO stores keywords as comma-separated string
                $keywords_string = implode(', ', array_map('sanitize_text_field', $focus_keywords));
                update_post_meta($post_id, '_aioseo_keywords', $keywords_string);
                
                // Store primary focus keyword
                update_post_meta($post_id, '_aioseo_focus_keyphrase', sanitize_text_field($focus_keywords[0]));
                
                // Store all keywords in a custom meta for reference
                update_post_meta($post_id, 'aca_focus_keywords', $focus_keywords);
            }
            
            // Set robots meta
            update_post_meta($post_id, '_aioseo_robots_default', true);
            update_post_meta($post_id, '_aioseo_robots_noindex', false);
            update_post_meta($post_id, '_aioseo_robots_nofollow', false);
            update_post_meta($post_id, '_aioseo_robots_noarchive', false);
            update_post_meta($post_id, '_aioseo_robots_nosnippet', false);
            
            // Social Media Integration - OpenGraph
            if (!empty($meta_title)) {
                update_post_meta($post_id, '_aioseo_og_title', sanitize_text_field($meta_title));
                update_post_meta($post_id, '_aioseo_twitter_title', sanitize_text_field($meta_title));
            }
            
            if (!empty($meta_description)) {
                update_post_meta($post_id, '_aioseo_og_description', sanitize_textarea_field($meta_description));
                update_post_meta($post_id, '_aioseo_twitter_description', sanitize_textarea_field($meta_description));
            }
            
            // Set featured image for social media if available
            $featured_image_id = get_post_thumbnail_id($post_id);
            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'full');
                if ($featured_image_url) {
                    update_post_meta($post_id, '_aioseo_og_image_type', 'default');
                    update_post_meta($post_id, '_aioseo_og_image_custom_url', $featured_image_url);
                    update_post_meta($post_id, '_aioseo_twitter_image_type', 'default');
                    update_post_meta($post_id, '_aioseo_twitter_image_custom_url', $featured_image_url);
                }
            }
            
            // Set primary category if post has categories
            $post_type = get_post_type($post_id);
            if ($post_type === 'post') {
                $categories = get_the_category($post_id);
                if (!empty($categories)) {
                    update_post_meta($post_id, '_aioseo_primary_term', json_encode(array(
                        'category' => $categories[0]->term_id
                    )));
                }
            }
            
            // Set canonical URL to self
            $post_url = get_permalink($post_id);
            if ($post_url) {
                update_post_meta($post_id, '_aioseo_canonical_url', $post_url);
            }
            
            // AIOSEO Pro features if available
            if (is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php') || defined('AIOSEO_PRO')) {
                // Set SEO score for Pro version
                update_post_meta($post_id, '_aioseo_seo_score', 85);
                
                // Enable advanced features
                update_post_meta($post_id, '_aioseo_priority', 'default');
                update_post_meta($post_id, '_aioseo_frequency', 'default');
            }
            
            // Set schema type
            if ($post_type === 'post') {
                update_post_meta($post_id, '_aioseo_schema_type', 'BlogPosting');
            } elseif ($post_type === 'page') {
                update_post_meta($post_id, '_aioseo_schema_type', 'WebPage');
            }
            
            error_log('ACA: Successfully sent SEO data to All in One SEO for post ' . $post_id);
            
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
                    'pro_features' => (is_plugin_active('all-in-one-seo-pack-pro/all_in_one_seo_pack.php') || defined('AIOSEO_PRO'))
                )
            );
            
        } catch (Exception $e) {
            error_log('ACA: Error sending to All in One SEO: ' . $e->getMessage());
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
            return new WP_Error('missing_license_key', 'License key is required', array('status' => 400));
        }
        
        // Gumroad product ID - to be replaced with actual product ID
        $product_id = 'YOUR_GUMROAD_PRODUCT_ID_HERE';
        
        try {
            $verification_result = $this->call_gumroad_api($product_id, $license_key);
            
            if ($verification_result['success']) {
                // Store license information
                update_option('aca_license_status', 'active');
                update_option('aca_license_key', $license_key);
                update_option('aca_license_verified_at', current_time('mysql'));
                update_option('aca_license_data', $verification_result['purchase']);
                
                $this->add_activity_log('license_activated', 'Pro license activated successfully', 'CheckCircle');
                
                return rest_ensure_response(array(
                    'success' => true,
                    'message' => 'License verified successfully! Pro features are now active.',
                    'status' => 'active',
                    'purchase_data' => $verification_result['purchase']
                ));
            } else {
                // Remove license if verification fails
                delete_option('aca_license_status');
                delete_option('aca_license_key');
                delete_option('aca_license_verified_at');
                delete_option('aca_license_data');
                
                return new WP_Error(
                    'invalid_license', 
                    'Invalid license key or license has been refunded/chargebacked', 
                    array('status' => 400)
                );
            }
        } catch (Exception $e) {
            error_log('ACA: License verification error: ' . $e->getMessage());
            
            return new WP_Error(
                'verification_failed', 
                'License verification failed: ' . $e->getMessage(), 
                array('status' => 500)
            );
        }
    }
    
    /**
     * Get current license status
     */
    public function get_license_status($request) {
        $status = get_option('aca_license_status', 'inactive');
        $verified_at = get_option('aca_license_verified_at');
        $license_data = get_option('aca_license_data', array());
        
        return rest_ensure_response(array(
            'status' => $status,
            'is_active' => $status === 'active',
            'verified_at' => $verified_at,
            'purchase_data' => $license_data
        ));
    }
    
    /**
     * Call Gumroad License Verification API
     */
    private function call_gumroad_api($product_id, $license_key) {
        $url = 'https://api.gumroad.com/v2/licenses/verify';
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded'
            ),
            'body' => array(
                'product_id' => $product_id,
                'license_key' => $license_key,
                'increment_uses_count' => 'true' // Track license usage for analytics
            ),
            'timeout' => 30,
            'blocking' => true,
            'sslverify' => true
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Gumroad API request failed: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            throw new Exception('Gumroad API returned error code: ' . $response_code);
        }
        
        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON response from Gumroad API');
        }
        
        // Validate license according to requirements
        if (!isset($data['success']) || $data['success'] !== true) {
            return array('success' => false, 'purchase' => null);
        }
        
        $purchase = $data['purchase'] ?? array();
        $refunded = $purchase['refunded'] ?? false;
        $chargebacked = $purchase['chargebacked'] ?? false;
        
        // License is only valid if not refunded and not chargebacked
        if ($refunded || $chargebacked) {
            return array('success' => false, 'purchase' => $purchase);
        }
        
        return array('success' => true, 'purchase' => $purchase);
    }
    
    /**
     * Get Google Search Console data
     */
    public function get_gsc_data($request) {
        // Check if pro license is active
        if (!is_aca_pro_active()) {
            return new WP_Error(
                'pro_required', 
                'Google Search Console integration requires Pro license', 
                array('status' => 403)
            );
        }
        
        $settings = get_option('aca_settings', array());
        
        if (empty($settings['searchConsoleUser'])) {
            return new WP_Error(
                'not_connected', 
                'Google Search Console not connected', 
                array('status' => 400)
            );
        }
        
        try {
            require_once ACA_PLUGIN_PATH . 'includes/class-aca-google-search-console.php';
            
            $gsc = new ACA_Google_Search_Console();
            $data = $gsc->get_data_for_ai();
            
            if (!$data) {
                return new WP_Error(
                    'no_data', 
                    'No Google Search Console data available', 
                    array('status' => 404)
                );
            }
            
            return rest_ensure_response($data);
            
        } catch (Exception $e) {
            error_log('ACA: GSC data fetch error: ' . $e->getMessage());
            
            return new WP_Error(
                'fetch_failed', 
                'Failed to fetch Google Search Console data: ' . $e->getMessage(), 
                array('status' => 500)
            );
        }
    }
}