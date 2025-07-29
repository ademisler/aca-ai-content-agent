<?php
/**
 * Settings REST API functionality
 * Handles all settings-related endpoints
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'class-aca-base-api.php';

class ACA_Settings_Api extends ACA_Base_Api {
    
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
    /**
     * Register settings-related REST API routes
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
        $settings = $request->get_json_params();
        update_option('aca_settings', $settings);
        
        $this->add_activity_log('settings', 'Settings Updated', 'Plugin settings were updated', 'success');
        
        return rest_ensure_response(array('success' => true));
    }
    
    /**
     * Get style guide
     */
    public function get_style_guide($request) {
        $style_guide = get_option('aca_style_guide', array());
        return rest_ensure_response($style_guide);
    }
    
    /**
     * Analyze style guide
     */
    public function analyze_style_guide($request = null, $is_auto = false) {
        $content = '';
        
        if ($request) {
            $content = $request->get_param('content');
        }
        
        if (empty($content)) {
            // Get recent posts for analysis
            $posts = get_posts(array(
                'numberposts' => 5,
                'post_status' => 'publish',
                'post_type' => 'post'
            ));
            
            if (empty($posts)) {
                return rest_ensure_response(array(
                    'error' => 'No content available for analysis. Please write some posts first or provide content to analyze.'
                ));
            }
            
            $content = '';
            foreach ($posts as $post) {
                $content .= $post->post_title . "\n\n" . $post->post_content . "\n\n---\n\n";
            }
        }
        
        $prompt = "Analyze the following content and create a comprehensive style guide. Focus on:\n\n";
        $prompt .= "1. Writing tone and voice\n";
        $prompt .= "2. Sentence structure and length preferences\n";
        $prompt .= "3. Vocabulary choices and terminology\n";
        $prompt .= "4. Content structure and organization patterns\n";
        $prompt .= "5. Target audience characteristics\n";
        $prompt .= "6. Brand personality traits\n\n";
        $prompt .= "Content to analyze:\n\n" . $content;
        $prompt .= "\n\nProvide a detailed style guide in JSON format with the following structure:\n";
        $prompt .= "{\n";
        $prompt .= '  "tone": "description",';
        $prompt .= '  "voice": "description",';
        $prompt .= '  "audience": "description",';
        $prompt .= '  "structure": "description",';
        $prompt .= '  "vocabulary": "description",';
        $prompt .= '  "guidelines": ["guideline1", "guideline2", ...]';
        $prompt .= "\n}";
        
        $result = $this->call_gemini_api($prompt);
        
        if (isset($result['error'])) {
            return rest_ensure_response(array('error' => $result['error']));
        }
        
        $analysis = $result['content'];
        
        // Try to extract JSON from the response
        $json_start = strpos($analysis, '{');
        $json_end = strrpos($analysis, '}');
        
        if ($json_start !== false && $json_end !== false) {
            $json_content = substr($analysis, $json_start, $json_end - $json_start + 1);
            $parsed = json_decode($json_content, true);
            
            if ($parsed) {
                $analysis = $parsed;
            }
        }
        
        if (!$is_auto) {
            $this->add_activity_log('style_guide', 'Style Guide Analyzed', 'AI analyzed content and generated style guide recommendations', 'success');
        }
        
        return rest_ensure_response(array(
            'success' => true,
            'analysis' => $analysis
        ));
    }
    
    /**
     * Save style guide
     */
    public function save_style_guide($request) {
        $style_guide = $request->get_json_params();
        update_option('aca_style_guide', $style_guide);
        
        $this->add_activity_log('style_guide', 'Style Guide Updated', 'Style guide was updated', 'success');
        
        return rest_ensure_response(array('success' => true));
    }
}