<?php
/**
 * Plugin deactivation functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Deactivator {
    
    /**
     * Plugin deactivation
     */
    public static function deactivate() {
        self::clear_scheduled_hooks();
    }
    
    /**
     * Clear scheduled cron hooks
     */
    private static function clear_scheduled_hooks() {
        wp_clear_scheduled_hook('aca_thirty_minute_event');
        wp_clear_scheduled_hook('aca_fifteen_minute_event');
    }
}