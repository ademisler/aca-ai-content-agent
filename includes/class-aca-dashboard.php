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
        echo '<h1>' . esc_html__( 'ACA Dashboard', 'aca-ai-content-agent' ) . '</h1>';

        // Overview Section
        self::render_overview_section();

        // Idea Stream Section
        self::render_idea_stream_section();

        // Cluster Planner Section
        self::render_cluster_planner_section();

        // Recent Activity Section
        self::render_recent_activity_section();

        // Search Console Section
        self::render_gsc_section();

        echo '</div>';
    }

    private static function render_overview_section() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $pending_ideas = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM `{$ideas_table}` WHERE status = %s", 'pending' ) );
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $drafted_posts = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM `{$ideas_table}` WHERE status = %s", 'drafted' ) );
        $api_usage = get_option('aca_api_usage_current_month', 0);
        $api_limit = get_option('aca_options', [])['api_monthly_limit'] ?? 0;

        echo '<h2>' . esc_html__( 'Overview', 'aca-ai-content-agent' ) . '</h2>';
        /* translators: 1: current API usage, 2: API limit */
        echo '<p>' . sprintf( esc_html__( 'API Usage: %1$s / %2$s calls this month.', 'aca-ai-content-agent' ), esc_html( number_format_i18n( $api_usage ) ), $api_limit > 0 ? esc_html( number_format_i18n( $api_limit ) ) : esc_html__( 'unlimited', 'aca-ai-content-agent' ) ) . '</p>';
        /* translators: %s: number of pending ideas */
        echo '<p>' . sprintf( esc_html__( 'Pending Ideas: %s', 'aca-ai-content-agent' ), esc_html( number_format_i18n( $pending_ideas ) ) ) . '</p>';
        /* translators: %s: number of drafted posts */
        echo '<p>' . sprintf( esc_html__( 'Drafted Posts: %s', 'aca-ai-content-agent' ), esc_html( number_format_i18n( $drafted_posts ) ) ) . '</p>';
    }

    private static function render_idea_stream_section() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $ideas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$ideas_table}` WHERE status = %s ORDER BY created_at DESC", 'pending' ) );

        echo '<h2>' . esc_html__( 'Idea Stream', 'aca-ai-content-agent' ) . '</h2>';

        if (!empty($ideas)) {
            echo '<ul class="aca-idea-list">';
            foreach ($ideas as $idea) {
                echo '<li data-id="' . esc_attr( $idea->id ) . '">' . esc_html( $idea->idea_title ) .
                     ' <button class="button-primary aca-write-draft" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Write Draft', 'aca-ai-content-agent' ) . '</button>' .
                     ' <span class="aca-draft-status"></span>' .
                     ' <button class="button-secondary aca-reject-idea" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Reject', 'aca-ai-content-agent' ) . '</button>' .
                     ' <button class="button aca-feedback-btn" data-value="1">👍</button>' .
                     ' <button class="button aca-feedback-btn" data-value="-1">👎</button>' .
                     '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__( 'No new ideas yet. Generate some!', 'aca-ai-content-agent' ) . '</p>';
        }

        echo '<button class="button-primary" id="aca-generate-ideas">' . esc_html__( 'Generate New Ideas Manually', 'aca-ai-content-agent' ) . '</button>';
        echo '<span id="aca-ideas-status"></span>';
    }

    private static function render_cluster_planner_section() {
        echo '<h2>' . esc_html__( 'Content Cluster Planner', 'aca-ai-content-agent' ) . '</h2>';
        echo '<input type="text" id="aca-cluster-topic" placeholder="' . esc_attr__( 'Main Topic', 'aca-ai-content-agent' ) . '" /> ';
        echo '<button class="button" id="aca-generate-cluster">' . esc_html__( 'Generate Cluster Ideas', 'aca-ai-content-agent' ) . '</button> ';
        echo '<span id="aca-cluster-status"></span>';
    }

    private static function render_recent_activity_section() {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'aca_logs';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $logs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$logs_table}` ORDER BY created_at DESC LIMIT %d", 10 ) );

        echo '<h2>' . esc_html__( 'Quick Actions', 'aca-ai-content-agent' ) . '</h2>';
        echo '<button class="button" id="aca-generate-style-guide">' . esc_html__( 'Update Style Guide Manually', 'aca-ai-content-agent' ) . '</button>';
        echo '<span id="aca-style-guide-status"></span>';

        echo '<h2>' . esc_html__( 'Recent Activities', 'aca-ai-content-agent' ) . '</h2>';
        if (!empty($logs)) {
            echo '<ul class="aca-log-list">';
            foreach ($logs as $log) {
                echo '<li class="log-' . esc_attr($log->log_type) . '">[' . esc_html($log->created_at) . '] ' . esc_html($log->log_message) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__( 'No recent activity.', 'aca-ai-content-agent' ) . '</p>';
        }
    }

    private static function render_gsc_section() {
        echo '<h2>' . esc_html__( 'Top Search Queries', 'aca-ai-content-agent' ) . '</h2>';
        echo '<button class="button" id="aca-fetch-gsc">' . esc_html__( 'Fetch Queries', 'aca-ai-content-agent' ) . '</button> ';
        echo '<button class="button" id="aca-generate-gsc-ideas">' . esc_html__( 'Generate Ideas', 'aca-ai-content-agent' ) . '</button>';
        echo '<div id="aca-gsc-results" style="margin-top:10px;"></div>';
    }
}