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
            'methods' => 'GET',
            'callback' => array($this, 'get_activity_logs'),
            'permission_callback' => array($this, 'check_permissions')
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
        
        $this->add_activity_log('settings_saved', 'Application settings were updated.', 'Settings');
        
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
            $analysis = $this->call_gemini_analyze_style($settings['geminiApiKey']);
            $parsed_analysis = json_decode($analysis, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            $parsed_analysis['lastAnalyzed'] = current_time('c');
            update_option('aca_style_guide', $parsed_analysis);
            
            $message = $is_auto ? 'Style Guide automatically refreshed.' : 'Style Guide was successfully updated.';
            $this->add_activity_log('style_updated', $message, 'BookOpen');
            
            return rest_ensure_response($parsed_analysis);
            
        } catch (Exception $e) {
            return new WP_Error('analysis_failed', $e->getMessage(), array('status' => 500));
        }
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
                        'status' => 'new',
                        'source' => $source,
                        'created_at' => current_time('c')
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
        $base_title = $params['baseTitle'];
        
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
                        'status' => 'new',
                        'source' => 'similar',
                        'created_at' => current_time('c')
                    );
                }
            }
            
            $this->add_activity_log('ideas_generated', "Generated " . count($saved_ideas) . " similar ideas.", 'Sparkles');
            
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
                'status' => 'new',
                'source' => 'manual',
                'created_at' => current_time('c')
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
            $this->add_activity_log('idea_title_updated', "Updated idea title to \"$new_title\"", 'Pencil');
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
                $this->add_activity_log('idea_archived', "Archived idea: \"{$idea->title}\"", 'Trash');
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
        $nonce_check = $this->verify_nonce($request);
        if (is_wp_error($nonce_check)) {
            return $nonce_check;
        }
        
        $params = $request->get_json_params();
        $idea_id = $params['ideaId'];
        
        return $this->create_draft_from_idea($idea_id);
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
                $existing_posts_context[] = array(
                    'title' => $post->post_title,
                    'url' => get_permalink($post->ID),
                    'content' => wp_strip_all_tags(substr($post->post_content, 0, 500))
                );
            }
            
            // Generate content using AI
            $draft_content = $this->call_gemini_create_draft(
                $settings['geminiApiKey'],
                $idea->title,
                json_encode($style_guide),
                $existing_posts_context
            );
            
            $draft_data = json_decode($draft_content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            // Generate or fetch image
            $image_data = $this->get_featured_image($idea->title, $settings);
            
            // Create WordPress post
            $post_data = array(
                'post_title' => $idea->title,
                'post_content' => $draft_data['content'],
                'post_status' => 'draft',
                'post_type' => 'post',
                'meta_input' => array(
                    '_aca_meta_title' => $draft_data['metaTitle'],
                    '_aca_meta_description' => $draft_data['metaDescription'],
                    '_aca_focus_keywords' => $draft_data['focusKeywords'],
                    '_aca_created_from_idea' => $idea_id
                )
            );
            
            $post_id = wp_insert_post($post_data);
            
            if (is_wp_error($post_id)) {
                throw new Exception('Failed to create WordPress post');
            }
            
            // Set featured image if we have one
            if ($image_data) {
                $attachment_id = $this->save_image_to_media_library($image_data, $idea->title);
                if ($attachment_id) {
                    set_post_thumbnail($post_id, $attachment_id);
                }
            }
            
            // Remove the idea from ideas table
            $wpdb->delete(
                $wpdb->prefix . 'aca_ideas',
                array('id' => $idea_id)
            );
            
            $this->add_activity_log('draft_created', "Created draft: \"{$idea->title}\"", 'FileText');
            
            // Return the created post
            $created_post = get_post($post_id);
            return rest_ensure_response($this->format_post_for_api($created_post));
            
        } catch (Exception $e) {
            return new WP_Error('creation_failed', $e->getMessage(), array('status' => 500));
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
        
        $this->add_activity_log('draft_updated', "Updated draft: \"{$params['title']}\"", 'Pencil');
        
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
        $featured_image = '';
        $attachment_id = get_post_thumbnail_id($post->ID);
        if ($attachment_id) {
            $image_url = wp_get_attachment_image_src($attachment_id, 'large');
            if ($image_url) {
                $featured_image = $image_url[0];
            }
        }
        
        return array(
            'id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'metaTitle' => get_post_meta($post->ID, '_aca_meta_title', true),
            'metaDescription' => get_post_meta($post->ID, '_aca_meta_description', true),
            'focusKeywords' => get_post_meta($post->ID, '_aca_focus_keywords', true),
            'featuredImage' => $featured_image,
            'createdAt' => $post->post_date,
            'status' => $post->post_status,
            'publishedAt' => $post->post_status === 'publish' ? $post->post_date : null,
            'url' => $post->post_status === 'publish' ? get_permalink($post->ID) : null,
            'scheduledFor' => get_post_meta($post->ID, '_aca_scheduled_for', true)
        );
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
    
    // AI Service calls (these would need to be implemented with actual API calls)
    
    private function call_gemini_analyze_style($api_key) {
        // This is a simplified version - you would implement the actual Gemini API call here
        // For now, return a mock response
        return json_encode(array(
            'tone' => 'Friendly and conversational',
            'sentenceStructure' => 'Mix of short, punchy sentences and longer, more descriptive ones',
            'paragraphLength' => 'Short, 2-3 sentences per paragraph',
            'formattingStyle' => 'Uses bullet points, bold text for emphasis, and subheadings (H2, H3)'
        ));
    }
    
    private function call_gemini_generate_ideas($api_key, $style_guide, $existing_titles, $count, $search_console_data) {
        // Mock response - implement actual API call
        $ideas = array(
            'How to Use AI for Content Marketing in 2024',
            'WordPress Automation Tools That Save Time',
            'The Future of Blog Writing with AI',
            'Content Strategy Tips for Small Businesses',
            'SEO Best Practices for AI-Generated Content'
        );
        
        return json_encode(array_slice($ideas, 0, $count));
    }
    
    private function call_gemini_generate_similar_ideas($api_key, $base_title, $existing_titles) {
        // Mock response - implement actual API call
        $similar_ideas = array(
            'Advanced ' . $base_title . ' Techniques',
            $base_title . ': A Complete Guide',
            'Common Mistakes in ' . $base_title
        );
        
        return json_encode($similar_ideas);
    }
    
    private function call_gemini_create_draft($api_key, $title, $style_guide, $existing_posts) {
        // Mock response - implement actual API call
        $content = "# " . $title . "\n\nThis is a comprehensive guide about " . strtolower($title) . ".\n\n## Introduction\n\nIn today's digital landscape, understanding this topic is crucial for success.\n\n## Main Content\n\nHere are the key points you need to know:\n\n- Important point 1\n- Important point 2\n- Important point 3\n\n## Conclusion\n\nBy following these guidelines, you'll be well on your way to mastering this subject.";
        
        return json_encode(array(
            'content' => $content,
            'metaTitle' => substr($title, 0, 60),
            'metaDescription' => 'Learn everything you need to know about ' . strtolower($title) . ' in this comprehensive guide.',
            'focusKeywords' => array(strtolower($title), 'guide', 'tips', 'best practices', '2024')
        ));
    }
    
    private function call_gemini_generate_image($api_key, $title, $style) {
        // Mock response - in real implementation, call Gemini image API
        // Return base64 encoded image data
        return base64_encode('mock_image_data_' . $title);
    }
    
    private function fetch_stock_photo($query, $provider, $api_key) {
        // Mock response - implement actual stock photo API calls
        return base64_encode('mock_stock_photo_' . $query);
    }
}