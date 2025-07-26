<?php
/**
 * ACA - AI Content Agent
 *
 * API Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Settings_Api {

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function register_settings() {
        add_settings_section(
            'aca_ai_content_agent_api_settings_section',
            esc_html__( 'API and Connection Settings', 'aca-ai-content-agent' ),
            null,
            'aca-ai-content-agent'
        );

        add_settings_field(
            'aca_ai_content_agent_gemini_api_key',
            esc_html__( 'Google Gemini API Key', 'aca-ai-content-agent' ),
            [ $this, 'render_api_key_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_api_test',
            esc_html__( 'Connection Test', 'aca-ai-content-agent' ),
            [ $this, 'render_api_test_button' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_copyscape_username',
            esc_html__( 'Copyscape Username', 'aca-ai-content-agent' ),
            [ $this, 'render_copyscape_username_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_copyscape_api_key',
            esc_html__( 'Copyscape API Key', 'aca-ai-content-agent' ),
            [ $this, 'render_copyscape_api_key_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_gsc_site_url',
            esc_html__( 'Search Console Site URL', 'aca-ai-content-agent' ),
            [ $this, 'render_gsc_site_url_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_gsc_api_key',
            esc_html__( 'Search Console API Key', 'aca-ai-content-agent' ),
            [ $this, 'render_gsc_api_key_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_pexels_api_key',
            esc_html__( 'Pexels API Key', 'aca-ai-content-agent' ),
            [ $this, 'render_pexels_api_key_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_openai_api_key',
            esc_html__( 'OpenAI API Key', 'aca-ai-content-agent' ),
            [ $this, 'render_openai_api_key_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_api_settings_section'
        );
    }

    public function render_api_key_field() {
        $api_key = get_option( 'aca_ai_content_agent_gemini_api_key' );
        $placeholder = ! empty( $api_key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : '';
        echo '<input type="password" id="aca_ai_content_agent_gemini_api_key" name="aca_ai_content_agent_gemini_api_key" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__( 'Enter your Google Gemini API key. Your key is obfuscated for security.', 'aca-ai-content-agent' ) . '</p>';
    }

    public function render_api_test_button() {
        echo '<button type="button" class="button" id="aca-ai-content-agent-test-connection">' . esc_html__( 'Test Connection', 'aca-ai-content-agent' ) . '</button>';
        echo '<span id="aca-ai-content-agent-test-status" style="margin-left: 10px;"></span>';
    }

    public function render_copyscape_username_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $username = isset( $options['copyscape_username'] ) ? $options['copyscape_username'] : '';
        echo '<input type="text" name="aca_ai_content_agent_options[copyscape_username]" value="' . esc_attr( $username ) . '" class="regular-text">';
    }

    public function render_copyscape_api_key_field() {
        $options     = get_option( 'aca_ai_content_agent_options' );
        $key         = $options['copyscape_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : '';
        echo '<input type="password" name="aca_ai_content_agent_options[copyscape_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    public function render_gsc_site_url_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $url = isset( $options['gsc_site_url'] ) ? $options['gsc_site_url'] : '';
        echo '<input type="text" name="aca_ai_content_agent_options[gsc_site_url]" value="' . esc_attr( $url ) . '" class="regular-text">';
    }

    public function render_gsc_api_key_field() {
        $options     = get_option( 'aca_ai_content_agent_options' );
        $key         = $options['gsc_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : '';
        echo '<input type="password" name="aca_ai_content_agent_options[gsc_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    public function render_pexels_api_key_field() {
        $options     = get_option( 'aca_ai_content_agent_options' );
        $key         = $options['pexels_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : '';
        echo '<input type="password" name="aca_ai_content_agent_options[pexels_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    public function render_openai_api_key_field() {
        $options     = get_option( 'aca_ai_content_agent_options' );
        $key         = $options['openai_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : '';
        echo '<input type="password" name="aca_ai_content_agent_options[openai_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }
}
