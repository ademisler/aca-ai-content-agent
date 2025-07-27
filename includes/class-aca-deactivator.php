<?php

/**
 * Fired during plugin deactivation
 */
class ACA_Deactivator {

    /**
     * Plugin deactivation handler
     */
    public static function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('aca_auto_generate_content');
        wp_clear_scheduled_hook('aca_auto_publish_content');
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}