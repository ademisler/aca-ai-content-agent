<?php
/**
 * ACA REST API Handler
 * Registers and handles all REST API endpoints for the plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_REST_API {
    
    private $namespace = 'aca/v1';
    
    /**
     * Register all REST API routes
     */
    public function register_routes() {
        // Settings endpoints
        register_rest_route($this->namespace, '/settings', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_settings'),
                'permission_callback' => array($this, 'check_permissions')
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'update_settings'),
                'permission_callback' => array($this, 'check_permissions')
            )
        ));
        
        // Style Guide endpoints
        register_rest_route($this->namespace, '/style-guide', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_style_guide'),
                'permission_callback' => array($this, 'check_permissions')
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'update_style_guide'),
                'permission_callback' => array($this, 'check_permissions')
            )
        ));
        
        register_rest_route($this->namespace, '/analyze-style', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'analyze_style'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Ideas endpoints
        register_rest_route($this->namespace, '/ideas', array(
            array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'get_ideas'),
                'permission_callback' => array($this, 'check_permissions')
            ),
            array(
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => array($this, 'generate_ideas'),
                'permission_callback' => array($this, 'check_permissions')
            )
        ));
        
        register_rest_route($this->namespace, '/ideas/similar', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'generate_similar_ideas'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route($this->namespace, '/ideas/manual', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'add_manual_idea'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route($this->namespace, '/ideas/(?P<id>\d+)', array(
            array(
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => array($this, 'update_idea'),
                'permission_callback' => array($this, 'check_permissions')
            ),
            array(
                'methods' => WP_REST_Server::DELETABLE,
                'callback' => array($this, 'delete_idea'),
                'permission_callback' => array($this, 'check_permissions')
            )
        ));
        
        // Posts endpoints
        register_rest_route($this->namespace, '/posts', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_posts'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route($this->namespace, '/posts/(?P<id>\d+)', array(
            'methods' => WP_REST_Server::EDITABLE,
            'callback' => array($this, 'update_post'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route($this->namespace, '/posts/(?P<id>\d+)/publish', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'publish_post'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        register_rest_route($this->namespace, '/posts/(?P<id>\d+)/schedule', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'schedule_post'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Create draft endpoint
        register_rest_route($this->namespace, '/create-draft', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'create_draft'),
            'permission_callback' => array($this, 'check_permissions')
        ));
        
        // Activity logs endpoint
        register_rest_route($this->namespace, '/activity-logs', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'get_activity_logs'),
            'permission_callback' => array($this, 'check_permissions')
        ));
    }
    
    /**
     * Check permissions and nonce
     */
    public function check_permissions($request) {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return false;
        }
        
        // Check nonce for non-GET requests
        if ($request->get_method() !== 'GET') {
            $nonce = $request->get_header('X-WP-Nonce');
            if (!wp_verify_nonce($nonce, 'wp_rest')) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Get settings
     */
    public function get_settings($request) {
        $settings = get_option('aca_settings', array());
        
        // Don't return API keys, only indicate if they exist
        $safe_settings = $settings;
        $api_keys = array('geminiApiKey', 'pexelsApiKey', 'unsplashApiKey', 'pixabayApiKey');
        
        foreach ($api_keys as $key) {
            if (isset($safe_settings[$key])) {
                $safe_settings[$key] = !empty($safe_settings[$key]);
            }
        }
        
        return rest_ensure_response($safe_settings);
    }
    
    /**
     * Update settings
     */
    public function update_settings($request) {
        $new_settings = $request->get_json_params();
        $current_settings = get_option('aca_settings', array());
        
        // Merge settings, preserving existing API keys if new ones are empty
        $api_keys = array('geminiApiKey', 'pexelsApiKey', 'unsplashApiKey', 'pixabayApiKey');
        
        foreach ($api_keys as $key) {
            if (isset($new_settings[$key]) && empty($new_settings[$key]) && !empty($current_settings[$key])) {
                $new_settings[$key] = $current_settings[$key];
            }
        }
        
        update_option('aca_settings', $new_settings);
        
        // Log the activity
        ACA_Database::add_activity_log('settings_saved', 'Settings were successfully saved.', 'Settings');
        
        // Reschedule cron jobs based on new settings
        ACA_Cron::reschedule_jobs();
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Get style guide
     */
    public function get_style_guide($request) {
        $style_guide = get_option('aca_style_guide', null);
        return rest_ensure_response($style_guide);
    }
    
    /**
     * Update style guide
     */
    public function update_style_guide($request) {
        $style_guide = $request->get_json_params();
        update_option('aca_style_guide', $style_guide);
        
        ACA_Database::add_activity_log('style_updated', 'Style Guide was manually updated.', 'BookOpen');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Analyze style using AI
     */
    public function analyze_style($request) {
        try {
            $gemini_service = new ACA_Gemini_Service();
            $analysis = $gemini_service->analyze_style();
            $parsed_analysis = json_decode($analysis, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            $parsed_analysis['lastAnalyzed'] = current_time('c');
            update_option('aca_style_guide', $parsed_analysis);
            
            ACA_Database::add_activity_log('style_updated', 'Style Guide was automatically analyzed and updated.', 'BookOpen');
            
            return rest_ensure_response($parsed_analysis);
            
        } catch (Exception $e) {
            return new WP_Error('analysis_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Get ideas
     */
    public function get_ideas($request) {
        $ideas = ACA_Database::get_ideas();
        return rest_ensure_response($ideas);
    }
    
    /**
     * Generate ideas using AI
     */
    public function generate_ideas($request) {
        try {
            $params = $request->get_json_params();
            $count = isset($params['count']) ? intval($params['count']) : 5;
            $is_auto = isset($params['isAuto']) ? $params['isAuto'] : false;
            
            $style_guide = get_option('aca_style_guide', null);
            if (!$style_guide) {
                return new WP_Error('no_style_guide', 'Please generate a Style Guide first.', array('status' => 400));
            }
            
            // Get existing titles
            $ideas = ACA_Database::get_ideas();
            $posts = ACA_Database::get_posts_by_status('draft') + ACA_Database::get_posts_by_status('publish');
            $existing_titles = array_merge(
                array_column($ideas, 'title'),
                array_column($posts, 'title')
            );
            
            // Get search console data if available
            $settings = get_option('aca_settings', array());
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
                $count,
                $search_console_data
            );
            
            $new_ideas = json_decode($new_ideas_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            // Save new ideas to database
            $created_ideas = array();
            foreach ($new_ideas as $title) {
                $idea_id = ACA_Database::create_idea($title, 'ai');
                if ($idea_id) {
                    $created_ideas[] = array(
                        'id' => $idea_id,
                        'title' => $title,
                        'status' => 'new',
                        'source' => 'ai'
                    );
                }
            }
            
            $message = $is_auto ? 'Ideas were automatically generated.' : 'New ideas generated successfully!';
            ACA_Database::add_activity_log('ideas_generated', $message, 'Lightbulb');
            
            return rest_ensure_response($created_ideas);
            
        } catch (Exception $e) {
            return new WP_Error('generation_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Generate similar ideas
     */
    public function generate_similar_ideas($request) {
        try {
            $params = $request->get_json_params();
            $base_title = $params['baseTitle'];
            
            // Get existing titles
            $ideas = ACA_Database::get_ideas();
            $posts = ACA_Database::get_posts_by_status('draft') + ACA_Database::get_posts_by_status('publish');
            $existing_titles = array_merge(
                array_column($ideas, 'title'),
                array_column($posts, 'title')
            );
            
            $gemini_service = new ACA_Gemini_Service();
            $similar_ideas_json = $gemini_service->generate_similar_ideas($base_title, $existing_titles);
            
            $similar_ideas = json_decode($similar_ideas_json, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON response from AI service');
            }
            
            // Save new ideas to database
            $created_ideas = array();
            foreach ($similar_ideas as $title) {
                $idea_id = ACA_Database::create_idea($title, 'similar');
                if ($idea_id) {
                    $created_ideas[] = array(
                        'id' => $idea_id,
                        'title' => $title,
                        'status' => 'new',
                        'source' => 'similar'
                    );
                }
            }
            
            ACA_Database::add_activity_log('ideas_generated', 'Similar ideas generated successfully!', 'Lightbulb');
            
            return rest_ensure_response($created_ideas);
            
        } catch (Exception $e) {
            return new WP_Error('generation_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Add manual idea
     */
    public function add_manual_idea($request) {
        $params = $request->get_json_params();
        $title = sanitize_text_field($params['title']);
        
        if (empty($title)) {
            return new WP_Error('empty_title', 'Title cannot be empty', array('status' => 400));
        }
        
        // Check for duplicates
        $existing_ideas = ACA_Database::get_ideas();
        foreach ($existing_ideas as $idea) {
            if (strtolower($idea['title']) === strtolower($title)) {
                return new WP_Error('duplicate_title', 'This idea already exists', array('status' => 400));
            }
        }
        
        $idea_id = ACA_Database::create_idea($title, 'manual');
        
        if ($idea_id) {
            ACA_Database::add_activity_log('idea_added', "Manual idea added: $title", 'PlusCircle');
            
            return rest_ensure_response(array(
                'id' => $idea_id,
                'title' => $title,
                'status' => 'new',
                'source' => 'manual'
            ));
        }
        
        return new WP_Error('creation_failed', 'Failed to create idea', array('status' => 500));
    }
    
    /**
     * Update idea
     */
    public function update_idea($request) {
        $id = $request['id'];
        $params = $request->get_json_params();
        
        $result = ACA_Database::update_idea($id, $params);
        
        if ($result) {
            if (isset($params['title'])) {
                ACA_Database::add_activity_log('idea_title_updated', "Idea title updated: {$params['title']}", 'Pencil');
            }
            if (isset($params['status']) && $params['status'] === 'archived') {
                ACA_Database::add_activity_log('idea_archived', "Idea archived: {$params['title']}", 'Trash');
            }
            
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('update_failed', 'Failed to update idea', array('status' => 500));
    }
    
    /**
     * Delete idea
     */
    public function delete_idea($request) {
        $id = $request['id'];
        
        $result = ACA_Database::delete_idea($id);
        
        if ($result) {
            ACA_Database::add_activity_log('idea_archived', 'Idea was deleted', 'Trash');
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('deletion_failed', 'Failed to delete idea', array('status' => 500));
    }
    
    /**
     * Get posts
     */
    public function get_posts($request) {
        $status = $request->get_param('status');
        
        if ($status && in_array($status, array('draft', 'publish'))) {
            $posts = ACA_Database::get_posts_by_status($status);
        } else {
            $drafts = ACA_Database::get_posts_by_status('draft');
            $published = ACA_Database::get_posts_by_status('publish');
            $posts = array_merge($drafts, $published);
        }
        
        return rest_ensure_response($posts);
    }
    
    /**
     * Update post
     */
    public function update_post($request) {
        $id = $request['id'];
        $params = $request->get_json_params();
        
        // Update post content if provided
        if (isset($params['title']) || isset($params['content'])) {
            $post_data = array('ID' => $id);
            
            if (isset($params['title'])) {
                $post_data['post_title'] = sanitize_text_field($params['title']);
            }
            
            if (isset($params['content'])) {
                $post_data['post_content'] = wp_kses_post($params['content']);
            }
            
            wp_update_post($post_data);
        }
        
        // Update meta data
        $meta_fields = array('metaTitle', 'metaDescription', 'focusKeywords', 'scheduledFor');
        $meta_data = array();
        
        foreach ($meta_fields as $field) {
            if (isset($params[$field])) {
                $meta_data[$field] = $params[$field];
            }
        }
        
        if (!empty($meta_data)) {
            ACA_Database::update_post_meta_data($id, $meta_data);
        }
        
        ACA_Database::add_activity_log('draft_updated', 'Draft was updated', 'Pencil');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Publish post
     */
    public function publish_post($request) {
        $id = $request['id'];
        
        $result = ACA_Database::publish_post($id);
        
        if ($result && !is_wp_error($result)) {
            $post = get_post($id);
            ACA_Database::add_activity_log('post_published', "Post published: {$post->post_title}", 'Send');
            
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('publish_failed', 'Failed to publish post', array('status' => 500));
    }
    
    /**
     * Schedule post
     */
    public function schedule_post($request) {
        $id = $request['id'];
        $params = $request->get_json_params();
        $scheduled_date = $params['scheduledFor'];
        
        $result = ACA_Database::schedule_post($id, $scheduled_date);
        
        if ($result) {
            $post = get_post($id);
            ACA_Database::add_activity_log('draft_scheduled', "Post scheduled: {$post->post_title}", 'Calendar');
            
            return rest_ensure_response(array('success' => true));
        }
        
        return new WP_Error('schedule_failed', 'Failed to schedule post', array('status' => 500));
    }
    
    /**
     * Create draft - The most complex endpoint
     */
    public function create_draft($request) {
        try {
            $params = $request->get_json_params();
            $title = $params['title'];
            
            // Step 1: Get style guide and existing posts for internal linking
            $style_guide = get_option('aca_style_guide', null);
            if (!$style_guide) {
                return new WP_Error('no_style_guide', 'Please generate a Style Guide first.', array('status' => 400));
            }
            
            $published_posts = ACA_Database::get_posts_by_status('publish');
            $existing_posts_for_linking = array();
            foreach ($published_posts as $post) {
                $existing_posts_for_linking[] = array(
                    'title' => $post['title'],
                    'url' => $post['url'],
                    'content' => wp_strip_all_tags($post['content'])
                );
            }
            
            // Step 2: Call AI service to create draft content
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
            
            // Step 3: Get image based on settings
            $settings = get_option('aca_settings', array());
            $image_provider = isset($settings['imageSourceProvider']) ? $settings['imageSourceProvider'] : 'ai';
            $featured_image_id = null;
            
            if ($image_provider === 'ai') {
                // Generate AI image
                $ai_style = isset($settings['aiImageStyle']) ? $settings['aiImageStyle'] : 'digital_art';
                $image_base64 = $gemini_service->generate_image($title, $ai_style);
                
                // Convert base64 to image file and upload
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
                // Get stock photo
                $stock_service = new ACA_Stock_Photo_Service();
                $photo_data = $stock_service->fetch_stock_photo($title, $image_provider);
                $featured_image_id = $stock_service->sideload_image($photo_data['url']);
            }
            
            // Step 4: Create the post
            $post_id = ACA_Database::create_draft(
                $title,
                $draft_data['content'],
                $draft_data,
                is_wp_error($featured_image_id) ? null : $featured_image_id
            );
            
            if (!$post_id) {
                throw new Exception('Failed to create post');
            }
            
            // Step 5: Add activity log
            ACA_Database::add_activity_log('draft_created', "Draft created: $title", 'FileText');
            
            // Step 6: Remove idea from ideas list if it exists
            $ideas = ACA_Database::get_ideas();
            foreach ($ideas as $idea) {
                if (strtolower($idea['title']) === strtolower($title)) {
                    ACA_Database::delete_idea($idea['id']);
                    break;
                }
            }
            
            // Step 7: Return the created post data
            $created_post = ACA_Database::get_posts_by_status('draft');
            $new_post = null;
            foreach ($created_post as $post) {
                if ($post['id'] == $post_id) {
                    $new_post = $post;
                    break;
                }
            }
            
            return rest_ensure_response($new_post);
            
        } catch (Exception $e) {
            return new WP_Error('creation_failed', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Get activity logs
     */
    public function get_activity_logs($request) {
        $logs = ACA_Database::get_activity_logs();
        return rest_ensure_response($logs);
    }
}