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

class ACA_Admin_Assets {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
    }

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

        // Enqueue JS
        wp_enqueue_script(
            'aca-ai-content-agent-admin-js',
            plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/aca-admin.js',
            [ 'jquery' ], // Add jquery dependency if needed
            ACA_AI_CONTENT_AGENT_VERSION,
            true
        );

        wp_localize_script( 'aca-ai-content-agent-admin-js', 'aca_ai_content_agent_admin_ajax', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'aca_ai_content_agent_admin_nonce' ),
        ] );
    }
}
