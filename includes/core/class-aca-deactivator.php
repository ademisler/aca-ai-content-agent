<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 * @author     Your Name <email@example.com>
 */
class ACA_Deactivator {

	/**
	 * The main deactivation method.
	 *
	 * @since    1.2.0
	 */
	public static function deactivate() {
        // Clear all scheduled actions for the plugin
        as_unschedule_all_actions( 'aca_ai_content_agent_run_main_automation' );
        as_unschedule_all_actions( 'aca_ai_content_agent_reset_api_usage_counter' );
        as_unschedule_all_actions( 'aca_ai_content_agent_generate_style_guide' );
        as_unschedule_all_actions( 'aca_ai_content_agent_verify_license' );
        as_unschedule_all_actions( 'aca_ai_content_agent_clean_logs' );

        // Also clear WP Cron schedules as a fallback
        wp_clear_scheduled_hook( 'aca_ai_content_agent_run_main_automation' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_reset_api_usage_counter' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_generate_style_guide' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_verify_license' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_clean_logs' );

        // Remove capabilities upon deactivation
        $role = get_role( 'administrator' );
        if ( $role ) {
            $role->remove_cap( 'manage_aca_ai_content_agent_settings' );
            $role->remove_cap( 'view_aca_ai_content_agent_dashboard' );
        }
	}

}