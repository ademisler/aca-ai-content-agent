<?php
/**
 * ACA - AI Content Agent
 *
 * Handles scheduled events using Action Scheduler when available.
 * This class manages all automated tasks including content generation,
 * license verification, log cleanup, and style guide updates.
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * The cron job handler.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes
 */

/**
 * Cron job handler for ACA AI Content Agent.
 *
 * Manages scheduled tasks including main automation, API usage reset,
 * license verification, log cleanup, and style guide updates.
 *
 * @since 1.2.0
 */
class ACA_Cron {

    /**
     * Default log retention days.
     *
     * @since 1.2.0
     * @var int
     */
    const DEFAULT_LOG_RETENTION_DAYS = 30;

    /**
     * Initialize the cron handler.
     *
     * @since 1.2.0
     */
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Register WordPress hooks for cron functionality.
     *
     * @since 1.2.0
     */
    private function register_hooks() {
        // Register custom cron schedules
        add_filter('cron_schedules', [$this, 'add_custom_schedules']);

        // Register cron event handlers
        add_action('aca_ai_content_agent_main_automation', [$this, 'run_main_automation']);
        add_action('aca_ai_content_agent_reset_api_usage', [$this, 'reset_api_usage_counter']);
        add_action('aca_ai_content_agent_generate_style_guide', [$this, 'generate_style_guide']);
        add_action('aca_ai_content_agent_verify_license', [$this, 'verify_license']);
        add_action('aca_ai_content_agent_clean_logs', [$this, 'clean_logs']);

        // Schedule events on plugin activation
        add_action('aca_ai_content_agent_activate', [$this, 'schedule_events']);
    }

    /**
     * Schedule all cron events.
     *
     * @since 1.2.0
     */
    public function schedule_events() {
        // Check if Action Scheduler is available
        if (!class_exists('ActionScheduler')) {
            // Fallback to WordPress cron
            $this->schedule_wordpress_cron_events();
        } else {
            // Use Action Scheduler for better reliability
            $this->schedule_action_scheduler_events();
        }
    }

    /**
     * Schedule events using WordPress cron (fallback).
     *
     * @since 1.3.0
     */
    private function schedule_wordpress_cron_events() {
        $options = $this->get_scheduling_options();
        $intervals = $this->calculate_intervals($options);

        // Schedule main automation
        $this->schedule_main_automation($options['working_mode'], $intervals['main_automation']);

        // Schedule API counter reset
        $this->schedule_api_counter_reset();

        // Schedule license verification
        $this->schedule_license_verification();

        // Schedule style guide generation
        $this->schedule_style_guide_generation($options['style_guide_frequency'], $intervals['style_guide']);

        // Schedule log cleanup
        $this->schedule_log_cleanup($options['auto_cleanup_logs'], $options['log_retention_days']);
    }

    /**
     * Schedule events using Action Scheduler.
     *
     * @since 1.3.0
     */
    private function schedule_action_scheduler_events() {
        $options = $this->get_scheduling_options();
        $intervals = $this->calculate_intervals($options);

        // Schedule main automation
        $this->schedule_action_scheduler_main_automation($options['working_mode'], $intervals['main_automation']);

        // Schedule API counter reset
        $this->schedule_action_scheduler_api_counter_reset();

        // Schedule license verification
        $this->schedule_action_scheduler_license_verification();

        // Schedule style guide generation
        $this->schedule_action_scheduler_style_guide_generation($options['style_guide_frequency'], $intervals['style_guide']);

        // Schedule log cleanup
        $this->schedule_action_scheduler_log_cleanup($options['auto_cleanup_logs'], $options['log_retention_days']);
    }

    /**
     * Get scheduling options from plugin settings.
     *
     * @since 1.2.0
     * @return array Scheduling options.
     */
    private function get_scheduling_options() {
        $options = get_option('aca_ai_content_agent_options', []);
        
        return [
            'working_mode' => $options['working_mode'] ?? 'manual',
            'style_guide_frequency' => $options['style_guide_frequency'] ?? 'weekly',
            'auto_cleanup_logs' => $options['auto_cleanup_logs'] ?? false,
            'log_retention_days' => $options['log_retention_days'] ?? self::DEFAULT_LOG_RETENTION_DAYS,
        ];
    }

