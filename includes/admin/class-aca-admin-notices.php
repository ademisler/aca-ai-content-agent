<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Notices
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin_Notices {

    public function __construct() {
        add_action( 'admin_notices', [ $this, 'display_admin_notices' ] );
    }

    public function display_admin_notices() {
        if ( ! defined( 'AUTH_KEY' ) || 'put your unique phrase here' === AUTH_KEY ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'ACA: AUTH_KEY is not defined in your wp-config.php file. This is a security risk. Please define it to ensure your data is encrypted securely.', 'aca-ai-content-agent' ) . '</p></div>';
        }

        if ( false === ( $api_key_notice = get_transient( 'aca_admin_notice_api_key' ) ) ) {
            $api_key_notice = empty( get_option( 'aca_ai_content_agent_gemini_api_key' ) );
            set_transient( 'aca_admin_notice_api_key', $api_key_notice, 5 * MINUTE_IN_SECONDS );
        }
        if ( $api_key_notice ) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'ACA: API key is not set. Please set it in the', 'aca-ai-content-agent' ) . ' <a href="?page=aca-ai-content-agent&amp;tab=settings">' . esc_html__( 'settings', 'aca-ai-content-agent' ) . '</a>.</p></div>';
        }

        if ( false === ( $usage_notice = get_transient( 'aca_admin_notice_usage' ) ) ) {
            $options = get_option( 'aca_ai_content_agent_options' );
            $limit = $options['api_monthly_limit'] ?? 0;
            $usage = get_option( 'aca_ai_content_agent_api_usage_current_month', 0 );
            $usage_notice = ( $limit > 0 && $usage / $limit >= 0.8 );
            set_transient( 'aca_admin_notice_usage', $usage_notice, 5 * MINUTE_IN_SECONDS );
        }

        if ( $usage_notice ) {
            /* translators: %s: percentage of usage */
            echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( esc_html__( 'ACA: You have used %s%% or more of your monthly API call limit.', 'aca-ai-content-agent' ), '80' ) . '</p></div>';
        }

        if ( false === ( $pending_ideas_notice = get_transient( 'aca_admin_notice_pending_ideas' ) ) ) {
            global $wpdb;
            $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $pending = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'pending' ) );
            $pending_ideas_notice = $pending;
            set_transient( 'aca_admin_notice_pending_ideas', $pending_ideas_notice, 5 * MINUTE_IN_SECONDS );
        }

        if ( $pending_ideas_notice > 0 ) {
            /* translators: %d: number of pending ideas */
            echo '<div class="notice notice-info is-dismissible"><p>' . sprintf( esc_html__( 'ACA: %d new ideas are awaiting your review.', 'aca-ai-content-agent' ), esc_html( $pending_ideas_notice ) ) . ' <a href="?page=aca-ai-content-agent&amp;tab=dashboard">' . esc_html__( 'Open Dashboard', 'aca-ai-content-agent' ) . '</a></p></div>';
        }

        if ( false === ( $latest_error_notice = get_transient( 'aca_admin_notice_latest_error' ) ) ) {
            global $wpdb;
            $logs_table = $wpdb->prefix . 'aca_ai_content_agent_logs';
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $latest_error = $wpdb->get_row( $wpdb->prepare( "SELECT message FROM {$logs_table} WHERE level = %s AND timestamp >= %s ORDER BY id DESC LIMIT 1", 'error', gmdate( 'Y-m-d H:i:s', strtotime( '-1 day' ) ) ) );
            $latest_error_notice = $latest_error;
            set_transient( 'aca_admin_notice_latest_error', $latest_error_notice, 5 * MINUTE_IN_SECONDS );
        }

        if ( $latest_error_notice ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $latest_error_notice->message ) . '</p></div>';
        }
    }
}
