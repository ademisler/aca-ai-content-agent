<?php
/**
 * ACA - AI Content Agent
 *
 * API Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the registration and rendering of API-related settings fields.
 *
 * @since 1.2.0
 */
class ACA_Settings_Api {

    /**
     * Constructor.
     *
     * Registers the settings section and fields for API configuration.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register API settings section and fields.
     *
     * @since 1.2.0
     */
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

    /**
     * Render the Google Gemini API key input field.
     *
     * @since 1.2.0
     */
    public function render_api_key_field() {
        $encrypted_api_key = get_option( 'aca_ai_content_agent_gemini_api_key' );
        $api_key = '';
        
        // Decrypt the API key if it exists
        if ( ! empty( $encrypted_api_key ) ) {
            $api_key = ACA_Encryption_Util::safe_decrypt( $encrypted_api_key );
        }
        
        $placeholder = ! empty( $api_key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : esc_html__( 'Enter your Google Gemini API key', 'aca-ai-content-agent' );
        
        echo '<div class="aca-form-row">';
        echo '<input type="password" id="aca_ai_content_agent_gemini_api_key" name="aca_ai_content_agent_gemini_api_key" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text aca-form-input">';
        echo '<p class="description">' . esc_html__( 'Enter your Google Gemini API key. Your key is encrypted and stored securely.', 'aca-ai-content-agent' ) . '</p>';
        echo '<p class="description"><a href="https://makersuite.google.com/app/apikey" target="_blank">' . esc_html__( 'Get your API key from Google AI Studio', 'aca-ai-content-agent' ) . '</a></p>';
        echo '</div>';
    }

    /**
     * Render the API connection test button.
     *
     * @since 1.2.0
     */
    public function render_api_test_button() {
        echo '<div class="aca-form-row">';
        echo '<button type="button" class="aca-action-button" id="aca-ai-content-agent-test-connection">';
        echo '<span class="dashicons dashicons-admin-network"></span> ' . esc_html__( 'Test Connection', 'aca-ai-content-agent' );
        echo '</button>';
        echo '<span id="aca-ai-content-agent-test-status" class="aca-status" style="margin-left: 10px;"></span>';
        echo '<p class="description">' . esc_html__( 'Test your API connection to ensure everything is working correctly.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the Copyscape username input field.
     *
     * @since 1.2.0
     */
    public function render_copyscape_username_field() {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        $username = isset( $options['copyscape_username'] ) ? $options['copyscape_username'] : '';
        
        echo '<div class="aca-form-row">';
        echo '<input type="text" name="aca_ai_content_agent_options[copyscape_username]" value="' . esc_attr( $username ) . '" class="regular-text aca-form-input" placeholder="' . esc_attr__( 'Enter your Copyscape username', 'aca-ai-content-agent' ) . '">';
        echo '<p class="description">' . esc_html__( 'Your Copyscape account username for plagiarism checking.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the Copyscape API key input field.
     *
     * @since 1.2.0
     */
    public function render_copyscape_api_key_field() {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        $encrypted_key = $options['copyscape_api_key'] ?? '';
        $key = '';
        
        // Decrypt the API key if it exists
        if ( ! empty( $encrypted_key ) ) {
            $key = ACA_Encryption_Util::safe_decrypt( $encrypted_key );
        }
        
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : esc_html__( 'Enter your Copyscape API key', 'aca-ai-content-agent' );
        
        echo '<div class="aca-form-row">';
        echo '<input type="password" name="aca_ai_content_agent_options[copyscape_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text aca-form-input">';
        echo '<p class="description">' . esc_html__( 'Your Copyscape API key for plagiarism checking. Leave empty to keep existing key.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the Search Console site URL input field.
     *
     * @since 1.2.0
     */
    public function render_gsc_site_url_field() {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        $url = isset( $options['gsc_site_url'] ) ? $options['gsc_site_url'] : '';
        
        echo '<div class="aca-form-row">';
        echo '<input type="url" name="aca_ai_content_agent_options[gsc_site_url]" value="' . esc_attr( $url ) . '" class="regular-text aca-form-input" placeholder="https://example.com">';
        echo '<p class="description">' . esc_html__( 'Your website URL as registered in Google Search Console.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the Search Console API key input field.
     *
     * @since 1.2.0
     */
    public function render_gsc_api_key_field() {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        $encrypted_key = $options['gsc_api_key'] ?? '';
        $key = '';
        
        // Decrypt the API key if it exists
        if ( ! empty( $encrypted_key ) ) {
            $key = ACA_Encryption_Util::safe_decrypt( $encrypted_key );
        }
        
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : esc_html__( 'Enter your Search Console API key', 'aca-ai-content-agent' );
        
        echo '<div class="aca-form-row">';
        echo '<input type="password" name="aca_ai_content_agent_options[gsc_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text aca-form-input">';
        echo '<p class="description">' . esc_html__( 'Your Google Search Console API key. Leave empty to keep existing key.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the Pexels API key input field.
     *
     * @since 1.2.0
     */
    public function render_pexels_api_key_field() {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        $encrypted_key = $options['pexels_api_key'] ?? '';
        $key = '';
        
        // Decrypt the API key if it exists
        if ( ! empty( $encrypted_key ) ) {
            $key = ACA_Encryption_Util::safe_decrypt( $encrypted_key );
        }
        
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : esc_html__( 'Enter your Pexels API key', 'aca-ai-content-agent' );
        
        echo '<div class="aca-form-row">';
        echo '<input type="password" name="aca_ai_content_agent_options[pexels_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text aca-form-input">';
        echo '<p class="description">' . esc_html__( 'Your Pexels API key for image suggestions. Leave empty to keep existing key.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the OpenAI API key input field.
     *
     * @since 1.2.0
     */
    public function render_openai_api_key_field() {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        $encrypted_key = $options['openai_api_key'] ?? '';
        $key = '';
        
        // Decrypt the API key if it exists
        if ( ! empty( $encrypted_key ) ) {
            $key = ACA_Encryption_Util::safe_decrypt( $encrypted_key );
        }
        
        $placeholder = ! empty( $key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : esc_html__( 'Enter your OpenAI API key', 'aca-ai-content-agent' );
        
        echo '<div class="aca-form-row">';
        echo '<input type="password" name="aca_ai_content_agent_options[openai_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text aca-form-input">';
        echo '<p class="description">' . esc_html__( 'Your OpenAI API key for additional AI features. Leave empty to keep existing key.', 'aca-ai-content-agent' ) . '</p>';
        echo '</div>';
    }

    /**
     * Render the main settings form.
     *
     * @since 1.2.0
     */
    public function render_settings_form() {
        ?>
        <form method="post" action="options.php">
            <?php settings_fields( 'aca_ai_content_agent_settings_group' ); ?>
            <?php do_settings_sections( 'aca-ai-content-agent' ); ?>
            <?php submit_button(); ?>
        </form>
        <?php
    }
}
