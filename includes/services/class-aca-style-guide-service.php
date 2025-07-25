<?php
/**
 * The style guide service.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 */

/**
 * The style guide service.
 *
 * This class defines all code necessary for style guide logic.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 * @author     Your Name <email@example.com>
 */
class ACA_Style_Guide_Service {

    /**
     * Generate and update the Style Guide.
     */
    public static function generate_style_guide() {
        ACA_Log_Service::add('Attempting to generate style guide.');
        $options     = get_option('aca_ai_content_agent_options');

        $post_types = $options['analysis_post_types'] ?? ['post'];
        $depth      = $options['analysis_depth'] ?? 20;

        $query_args = [
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'posts_per_page' => $depth,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        if (!empty($options['analysis_include_categories'])) {
            $query_args['category__in'] = array_map('intval', $options['analysis_include_categories']);
        }

        if (!empty($options['analysis_exclude_categories'])) {
            $query_args['category__not_in'] = array_map('intval', $options['analysis_exclude_categories']);
        }

        $posts    = get_posts($query_args);
        $contents = '';
        foreach ($posts as $p) {
            $contents .= "\n\n" . wp_strip_all_tags($p->post_content);
        }

        if (empty($contents)) {
            return new WP_Error('no_content', __('No content found for analysis.', 'aca-ai-content-agent'));
        }

        $prompts = self::get_default_prompts();
        $prompt  = sprintf($prompts['style_guide'], $contents);

        $style_guide = ACA_Gemini_Api::call($prompt);

        if (!is_wp_error($style_guide)) {
            set_transient('aca_ai_content_agent_style_guide', $style_guide, WEEK_IN_SECONDS);
            update_option('aca_ai_content_agent_style_guide', $style_guide);
            // ACA_AI_Content_Agent_Engine::add_log('Style guide generated and cached successfully.', 'success');
        } else {
            ACA_Log_Service::add('Failed to generate style guide: ' . $style_guide->get_error_message(), 'error');
        }

        return $style_guide;
    }

    /**
     * Get style guide for a specific profile name.
     */
    public static function get_style_guide_for_profile($name) {
        $profiles = self::get_brand_profiles();
        if (isset($profiles[$name])) {
            return $profiles[$name];
        }
        return get_option('aca_ai_content_agent_style_guide', '');
    }

    /**
     * Retrieve stored brand voice profiles.
     */
    public static function get_brand_profiles() {
        $profiles = get_option('aca_ai_content_agent_brand_profiles', []);
        if (!is_array($profiles)) {
            $profiles = [];
        }
        return $profiles;
    }

    /**
     * Retrieve stored prompts, falling back to defaults when not set.
     *
     * @return array Associative array of prompt templates.
     */
    public static function get_prompts() {
        $defaults = self::get_default_prompts();
        $stored   = get_option( 'aca_ai_content_agent_prompts', [] );
        if ( is_array( $stored ) ) {
            return array_merge( $defaults, array_intersect_key( $stored, $defaults ) );
        }
        return $defaults;
    }

    public static function get_default_prompts() {
        return [
            'style_guide'      => "Analyze the following texts. Create a 'Style Guide' that defines the writing tone (e.g., friendly, formal, witty), sentence structure (short, long), paragraph length, and general formatting style (e.g., use of lists, bold text). This guide should be a set of instructions for another writer to imitate this style. The texts are:\n\n%s",
            'idea_generation'  => "Existing blog post titles are: [%s]. Based on these topics, suggest %d new, SEO-friendly, and engaging blog post titles that are related but do not repeat these. Return only a numbered list of titles.",
            'content_writing'  => "Task: Write a SEO-friendly blog post of approximately 800 words with the title '%s'. Structure the post with an introduction, a main body with H2 and H3 subheadings, and a conclusion. At the end, return the entire response as a single JSON object with the keys: postContent, tags, metaDescription, and sources.",
        ];
    }

    /**
     * Save or update a brand voice profile.
     */
    public static function save_brand_profile($name, $guide) {
        $profiles = self::get_brand_profiles();
        $profiles[sanitize_key($name)] = sanitize_textarea_field($guide);
        update_option('aca_ai_content_agent_brand_profiles', $profiles);
    }
}