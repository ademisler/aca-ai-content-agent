<?php
/**
 * ACA - AI Content Agent
 *
 * Automation Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the registration and rendering of automation-related settings fields.
 *
 * @since 1.2.0
 */
class ACA_Settings_Automation {

    /**
     * Constructor.
     *
     * Registers the settings section and fields for automation configuration.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register automation settings section and fields.
     *
     * @since 1.2.0
     */
    public function register_settings() {
        add_settings_section(
            'aca_ai_content_agent_automation_settings_section',
            esc_html__( 'Automation Settings', 'aca-ai-content-agent' ),
            null,
            'aca-ai-content-agent'
        );

        add_settings_field(
            'aca_ai_content_agent_working_mode',
            esc_html__( 'Working Mode', 'aca-ai-content-agent' ),
            [ $this, 'render_working_mode_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_automation_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_automation_frequency',
            esc_html__( 'Automation Frequency', 'aca-ai-content-agent' ),
            [ $this, 'render_automation_frequency_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_automation_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_generation_limit',
            esc_html__( 'Generation Limit', 'aca-ai-content-agent' ),
            [ $this, 'render_generation_limit_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_automation_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_default_author',
            esc_html__( 'Default Author', 'aca-ai-content-agent' ),
            [ $this, 'render_default_author_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_automation_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_default_profile',
            esc_html__( 'Brand Voice Profile', 'aca-ai-content-agent' ),
            [ $this, 'render_default_profile_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_automation_settings_section'
        );
    }

    /**
     * Render the working mode select field.
     *
     * @since 1.2.0
     */
    public function render_working_mode_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $current_mode = isset( $options['working_mode'] ) ? $options['working_mode'] : 'manual';
        if ( ! ACA_Helper::is_pro() ) {
            echo '<select name="aca_ai_content_agent_options[working_mode]" disabled>';
            echo '<option value="manual" selected>' . esc_html__( 'Manual Mode', 'aca-ai-content-agent' ) . '</option>';
            echo '</select>';
            echo '<p class="description">' . esc_html__( 'Automation modes require ACA Pro.', 'aca-ai-content-agent' ) . '</p>';
            echo '<input type="hidden" name="aca_ai_content_agent_options[working_mode]" value="manual">';
            return;
        }

        $modes = [
            'manual'    => esc_html__( 'Manual Mode', 'aca-ai-content-agent' ),
            'semi-auto' => esc_html__( 'Semi-Automatic (Ideas & Approval)', 'aca-ai-content-agent' ),
            'full-auto' => esc_html__( 'Fully Automatic (Draft Creation)', 'aca-ai-content-agent' ),
        ];

        echo '<select name="aca_ai_content_agent_options[working_mode]">';
        foreach ( $modes as $key => $label ) {
            $selected = selected( $current_mode, $key, false );
            echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'Choose how ACA operates. Note: All content is always saved as a draft.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the automation frequency select field.
     *
     * @since 1.2.0
     */
    public function render_automation_frequency_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $current_freq = isset( $options['automation_frequency'] ) ? $options['automation_frequency'] : 'daily';
        $schedules = wp_get_schedules();
        $frequencies = [];
        foreach ( $schedules as $key => $schedule ) {
            $frequencies[ $key ] = $schedule['display'];
        }

        echo '<select name="aca_ai_content_agent_options[automation_frequency]">';
        foreach ( $frequencies as $key => $label ) {
            $selected = selected( $current_freq, $key, false );
            echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'How often the automatic tasks should run.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the generation limit input field.
     *
     * @since 1.2.0
     */
    public function render_generation_limit_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $limit = isset( $options['generation_limit'] ) ? $options['generation_limit'] : 5;
        echo '<input type="number" name="aca_ai_content_agent_options[generation_limit]" value="' . esc_attr( $limit ) . '" class="small-text" min="1">';
        echo '<p class="description">' . esc_html__( 'Max number of ideas/drafts per cycle to control API costs.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the default author dropdown field.
     *
     * @since 1.2.0
     */
    public function render_default_author_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $selected_author = isset( $options['default_author'] ) ? $options['default_author'] : get_current_user_id();
        wp_dropdown_users( [
            'name' => 'aca_ai_content_agent_options[default_author]',
            'selected' => $selected_author,
            'show_option_none' => esc_html__( 'Select an Author', 'aca-ai-content-agent' ),
        ] );
    }

    /**
     * Render the brand voice profile select field.
     *
     * @since 1.2.0
     */
    public function render_default_profile_field() {
        $options  = get_option( 'aca_ai_content_agent_options' );
        $profiles = ACA_Style_Guide_Service::get_brand_profiles();
        $current  = $options['default_profile'] ?? '';

        echo '<select name="aca_ai_content_agent_options[default_profile]">';
        echo '<option value="">' . esc_html__( 'Default', 'aca-ai-content-agent' ) . '</option>';
        foreach ( $profiles as $key => $label ) {
            $selected = selected( $current, $key, false );
            echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $key ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'Select which brand voice profile to use for new drafts.', 'aca-ai-content-agent' ) . '</p>';
    }
}
