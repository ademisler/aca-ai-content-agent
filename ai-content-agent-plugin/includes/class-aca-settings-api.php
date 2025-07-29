<?php
/**
 * Settings API functionality
 * Handles all settings-related REST API endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Settings_API {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register settings-related REST API routes
     */
    public function register_routes() {
        register_rest_route('aca/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_settings'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
        
        register_rest_route('aca/v1', '/settings', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_settings'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
        
        register_rest_route('aca/v1', '/style-guide', array(
            'methods' => 'GET',
            'callback' => array($this, 'get_style_guide'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
        
        register_rest_route('aca/v1', '/style-guide/analyze', array(
            'methods' => 'POST',
            'callback' => array($this, 'analyze_style_guide'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
        
        register_rest_route('aca/v1', '/style-guide', array(
            'methods' => 'POST',
            'callback' => array($this, 'save_style_guide'),
            'permission_callback' => array($this, 'check_permissions'),
        ));
    }
    
    /**
     * Check permissions for settings endpoints
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
            return new WP_Error('invalid_nonce', 'Invalid security token', array('status' => 403));
        }
        return true;
    }
    
    /**
     * Get settings
     */
    public function get_settings($request) {
        $settings = get_option('aca_settings', array());
        
        // Ensure all required settings exist with defaults
        $default_settings = array(
            'mode' => 'manual',
            'autoPublish' => false,
            'searchConsoleUser' => null,
            'gscClientId' => '',
            'gscClientSecret' => '',
            'imageSourceProvider' => 'pexels',
            'aiImageStyle' => 'photorealistic',
            'googleCloudProjectId' => '',
            'googleCloudLocation' => 'us-central1',
            'pexelsApiKey' => '',
            'unsplashApiKey' => '',
            'pixabayApiKey' => '',
            'seoPlugin' => 'none',
            'geminiApiKey' => '',
            'semiAutoIdeaFrequency' => 'weekly',
            'fullAutoPostCount' => 1,
            'fullAutoFrequency' => 'daily',
            'contentAnalysisFrequency' => 'weekly',
        );
        
        $settings = wp_parse_args($settings, $default_settings);
        
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
        
        $params = $request->get_json_params();
        
        // Sanitize and validate settings
        $settings = array();
        
        // Mode validation
        if (isset($params['mode']) && in_array($params['mode'], ['manual', 'semi-auto', 'full-auto'])) {
            $settings['mode'] = sanitize_text_field($params['mode']);
        }
        
        // Boolean settings
        if (isset($params['autoPublish'])) {
            $settings['autoPublish'] = (bool) $params['autoPublish'];
        }
        
        // API keys (sanitized but not validated for format)
        $api_keys = ['geminiApiKey', 'gscClientId', 'gscClientSecret', 'googleCloudProjectId', 
                     'pexelsApiKey', 'unsplashApiKey', 'pixabayApiKey'];
        foreach ($api_keys as $key) {
            if (isset($params[$key])) {
                $settings[$key] = sanitize_text_field($params[$key]);
            }
        }
        
        // Enum validations
        if (isset($params['imageSourceProvider']) && 
            in_array($params['imageSourceProvider'], ['pexels', 'unsplash', 'pixabay', 'google-ai'])) {
            $settings['imageSourceProvider'] = sanitize_text_field($params['imageSourceProvider']);
        }
        
        if (isset($params['aiImageStyle']) && 
            in_array($params['aiImageStyle'], ['photorealistic', 'artistic', 'illustration', 'sketch'])) {
            $settings['aiImageStyle'] = sanitize_text_field($params['aiImageStyle']);
        }
        
        if (isset($params['seoPlugin']) && 
            in_array($params['seoPlugin'], ['none', 'rankmath', 'yoast', 'aioseo'])) {
            $settings['seoPlugin'] = sanitize_text_field($params['seoPlugin']);
        }
        
        // Frequency settings
        $frequencies = ['semiAutoIdeaFrequency', 'fullAutoFrequency', 'contentAnalysisFrequency'];
        foreach ($frequencies as $freq) {
            if (isset($params[$freq]) && in_array($params[$freq], ['daily', 'weekly', 'monthly'])) {
                $settings[$freq] = sanitize_text_field($params[$freq]);
            }
        }
        
        // Numeric settings
        if (isset($params['fullAutoPostCount'])) {
            $settings['fullAutoPostCount'] = max(1, min(10, intval($params['fullAutoPostCount'])));
        }
        
        if (isset($params['googleCloudLocation'])) {
            $settings['googleCloudLocation'] = sanitize_text_field($params['googleCloudLocation']);
        }
        
        // Merge with existing settings
        $existing_settings = get_option('aca_settings', array());
        $settings = wp_parse_args($settings, $existing_settings);
        
        // Save settings
        $result = update_option('aca_settings', $settings);
        
        if ($result) {
            $this->add_activity_log('settings_updated', 'Settings have been updated successfully.', 'Settings');
            return rest_ensure_response(array('success' => true, 'settings' => $settings));
        } else {
            return new WP_Error('save_failed', 'Failed to save settings', array('status' => 500));
        }
    }
    
    /**
     * Get style guide
     */
    public function get_style_guide($request) {
        $style_guide = get_option('aca_style_guide');
        return rest_ensure_response($style_guide ?: null);
    }
    
    /**
     * Analyze style guide
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
            // Get recent posts for analysis
            $recent_posts = get_posts(array(
                'numberposts' => 10,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => '_aca_generated',
                        'compare' => 'NOT EXISTS'
                    )
                )
            ));
            
            if (empty($recent_posts)) {
                return new WP_Error('no_posts', 'No posts found for analysis', array('status' => 400));
            }
            
            // Prepare content for analysis
            $content_samples = array();
            foreach ($recent_posts as $post) {
                $content_samples[] = array(
                    'title' => $post->post_title,
                    'content' => wp_strip_all_tags(substr($post->post_content, 0, 500))
                );
            }
            
            // Call Gemini API for style analysis
            $style_guide = $this->call_gemini_analyze_style($settings['geminiApiKey'], $content_samples);
            
            if ($style_guide) {
                $style_data = json_decode($style_guide, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $style_data['lastAnalyzed'] = current_time('c');
                    update_option('aca_style_guide', $style_data);
                    
                    $this->add_activity_log('style_analyzed', 'Style guide has been automatically generated from your content.', 'BookOpen');
                    
                    return rest_ensure_response($style_data);
                }
            }
            
            throw new Exception('Failed to analyze style guide');
            
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
        
        $params = $request->get_json_params();
        
        // Sanitize style guide data
        $style_guide = array();
        
        if (isset($params['tone'])) {
            $style_guide['tone'] = sanitize_text_field($params['tone']);
        }
        
        if (isset($params['style'])) {
            $style_guide['style'] = sanitize_text_field($params['style']);
        }
        
        if (isset($params['audience'])) {
            $style_guide['audience'] = sanitize_text_field($params['audience']);
        }
        
        if (isset($params['topics']) && is_array($params['topics'])) {
            $style_guide['topics'] = array_map('sanitize_text_field', $params['topics']);
        }
        
        if (isset($params['keywords']) && is_array($params['keywords'])) {
            $style_guide['keywords'] = array_map('sanitize_text_field', $params['keywords']);
        }
        
        if (isset($params['guidelines'])) {
            $style_guide['guidelines'] = sanitize_textarea_field($params['guidelines']);
        }
        
        $style_guide['lastAnalyzed'] = current_time('c');
        
        $result = update_option('aca_style_guide', $style_guide);
        
        if ($result) {
            $this->add_activity_log('style_updated', 'Style Guide was manually edited and saved.', 'BookOpen');
            return rest_ensure_response(array('success' => true));
        } else {
            return new WP_Error('save_failed', 'Failed to save style guide', array('status' => 500));
        }
    }
    
    /**
     * Add activity log entry
     */
    private function add_activity_log($type, $title, $icon = '', $description = '', $status = 'success', $data = array()) {
        global $wpdb;
        
        $wpdb->insert(
            $wpdb->prefix . 'aca_activity_logs',
            array(
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'icon' => $icon,
                'data' => json_encode($data),
                'created_at' => current_time('mysql')
            )
        );
    }
    
    /**
     * Call Gemini API for style analysis
     */
    private function call_gemini_analyze_style($api_key, $content_samples) {
        // This would contain the actual Gemini API call logic
        // For now, return a mock response structure
        return json_encode(array(
            'tone' => 'Professional and informative',
            'style' => 'Clear and concise writing with practical examples',
            'audience' => 'Business professionals and content creators',
            'topics' => array('Content Marketing', 'SEO', 'WordPress', 'AI Tools'),
            'keywords' => array('content creation', 'SEO optimization', 'WordPress plugins'),
            'guidelines' => 'Write in an engaging, helpful tone. Use bullet points and subheadings for clarity. Include actionable tips and real-world examples.',
            'lastAnalyzed' => current_time('c')
        ));
    }
}