    /**
     * Calculate intervals based on options.
     *
     * @since 1.2.0
     * @param array $options Scheduling options.
     * @return array Calculated intervals.
     */
    private function calculate_intervals($options) {
        $intervals = [
            'main_automation' => 'daily',
            'style_guide' => 'weekly',
        ];

        // Adjust main automation based on working mode
        if ($options['working_mode'] === 'semi_automated') {
            $intervals['main_automation'] = 'twice_daily';
        } elseif ($options['working_mode'] === 'fully_automated') {
            $intervals['main_automation'] = 'hourly';
        }

        // Adjust style guide frequency
        if ($options['style_guide_frequency'] === 'daily') {
            $intervals['style_guide'] = 'daily';
        } elseif ($options['style_guide_frequency'] === 'monthly') {
            $intervals['style_guide'] = 'monthly';
        }

        return $intervals;
    }

    /**
     * Schedule main automation task.
     *
     * @since 1.2.0
     * @param string $working_mode The working mode.
     * @param string $interval The interval for scheduling.
     */
    private function schedule_main_automation($working_mode, $interval) {
        $hook = 'aca_ai_content_agent_main_automation';
        
        // Only schedule if not manual mode
        if ($working_mode !== 'manual') {
            $this->schedule_recurring_action($hook, time(), $interval);
        } else {
            $this->unschedule_action($hook);
        }
    }

    /**
     * Schedule API counter reset.
     *
     * @since 1.2.0
     */
    private function schedule_api_counter_reset() {
        $hook = 'aca_ai_content_agent_reset_api_usage';
        
        // Schedule for first day of each month at 00:00
        $next_month = strtotime('first day of next month 00:00:00');
        $this->schedule_recurring_action($hook, $next_month, 'monthly');
    }

    /**
     * Schedule license verification.
     *
     * @since 1.2.0
     */
    private function schedule_license_verification() {
        $hook = 'aca_ai_content_agent_verify_license';
        
        // Schedule for every 6 hours
        $this->schedule_recurring_action($hook, time(), 'six_hours');
    }

    /**
     * Schedule style guide generation.
     *
     * @since 1.2.0
     * @param string $style_freq The frequency for style guide generation.
     * @param string $interval The interval for scheduling.
     */
    private function schedule_style_guide_generation($style_freq, $interval) {
        $hook = 'aca_ai_content_agent_generate_style_guide';
        
        if ($style_freq !== 'never') {
            $this->schedule_recurring_action($hook, time(), $interval);
        } else {
            $this->unschedule_action($hook);
        }
    }

    /**
     * Schedule log cleanup.
     *
     * @since 1.2.0
     * @param bool $cleanup_enabled Whether cleanup is enabled.
     * @param int $retention Retention days.
     */
    private function schedule_log_cleanup($cleanup_enabled, $retention) {
        $hook = 'aca_ai_content_agent_clean_logs';
        
        if ($cleanup_enabled) {
            // Schedule for daily at 02:00
            $next_run = strtotime('tomorrow 02:00:00');
            $this->schedule_recurring_action($hook, $next_run, 'daily', [$retention]);
        } else {
            $this->unschedule_action($hook);
        }
    }

    /**
     * Schedule a recurring action.
     *
     * @since 1.2.0
     * @param string $hook The hook name.
     * @param int $start_time The start time.
     * @param string $interval The interval.
     * @param array $args Optional arguments.
     */
    private function schedule_recurring_action($hook, $start_time, $interval, $args = []) {
        if (!wp_next_scheduled($hook)) {
            wp_schedule_event($start_time, $interval, $hook, $args);
        }
    }

    /**
     * Unschedule an action.
     *
     * @since 1.2.0
     * @param string $hook The hook name.
     */
    private function unschedule_action($hook) {
        $timestamp = wp_next_scheduled($hook);
        if ($timestamp) {
            wp_unschedule_event($timestamp, $hook);
        }
    }

    /**
     * Get WordPress schedule name from interval.
     *
     * @since 1.2.0
     * @param string $interval The interval name.
     * @return string WordPress schedule name.
     */
    private function get_wp_schedule_name($interval) {
        $schedules = [
            'hourly' => 'hourly',
            'twice_daily' => 'twice_daily',
            'daily' => 'daily',
            'weekly' => 'weekly',
            'monthly' => 'monthly',
            'six_hours' => 'six_hours',
        ];

        return $schedules[$interval] ?? 'daily';
    }

