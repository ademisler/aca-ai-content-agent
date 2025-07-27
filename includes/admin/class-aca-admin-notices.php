<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Notices
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
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
        // Only show notices on ACA plugin pages
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'aca-ai-content-agent') === false) {
            return;
        }
        
        // FIX: Add capability check notice
        $current_user = wp_get_current_user();
        if (!current_user_can('edit_posts') && !current_user_can('manage_options')) {
            echo '<div class="notice notice-error"><p>' . 
                 sprintf(
                     esc_html__('ACA: Your user account lacks the necessary permissions. Current roles: %s. Please contact your administrator or try logging out and back in.', 'aca-ai-content-agent'),
                     '<strong>' . implode(', ', $current_user->roles) . '</strong>'
                 ) . 
                 '</p></div>';
            return; // Don't show other notices if user lacks basic permissions
        }
        
        // AUTH_KEY check
        if ( ! defined( 'AUTH_KEY' ) || 'put your unique phrase here' === AUTH_KEY ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'ACA: AUTH_KEY is not defined or is set to the default in your wp-config.php file. This is a security risk and will break encryption. Please set a unique AUTH_KEY.', 'aca-ai-content-agent' ) . '</p></div>';
        }
        
        // Gemini API key check
        $api_key = get_option( 'aca_ai_content_agent_gemini_api_key' );
        if ( empty( $api_key ) ) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . 
                 esc_html__( 'ACA: Gemini API key is missing. ', 'aca-ai-content-agent' ) . 
                 '<a href="' . admin_url('admin.php?page=aca-ai-content-agent-settings') . '">' . 
                 esc_html__( 'Please configure it in settings.', 'aca-ai-content-agent' ) . 
                 '</a></p></div>';
        }
        
        // Database tables check
        global $wpdb;
        $required_tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
        ];
        $missing_tables = [];
        foreach ( $required_tables as $table ) {
            // SECURITY FIX: Use proper table name escaping
            $exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
            if ( ! $exists ) {
                $missing_tables[] = $table;
            }
        }
        if ( ! empty( $missing_tables ) ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'ACA: One or more required database tables are missing: ', 'aca-ai-content-agent' ) . esc_html( implode( ', ', $missing_tables ) ) . '. ' . esc_html__( 'Please deactivate and reactivate the plugin or check your database permissions.', 'aca-ai-content-agent' ) . '</p></div>';
        }

        // PERFORMANCE FIX: Optimize notice caching with longer intervals
        if ( false === ( $usage_notice = get_transient( 'aca_admin_notice_usage' ) ) ) {
            $options = get_option( 'aca_ai_content_agent_options' );
            $limit = $options['api_monthly_limit'] ?? 0;
            $usage = get_option( 'aca_ai_content_agent_api_usage_current_month', 0 );
            $usage_notice = ( $limit > 0 && $usage / $limit >= 0.8 );
            // PERFORMANCE FIX: Cache for 15 minutes instead of 5
            set_transient( 'aca_admin_notice_usage', $usage_notice, 15 * MINUTE_IN_SECONDS );
        }

        if ( $usage_notice ) {
            /* translators: %s: percentage of usage */
            echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( esc_html__( 'ACA: You have used %s%% or more of your monthly API call limit.', 'aca-ai-content-agent' ), '80' ) . '</p></div>';
        }

        // PERFORMANCE FIX: Optimize pending ideas notice with better caching
        if ( false === ( $pending_ideas_notice = get_transient( 'aca_admin_notice_pending_ideas' ) ) ) {
            global $wpdb;
            // SECURITY FIX: Use proper table name escaping
            $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
            $pending = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'pending' ) );
            $pending_ideas_notice = $pending;
            // PERFORMANCE FIX: Cache for 10 minutes instead of 5
            set_transient( 'aca_admin_notice_pending_ideas', $pending_ideas_notice, 10 * MINUTE_IN_SECONDS );
        }

        if ( $pending_ideas_notice > 0 ) {
            /* translators: %d: number of pending ideas */
            echo '<div class="notice notice-info is-dismissible"><p>' . sprintf( esc_html__( 'ACA: %d new ideas are awaiting your review.', 'aca-ai-content-agent' ), esc_html( $pending_ideas_notice ) ) . ' <a href="' . admin_url('admin.php?page=aca-ai-content-agent') . '">' . esc_html__( 'Open Dashboard', 'aca-ai-content-agent' ) . '</a></p></div>';
        }
    }
}
