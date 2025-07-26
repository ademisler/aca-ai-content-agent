<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Assets
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Admin assets management class.
 *
 * Handles the enqueuing of CSS and JavaScript files for the admin interface.
 *
 * @since 1.2.0
 */
class ACA_Admin_Assets {

    /**
     * Constructor.
     *
     * Registers the enqueue_scripts method to the admin_enqueue_scripts hook.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @since 1.2.0
     * @param string $hook The current admin page hook.
     * @return void
     */
    public function enqueue_scripts( $hook ) {
        if ( strpos( $hook, 'page_aca-ai-content-agent' ) === false && $hook !== 'edit.php' ) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'aca-ai-content-agent-admin-css',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/aca-admin.css',
            [],
            ACA_AI_CONTENT_AGENT_VERSION
        );

        // Enqueue JS with proper dependencies
        wp_enqueue_script(
            'aca-ai-content-agent-admin-js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/aca-admin.js',
            [ 'jquery' ],
            ACA_AI_CONTENT_AGENT_VERSION,
            true
        );

        // Localize script with AJAX data
        wp_localize_script( 'aca-ai-content-agent-admin-js', 'aca_ai_content_agent_admin_ajax', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'aca_ai_content_agent_admin_nonce' ),
            'strings'  => [
                'confirm_delete' => __( 'Are you sure you want to delete this item?', 'aca-ai-content-agent' ),
                'error_occurred' => __( 'An error occurred. Please try again.', 'aca-ai-content-agent' ),
                'success'        => __( 'Operation completed successfully.', 'aca-ai-content-agent' ),
            ],
        ] );

        // SECURITY: Add security headers for admin pages
        if (!headers_sent()) {
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: SAMEORIGIN');
            header('X-XSS-Protection: 1; mode=block');
        }
    }
}
