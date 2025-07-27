<?php
/**
 * ACA - AI Content Agent
 *
 * Dashboard Page
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Dashboard {

    /**
     * Render the dashboard content.
     */
    public static function render() {
        echo '<div class="wrap aca-admin-page">';
        
        // Page header
        self::render_page_header();
        
        // Navigation tabs
        self::render_navigation_tabs();
        
        // Dashboard content
        self::render_dashboard_content();
        
        // Ideas page content
        self::render_ideas_content();
        
        // Settings page content
        self::render_settings_content();
        
        // License page content
        self::render_license_content();
        
        // Notification container
        echo '<div id="aca-notification-container"></div>';
        
        echo '</div>';
    }

    /**
     * Render page header.
     */
    private static function render_page_header() {
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;
        $pending_ideas = self::get_pending_ideas_count();
        $total_drafts = self::get_total_drafts_count();
        
        echo '<header class="aca-page-header">';
        echo '<div class="aca-header-content">';
        echo '<h1><i class="bi bi-robot"></i> ACA - AI Content Agent</h1>';
        echo '<div class="aca-header-meta">';
        echo '<span>' . sprintf( esc_html__( 'Hello, %s! Welcome to automated content generation.', 'aca-ai-content-agent' ), '<strong>' . esc_html( $user_name ) . '</strong>' ) . '</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-header-stats">';
        echo '<div class="aca-stat-item">';
        echo '<span class="aca-stat-number" id="pending-ideas-count">' . esc_html( $pending_ideas ) . '</span>';
        echo '<span class="aca-stat-label">' . esc_html__( 'Pending Ideas', 'aca-ai-content-agent' ) . '</span>';
        echo '</div>';
        echo '<div class="aca-stat-item">';
        echo '<span class="aca-stat-number" id="total-drafts-count">' . esc_html( $total_drafts ) . '</span>';
        echo '<span class="aca-stat-label">' . esc_html__( 'Total Drafts', 'aca-ai-content-agent' ) . '</span>';
        echo '</div>';
        echo '</div>';
        echo '</header>';
    }

    /**
     * Render navigation tabs.
     */
    private static function render_navigation_tabs() {
        echo '<nav class="aca-nav-tabs">';
        echo '<button class="aca-nav-tab aca-nav-tab-active" data-tab="dashboard"><i class="bi bi-grid-1x2-fill"></i> ' . esc_html__( 'Dashboard', 'aca-ai-content-agent' ) . '</button>';
        echo '<button class="aca-nav-tab" data-tab="ideas"><i class="bi bi-lightbulb-fill"></i> ' . esc_html__( 'Content Ideas', 'aca-ai-content-agent' ) . '</button>';
        echo '<button class="aca-nav-tab" data-tab="settings"><i class="bi bi-gear-fill"></i> ' . esc_html__( 'Settings', 'aca-ai-content-agent' ) . '</button>';
        echo '<button class="aca-nav-tab" data-tab="license"><i class="bi bi-patch-check-fill"></i> ' . esc_html__( 'License', 'aca-ai-content-agent' ) . '</button>';
        echo '</nav>';
    }

    /**
     * Render dashboard content.
     */
    private static function render_dashboard_content() {
        echo '<div id="dashboard-content" class="aca-tab-content active">';
        
        // Overview section
        self::render_overview_section();
        
        // Quick actions and cluster planner
        self::render_quick_actions_section();
        
        // Idea stream
        self::render_idea_stream_section();
        
        echo '</div>';
    }

    /**
     * Render overview section.
     */
    private static function render_overview_section() {
        $pending_ideas = self::get_pending_ideas_count();
        $total_drafts = self::get_total_drafts_count();
        
        echo '<section class="aca-section aca-overview-section">';
        echo '<h2><i class="bi bi-bar-chart-line-fill"></i> Content Overview</h2>';
        echo '<div class="aca-overview-grid">';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">💡</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number" id="overview-pending-ideas">' . esc_html( $pending_ideas ) . '</div>';
        echo '<div class="label">Pending Ideas</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">📝</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number" id="overview-total-drafts">' . esc_html( $total_drafts ) . '</div>';
        echo '<div class="label">Total Drafts</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">📊</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number">34</div>';
        echo '<div class="label">Content Ideas Generated This Month</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">⏱️</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number">2.1s</div>';
        echo '<div class="label">Average Generation Time</div>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        echo '</section>';
    }

    /**
     * Render quick actions section.
     */
    private static function render_quick_actions_section() {
        echo '<div class="dashboard-grid">';
        
        // Quick actions
        echo '<section class="aca-section">';
        echo '<h2><i class="bi bi-lightning-charge-fill"></i> Quick Actions</h2>';
        echo '<p>Perform quick actions for frequently used tasks.</p>';
        echo '<div style="display: flex; gap: 15px; margin-top: 20px;">';
        echo '<button class="aca-action-button" id="generate-ideas-btn">';
        echo '<span class="button-icon"><i class="bi bi-lightbulb"></i></span>';
        echo '<span class="button-text">Generate New Ideas</span>';
        echo '<span class="button-loader" style="display: none;"><span class="aca-loading-spinner"></span></span>';
        echo '</button>';
        echo '<button class="aca-action-button secondary" id="update-style-guide-btn">';
        echo '<span class="button-icon"><i class="bi bi-palette-fill"></i></span>';
        echo '<span class="button-text">Update Style Guide</span>';
        echo '<span class="button-loader" style="display: none;"><span class="aca-loading-spinner"></span></span>';
        echo '</button>';
        echo '</div>';
        echo '<div id="quick-actions-status" style="margin-top: 20px;"></div>';
        echo '</section>';
        
        // Cluster planner
        echo '<section class="aca-section">';
        echo '<h2><i class="bi bi-diagram-3-fill"></i> Content Cluster Planner <span class="pro-badge">PRO</span></h2>';
        echo '<p>Create content clusters around a main topic to strengthen your SEO strategy.</p>';
        echo '<div style="display: flex; gap: 15px; margin-top: 20px;">';
        echo '<input type="text" id="cluster-topic-input" class="aca-input" placeholder="Enter main topic (e.g., WordPress SEO)">';
        echo '<button class="aca-action-button" id="generate-cluster-btn">';
        echo '<i class="bi bi-magic"></i> Generate Cluster';
        echo '</button>';
        echo '</div>';
        echo '</section>';
        
        echo '</div>';
    }

    /**
     * Render idea stream section.
     */
    private static function render_idea_stream_section() {
        echo '<section class="aca-section">';
        echo '<h2><i class="bi bi-stream"></i> Idea Stream</h2>';
        echo '<div id="idea-stream-container">';
        
        // Empty state
        echo '<div class="aca-empty-state" id="idea-empty-state">';
        echo '<div class="aca-empty-state-icon">💡</div>';
        echo '<h3>No Pending Ideas Yet</h3>';
        echo '<p>Ask your AI assistant to generate new content ideas for you.</p>';
        echo '<button class="aca-action-button" id="generate-ideas-btn-empty">';
        echo '<i class="bi bi-plus-lg"></i> Generate Idea';
        echo '</button>';
        echo '</div>';
        
        // Idea list
        echo '<ul class="aca-idea-list" id="idea-list">';
        self::render_sample_ideas();
        echo '</ul>';
        
        echo '</div>';
        echo '</section>';
    }

    /**
     * Render sample ideas.
     */
    private static function render_sample_ideas() {
        $sample_ideas = [
            [
                'id' => 101,
                'title' => 'Performance Optimization Tips for WordPress Sites',
                'keywords' => 'wordpress, seo, performance',
                'time' => '2 hours ago'
            ],
            [
                'id' => 102,
                'title' => '2025 Top Digital Marketing Trends',
                'keywords' => 'digital marketing, trends',
                'time' => '5 hours ago'
            ]
        ];
        
        foreach ($sample_ideas as $idea) {
            echo '<li data-id="' . esc_attr($idea['id']) . '">';
            echo '<div class="aca-idea-content">';
            echo '<div class="aca-idea-title">' . esc_html($idea['title']) . '</div>';
            echo '<div class="aca-idea-meta">';
            echo '<i class="bi bi-clock"></i> ' . esc_html($idea['time']) . ' &nbsp;&nbsp; ';
            echo '<i class="bi bi-tags"></i> ' . esc_html($idea['keywords']);
            echo '</div>';
            echo '</div>';
            echo '<div class="aca-idea-actions">';
            echo '<button class="aca-action-button write-draft-btn"><i class="bi bi-pencil-square"></i> Write Draft</button>';
            echo '<button class="aca-action-button secondary reject-idea-btn"><i class="bi bi-x-circle"></i> Reject</button>';
            echo '<button class="aca-feedback-btn" title="Good idea">👍</button>';
            echo '<button class="aca-feedback-btn" title="Bad idea">👎</button>';
            echo '</div>';
            echo '</li>';
        }
    }

    /**
     * Render ideas content.
     */
    private static function render_ideas_content() {
        echo '<div id="ideas-content" class="aca-tab-content">';
        echo '<section class="aca-section">';
        
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 40%;">Title</th>';
        echo '<th>Status</th>';
        echo '<th>Keywords</th>';
        echo '<th>Created</th>';
        echo '<th>Actions</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $sample_ideas_table = [
            [
                'title' => 'Performance Optimization Tips for WordPress Sites',
                'status' => 'pending',
                'keywords' => 'wordpress, seo, performance',
                'time' => '2 hours ago'
            ],
            [
                'title' => '2025 Top Digital Marketing Trends',
                'status' => 'pending',
                'keywords' => 'digital marketing, trends',
                'time' => '5 hours ago'
            ],
            [
                'title' => 'Google Analytics 4 Guide for Beginners',
                'status' => 'drafted',
                'keywords' => 'google analytics, ga4, analytics',
                'time' => '1 day ago'
            ],
            [
                'title' => 'How Artificial Intelligence is Used in Content Marketing?',
                'status' => 'rejected',
                'keywords' => 'artificial intelligence, content marketing',
                'time' => '2 days ago'
            ]
        ];
        
        foreach ($sample_ideas_table as $idea) {
            echo '<tr>';
            echo '<td><strong>' . esc_html($idea['title']) . '</strong></td>';
            echo '<td><span class="aca-status-badge aca-status-' . esc_attr($idea['status']) . '">' . self::get_status_text($idea['status']) . '</span></td>';
            echo '<td>' . esc_html($idea['keywords']) . '</td>';
            echo '<td>' . esc_html($idea['time']) . '</td>';
            echo '<td>';
            if ($idea['status'] === 'pending') {
                echo '<button class="aca-action-button" style="padding: 8px 16px;"><i class="bi bi-pencil-square"></i> Write Draft</button>';
            } else {
                echo '<button class="aca-action-button secondary" disabled>Completed</button>';
            }
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        
        // Pagination
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo '<span class="displaying-num">12 items</span>';
        echo '<span class="pagination-links">';
        echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
        echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
        echo '<span class="screen-reader-text">Current page</span>';
        echo '<span id="table-paging" class="paging-input">';
        echo '<span class="tablenav-paging-text">1 / <span class="total-pages">3</span></span>';
        echo '</span>';
        echo '<a class="next-page button" href="#"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
        echo '<a class="last-page button" href="#"><span class="screen-reader-text">Last page</span><span aria-hidden="true">»</span></a>';
        echo '</span>';
        echo '</div>';
        echo '<br class="clear">';
        echo '</div>';
        
        echo '</section>';
        echo '</div>';
    }

    /**
     * Render settings content.
     */
    private static function render_settings_content() {
        echo '<div id="settings-content" class="aca-tab-content">';
        echo '<form id="settings-form">';
        echo '<div class="aca-settings-grid">';
        
        // API Settings
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-key-fill"></i> API & Connection Settings</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="gemini_api_key">Google Gemini API Key</label>';
        echo '<input type="password" id="gemini_api_key" name="gemini_api_key" placeholder="*****************">';
        echo '<p class="description">Enter the key you received from Google AI Studio.</p>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<button type="button" class="aca-action-button secondary" id="test-connection-btn">';
        echo '<i class="bi bi-broadcast-pin"></i> Test Connection';
        echo '</button>';
        echo '<span id="connection-test-status" style="margin-left: 10px;"></span>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="openai_api_key">OpenAI API Key (for DALL-E 3) <span class="pro-badge">PRO</span></label>';
        echo '<input type="password" id="openai_api_key" name="openai_api_key" placeholder="Active in Pro version">';
        echo '</div>';
        echo '</section>';
        
        // Automation Settings
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-robot"></i> Automation Settings</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="working_mode">Working Mode</label>';
        echo '<select id="working_mode" name="working_mode">';
        echo '<option value="manual">Manual (Under My Control)</option>';
        echo '<option value="semi">Semi-Automatic (Only Idea Generation)</option>';
        echo '<option value="full">Fully Automatic (Idea Generation and Draft Writing) - PRO</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="default_author">Default Author</label>';
        echo '<select id="default_author" name="default_author">';
        echo '<option value="1">Adem Isler</option>';
        echo '<option value="2">Editor</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="generation_limit">Generation Limit per Cycle</label>';
        echo '<input type="number" id="generation_limit" name="generation_limit" value="5" min="1" max="20">';
        echo '<p class="description">Maximum number of ideas/drafts to generate per automation cycle to control costs.</p>';
        echo '</div>';
        echo '</section>';
        
        // Content Analysis
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-book-half"></i> Content Analysis & Learning</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label>Content Types to Analyze</label>';
        echo '<div>';
        echo '<label><input type="checkbox" checked> Posts</label>';
        echo '<label style="margin-left: 15px;"><input type="checkbox"> Pages</label>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="analysis_depth">Analysis Depth</label>';
        echo '<input type="number" id="analysis_depth" name="analysis_depth" value="20" min="10" max="100">';
        echo '<p class="description">Number of recent posts to analyze for style learning.</p>';
        echo '</div>';
        echo '</section>';
        
        // Content Enrichment
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-gem"></i> Content Enrichment</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="internal_links">Maximum Internal Links</label>';
        echo '<input type="number" id="internal_links" name="internal_links" value="3" min="0" max="10">';
        echo '<p class="description">Maximum number of internal links to be automatically added to each draft.</p>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="image_provider">Featured Image Provider</label>';
        echo '<select id="image_provider" name="image_provider">';
        echo '<option value="none">None</option>';
        echo '<option value="pexels">Pexels</option>';
        echo '<option value="dalle">DALL-E 3 (PRO)</option>';
        echo '</select>';
        echo '</div>';
        echo '</section>';
        
        echo '</div>';
        
        echo '<div class="aca-form-actions">';
        echo '<button type="submit" class="aca-action-button">';
        echo '<i class="bi bi-save-fill"></i> Save Settings';
        echo '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Render license content.
     */
    private static function render_license_content() {
        echo '<div id="license-content" class="aca-tab-content">';
        echo '<section class="aca-section aca-license-section">';
        
        echo '<div class="aca-license-status free" id="license-status-box">';
        echo '<p>Current Status: <strong><span id="license-status-text">FREE VERSION</span></strong></p>';
        echo '</div>';
        
        echo '<div id="license-form-container">';
        echo '<h3><i class="bi bi-key-fill"></i> Activate Pro License</h3>';
        echo '<p>Enter your license key to unlock all premium features.</p>';
        echo '<div style="max-width: 500px; margin: 20px auto; display: flex; gap: 10px;">';
        echo '<input type="text" id="license-key-input" class="aca-input" placeholder="Your license key...">';
        echo '<button class="aca-action-button" id="validate-license-btn">Validate</button>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-pro-features">';
        echo '<h3><i class="bi bi-star-fill"></i> ACA Pro Features</h3>';
        echo '<ul>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Content Cluster Planner</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Original Image Generation with DALL-E 3</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Copyscape for Plagiarism Check</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Update Existing Content Assistant</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Full Automation Mode</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Unlimited Idea and Draft Generation</li>';
        echo '</ul>';
        echo '<a href="#" class="aca-action-button" style="background: var(--aca-success); box-shadow: 0 4px 12px #22c55e60;">';
        echo '<i class="bi bi-rocket-takeoff-fill"></i> Purchase ACA Pro';
        echo '</a>';
        echo '</div>';
        
        echo '</section>';
        echo '</div>';
    }

    /**
     * Get pending ideas count.
     */
    private static function get_pending_ideas_count() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $pending_ideas = get_transient('aca_ai_content_agent_pending_ideas_count');
        if (false === $pending_ideas) {
            $pending_ideas = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'pending' ) );
            set_transient('aca_ai_content_agent_pending_ideas_count', $pending_ideas, 5 * MINUTE_IN_SECONDS);
        }

        return $pending_ideas ?: 5; // Fallback to 5 for demo
    }

    /**
     * Get total drafts count.
     */
    private static function get_total_drafts_count() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $total_drafts = get_transient('aca_ai_content_agent_total_drafts_count');
        if (false === $total_drafts) {
            $total_drafts = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'drafted' ) );
            set_transient('aca_ai_content_agent_total_drafts_count', $total_drafts, 5 * MINUTE_IN_SECONDS);
        }

        return $total_drafts ?: 12; // Fallback to 12 for demo
    }

    /**
     * Get status text.
     */
    private static function get_status_text($status) {
        $status_texts = [
            'pending' => 'Pending',
            'drafted' => 'Draft Created',
            'rejected' => 'Rejected'
        ];
        
        return isset($status_texts[$status]) ? $status_texts[$status] : 'Unknown';
    }
}