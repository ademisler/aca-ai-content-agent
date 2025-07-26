<?php
/**
 * ACA - AI Content Agent
 *
 * Enrichment Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Settings_Enrichment {

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    public function register_settings() {
        add_settings_section(
            'aca_ai_content_agent_enrichment_settings_section',
            esc_html__( 'Content Enrichment', 'aca-ai-content-agent' ),
            null,
            'aca-ai-content-agent'
        );

        add_settings_field(
            'aca_ai_content_agent_internal_links_max',
            esc_html__( 'Max Internal Links', 'aca-ai-content-agent' ),
            [ $this, 'render_internal_links_max_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_enrichment_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_featured_image_provider',
            esc_html__( 'Featured Image Provider', 'aca-ai-content-agent' ),
            [ $this, 'render_featured_image_provider_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_enrichment_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_add_data_sections',
            esc_html__( 'Add Data Sections', 'aca-ai-content-agent' ),
            [ $this, 'render_add_data_sections_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_enrichment_settings_section'
        );
    }

    public function render_internal_links_max_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $max = isset( $options['internal_links_max'] ) ? $options['internal_links_max'] : 3;
        echo '<input type="number" name="aca_ai_content_agent_options[internal_links_max]" value="' . esc_attr( $max ) . '" class="small-text" min="0" max="10">';
        echo '<p class="description">' . esc_html__( 'Maximum number of internal links (0-10) to add to each new draft.', 'aca-ai-content-agent' ) . '</p>';
    }

    public function render_featured_image_provider_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $provider = isset( $options['featured_image_provider'] ) ? $options['featured_image_provider'] : 'none';
        $providers = [
            'none'    => esc_html__( 'None', 'aca-ai-content-agent' ),
            'unsplash'=> esc_html__( 'Unsplash', 'aca-ai-content-agent' ),
            'pexels'  => esc_html__( 'Pexels', 'aca-ai-content-agent' ),
            'dalle'   => esc_html__( 'DALL-E 3', 'aca-ai-content-agent' ),
        ];

        echo '<select name="aca_ai_content_agent_options[featured_image_provider]">';
        foreach ( $providers as $key => $label ) {
            $selected = selected( $provider, $key, false );
            echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'Select a provider for automatically fetching a featured image for each draft.', 'aca-ai-content-agent' ) . '</p>';
    }

    public function render_add_data_sections_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $checked = isset( $options['add_data_sections'] ) ? (bool) $options['add_data_sections'] : false;
        echo '<label><input type="checkbox" name="aca_ai_content_agent_options[add_data_sections]" value="1" ' . checked( $checked, true, false ) . '> ' . esc_html__( 'Append statistics section to drafts (Pro only)', 'aca-ai-content-agent' ) . '</label>';
    }
}
