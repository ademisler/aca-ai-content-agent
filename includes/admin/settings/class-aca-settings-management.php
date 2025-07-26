<?php
/**
 * ACA - AI Content Agent
 *
 * Management Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the registration and rendering of management and cost control settings fields.
 *
 * @since 1.2.0
 */
class ACA_Settings_Management {

    /**
     * Constructor.
     *
     * Registers the settings section and fields for management configuration.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register management settings section and fields.
     *
     * @since 1.2.0
     */
    public function register_settings() {
        add_settings_section(
            'aca_ai_content_agent_management_settings_section',
            esc_html__( 'Management and Cost Control', 'aca-ai-content-agent' ),
            null,
            'aca-ai-content-agent'
        );

        add_settings_field(
            'aca_ai_content_agent_api_monthly_limit',
            esc_html__( 'Monthly API Limit', 'aca-ai-content-agent' ),
            [ $this, 'render_api_monthly_limit_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_management_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_api_usage_display',
            esc_html__( 'Current API Usage', 'aca-ai-content-agent' ),
            [ $this, 'render_api_usage_display_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_management_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_log_cleanup_enabled',
            esc_html__( 'Enable Log Cleanup', 'aca-ai-content-agent' ),
            [ $this, 'render_log_cleanup_enabled_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_management_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_log_retention_days',
            esc_html__( 'Log Retention (days)', 'aca-ai-content-agent' ),
            [ $this, 'render_log_retention_days_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_management_settings_section'
        );
    }

    /**
     * Render the monthly API limit input field.
     *
     * @since 1.2.0
     */
    public function render_api_monthly_limit_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $limit = isset( $options['api_monthly_limit'] ) ? $options['api_monthly_limit'] : 0;
        echo '<input type="number" name="aca_ai_content_agent_options[api_monthly_limit]" value="' . esc_attr( $limit ) . '" class="small-text" min="0">';
        echo '<p class="description">' . esc_html__( 'Set a monthly API call limit to control costs. Set to 0 for no limit.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the current API usage display field.
     *
     * @since 1.2.0
     */
    public function render_api_usage_display_field() {
        $usage = get_option( 'aca_ai_content_agent_api_usage_current_month', 0 );
        /* translators: %d: number of API calls */
        echo '<span>' . sprintf( esc_html__( '%d calls this month.', 'aca-ai-content-agent' ), esc_html( $usage ) ) . '</span>';
        echo '<p class="description">' . esc_html__( 'This counter resets on the first day of each month.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the log cleanup enabled checkbox field.
     *
     * @since 1.2.0
     */
    public function render_log_cleanup_enabled_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $checked = ! empty( $options['log_cleanup_enabled'] );
        echo '<label><input type="checkbox" name="aca_ai_content_agent_options[log_cleanup_enabled]" value="1" ' . checked( $checked, true, false ) . '> ' . esc_html__( 'Delete old log entries automatically', 'aca-ai-content-agent' ) . '</label>';
    }

    /**
     * Render the log retention days input field.
     *
     * @since 1.2.0
     */
    public function render_log_retention_days_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $days = isset( $options['log_retention_days'] ) ? $options['log_retention_days'] : 60;
        echo '<input type="number" name="aca_ai_content_agent_options[log_retention_days]" value="' . esc_attr( $days ) . '" class="small-text" min="1">';
        echo '<p class="description">' . esc_html__( 'Remove logs older than this number of days.', 'aca-ai-content-agent' ) . '</p>';
    }
}