    /**
     * Run the main automation task.
     *
     * @since 1.2.0
     */
    public function run_main_automation() {
        $options = get_option('aca_ai_content_agent_options', []);
        $working_mode = $options['working_mode'] ?? 'manual';

        if ($working_mode === 'semi_automated') {
            $this->run_semi_automation();
        } elseif ($working_mode === 'fully_automated') {
            $this->run_full_automation();
        }
    }

    /**
     * Run semi-automated mode.
     *
     * @since 1.2.0
     */
    private function run_semi_automation() {
        // Generate ideas only
        ACA_Idea_Service::generate_ideas();
    }

    /**
     * Run fully automated mode.
     *
     * @since 1.2.0
     */
    private function run_full_automation() {
        // Generate ideas and create drafts
        $ideas = ACA_Idea_Service::generate_ideas();
        
        if (!is_wp_error($ideas) && !empty($ideas)) {
            // Create drafts for the first few ideas
            $draft_count = 0;
            $max_drafts = 3;
            
            foreach ($ideas as $idea_id) {
                if ($draft_count >= $max_drafts) {
                    break;
                }
                
                ACA_Draft_Service::write_post_draft($idea_id);
                $draft_count++;
            }
        }
    }

    /**
     * Reset API usage counters.
     *
     * @since 1.2.0
     */
    public function reset_api_usage_counter() {
        delete_transient('aca_ai_content_agent_monthly_idea_count');
        delete_transient('aca_ai_content_agent_monthly_draft_count');
        
        // Reset hourly and per-minute counters
        delete_transient('aca_ai_content_agent_hourly_api_calls');
        delete_transient('aca_ai_content_agent_minute_api_calls');
        
        if (class_exists('ACA_Log_Service')) {
            ACA_Log_Service::info('API usage counters reset for new month');
        }
    }

    /**
     * Generate style guide.
     *
     * @since 1.2.0
     */
    public function generate_style_guide() {
        ACA_Style_Guide_Service::generate_style_guide();
    }

    /**
     * Verify license status in the background.
     *
     * Checks license validity and updates status accordingly.
     *
     * @since 1.2.0
     */
    public function verify_license() {
        if (function_exists('ACA_Helper::maybe_check_license')) {
            ACA_Helper::maybe_check_license(true);
        }
    }

