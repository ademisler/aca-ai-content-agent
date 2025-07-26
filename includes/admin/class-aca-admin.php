<?php
/**
 * ACA - AI Content Agent
 *
 * Main Admin Class
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin {

    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        require_once plugin_dir_path( __FILE__ ) . 'class-aca-admin-menu.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-aca-admin-notices.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-aca-admin-assets.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-api.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-automation.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-analysis.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-enrichment.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-management.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-prompts.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-license.php';
    }

    private function init_hooks() {
        add_action( 'admin_init', [ $this, 'register_core_settings' ] );

        new ACA_Admin_Menu();
        new ACA_Admin_Notices();
        new ACA_Admin_Assets();
        new ACA_Settings_Api();
        new ACA_Settings_Automation();
        new ACA_Settings_Analysis();
        new ACA_Settings_Enrichment();
        new ACA_Settings_Management();
        new ACA_Settings_Prompts();
        new ACA_Settings_License();
    }

    /**
     * Register the core settings required by the plugin settings page.
     * This tells WordPress which options are allowed to be saved for our settings group.
     */
    public function register_core_settings() {
        // Bu grup, ayarlar sayfasındaki formda settings_fields() ile belirttiğiniz grup adıyla eşleşmelidir.
        $settings_group = 'aca_ai_content_agent_settings_group';

        // 1. 'aca_ai_content_agent_options' seçeneğini kaydet. Bu, ayarların çoğunu içeren bir dizidir.
        register_setting( $settings_group, 'aca_ai_content_agent_options', [ $this, 'sanitize_options_array' ] );

        // 2. 'aca_ai_content_agent_gemini_api_key' seçeneğini ayrı olarak kaydet.
        register_setting( $settings_group, 'aca_ai_content_agent_gemini_api_key', [ $this, 'sanitize_and_encrypt_api_key' ] );
    }

    /**
     * Sanitize and encrypt the Gemini API key before saving.
     *
     * @param string $input The raw API key from the form.
     * @return string The encrypted key or the existing key if the input is empty.
     */
    public function sanitize_and_encrypt_api_key( $input ) {
        $existing_key = get_option( 'aca_ai_content_agent_gemini_api_key' );

        // If the user didn't enter a new key, keep the old one.
        if ( empty( trim( $input ) ) ) {
            return $existing_key;
        }

        $sanitized_key = sanitize_text_field( $input );
        $encrypted_key = ACA_Encryption_Util::encrypt( $sanitized_key );

        return is_wp_error( $encrypted_key ) ? $existing_key : $encrypted_key;
    }

    /**
     * Sanitize the main options array.
     *
     * @param array $input The raw options array from the form.
     * @return array The sanitized options array.
     */
    public function sanitize_options_array( $input ) {
        $options = get_option( 'aca_ai_content_agent_options', [] );
        if ( ! is_array( $input ) ) {
            return $options; // Return existing options if input is not an array
        }

        // Sanitize each expected key here
        // Example for automation settings
        if ( isset( $input['working_mode'] ) ) {
            $options['working_mode'] = sanitize_key( $input['working_mode'] );
        }
        if ( isset( $input['automation_frequency'] ) ) {
            $options['automation_frequency'] = sanitize_key( $input['automation_frequency'] );
        }

        // Example for analysis settings
        if ( isset( $input['analysis_post_types'] ) && is_array( $input['analysis_post_types'] ) ) {
            $options['analysis_post_types'] = array_map( 'sanitize_text_field', $input['analysis_post_types'] );
        } else {
            $options['analysis_post_types'] = [];
        }

        // Add sanitization for all other options keys...
        // ... (pexels_api_key, copyscape_username, etc.)
        // Example for an API key within the options array
        if ( isset( $input['pexels_api_key'] ) ) {
            if ( empty(trim($input['pexels_api_key'])) ) {
                // If input is empty, don't overwrite existing key
                $options['pexels_api_key'] = $options['pexels_api_key'] ?? '';
            } else {
                 $encrypted_key = ACA_Encryption_Util::encrypt( sanitize_text_field( $input['pexels_api_key'] ) );
                 $options['pexels_api_key'] = is_wp_error($encrypted_key) ? '' : $encrypted_key;
            }
        }


        return $options;
    }
}
