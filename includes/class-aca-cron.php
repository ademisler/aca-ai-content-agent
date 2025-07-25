<?php
/**
 * ACA - AI Content Agent
 *
 * Handles scheduled events using Action Scheduler when available.
 *
 * @package ACA_AI_Content_Agent
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_AI_Content_Agent_Cron {

    public function __construct() {
        add_action('init', [$this, 'schedule_events']);
        add_action('aca_ai_content_agent_run_main_automation', [$this, 'run_main_automation']);
        add_action('aca_ai_content_agent_reset_api_usage_counter', [$this, 'reset_api_usage_counter']);
        add_action('aca_ai_content_agent_generate_style_guide', [$this, 'generate_style_guide']);
        add_action('aca_ai_content_agent_verify_license', [$this, 'verify_license']);
        add_action('aca_ai_content_agent_clean_logs', [$this, 'clean_logs'], 10, 1);

        // Ensure custom schedules like weekly and monthly are available.
        add_filter('cron_schedules', [$this, 'add_custom_schedules']);

        // Hook to reschedule events when settings are saved.
        add_action('update_option_aca_ai_content_agent_options', [$this, 'schedule_events']);
    }

    /**
     * Schedule or clear cron events based on settings.
     */
    public function schedule_events() {
        $options = get_option('aca_ai_content_agent_options');
        $working_mode = $options['working_mode'] ?? 'manual';
        $frequency = $options['automation_frequency'] ?? 'daily';
        $style_freq = $options['style_guide_frequency'] ?? 'weekly';
        $cleanup_enabled = ! empty( $options['log_cleanup_enabled'] );
        $retention = isset( $options['log_retention_days'] ) ? absint( $options['log_retention_days'] ) : 60;

        // Determine interval seconds from WP schedules.
        $schedules = wp_get_schedules();
        $interval  = isset( $schedules[ $frequency ] ) ? intval( $schedules[ $frequency ]['interval'] ) : DAY_IN_SECONDS;
        $style_interval = isset( $schedules[ $style_freq ] ) ? intval( $schedules[ $style_freq ]['interval'] ) : WEEK_IN_SECONDS;

        // Only schedule the main automation if the mode is not manual.
        if ( $working_mode !== 'manual' ) {
            if ( function_exists( 'as_next_scheduled_action' ) ) {
                if ( ! as_next_scheduled_action( 'aca_ai_content_agent_run_main_automation' ) ) {
                    as_schedule_recurring_action( time(), $interval, 'aca_ai_content_agent_run_main_automation' );
                }
            } elseif ( ! wp_next_scheduled( 'aca_ai_content_agent_run_main_automation' ) ) {
                wp_schedule_event( time(), $frequency, 'aca_ai_content_agent_run_main_automation' );
            }
        } else {
            if ( function_exists( 'as_unschedule_all_actions' ) ) {
                as_unschedule_all_actions( 'aca_ai_content_agent_run_main_automation' );
            } else {
                wp_clear_scheduled_hook( 'aca_ai_content_agent_run_main_automation' );
            }
        }

        // Schedule the monthly API counter reset task, regardless of mode.
        if ( function_exists( 'as_next_scheduled_action' ) ) {
            if ( ! as_next_scheduled_action( 'aca_ai_content_agent_reset_api_usage_counter' ) ) {
                as_schedule_recurring_action( strtotime( 'first day of next month' ), MONTH_IN_SECONDS, 'aca_ai_content_agent_reset_api_usage_counter' );
            }
        } elseif ( ! wp_next_scheduled( 'aca_ai_content_agent_reset_api_usage_counter' ) ) {
            wp_schedule_event( strtotime( 'first day of next month' ), 'monthly', 'aca_ai_content_agent_reset_api_usage_counter' );
        }

        // Schedule weekly license verification
        if ( function_exists( 'as_next_scheduled_action' ) ) {
            if ( ! as_next_scheduled_action( 'aca_ai_content_agent_verify_license' ) ) {
                as_schedule_recurring_action( time(), WEEK_IN_SECONDS, 'aca_ai_content_agent_verify_license' );
            }
        } elseif ( ! wp_next_scheduled( 'aca_ai_content_agent_verify_license' ) ) {
            wp_schedule_event( time(), 'weekly', 'aca_ai_content_agent_verify_license' );
        }

        // Schedule style guide generation if enabled
        if ( $style_freq !== 'manual' ) {
            if ( function_exists( 'as_next_scheduled_action' ) ) {
                if ( ! as_next_scheduled_action( 'aca_ai_content_agent_generate_style_guide' ) ) {
                    as_schedule_recurring_action( time(), $style_interval, 'aca_ai_content_agent_generate_style_guide' );
                }
            } elseif ( ! wp_next_scheduled( 'aca_ai_content_agent_generate_style_guide' ) ) {
                wp_schedule_event( time(), $style_freq, 'aca_ai_content_agent_generate_style_guide' );
            }
        } else {
            if ( function_exists( 'as_unschedule_all_actions' ) ) {
                as_unschedule_all_actions( 'aca_ai_content_agent_generate_style_guide' );
            } else {
                wp_clear_scheduled_hook( 'aca_ai_content_agent_generate_style_guide' );
            }
        }

        // Schedule log cleanup if enabled
        if ( $cleanup_enabled ) {
            if ( function_exists( 'as_next_scheduled_action' ) ) {
                if ( ! as_next_scheduled_action( 'aca_ai_content_agent_clean_logs' ) ) {
                    as_schedule_recurring_action( time(), WEEK_IN_SECONDS, 'aca_ai_content_agent_clean_logs', [ $retention ] );
                }
            } elseif ( ! wp_next_scheduled( 'aca_ai_content_agent_clean_logs' ) ) {
                wp_schedule_event( time(), 'weekly', 'aca_ai_content_agent_clean_logs', [ $retention ] );
            }
        } else {
            if ( function_exists( 'as_unschedule_all_actions' ) ) {
                as_unschedule_all_actions( 'aca_ai_content_agent_clean_logs' );
            } else {
                wp_clear_scheduled_hook( 'aca_ai_content_agent_clean_logs' );
            }
        }
    }

    /**
     * The main callback for the automation cron job.
     */
    public function run_main_automation() {
        $options = get_option('aca_ai_content_agent_options');
        $working_mode = $options['working_mode'] ?? 'manual';

        if ($working_mode === 'semi-auto') {
            // In semi-auto mode, only generate ideas.
            ACA_Idea_Service::generate_ideas();
        } elseif ($working_mode === 'full-auto') {
            // In full-auto mode, generate ideas and then write drafts.
            $idea_ids = ACA_Idea_Service::generate_ideas();
            if (!is_wp_error($idea_ids) && !empty($idea_ids)) {
                foreach ($idea_ids as $idea_id) {
                    ACA_Draft_Service::write_post_draft($idea_id);
                }
            }
        }
    }

    /**
     * Reset the API usage counter.
     */
    public function reset_api_usage_counter() {
        update_option('aca_ai_content_agent_api_usage_current_month', 0);
        update_option('aca_ai_content_agent_idea_count_current_month', 0);
        update_option('aca_ai_content_agent_draft_count_current_month', 0);
    }

    /**
     * Generate style guide periodically.
     */
    public function generate_style_guide() {
        ACA_Style_Guide_Service::generate_style_guide();
    }

    /**
     * Verify license status in the background.
     */
    public function verify_license() {
        if ( function_exists( 'ACA_Helper::maybe_check_license' ) ) {
            ACA_Helper::maybe_check_license( true );
        }
    }

    /**
     * Clean old log entries from the database.
     *
     * @param int $retention_days Number of days to keep logs.
     */
    public function clean_logs( $retention_days = 60 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $cutoff = gmdate( 'Y-m-d H:i:s', strtotime( '-' . absint( $retention_days ) . ' days' ) );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE timestamp < %s", $cutoff ) );
    }

    /**
     * Register additional cron schedules used by the plugin.
     *
     * @param array $schedules Existing schedules.
     * @return array Modified schedules array.
     */
    public function add_custom_schedules($schedules) {
        if (!isset($schedules['weekly'])) {
            $schedules['weekly'] = [
                'interval' => WEEK_IN_SECONDS,
                'display'  => __('Once Weekly', 'aca-ai-content-agent'),
            ];
        }

        if (!isset($schedules['monthly'])) {
            $schedules['monthly'] = [
                'interval' => MONTH_IN_SECONDS,
                'display'  => __('Once Monthly', 'aca-ai-content-agent'),
            ];
        }

        return $schedules;
    }
}
