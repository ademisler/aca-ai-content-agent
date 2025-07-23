<?php
/**
 * ACA - AI Content Agent
 *
 * Core Functionality
 *
 * @package ACA
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Core {

    /**
     * Add a log entry to the database.
     */
    public static function add_log($message, $type = 'info') {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_logs';
        $wpdb->insert(
            $table_name,
            [
                'log_message' => $message,
                'log_type'    => $type,
                'created_at'  => current_time('mysql'),
            ]
        );
    }

    /**
     * Get the prompts for generation, using custom ones if available.
     */
    public static function get_prompts() {
        $defaults = [
            'style_guide' => "Analyze the following texts. Create a 'Style Guide' that defines the writing tone (e.g., friendly, formal, witty), sentence structure (short, long), paragraph length, and general formatting style (e.g., use of lists, bold text). This guide should be a set of instructions for another writer to imitate this style. The texts are:\n\n%s",
            'idea_generation' => "Existing blog post titles are: [%s]. Based on these topics, suggest %d new, SEO-friendly, and engaging blog post titles that are related but do not repeat these. Return only a numbered list of titles.",
            'content_writing' => "Task: Write a SEO-friendly blog post of approximately 800 words with the title '%s'. Structure the post with an introduction, a main body with H2 and H3 subheadings, and a conclusion.\n\n---\n\nMetadata Request: At the end of the post, provide 5 relevant tags, a meta description of 155 characters, and at least 2 reliable source URLs for any significant data mentioned.\n\n---\n\nFormatting Instruction: Provide the output in the following format, and do not add any other text outside of this structure: ---POST CONTENT--- [Post] ---TAGS--- [Tags] ---META DESCRIPTION--- [Description] ---SOURCES--- [URLs]",
        ];

        $custom_prompts = get_option('aca_prompts', []);
        return wp_parse_args($custom_prompts, $defaults);
    }

    /**
     * Generate and update the Style Guide.
     */
    public static function generate_style_guide() {
        self::add_log('Attempting to generate style guide.');
        $options = get_option('aca_options');
        // ... (rest of the function is the same)

        $style_guide = aca_call_gemini_api($prompt);

        if (!is_wp_error($style_guide)) {
            set_transient('aca_style_guide', $style_guide, WEEK_IN_SECONDS);
            update_option('aca_style_guide', $style_guide);
            self::add_log('Style guide generated and cached successfully.', 'success');
        } else {
            self::add_log('Failed to generate style guide: ' . $style_guide->get_error_message(), 'error');
        }

        return $style_guide;
    }

    /**
     * Generate new post ideas.
     */
    public static function generate_ideas() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ideas';
        self::add_log('Attempting to generate new ideas.');

        // ... (rest of the function is the same)

        $response = aca_call_gemini_api($prompt);

        if (is_wp_error($response)) {
            self::add_log('Failed to generate ideas: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $ideas = explode("\n", trim($response));
        $cleaned_ideas = [];
        foreach ($ideas as $idea) {
            $cleaned_idea = preg_replace('/^\d+\.\s*/', '', trim($idea));
            if (!empty($cleaned_idea)) {
                $cleaned_ideas[] = $cleaned_idea;
                $wpdb->insert(
                    $table_name,
                    [
                        'idea_title' => $cleaned_idea,
                        'created_at' => current_time('mysql'),
                    ]
                );
            }
        }
        self::add_log(sprintf('%d new ideas generated and saved.', count($cleaned_ideas)), 'success');
        return $cleaned_ideas;
    }

    /**
     * Generate a full post draft from an idea.
     */
    public static function write_post_draft($idea_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ideas';
        $idea = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $idea_id));

        if (!$idea) {
            return new WP_Error('idea_not_found', __('Idea not found.', 'aca'));
        }

        self::add_log(sprintf('Attempting to write draft for idea #%d: "%s"', $idea_id, $idea->idea_title));

        // ... (rest of the function is the same)

        $response = aca_call_gemini_api($prompt, $style_guide);

        if (is_wp_error($response)) {
            self::add_log('Failed to write draft: ' . $response->get_error_message(), 'error');
            return $response;
        }

        // ... (rest of the function is the same)

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            self::add_log('Failed to insert post into database: ' . $post_id->get_error_message(), 'error');
            return $post_id;
        }

        // Update idea status in the database
        $wpdb->update(
            $table_name,
            ['status' => 'drafted'],
            ['id' => $idea_id]
        );

        self::add_log(sprintf('Successfully created draft (Post ID: %d) for idea #%d.', $post_id, $idea_id), 'success');

        // Additional enrichment steps
        self::enrich_draft($post_id, $response, $idea->idea_title);

        // ... (rest of the function is the same)

        return $post_id;
    }

    /**
     * Enrich the generated draft with additional features.
     */
    public static function enrich_draft($post_id, $ai_response, $title) {
        $parts = self::parse_ai_response($ai_response);

        // Append sources if available
        if (!empty($parts['sources'])) {
            self::append_sources($post_id, $parts['sources']);
        }

        // Add internal links
        $options = get_option('aca_options');
        $max_links = $options['internal_links_max'] ?? 3;
        self::add_internal_links($post_id, $max_links);

        // Maybe set featured image
        $provider = $options['featured_image_provider'] ?? 'none';
        if ($provider !== 'none') {
            self::maybe_set_featured_image($post_id, $title, $provider);
        }

        // Optional plagiarism check
        if (!empty($parts['content'])) {
            self::check_plagiarism($post_id, $parts['content']);
        }
    }

    /**
     * Parse AI response into sections.
     */
    public static function parse_ai_response($response) {
        $sections = [
            'content' => '',
            'tags' => '',
            'meta_description' => '',
            'sources' => ''
        ];

        if (preg_match('/---POST CONTENT---\s*(.*?)\s*---TAGS---/s', $response, $m)) {
            $sections['content'] = trim($m[1]);
        }
        if (preg_match('/---TAGS---\s*(.*?)\s*---META DESCRIPTION---/s', $response, $m)) {
            $sections['tags'] = trim($m[1]);
        }
        if (preg_match('/---META DESCRIPTION---\s*(.*?)\s*---SOURCES---/s', $response, $m)) {
            $sections['meta_description'] = trim($m[1]);
        }
        if (preg_match('/---SOURCES---\s*(.*)$/s', $response, $m)) {
            $sections['sources'] = trim($m[1]);
        }

        return $sections;
    }

    /**
     * Append sources to the end of the post content.
     */
    public static function append_sources($post_id, $sources) {
        $post = get_post($post_id);
        if (!$post) {
            return;
        }
        $content = $post->post_content;
        $content .= "\n\n<h3>Sources</h3>\n<ul>";
        foreach (preg_split('/\n+/', $sources) as $src) {
            $src = trim($src);
            if (!empty($src)) {
                $content .= '<li><a href="' . esc_url($src) . '" target="_blank" rel="nofollow">' . esc_html($src) . '</a></li>';
            }
        }
        $content .= '</ul>';
        wp_update_post([
            'ID' => $post_id,
            'post_content' => $content
        ]);
    }

    /**
     * Add internal links to the post content.
     */
    public static function add_internal_links($post_id, $max_links) {
        if ($max_links <= 0) {
            return;
        }

        $post = get_post($post_id);
        if (!$post) {
            return;
        }

        $content = $post->post_content;
        $existing_posts = get_posts([
            'numberposts' => $max_links,
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'rand'
        ]);

        foreach ($existing_posts as $existing) {
            $title = preg_quote($existing->post_title, '/');
            if (preg_match('/\b' . $title . '\b/i', $content)) {
                $link = get_permalink($existing->ID);
                $content = preg_replace('/\b' . $title . '\b/i', '<a href="' . esc_url($link) . '">' . $existing->post_title . '</a>', $content, 1);
            }
        }

        wp_update_post([
            'ID' => $post_id,
            'post_content' => $content
        ]);
    }

    /**
     * Download and set a featured image using Unsplash.
     */
    public static function maybe_set_featured_image($post_id, $query, $provider) {
        if ($provider !== 'unsplash') {
            return;
        }

        $url = 'https://source.unsplash.com/1600x900/?' . urlencode($query);
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            return;
        }

        $file_array = [
            'name'     => basename($url) . '.jpg',
            'tmp_name' => $tmp,
        ];

        $id = media_handle_sideload($file_array, $post_id);
        if (!is_wp_error($id)) {
            set_post_thumbnail($post_id, $id);
        }
        @unlink($tmp);
    }

    /**
     * Check plagiarism via Copyscape API and store the result.
     */
    public static function check_plagiarism($post_id, $content) {
        $options = get_option('aca_options');
        $user = $options['copyscape_username'] ?? '';
        $key  = $options['copyscape_api_key'] ?? '';
        if (empty($user) || empty($key)) {
            return;
        }

        $endpoint = add_query_arg([
            'c' => 'csearch',
            'u' => $user,
            'k' => $key,
            'o' => 'json',
            't' => urlencode(wp_trim_words($content, 5000, ''))
        ], 'https://www.copyscape.com/api/');

        $response = wp_remote_get($endpoint);
        if (is_wp_error($response)) {
            return;
        }
        $body = wp_remote_retrieve_body($response);
        update_post_meta($post_id, '_aca_plagiarism_raw', $body);
    }

    // ... (rest of the file is the same)
}