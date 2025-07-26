<?php
/**
 * ACA - AI Content Agent
 *
 * Prompts Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the registration and rendering of prompt and brand profile settings fields and page.
 *
 * @since 1.2.0
 */
class ACA_Settings_Prompts {

    /**
     * Constructor.
     *
     * Registers the prompts settings group and fields.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register prompts and brand profiles settings group and fields.
     *
     * @since 1.2.0
     */
    public function register_settings() {
        register_setting( 'aca_ai_content_agent_prompts_group', 'aca_ai_content_agent_prompts', [ 'sanitize_callback' => [ $this, 'sanitize_prompts' ] ] );
        register_setting( 'aca_ai_content_agent_prompts_group', 'aca_ai_content_agent_brand_profiles', [ 'sanitize_callback' => [ $this, 'sanitize_brand_profiles' ] ] );
    }

    /**
     * Render the prompts and brand profiles settings page.
     *
     * @since 1.2.0
     */
    public function render_prompts_page() {
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'aca_ai_content_agent_prompts_group' );
            $prompts = ACA_Style_Guide_Service::get_default_prompts();
            $profiles = ACA_Style_Guide_Service::get_brand_profiles();
            ?>
            <h3><?php esc_html_e( 'Style Guide Prompt', 'aca-ai-content-agent' ); ?></h3>
            <textarea name="aca_ai_content_agent_prompts[style_guide]" rows="10" cols="50" class="large-text"><?php echo esc_textarea( $prompts['style_guide'] ); ?></textarea>
            <p class="description"><?php esc_html_e( 'This prompt is used to create the style guide from your existing content. Available variables: <code>{{content_corpus}}</code>', 'aca-ai-content-agent' ); ?></p>

            <h3><?php esc_html_e( 'Idea Generation Prompt', 'aca-ai-content-agent' ); ?></h3>
            <textarea name="aca_ai_content_agent_prompts[idea_generation]" rows="10" cols="50" class="large-text"><?php echo esc_textarea( $prompts['idea_generation'] ); ?></textarea>
            <p class="description"><?php esc_html_e( 'This prompt is used to generate new article ideas. Available variables: <code>{{existing_titles}}</code>, <code>{{limit}}</code>', 'aca-ai-content-agent' ); ?></p>

            <h3><?php esc_html_e( 'Content Writing Prompt', 'aca-ai-content-agent' ); ?></h3>
            <textarea name="aca_ai_content_agent_prompts[content_writing]" rows="10" cols="50" class="large-text"><?php echo esc_textarea( $prompts['content_writing'] ); ?></textarea>
            <p class="description"><?php esc_html_e( 'This prompt is used to write the full article draft. Available variables: <code>{{style_guide}}</code>, <code>{{title}}</code>', 'aca-ai-content-agent' ); ?></p>

            <?php submit_button( esc_html__( 'Save Prompts', 'aca-ai-content-agent' ) ); ?>
            <h3><?php esc_html_e( 'Brand Voice Profiles', 'aca-ai-content-agent' ); ?></h3>
            <table class="widefat">
                <thead><tr><th><?php esc_html_e( 'Profile Key', 'aca-ai-content-agent' ); ?></th><th><?php esc_html_e( 'Style Guide', 'aca-ai-content-agent' ); ?></th></tr></thead>
                <tbody>
                    <?php foreach ( $profiles as $key => $guide ) : ?>
                        <tr><td><?php echo esc_html( $key ); ?></td><td><textarea name="aca_ai_content_agent_brand_profiles[<?php echo esc_attr( $key ); ?>]" rows="4" class="large-text"><?php echo esc_textarea( $guide ); ?></textarea></td></tr>
                    <?php endforeach; ?>
                    <tr><td><input type="text" name="aca_ai_content_agent_brand_profiles[new_key]" placeholder="<?php esc_attr_e( 'new-profile', 'aca-ai-content-agent' ); ?>"></td><td><textarea name="aca_ai_content_agent_brand_profiles[new_value]" rows="4" class="large-text"></textarea></td></tr>
                </tbody>
            </table>
            <p class="description"><?php esc_html_e( 'Define additional style guides for different content types.', 'aca-ai-content-agent' ); ?></p>
            <?php submit_button( esc_html__( 'Save Profiles', 'aca-ai-content-agent' ) ); ?>
        </form>
        <?php
    }

    /**
     * Sanitize the prompts before saving.
     *
     * @since 1.2.0
     * @param array $input The input prompts array.
     * @return array The sanitized prompts array.
     */
    public function sanitize_prompts( $input ) {
        $new_input = [];
        $default_prompts = ACA_Style_Guide_Service::get_default_prompts();

        foreach ( $default_prompts as $key => $value ) {
            if ( isset( $input[ $key ] ) && ! empty( trim( $input[ $key ] ) ) ) {
                $new_input[ $key ] = sanitize_textarea_field( $input[ $key ] );
            } else {
                $new_input[ $key ] = $value; // Restore default if empty
            }
        }
        return $new_input;
    }

    /**
     * Sanitize the brand profiles before saving.
     *
     * @since 1.2.0
     * @param array $input The input brand profiles array.
     * @return array The sanitized brand profiles array.
     */
    public function sanitize_brand_profiles( $input ) {
        $clean = [];
        if ( is_array( $input ) ) {
            foreach ( $input as $key => $value ) {
                if ( 'new_key' === $key || 'new_value' === $key ) {
                    continue;
                }
                $clean[ sanitize_key( $key ) ] = sanitize_textarea_field( $value );
            }

            if ( ! empty( $input['new_key'] ) && ! empty( $input['new_value'] ) ) {
                $clean[ sanitize_key( $input['new_key'] ) ] = sanitize_textarea_field( $input['new_value'] );
            }
        }
        return $clean;
    }
}
