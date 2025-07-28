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
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route('aca/v1', '/settings', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_settings'),
            'permission_callback' => array($this, 'check_permissions')
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
    }
    
    /**
     * Check permissions
     */
    public function check_permissions() {
        return current_user_can('manage_options');
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
        update_option('aca_settings', $settings);
        
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
        
        $ideas = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}aca_ideas WHERE status = 'new' ORDER BY created_at DESC"
        );
        
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
                $search_console_data = array(
                    'topQueries' => array('AI for content marketing', 'how to write blog posts faster', 'wordpress automation tools'),
                    'underperformingPages' => array('/blog/old-seo-tips', '/blog/2022-social-media-trends')
                );
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
        
        $result = $wpdb->delete(
            $wpdb->prefix . 'aca_ideas',
            array('id' => $id)
        );
        
        if ($result) {
            if ($idea) {
                $this->add_activity_log('idea_archived', "Archived idea: \"{$idea->title}\"", 'Archive');
            }
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('delete_failed', 'Failed to delete idea', array('status' => 500));
    }
    
    /**
     * Get drafts
     */
    public function get_drafts($request) {
        $drafts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'draft',
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
        $posts = get_posts(array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'meta_key' => '_aca_meta_title',
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
                    error_log('ACA Raw Response: ' . $draft_content);
                    throw new Exception('Invalid JSON response from AI service: ' . json_last_error_msg());
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
                $attachment_id = $this->save_image_to_media_library($image_data, $idea->title);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
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
                'status' => $created_post->post_status ?: 'draft',
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
                        'status' => 'draft',
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
        $scheduled_date = $params['date'];
        
        update_post_meta($post_id, '_aca_scheduled_for', $scheduled_date);
        
        $post = get_post($post_id);
        $this->add_activity_log('draft_scheduled', "Scheduled draft: \"{$post->post_title}\"", 'Calendar');
        
        return rest_ensure_response(array('success' => true));
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
                'status' => $post->post_status ?: 'draft',
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
     * Save image to WordPress media library
     */
    private function save_image_to_media_library($image_data, $title) {
        if (!function_exists('media_handle_sideload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        
        // Create temporary file
        $temp_file = wp_tempnam();
        file_put_contents($temp_file, base64_decode($image_data));
        
        $file_array = array(
            'name' => sanitize_file_name($title) . '.jpg',
            'tmp_name' => $temp_file
        );
        
        $attachment_id = media_handle_sideload($file_array, 0);
        
        if (is_wp_error($attachment_id)) {
            unlink($temp_file);
            return false;
        }
        
        return $attachment_id;
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

        $prompt = "
            Create a comprehensive blog post based on this idea: \"{$safe_title}\"
            
            Use this style guide: {$safe_style_guide}
            
            {$context_string}
            
            {$categories_string}
            
            Requirements:
            - Write a well-structured blog post with clear H2 and H3 headings
            - 800-1500 words in length
            - Engaging introduction and compelling conclusion
            - SEO-optimized content matching the style guide
            - Include 2-3 internal links to the provided existing posts where contextually relevant
            - Use markdown format for links: [anchor text](URL)
            - For categories: ONLY use category IDs from the provided list above. Select 1-2 most relevant ones.
            - For tags: Create new relevant tags as strings
            
            Return a JSON object with this exact structure:
            {
              \"content\": \"The full blog post content in HTML format with proper headings (H2, H3), paragraphs, internal links, and formatting\",
              \"metaTitle\": \"SEO-optimized title (50-60 characters)\",
              \"metaDescription\": \"Compelling meta description (150-160 characters)\",
              \"focusKeywords\": [\"keyword1\", \"keyword2\", \"keyword3\", \"keyword4\", \"keyword5\"],
              \"tags\": [\"tag1\", \"tag2\", \"tag3\", \"tag4\", \"tag5\"],
              \"categoryIds\": [1, 5],
              \"excerpt\": \"Brief excerpt for the post (150 characters)\"
            }
        ";
        
        return $this->call_gemini_api($api_key, $prompt);
    }
    
    private function call_gemini_generate_image($api_key, $title, $style) {
        $style_prompts = array(
            'photorealistic' => 'photorealistic, high quality, professional photography',
            'digital_art' => 'digital art, illustration, creative, artistic'
        );
        
        $style_prompt = isset($style_prompts[$style]) ? $style_prompts[$style] : $style_prompts['digital_art'];
        $prompt = "Create a {$style_prompt} image for a blog post titled: \"{$title}\". The image should be relevant to the topic, visually appealing, and suitable for use as a featured image on a professional blog.";
        
        // Note: This is a simplified implementation for image generation
        // In a real implementation, you would use the Gemini image API
        // For now, we'll return a placeholder
        return base64_encode('placeholder_image_for_' . sanitize_title($title));
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
     * Make actual API call to Gemini
     */
    private function call_gemini_api($api_key, $prompt) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
        
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
                'maxOutputTokens' => 2048,
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
                    'timeout' => 90, // Increased timeout for content generation
                    'blocking' => true,
                    'sslverify' => true
                ));
        
        if (is_wp_error($response)) {
            error_log('ACA Gemini API WP Error: ' . $response->get_error_message());
            throw new Exception('Gemini API request failed: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
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
}