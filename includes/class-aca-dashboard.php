<?php
/**
 * ACA - AI Content Agent
 *
 * Dashboard Page
 *
 * @package ACA
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Dashboard {

    /**
     * Render the dashboard content.
     */
    public static function render() {
        echo '<div class="wrap aca-dashboard">';
        echo '<h1>' . __( 'ACA Dashboard', 'aca' ) . '</h1>';

        // Overview Section
        self::render_overview_section();

        // Idea Stream Section
        self::render_idea_stream_section();

        // Recent Activity Section
        self::render_recent_activity_section();

        // Search Console Section
        self::render_gsc_section();

        echo '</div>';
    }

    private static function render_overview_section() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        $pending_ideas = $wpdb->get_var("SELECT COUNT(id) FROM $ideas_table WHERE status = 'pending'");
        $drafted_posts = $wpdb->get_var("SELECT COUNT(id) FROM $ideas_table WHERE status = 'drafted'");
        $api_usage = get_option('aca_api_usage_current_month', 0);
        $api_limit = get_option('aca_options', [])['api_monthly_limit'] ?? 0;

        echo '<h2>' . __( 'Overview', 'aca' ) . '</h2>';
        echo '<p>' . sprintf(__('API Usage: %d / %d calls this month.', 'aca'), $api_usage, $api_limit > 0 ? $api_limit : 'unlimited') . '</p>';
        echo '<p>' . sprintf(__('Pending Ideas: %d', 'aca'), $pending_ideas) . '</p>';
        echo '<p>' . sprintf(__('Drafted Posts: %d', 'aca'), $drafted_posts) . '</p>';
    }

    private static function render_idea_stream_section() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        $ideas = $wpdb->get_results("SELECT * FROM $ideas_table WHERE status = 'pending' ORDER BY created_at DESC");

        echo '<h2>' . __( 'Idea Stream', 'aca' ) . '</h2>';

        if (!empty($ideas)) {
            echo '<ul class="aca-idea-list">';
            foreach ($ideas as $idea) {
                echo '<li data-id="' . $idea->id . '">' . esc_html($idea->idea_title) .
                     ' <button class="button-primary aca-write-draft" data-id="' . $idea->id . '">' . __('Write Draft', 'aca') . '</button>' .
                     ' <span class="aca-draft-status"></span>' .
                     ' <button class="button-secondary aca-reject-idea" data-id="' . $idea->id . '">' . __('Reject', 'aca') . '</button>' .
                     ' <button class="button aca-feedback-btn" data-value="1">👍</button>' .
                     ' <button class="button aca-feedback-btn" data-value="-1">👎</button>' .
                     '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . __( 'No new ideas yet. Generate some!', 'aca' ) . '</p>';
        }

        echo '<button class="button-primary" id="aca-generate-ideas">' . __( 'Generate New Ideas Manually', 'aca' ) . '</button>';
        echo '<span id="aca-ideas-status"></span>';
    }

    private static function render_recent_activity_section() {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'aca_logs';
        $logs = $wpdb->get_results("SELECT * FROM $logs_table ORDER BY created_at DESC LIMIT 10");

        echo '<h2>' . __( 'Quick Actions', 'aca' ) . '</h2>';
        echo '<button class="button" id="aca-generate-style-guide">' . __( 'Update Style Guide Manually', 'aca' ) . '</button>';
        echo '<span id="aca-style-guide-status"></span>';

        echo '<h2>' . __( 'Recent Activities', 'aca' ) . '</h2>';
        if (!empty($logs)) {
            echo '<ul class="aca-log-list">';
            foreach ($logs as $log) {
                echo '<li class="log-' . esc_attr($log->log_type) . '">[' . esc_html($log->created_at) . '] ' . esc_html($log->log_message) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . __( 'No recent activity.', 'aca' ) . '</p>';
        }
    }

    private static function render_gsc_section() {
        echo '<h2>' . __( 'Top Search Queries', 'aca' ) . '</h2>';
        echo '<button class="button" id="aca-fetch-gsc">' . __( 'Fetch Queries', 'aca' ) . '</button> ';
        echo '<button class="button" id="aca-generate-gsc-ideas">' . __( 'Generate Ideas', 'aca' ) . '</button>';
        echo '<div id="aca-gsc-results" style="margin-top:10px;"></div>';
    }
}
