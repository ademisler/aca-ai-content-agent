<?php
/**
 * Cleanup Interface for AI Content Agent
 * 
 * @package AI_Content_Agent
 * @version 2.3.14
 * @since 2.3.14
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cleanup Interface for classes that need cleanup functionality
 */
interface ACA_Cleanup_Interface {
    
    /**
     * Clean up class data and resources
     * 
     * @return void
     */
    public static function cleanup();
    
    /**
     * Get cleanup status or summary
     * 
     * @return array
     */
    public static function get_cleanup_status();
}
