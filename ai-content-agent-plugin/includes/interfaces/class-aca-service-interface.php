<?php
/**
 * Service Interface for AI Content Agent
 * 
 * Defines the contract for all service classes in the plugin
 * following SOLID principles and dependency injection patterns.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Base Service Interface
 */
interface ACA_Service_Interface {
    
    /**
     * Initialize the service
     * 
     * @return bool Success status
     */
    public function initialize();
    
    /**
     * Get service name
     * 
     * @return string
     */
    public function get_name();
    
    /**
     * Get service version
     * 
     * @return string
     */
    public function get_version();
    
    /**
     * Check if service is available
     * 
     * @return bool
     */
    public function is_available();
}

/**
 * API Service Interface
 */
interface ACA_API_Service_Interface extends ACA_Service_Interface {
    
    /**
     * Make API request
     * 
     * @param string $endpoint
     * @param array $data
     * @param string $method
     * @return array|WP_Error
     */
    public function make_request($endpoint, $data = [], $method = 'POST');
    
    /**
     * Get API key
     * 
     * @return string
     */
    public function get_api_key();
    
    /**
     * Validate API key
     * 
     * @return bool
     */
    public function validate_api_key();
}

/**
 * AI Service Interface
 */
interface ACA_AI_Service_Interface extends ACA_API_Service_Interface {
    
    /**
     * Generate content
     * 
     * @param array $parameters
     * @return array|WP_Error
     */
    public function generate_content($parameters);
    
    /**
     * Analyze content
     * 
     * @param string $content
     * @param array $parameters
     * @return array|WP_Error
     */
    public function analyze_content($content, $parameters = []);
    
    /**
     * Get supported models
     * 
     * @return array
     */
    public function get_supported_models();
}

/**
 * Cache Service Interface
 */
interface ACA_Cache_Service_Interface extends ACA_Service_Interface {
    
    /**
     * Get cached data
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null);
    
    /**
     * Set cached data
     * 
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function set($key, $value, $expiration = 3600);
    
    /**
     * Delete cached data
     * 
     * @param string $key
     * @return bool
     */
    public function delete($key);
    
    /**
     * Clear all cache
     * 
     * @return bool
     */
    public function clear();
}

/**
 * Database Service Interface
 */
interface ACA_Database_Service_Interface extends ACA_Service_Interface {
    
    /**
     * Insert data
     * 
     * @param string $table
     * @param array $data
     * @return int|WP_Error Insert ID or error
     */
    public function insert($table, $data);
    
    /**
     * Update data
     * 
     * @param string $table
     * @param array $data
     * @param array $where
     * @return bool|WP_Error
     */
    public function update($table, $data, $where);
    
    /**
     * Delete data
     * 
     * @param string $table
     * @param array $where
     * @return bool|WP_Error
     */
    public function delete($table, $where);
    
    /**
     * Select data
     * 
     * @param string $table
     * @param array $where
     * @param array $options
     * @return array|WP_Error
     */
    public function select($table, $where = [], $options = []);
}

/**
 * Logger Service Interface
 */
interface ACA_Logger_Service_Interface extends ACA_Service_Interface {
    
    /**
     * Log info message
     * 
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function info($message, $context = []);
    
    /**
     * Log error message
     * 
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function error($message, $context = []);
    
    /**
     * Log warning message
     * 
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function warning($message, $context = []);
    
    /**
     * Log debug message
     * 
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function debug($message, $context = []);
}

/**
 * Validation Service Interface
 */
interface ACA_Validation_Service_Interface extends ACA_Service_Interface {
    
    /**
     * Validate data against rules
     * 
     * @param array $data
     * @param array $rules
     * @return bool|WP_Error
     */
    public function validate($data, $rules);
    
    /**
     * Sanitize data
     * 
     * @param array $data
     * @param array $rules
     * @return array
     */
    public function sanitize($data, $rules);
    
    /**
     * Get validation errors
     * 
     * @return array
     */
    public function get_errors();
}