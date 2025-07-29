<?php
/**
 * Base REST API functionality
 * Common methods and utilities shared by all API classes
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class ACA_Base_Api {
    
    /**
     * Check if user has permissions for general operations
     */
    public function check_permissions() {
        return current_user_can('edit_posts');
    }
    
    /**
     * Check if user has admin permissions
     */
    public function check_admin_permissions() {
        return current_user_can('manage_options');
    }
    
    /**
     * Check if user has SEO permissions
     */
    public function check_seo_permissions() {
        return current_user_can('edit_posts');
    }
    
    /**
     * Add activity log entry
     */
    protected function add_activity_log($type, $title, $description = '', $status = 'success', $data = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_activity_logs';
        
        $wpdb->insert(
            $table_name,
            array(
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'data' => json_encode($data),
                'created_at' => current_time('mysql'),
                'user_id' => get_current_user_id()
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%d')
        );
    }
    
    /**
     * Send SEO data to detected SEO plugins
     */
    protected function send_seo_data_to_plugins($post_id, $seo_title, $meta_description, $focus_keyword) {
        // Detect and send to RankMath
        if (class_exists('RankMath') || defined('RANK_MATH_FILE')) {
            update_post_meta($post_id, 'rank_math_title', $seo_title);
            update_post_meta($post_id, 'rank_math_description', $meta_description);
            update_post_meta($post_id, 'rank_math_focus_keyword', $focus_keyword);
            update_post_meta($post_id, 'rank_math_seo_score', 85);
        }
        
        // Detect and send to Yoast SEO
        if (class_exists('WPSEO_Frontend') || defined('WPSEO_VERSION')) {
            update_post_meta($post_id, '_yoast_wpseo_title', $seo_title);
            update_post_meta($post_id, '_yoast_wpseo_metadesc', $meta_description);
            update_post_meta($post_id, '_yoast_wpseo_focuskw', $focus_keyword);
            update_post_meta($post_id, '_yoast_wpseo_content_score', 75);
        }
        
        // Detect and send to All in One SEO
        if (class_exists('AIOSEO\Plugin\AIOSEO') || defined('AIOSEO_VERSION')) {
            update_post_meta($post_id, '_aioseo_title', $seo_title);
            update_post_meta($post_id, '_aioseo_description', $meta_description);
            update_post_meta($post_id, '_aioseo_focus_keyphrase', $focus_keyword);
        }
    }
    
    /**
     * Call Gemini API with retry logic
     */
    protected function call_gemini_api($prompt, $max_retries = 3) {
        $settings = get_option('aca_settings', array());
        $api_key = isset($settings['gemini_api_key']) ? $settings['gemini_api_key'] : '';
        
        if (empty($api_key)) {
            return array('error' => 'Gemini API key not configured');
        }
        
        $models = array('gemini-2.0-flash-exp', 'gemini-1.5-pro');
        $attempt = 0;
        
        while ($attempt < $max_retries) {
            $model = $models[$attempt % count($models)];
            
            $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . $api_key, array(
                'timeout' => 120,
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode(array(
                    'contents' => array(
                        array(
                            'parts' => array(
                                array('text' => $prompt)
                            )
                        )
                    ),
                    'generationConfig' => array(
                        'maxOutputTokens' => 4096,
                        'temperature' => 0.7
                    )
                ))
            ));
            
            if (is_wp_error($response)) {
                $attempt++;
                if ($attempt < $max_retries) {
                    sleep(pow(2, $attempt)); // Exponential backoff
                    continue;
                }
                return array('error' => 'Network error: ' . $response->get_error_message());
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            $status_code = wp_remote_retrieve_response_code($response);
            
            if ($status_code === 503 || $status_code === 429) {
                $attempt++;
                if ($attempt < $max_retries) {
                    sleep(pow(2, $attempt)); // Exponential backoff
                    continue;
                }
                return array('error' => 'Service temporarily overloaded. Please try again later.');
            }
            
            if ($status_code !== 200) {
                return array('error' => 'API error: ' . $status_code);
            }
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                return array('success' => true, 'content' => $data['candidates'][0]['content']['parts'][0]['text']);
            }
            
            return array('error' => 'Invalid API response');
        }
        
        return array('error' => 'Maximum retry attempts exceeded');
    }
    
    /**
     * Validate and sanitize request data
     */
    protected function validate_request_data($request, $required_fields = array()) {
        $data = array();
        $errors = array();
        
        foreach ($required_fields as $field) {
            if (!$request->has_param($field)) {
                $errors[] = "Missing required field: {$field}";
            } else {
                $data[$field] = sanitize_text_field($request->get_param($field));
            }
        }
        
        if (!empty($errors)) {
            return new WP_Error('validation_failed', implode(', ', $errors), array('status' => 400));
        }
        
        return $data;
    }
}