    /**
     * Clean old log entries.
     *
     * @since 1.2.0
     * @param int $retention_days Number of days to retain logs.
     */
    public function clean_logs($retention_days = self::DEFAULT_LOG_RETENTION_DAYS) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_logs';
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$retention_days} days"));
        
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$table_name} WHERE timestamp < %s",
            $cutoff_date
        ));
        
        if ($deleted !== false && class_exists('ACA_Log_Service')) {
            ACA_Log_Service::info("Cleaned {$deleted} old log entries");
        }
    }

    /**
     * Add custom cron schedules.
     *
     * @since 1.2.0
     * @param array $schedules Existing schedules.
     * @return array Modified schedules.
     */
    public function add_custom_schedules($schedules) {
        $schedules['six_hours'] = [
            'interval' => 6 * HOUR_IN_SECONDS,
            'display' => __('Every 6 Hours', 'aca-ai-content-agent')
        ];
        
        $schedules['monthly'] = [
            'interval' => 30 * DAY_IN_SECONDS,
            'display' => __('Monthly', 'aca-ai-content-agent')
        ];
        
        return $schedules;
    }

    /**
     * Schedule main automation using Action Scheduler.
     *
     * @since 1.3.0
     * @param string $working_mode Working mode.
     * @param int $interval Interval in seconds.
     */
    private function schedule_action_scheduler_main_automation($working_mode, $interval) {
        if (!class_exists('ActionScheduler')) {
            return;
        }

        $hook = 'aca_ai_content_agent_main_automation';
        $args = array('working_mode' => $working_mode);
        
        // Clear existing actions
        ActionScheduler::unschedule_all_actions($hook, $args);
        
        // Schedule new action
        ActionScheduler::schedule_recurring_action(
            time(),
            $interval,
            $hook,
            $args,
            'aca-ai-content-agent'
        );
    }

    /**
     * Schedule API counter reset using Action Scheduler.
     *
     * @since 1.3.0
     */
    private function schedule_action_scheduler_api_counter_reset() {
        if (!class_exists('ActionScheduler')) {
            return;
        }

        $hook = 'aca_ai_content_agent_reset_api_usage';
        
        // Clear existing actions
        ActionScheduler::unschedule_all_actions($hook);
        
        // Schedule for first day of each month
        $next_month = strtotime('first day of next month 00:00:00');
        ActionScheduler::schedule_single_action(
            $next_month,
            $hook,
            array(),
            'aca-ai-content-agent'
        );
    }

    /**
     * Schedule license verification using Action Scheduler.
     *
     * @since 1.3.0
     */
    private function schedule_action_scheduler_license_verification() {
        if (!class_exists('ActionScheduler')) {
            return;
        }

        $hook = 'aca_ai_content_agent_verify_license';
        
        // Clear existing actions
        ActionScheduler::unschedule_all_actions($hook);
        
        // Schedule daily
        ActionScheduler::schedule_recurring_action(
            time(),
            DAY_IN_SECONDS,
            $hook,
            array(),
            'aca-ai-content-agent'
        );
    }

    /**
     * Schedule style guide generation using Action Scheduler.
     *
     * @since 1.3.0
     * @param string $frequency Frequency setting.
     * @param int $interval Interval in seconds.
     */
    private function schedule_action_scheduler_style_guide_generation($frequency, $interval) {
        if (!class_exists('ActionScheduler')) {
            return;
        }

        $hook = 'aca_ai_content_agent_generate_style_guide';
        
        // Clear existing actions
        ActionScheduler::unschedule_all_actions($hook);
        
        if ($frequency !== 'never') {
            ActionScheduler::schedule_recurring_action(
                time(),
                $interval,
                $hook,
                array(),
                'aca-ai-content-agent'
            );
        }
    }

    /**
     * Schedule log cleanup using Action Scheduler.
     *
     * @since 1.3.0
     * @param bool $cleanup_enabled Whether cleanup is enabled.
     * @param int $retention_days Retention period in days.
     */
    private function schedule_action_scheduler_log_cleanup($cleanup_enabled, $retention_days) {
        if (!class_exists('ActionScheduler')) {
            return;
        }

        $hook = 'aca_ai_content_agent_clean_logs';
        $args = array('retention_days' => $retention_days);
        
        // Clear existing actions
        ActionScheduler::unschedule_all_actions($hook, $args);
        
        if ($cleanup_enabled) {
            ActionScheduler::schedule_recurring_action(
                time(),
                WEEK_IN_SECONDS,
                $hook,
                $args,
                'aca-ai-content-agent'
            );
        }
    }

    /**
     * Output cron diagnostics for the diagnostics page.
     *
     * Displays the status of all scheduled tasks for debugging purposes.
     *
     * @since 1.2.0
     */
    public static function output_cron_diagnostics() {
        $hooks = [
            'aca_ai_content_agent_main_automation',
            'aca_ai_content_agent_reset_api_usage',
            'aca_ai_content_agent_verify_license',
            'aca_ai_content_agent_generate_style_guide',
            'aca_ai_content_agent_clean_logs',
        ];

        echo '<h3>' . esc_html__('Cron Job Status', 'aca-ai-content-agent') . '</h3>';
        echo '<table class="widefat">';
        echo '<thead><tr><th>' . esc_html__('Hook', 'aca-ai-content-agent') . '</th><th>' . esc_html__('Next Run', 'aca-ai-content-agent') . '</th><th>' . esc_html__('Status', 'aca-ai-content-agent') . '</th></tr></thead>';
        echo '<tbody>';

        foreach ($hooks as $hook) {
            $next_run = wp_next_scheduled($hook);
            $status = $next_run ? 'Scheduled' : 'Not Scheduled';
            $next_run_formatted = $next_run ? date('Y-m-d H:i:s', $next_run) : 'N/A';
            
            echo '<tr>';
            echo '<td>' . esc_html($hook) . '</td>';
            echo '<td>' . esc_html($next_run_formatted) . '</td>';
            echo '<td>' . esc_html($status) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }
}
