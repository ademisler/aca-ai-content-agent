<?php
/**
 * ACA - AI Content Agent
 *
 * License Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Settings_License {

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function register_settings() {
        register_setting( 'aca_ai_content_agent_license_group', 'aca_ai_content_agent_license_key', [ 'sanitize_callback' => [ $this, 'sanitize_license_key' ] ] );
    }

    public function render_license_page() {
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'aca_ai_content_agent_license_group' );
            ?>
            <h3><?php esc_html_e( 'ACA Pro License', 'aca-ai-content-agent' ); ?></h3>
            <p><?php esc_html_e( 'Enter your license key to unlock all features and receive updates.', 'aca-ai-content-agent' ); ?></p>
            <?php $lic_key = get_option( 'aca_ai_content_agent_license_key' ); ?>
            <?php $placeholder = ! empty( $lic_key ) ? esc_html__( '***************** (already saved)', 'aca-ai-content-agent' ) : ''; ?>
            <input type="text" id="aca_ai_content_agent_license_key" name="aca_ai_content_agent_license_key" value="" placeholder="<?php echo esc_attr( $placeholder ); ?>" class="regular-text">
            <button type="button" class="button" id="aca-ai-content-agent-validate-license"><?php esc_html_e( 'Validate License', 'aca-ai-content-agent' ); ?></button>
            <span id="aca-ai-content-agent-license-status" style="margin-left: 10px;"></span>
            <?php submit_button( esc_html__( 'Activate License', 'aca-ai-content-agent' ) ); ?>
        </form>
        <?php
    }

    public function sanitize_license_key( $input ) {
        $existing = get_option( 'aca_ai_content_agent_license_key' );

        if ( ! isset( $input ) || '' === trim( $input ) ) {
            return $existing;
        }

        $sanitized_key = sanitize_text_field( $input );

        return aca_ai_content_agent_encrypt( $sanitized_key );
    }
}
