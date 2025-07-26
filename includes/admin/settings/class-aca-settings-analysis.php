<?php
/**
 * ACA - AI Content Agent
 *
 * Analysis Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the registration and rendering of content analysis settings fields.
 *
 * @since 1.2.0
 */
class ACA_Settings_Analysis {

    /**
     * Constructor.
     *
     * Registers the settings section and fields for content analysis configuration.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register content analysis settings section and fields.
     *
     * @since 1.2.0
     */
    public function register_settings() {
        add_settings_section(
            'aca_ai_content_agent_analysis_settings_section',
            esc_html__( 'Content Analysis & Learning Rules', 'aca-ai-content-agent' ),
            null,
            'aca-ai-content-agent'
        );

        add_settings_field(
            'aca_ai_content_agent_analysis_post_types',
            esc_html__( 'Analyze Content Types', 'aca-ai-content-agent' ),
            [ $this, 'render_analysis_post_types_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_analysis_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_analysis_depth',
            esc_html__( 'Analysis Depth', 'aca-ai-content-agent' ),
            [ $this, 'render_analysis_depth_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_analysis_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_analysis_categories',
            esc_html__( 'Analysis Categories', 'aca-ai-content-agent' ),
            [ $this, 'render_analysis_categories_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_analysis_settings_section'
        );

        add_settings_field(
            'aca_ai_content_agent_style_guide_frequency',
            esc_html__( 'Style Guide Refresh', 'aca-ai-content-agent' ),
            [ $this, 'render_style_guide_frequency_field' ],
            'aca-ai-content-agent',
            'aca_ai_content_agent_analysis_settings_section'
        );
    }

    /**
     * Render the post types checkbox field for analysis.
     *
     * @since 1.2.0
     */
    public function render_analysis_post_types_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $checked_post_types = isset( $options['analysis_post_types'] ) ? $options['analysis_post_types'] : [ 'post' ];

        foreach ( $post_types as $post_type ) {
            if ( in_array( $post_type->name, [ 'attachment', 'page' ] ) ) {
                continue;
            }
            $checked = in_array( $post_type->name, $checked_post_types ) ? 'checked' : '';
            echo '<label><input type="checkbox" name="aca_ai_content_agent_options[analysis_post_types][]" value="' . esc_attr( $post_type->name ) . '" ' . esc_attr( $checked ) . '> ' . esc_html( $post_type->label ) . '</label><br>';
        }
        echo '<p class="description">' . esc_html__( 'Select the content types ACA should analyze to learn the writing style.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the analysis depth input field.
     *
     * @since 1.2.0
     */
    public function render_analysis_depth_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $depth = isset( $options['analysis_depth'] ) ? $options['analysis_depth'] : 20;
        echo '<input type="number" name="aca_ai_content_agent_options[analysis_depth]" value="' . esc_attr( $depth ) . '" class="small-text" min="5" max="100">';
        echo '<p class="description">' . esc_html__( 'Number of recent posts (5-100) to analyze for learning the writing style.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the include/exclude categories checkboxes for analysis.
     *
     * @since 1.2.0
     */
    public function render_analysis_categories_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $include_categories = isset( $options['analysis_include_categories'] ) ? $options['analysis_include_categories'] : [];
        $exclude_categories = isset( $options['analysis_exclude_categories'] ) ? $options['analysis_exclude_categories'] : [];
        $categories = get_categories( [ 'hide_empty' => 0 ] );

        echo '<div style="display: flex; gap: 20px;">';
        echo '<div style="flex: 1;">';
        echo '<strong>' . esc_html__( 'Include Categories', 'aca-ai-content-agent' ) . '</strong>';
        echo '<div style="height: 150px; overflow-y: scroll; border: 1px solid #ddd; padding: 5px; background: #fff;">';
        foreach ( $categories as $category ) {
            $checked = in_array( $category->term_id, $include_categories ) ? 'checked' : '';
            echo '<label><input type="checkbox" name="aca_ai_content_agent_options[analysis_include_categories][]" value="' . esc_attr( $category->term_id ) . '" ' . esc_attr( $checked ) . '> ' . esc_html( $category->name ) . '</label><br>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div style="flex: 1;">';
        echo '<strong>' . esc_html__( 'Exclude Categories', 'aca-ai-content-agent' ) . '</strong>';
        echo '<div style="height: 150px; overflow-y: scroll; border: 1px solid #ddd; padding: 5px; background: #fff;">';
        foreach ( $categories as $category ) {
            $checked = in_array( $category->term_id, $exclude_categories ) ? 'checked' : '';
            echo '<label><input type="checkbox" name="aca_ai_content_agent_options[analysis_exclude_categories][]" value="' . esc_attr( $category->term_id ) . '" ' . esc_attr( $checked ) . '> ' . esc_html( $category->name ) . '</label><br>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<p class="description">' . esc_html__( 'Fine-tune the style analysis by including or excluding specific categories. If any category is included, only posts from those categories will be analyzed.', 'aca-ai-content-agent' ) . '</p>';
    }

    /**
     * Render the style guide refresh frequency select field.
     *
     * @since 1.2.0
     */
    public function render_style_guide_frequency_field() {
        $options = get_option( 'aca_ai_content_agent_options' );
        $current = isset( $options['style_guide_frequency'] ) ? $options['style_guide_frequency'] : 'weekly';
        $schedules = wp_get_schedules();
        $frequencies = [ 'manual' => esc_html__( 'Manual', 'aca-ai-content-agent' ) ];
        echo '<select name="aca_ai_content_agent_options[style_guide_frequency]">';
        foreach ( $schedules as $key => $label ) {
            $selected = selected( $current, $key, false );
            echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $label ) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'How often ACA should regenerate the style guide automatically.', 'aca-ai-content-agent' ) . '</p>';
    }
}
