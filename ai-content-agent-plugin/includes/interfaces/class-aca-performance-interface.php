<?php
/**
 * Performance Interface for AI Content Agent
 * 
 * @package AI_Content_Agent
 * @version 2.3.14
 * @since 2.3.14
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Performance Monitoring Interface
 */
interface ACA_Performance_Interface {
    
    /**
     * Start performance tracking
     * 
     * @param string $operation_name
     * @return string Tracking ID
     */
    public static function start($operation_name);
    
    /**
     * End performance tracking
     * 
     * @param string $tracking_id
     * @param array $additional_data
     * @return array Performance metrics
     */
    public static function end($tracking_id, $additional_data = []);
    
    /**
     * Get performance statistics
     * 
     * @return array
     */
    public static function get_stats();
    
    /**
     * Clean up performance data
     * 
     * @return void
     */
    public static function cleanup();
}
