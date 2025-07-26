<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */

/**
 * The deactivator.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/core
 */
class ACA_Deactivator {

	/**
	 * The main deactivation method.
	 *
	 * Clears all scheduled cron jobs and action scheduler tasks
	 * to prevent orphaned processes after plugin deactivation.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public static function deactivate() {
        // Clear all scheduled actions for the plugin
        if ( function_exists('as_unschedule_all_actions') ) {
            as_unschedule_all_actions( 'aca_ai_content_agent_run_main_automation' );
            as_unschedule_all_actions( 'aca_ai_content_agent_reset_api_usage_counter' );
            as_unschedule_all_actions( 'aca_ai_content_agent_generate_style_guide' );
            as_unschedule_all_actions( 'aca_ai_content_agent_verify_license' );
            as_unschedule_all_actions( 'aca_ai_content_agent_clean_logs' );
        }

        // Also clear WP Cron schedules as a fallback
        wp_clear_scheduled_hook( 'aca_ai_content_agent_run_main_automation' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_reset_api_usage_counter' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_generate_style_guide' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_verify_license' );
        wp_clear_scheduled_hook( 'aca_ai_content_agent_clean_logs' );

        
	}

}