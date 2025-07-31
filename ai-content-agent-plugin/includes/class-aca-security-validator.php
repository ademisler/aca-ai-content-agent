<?php
/**
 * Security Validator Class
 * 
 * @package AI_Content_Agent
 * @since 2.3.14
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class ACA_Security_Validator
 * 
 * Handles security validation for the plugin
 */
class ACA_Security_Validator {
    
    /**
     * Validate API key format and basic security
     * 
     * @param string $key The API key to validate
     * @return bool True if valid format, false otherwise
     */
    public static function validate_api_key($key) {
        // Basic validation - not empty and reasonable length
        if (empty($key) || strlen($key) < 10) {
            return false;
        }
        
        // Check for common invalid patterns
        if (strpos($key, ' ') !== false || strpos($key, '<') !== false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Sanitize input data
     * 
     * @param mixed $data Data to sanitize
     * @return mixed Sanitized data
     */
    public static function sanitize_input($data) {
        if (is_string($data)) {
            return sanitize_text_field($data);
        } elseif (is_array($data)) {
            return array_map(array(self::class, 'sanitize_input'), $data);
        }
        
        return $data;
    }
}
