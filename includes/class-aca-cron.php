<?php
/**
 * ACA - AI Content Agent
 *
 * Handles scheduled events (WP-Cron).
 *
 * @package ACA
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Cron {

    public function __construct() {
        add_action('init', [$this, 'schedule_events']);
        add_action('aca_run_main_automation', [$this, 'run_main_automation']);
        add_action('aca_reset_api_usage_counter', [$this, 'reset_api_usage_counter']);

        // Hook to reschedule events when settings are saved.
        add_action('update_option_aca_options', [$this, 'schedule_events']);
    }

    /**
     * Schedule or clear cron events based on settings.
     */
    public function schedule_events() {
        // Clear any existing hooks to ensure we're using the latest settings.
        wp_clear_scheduled_hook('aca_run_main_automation');

        $options = get_option('aca_options');
        $working_mode = $options['working_mode'] ?? 'manual';
        $frequency = $options['automation_frequency'] ?? 'daily';

        // Only schedule the main automation if the mode is not manual.
        if ($working_mode !== 'manual') {
            if (!wp_next_scheduled('aca_run_main_automation')) {
                wp_schedule_event(time(), $frequency, 'aca_run_main_automation');
            }
        }

        // Schedule the monthly API counter reset task, regardless of mode.
        if (!wp_next_scheduled('aca_reset_api_usage_counter')) {
            wp_schedule_event(time(), 'monthly', 'aca_reset_api_usage_counter');
        }
    }

    /**
     * The main callback for the automation cron job.
     */
    public function run_main_automation() {
        $options = get_option('aca_options');
        $working_mode = $options['working_mode'] ?? 'manual';

        if ($working_mode === 'semi-auto') {
            // In semi-auto mode, only generate ideas.
            ACA_Core::generate_ideas();
        } elseif ($working_mode === 'full-auto') {
            // In full-auto mode, generate ideas and then write drafts.
            $ideas = ACA_Core::generate_ideas();
            if (!is_wp_error($ideas) && !empty($ideas)) {
                // Respect the generation limit.
                $limit = $options['generation_limit'] ?? 1;
                $ideas_to_write = array_slice($ideas, 0, $limit);

                foreach ($ideas_to_write as $idea_title) {
                    ACA_Core::write_post_draft($idea_title);
                }
            }
        }
    }

    /**
     * Reset the API usage counter.
     */
    public function reset_api_usage_counter() {
        update_option('aca_api_usage_current_month', 0);
    }
}