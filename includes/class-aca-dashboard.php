<?php
/**
 * ACA - AI Content Agent
 *
 * Dashboard Page
 *
 * @package ACA_AI_Content_Agent
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_AI_Content_Agent_Dashboard {

    /**
     * Render the dashboard content.
     */
    public static function render() {
        echo '<div class="wrap aca-ai-content-agent-dashboard">';
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
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        // Try to read from cache
        $pending_ideas = get_transient('aca_ai_content_agent_pending_ideas_count');
        if (false === $pending_ideas) {
            // If not in cache, fetch from DB and store for 5 minutes
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $pending_ideas = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'pending' ) );
            set_transient('aca_ai_content_agent_pending_ideas_count', $pending_ideas, 5 * MINUTE_IN_SECONDS);
        }

        $api_usage = get_option('aca_ai_content_agent_api_usage_current_month', 0);
        $api_limit = get_option('aca_ai_content_agent_options', [])['api_monthly_limit'] ?? 0;

        // Try to read from cache
        $drafted_posts = get_transient('aca_ai_content_agent_drafted_posts_count');
        if (false === $drafted_posts) {
            // If not in cache, fetch from DB and store for 5 minutes
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $drafted_posts = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'drafted' ) );
            set_transient('aca_ai_content_agent_drafted_posts_count', $drafted_posts, 5 * MINUTE_IN_SECONDS);
        }

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
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $ideas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$ideas_table} WHERE status = %s ORDER BY generated_date DESC", 'pending' ) );

        echo '<h2>' . esc_html__( 'Idea Stream', 'aca-ai-content-agent' ) . '</h2>';

        if (!empty($ideas)) {
            echo '<ul class="aca-ai-content-agent-idea-list">';
            foreach ($ideas as $idea) {
                echo '<li data-id="' . esc_attr( $idea->id ) . '">' . esc_html( $idea->title ) .
                     ' <button class="button-primary aca-ai-content-agent-write-draft" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Write Draft', 'aca-ai-content-agent' ) . '</button>' .
                     ' <span class="aca-ai-content-agent-draft-status"></span>' .
                     ' <button class="button-secondary aca-ai-content-agent-reject-idea" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Reject', 'aca-ai-content-agent' ) . '</button>' .
                     ' <button class="button aca-ai-content-agent-feedback-btn" data-value="1">👍</button>' .
                     ' <button class="button aca-ai-content-agent-feedback-btn" data-value="-1">👎</button>' .
                     '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__( 'No new ideas yet. Generate some!', 'aca-ai-content-agent' ) . '</p>';
        }

        echo '<button class="button-primary" id="aca-ai-content-agent-generate-ideas">' . esc_html__( 'Generate New Ideas Manually', 'aca-ai-content-agent' ) . '</button>';
        echo '<span id="aca-ai-content-agent-ideas-status"></span>';
    }

    private static function render_cluster_planner_section() {
        echo '<h2>' . esc_html__( 'Content Cluster Planner', 'aca-ai-content-agent' ) . '</h2>';
        echo '<input type="text" id="aca-ai-content-agent-cluster-topic" placeholder="' . esc_attr__( 'Main Topic', 'aca-ai-content-agent' ) . '" /> ';
        echo '<button class="button" id="aca-ai-content-agent-generate-cluster">' . esc_html__( 'Generate Cluster Ideas', 'aca-ai-content-agent' ) . '</button> ';
        echo '<span id="aca-ai-content-agent-cluster-status"></span>';
    }

    private static function render_recent_activity_section() {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'aca_ai_content_agent_logs';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $logs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$logs_table} ORDER BY timestamp DESC LIMIT %d", 10 ) );

        echo '<h2>' . esc_html__( 'Quick Actions', 'aca-ai-content-agent' ) . '</h2>';
        echo '<button class="button" id="aca-ai-content-agent-generate-style-guide">' . esc_html__( 'Update Style Guide Manually', 'aca-ai-content-agent' ) . '</button>';
        echo '<span id="aca-ai-content-agent-style-guide-status"></span>';

        echo '<h2>' . esc_html__( 'Recent Activities', 'aca-ai-content-agent' ) . '</h2>';
        if (!empty($logs)) {
            echo '<ul class="aca-ai-content-agent-log-list">';
            foreach ($logs as $log) {
                echo '<li class="log-' . esc_attr($log->log_type) . '">[' . esc_html($log->timestamp) . '] ' . esc_html($log->message) . '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>' . esc_html__( 'No recent activity.', 'aca-ai-content-agent' ) . '</p>';
        }
    }

    private static function render_gsc_section() {
        echo '<h2>' . esc_html__( 'Top Search Queries', 'aca-ai-content-agent' ) . '</h2>';
        echo '<button class="button" id="aca-ai-content-agent-fetch-gsc">' . esc_html__( 'Fetch Queries', 'aca-ai-content-agent' ) . '</button> ';
        echo '<button class="button" id="aca-ai-content-agent-generate-gsc-ideas">' . esc_html__( 'Generate Ideas', 'aca-ai-content-agent' ) . '</button>';
        echo '<div id="aca-ai-content-agent-gsc-results" style="margin-top:10px;"></div>';
    }
}