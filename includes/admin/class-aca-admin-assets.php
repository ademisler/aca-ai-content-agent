<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Assets Handler
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin_Assets {

    /**
     * Initialize the assets handler.
     */
    public static function init() {
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
        add_action( 'admin_head', array( __CLASS__, 'add_custom_styles' ) );
    }

    /**
     * Enqueue admin assets.
     */
    public static function enqueue_admin_assets( $hook ) {
        // Only load on our plugin pages
        if ( ! self::is_aca_page( $hook ) ) {
            return;
        }

        // Enqueue main admin styles
        wp_enqueue_style(
            'aca-admin-css',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/css/aca-admin.css',
            array(),
            ACA_AI_CONTENT_AGENT_VERSION
        );

        // Enqueue additional components styles
        wp_enqueue_style(
            'aca-admin-components-css',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/css/aca-admin-components.css',
            array('aca-admin-css'),
            ACA_AI_CONTENT_AGENT_VERSION
        );

        // Enqueue admin JavaScript
        wp_enqueue_script(
            'aca-admin-js',
            plugin_dir_url(dirname(dirname(__FILE__))) . 'admin/js/aca-admin.js',
            array('jquery'),
            ACA_AI_CONTENT_AGENT_VERSION,
            true
        );

        // Localize script with enhanced data
        wp_localize_script('aca-admin-js', 'aca_ai_content_agent_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aca_ai_content_agent_admin_nonce'),
            'strings' => array(
                'confirm_delete' => __('Are you sure you want to delete this item?', 'aca-ai-content-agent'),
                'confirm_reject' => __('Are you sure you want to reject this idea?', 'aca-ai-content-agent'),
                'processing' => __('Processing...', 'aca-ai-content-agent'),
                'success' => __('Success!', 'aca-ai-content-agent'),
                'error' => __('Error', 'aca-ai-content-agent'),
                'warning' => __('Warning', 'aca-ai-content-agent'),
                'info' => __('Information', 'aca-ai-content-agent'),
                'loading' => __('Loading...', 'aca-ai-content-agent'),
                'save_changes' => __('Save Changes', 'aca-ai-content-agent'),
                'cancel' => __('Cancel', 'aca-ai-content-agent'),
                'close' => __('Close', 'aca-ai-content-agent')
            ),
            'settings' => array(
                'animation_duration' => 300,
                'notification_duration' => 4000,
                'auto_save_delay' => 2000
            )
        ));

        // Enqueue Bootstrap Icons with fallback
        wp_enqueue_style(
            'bootstrap-icons',
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
            array(),
            '1.11.3'
        );
        
        // Add fallback for Bootstrap Icons
        add_action('wp_head', function() {
            echo '<style>
                /* Fallback for Bootstrap Icons */
                .bi::before {
                    content: "•";
                    font-family: inherit;
                }
                .bi-robot::before { content: "🤖"; }
                .bi-grid-1x2-fill::before { content: "📊"; }
                .bi-lightbulb-fill::before { content: "💡"; }
                .bi-gear-fill::before { content: "⚙️"; }
                .bi-patch-check-fill::before { content: "✅"; }
                .bi-check-circle-fill::before { content: "✅"; }
                .bi-x-circle-fill::before { content: "❌"; }
                .bi-plus::before { content: "➕"; }
                .bi-arrow-right::before { content: "➡️"; }
                .bi-clock::before { content: "🕐"; }
                .bi-graph-up::before { content: "📈"; }
                .bi-file-text::before { content: "📄"; }
                .bi-star::before { content: "⭐"; }
                .bi-heart::before { content: "❤️"; }
                .bi-shield-check::before { content: "🛡️"; }
                .bi-lightning::before { content: "⚡"; }
                .bi-people::before { content: "👥"; }
                .bi-chat::before { content: "💬"; }
                .bi-search::before { content: "🔍"; }
                .bi-funnel::before { content: "🔽"; }
                .bi-sort-down::before { content: "🔽"; }
                .bi-sort-up::before { content: "🔼"; }
                .bi-eye::before { content: "👁️"; }
                .bi-pencil::before { content: "✏️"; }
                .bi-trash::before { content: "🗑️"; }
                .bi-download::before { content: "⬇️"; }
                .bi-upload::before { content: "⬆️"; }
                .bi-link::before { content: "🔗"; }
                .bi-exclamation-triangle::before { content: "⚠️"; }
                .bi-info-circle::before { content: "ℹ️"; }
                .bi-question-circle::before { content: "❓"; }
                .bi-calendar::before { content: "📅"; }
                .bi-clock-history::before { content: "⏰"; }
                .bi-speedometer2::before { content: "📊"; }
                .bi-graph-up-arrow::before { content: "📈"; }
                .bi-activity::before { content: "📊"; }
                .bi-trophy::before { content: "🏆"; }
                .bi-award::before { content: "🏅"; }
                .bi-gem::before { content: "💎"; }
                .bi-diamond::before { content: "💎"; }
                .bi-crown::before { content: "👑"; }
                .bi-fire::before { content: "🔥"; }
                .bi-rocket::before { content: "🚀"; }
                .bi-target::before { content: "🎯"; }
                .bi-bullseye::before { content: "🎯"; }
                .bi-flag::before { content: "🚩"; }
                .bi-bookmark::before { content: "🔖"; }
                .bi-bookmark-star::before { content: "⭐"; }
                .bi-bookmark-heart::before { content: "❤️"; }
                .bi-bookmark-check::before { content: "✅"; }
                .bi-bookmark-plus::before { content: "➕"; }
                .bi-bookmark-x::before { content: "❌"; }
                .bi-bookmark-dash::before { content: "➖"; }
                .bi-bookmark-star-fill::before { content: "⭐"; }
                .bi-bookmark-heart-fill::before { content: "❤️"; }
                .bi-bookmark-check-fill::before { content: "✅"; }
                .bi-bookmark-plus-fill::before { content: "➕"; }
                .bi-bookmark-x-fill::before { content: "❌"; }
                .bi-bookmark-dash-fill::before { content: "➖"; }
            </style>';
        });

        // Enqueue Inter font
        wp_enqueue_style(
            'inter-font',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap',
            array(),
            null
        );

        // Enqueue our admin CSS
        wp_enqueue_style(
            'aca-admin-css',
            plugin_dir_url( ACA_AI_CONTENT_AGENT_PLUGIN_FILE ) . 'admin/css/aca-admin.css',
            array( 'bootstrap-icons', 'inter-font' ),
            ACA_AI_CONTENT_AGENT_VERSION
        );

        // Enqueue our admin JavaScript
        wp_enqueue_script(
            'aca-admin-js',
            plugin_dir_url( ACA_AI_CONTENT_AGENT_PLUGIN_FILE ) . 'admin/js/aca-admin.js',
            array( 'jquery' ),
            ACA_AI_CONTENT_AGENT_VERSION,
            true
        );

        // Localize script with AJAX data
        wp_localize_script(
            'aca-admin-js',
            'aca_ai_content_agent_admin_ajax',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'aca_ai_content_agent_admin_nonce' ),
                'strings'  => array(
                    'loading'           => __( 'Loading...', 'aca-ai-content-agent' ),
                    'error'             => __( 'An error occurred.', 'aca-ai-content-agent' ),
                    'success'           => __( 'Operation completed successfully.', 'aca-ai-content-agent' ),
                    'confirm_delete'    => __( 'Are you sure you want to delete this item?', 'aca-ai-content-agent' ),
                    'no_ideas'          => __( 'No ideas available.', 'aca-ai-content-agent' ),
                    'generating_ideas'  => __( 'Generating ideas...', 'aca-ai-content-agent' ),
                    'writing_draft'     => __( 'Writing draft...', 'aca-ai-content-agent' ),
                    'testing_connection' => __( 'Testing connection...', 'aca-ai-content-agent' ),
                    'saving_settings'   => __( 'Saving settings...', 'aca-ai-content-agent' ),
                    'validating_license' => __( 'Validating license...', 'aca-ai-content-agent' ),
                ),
            )
        );
    }

    /**
     * Add custom styles to admin head.
     */
    public static function add_custom_styles() {
        $hook = get_current_screen();
        
        if ( ! self::is_aca_page( $hook->id ) ) {
            return;
        }

        echo '<style>
            /* Ensure Inter font is applied globally on ACA pages */
            body {
                font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
            }
            
            /* Override WordPress admin styles for ACA pages */
            .wrap.aca-admin-page {
                font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            }
            
            /* Ensure Bootstrap Icons work properly */
            .bi {
                display: inline-block;
                font-family: "bootstrap-icons" !important;
                font-style: normal;
                font-weight: normal !important;
                font-variant: normal;
                text-transform: none;
                line-height: 1;
                vertical-align: middle;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            
            /* Hide WordPress admin header on ACA pages for cleaner look */
            .wrap.aca-admin-page .wp-header-end {
                display: none;
            }
            
            /* Ensure proper spacing */
            .wrap.aca-admin-page {
                margin: 0;
                padding: 20px;
            }
            
            /* Override WordPress admin colors for consistency */
            .wrap.aca-admin-page .notice {
                border-left-color: var(--aca-primary-start);
            }
            
            .wrap.aca-admin-page .notice-success {
                border-left-color: var(--aca-success);
            }
            
            .wrap.aca-admin-page .notice-error {
                border-left-color: var(--aca-error);
            }
            
            .wrap.aca-admin-page .notice-warning {
                border-left-color: var(--aca-warning);
            }
        </style>';
    }

    /**
     * Check if current page is an ACA page.
     */
    private static function is_aca_page( $hook ) {
        $aca_pages = array(
            'toplevel_page_aca-ai-content-agent',
            'aca-ai-content-agent_page_aca-ai-content-agent-settings',
            'aca-ai-content-agent_page_aca-ai-content-agent-license',
            'aca-ai-content-agent_page_aca-ai-content-agent-diagnostics',
        );

        return in_array( $hook, $aca_pages, true );
    }

    /**
     * Get asset URL.
     */
    public static function get_asset_url( $path ) {
        return plugin_dir_url( ACA_AI_CONTENT_AGENT_PLUGIN_FILE ) . 'admin/' . ltrim( $path, '/' );
    }

    /**
     * Get asset path.
     */
    public static function get_asset_path( $path ) {
        return plugin_dir_path( ACA_AI_CONTENT_AGENT_PLUGIN_FILE ) . 'admin/' . ltrim( $path, '/' );
    }

    /**
     * Check if asset exists.
     */
    public static function asset_exists( $path ) {
        return file_exists( self::get_asset_path( $path ) );
    }

    /**
     * Get asset version for cache busting.
     */
    public static function get_asset_version( $path ) {
        $file_path = self::get_asset_path( $path );
        
        if ( file_exists( $file_path ) ) {
            return filemtime( $file_path );
        }
        
        return ACA_AI_CONTENT_AGENT_VERSION;
    }
}
