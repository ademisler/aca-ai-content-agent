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

class ACA_Engine {

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
            'content_writing' => "Task: Write a SEO-friendly blog post of approximately 800 words with the title '%s'. Structure the post with an introduction, a main body with H2 and H3 subheadings, and a conclusion. At the end, return the entire response as a single JSON object with the keys: postContent, tags, metaDescription, and sources.",
        ];

        $custom_prompts = get_option('aca_prompts', []);
        return wp_parse_args($custom_prompts, $defaults);
    }

    /**
     * Return the default prompt set.
     */
    public static function get_default_prompts() {
        return [
            'style_guide'      => "Analyze the following texts. Create a 'Style Guide' that defines the writing tone (e.g., friendly, formal, witty), sentence structure (short, long), paragraph length, and general formatting style (e.g., use of lists, bold text). This guide should be a set of instructions for another writer to imitate this style. The texts are:\n\n%s",
            'idea_generation'  => "Existing blog post titles are: [%s]. Based on these topics, suggest %d new, SEO-friendly, and engaging blog post titles that are related but do not repeat these. Return only a numbered list of titles.",
            'content_writing'  => "Task: Write a SEO-friendly blog post of approximately 800 words with the title '%s'. Structure the post with an introduction, a main body with H2 and H3 subheadings, and a conclusion. At the end, return the entire response as a single JSON object with the keys: postContent, tags, metaDescription, and sources.",
        ];
    }

    /**
     * Retrieve stored brand voice profiles.
     */
    public static function get_brand_profiles() {
        $profiles = get_option('aca_brand_profiles', []);
        if (!is_array($profiles)) {
            $profiles = [];
        }
        return $profiles;
    }

    /**
     * Save or update a brand voice profile.
     */
    public static function save_brand_profile($name, $guide) {
        $profiles = self::get_brand_profiles();
        $profiles[sanitize_key($name)] = sanitize_textarea_field($guide);
        update_option('aca_brand_profiles', $profiles);
    }

    /**
     * Get style guide for a specific profile name.
     */
    public static function get_style_guide_for_profile($name) {
        $profiles = self::get_brand_profiles();
        if (isset($profiles[$name])) {
            return $profiles[$name];
        }
        return get_option('aca_style_guide', '');
    }

    /**
     * Generate and update the Style Guide.
     */
    public static function generate_style_guide() {
        self::add_log('Attempting to generate style guide.');
        $options     = get_option('aca_options');

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

        $prompts = self::get_prompts();
        $prompt  = sprintf($prompts['style_guide'], $contents);

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

        $options = get_option('aca_options');

        // Free version monthly limit
        if ( ! aca_is_pro() ) {
            $count = get_option( 'aca_idea_count_current_month', 0 );
            if ( $count >= 5 ) {
                return new WP_Error( 'limit_reached', __( 'Monthly idea limit reached for free version.', 'aca-ai-content-agent' ) );
            }
        }

        $prompts    = self::get_prompts();
        $post_types = $options['analysis_post_types'] ?? ['post'];
        $depth      = 20;
        $posts      = get_posts([
            'post_type'      => $post_types,
            'post_status'    => 'publish',
            'posts_per_page' => $depth,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ]);
        $titles = [];
        foreach ($posts as $p) {
            $titles[] = $p->post_title;
        }
        $existing_titles = implode(', ', $titles);
        $limit  = $options['generation_limit'] ?? 5;
        $prompt = sprintf($prompts['idea_generation'], $existing_titles, $limit);

        $response = aca_call_gemini_api($prompt);

        if (is_wp_error($response)) {
            self::add_log('Failed to generate ideas: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $ideas = explode("\n", trim($response));
        $cleaned_ideas = [];
        $inserted_ids   = [];
        foreach ($ideas as $idea) {
            $cleaned_idea = preg_replace('/^\d+\.\s*/', '', trim($idea));
            if (!empty($cleaned_idea)) {
                $wpdb->insert(
                    $table_name,
                    [
                        'idea_title' => $cleaned_idea,
                        'created_at' => current_time('mysql'),
                    ]
                );
                $inserted_ids[] = $wpdb->insert_id;
                $cleaned_ideas[] = $cleaned_idea;
            }
        }

        if ( ! aca_is_pro() ) {
            $count = get_option( 'aca_idea_count_current_month', 0 );
            update_option( 'aca_idea_count_current_month', $count + count( $inserted_ids ) );
        }
        self::add_log(sprintf('%d new ideas generated and saved.', count($inserted_ids)), 'success');
        return $inserted_ids;
    }

    /**
     * Generate a full post draft from an idea.
     */
    public static function write_post_draft($idea_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ideas';
        $idea = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $idea_id));

        if (!$idea) {
            return new WP_Error('idea_not_found', __('Idea not found.', 'aca-ai-content-agent'));
        }

        if ( ! aca_is_pro() ) {
            $draft_count = get_option( 'aca_draft_count_current_month', 0 );
            if ( $draft_count >= 2 ) {
                return new WP_Error( 'limit_reached', __( 'Monthly draft limit reached for free version.', 'aca-ai-content-agent' ) );
            }
        }

        self::add_log(sprintf('Attempting to write draft for idea #%d: "%s"', $idea_id, $idea->idea_title));

        $options   = get_option('aca_options');
        $prompts   = self::get_prompts();
        $profile   = $options['default_profile'] ?? '';
        $style_guide = self::get_style_guide_for_profile($profile);
        $prompt    = sprintf($prompts['content_writing'], $idea->idea_title);

        $response = aca_call_gemini_api($prompt, $style_guide);

        if (is_wp_error($response)) {
            self::add_log('Failed to write draft: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $parts = self::parse_ai_response($response);

        $post_data = [
            'post_title'  => $idea->idea_title,
            'post_content'=> $parts['content'],
            'post_status' => 'draft',
            'post_author' => $options['default_author'] ?? get_current_user_id(),
        ];

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            self::add_log('Failed to insert post into database: ' . $post_id->get_error_message(), 'error');
            return $post_id;
        }

        if ( ! aca_is_pro() ) {
            $draft_count = get_option( 'aca_draft_count_current_month', 0 );
            update_option( 'aca_draft_count_current_month', $draft_count + 1 );
        }

        // Update idea status in the database
        $wpdb->update(
            $table_name,
            ['status' => 'drafted'],
            ['id' => $idea_id]
        );

        self::add_log(sprintf('Successfully created draft (Post ID: %d) for idea #%d.', $post_id, $idea_id), 'success');

        // Additional enrichment steps
        if (!is_wp_error($post_id)) {
            if (!empty($parts['tags'])) {
                wp_set_post_tags($post_id, $parts['tags']);
            }
            if (!empty($parts['meta_description'])) {
                update_post_meta($post_id, '_aca_meta_description', $parts['meta_description']);
            }

            self::enrich_draft($post_id, $response, $idea->idea_title);
        }

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

        // Optional data-backed section
        $options = get_option('aca_options');
        if (!empty($options['add_data_sections'])) {
            self::add_data_section($post_id, $title);
        }
    }

    /**
     * Parse AI response into sections.
     */
    public static function parse_ai_response($response) {
        $sections = [
            'content'          => '',
            'tags'             => '',
            'meta_description' => '',
            'sources'          => ''
        ];

        $json = json_decode($response, true);
        if (null !== $json && json_last_error() === JSON_ERROR_NONE) {
            $sections['content']          = $json['postContent'] ?? '';
            $sections['tags']             = is_array($json['tags'] ?? null) ? implode(',', $json['tags']) : ($json['tags'] ?? '');
            $sections['meta_description'] = $json['metaDescription'] ?? '';
            $sections['sources']          = is_array($json['sources'] ?? null) ? implode("\n", $json['sources']) : ($json['sources'] ?? '');
            return $sections;
        }

        // Fallback to legacy parsing for backward compatibility
        if (preg_match('/---POST CONTENT---\s*(.*?)\s*---TAGS---/is', $response, $m)) {
            $sections['content'] = trim($m[1]);
        }
        if (preg_match('/---TAGS---\s*(.*?)\s*---META DESCRIPTION---/is', $response, $m)) {
            $sections['tags'] = trim($m[1]);
        }
        if (preg_match('/---META DESCRIPTION---\s*(.*?)\s*---SOURCES---/is', $response, $m)) {
            $sections['meta_description'] = trim($m[1]);
        }
        if (preg_match('/---SOURCES---\s*(.*)$/is', $response, $m)) {
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

        $content  = $post->post_content;

        $keywords = self::extract_keywords($post->post_title . ' ' . wp_strip_all_tags($content), 10);
        $inserted = 0;

        global $wpdb;
        $likes = [];
        foreach ($keywords as $kw) {
            $like = '%' . $wpdb->esc_like($kw) . '%';
            $likes[] = $wpdb->prepare('(post_title LIKE %s OR post_content LIKE %s)', $like, $like);
        }

        if (!empty($likes)) {
            $sql = $wpdb->prepare(
                "SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_status='publish' AND post_type='post' AND ID != %d AND (" . implode(' OR ', $likes) . ")",
                $post_id
            );
            $targets = $wpdb->get_results($sql);

            foreach ($keywords as $keyword) {
                foreach ($targets as $t) {
                    if (stripos($t->post_content, $keyword) !== false || stripos($t->post_title, $keyword) !== false) {
                        if (preg_match('/(' . preg_quote($keyword, '/') . ')/i', $content, $match)) {
                            $link = get_permalink($t->ID);
                            $content = preg_replace('/' . preg_quote($match[1], '/') . '/i', '<a href="' . esc_url($link) . '">' . $match[1] . '</a>', $content, 1);
                            $inserted++;
                            break;
                        }
                    }
                }

                if ($inserted >= $max_links) {
                    break;
                }
            }
        }

        wp_update_post([
            'ID'           => $post_id,
            'post_content' => $content,
        ]);
    }

    /**
     * Extract simple keywords from text.
     */
    private static function extract_keywords($text, $limit = 10) {
        $text  = strtolower($text);
        $words = preg_split('/[^a-zA-Z0-9ğüşöçıİĞÜŞÖÇ]+/', $text);
        $stop  = ['the','and','for','with','that','this','have','from','your','you','about','into','will','been','them','then','than'];
        $keywords = [];
        foreach ($words as $w) {
            if (strlen($w) > 3 && !in_array($w, $stop) && !in_array($w, $keywords)) {
                $keywords[] = $w;
                if (count($keywords) >= $limit) {
                    break;
                }
            }
        }
        return $keywords;
    }

    /**
     * Download and set a featured image using Unsplash.
     */
    public static function maybe_set_featured_image($post_id, $query, $provider) {
        if ($provider === 'unsplash') {
            $url = 'https://source.unsplash.com/1600x900/?' . urlencode($query);
        } elseif ($provider === 'pexels') {
            $options     = get_option('aca_options');
            $api_key_enc = $options['pexels_api_key'] ?? '';
            $api_key     = aca_safe_decrypt( $api_key_enc );
            if (empty($api_key)) {
                return;
            }
            $endpoint = 'https://api.pexels.com/v1/search?per_page=1&query=' . urlencode($query);
            $response = wp_remote_get($endpoint, [
                'headers' => ['Authorization' => $api_key],
                'timeout' => 15,
            ]);
            if (is_wp_error($response)) {
                self::add_log('Pexels request failed: ' . $response->get_error_message(), 'error');
                return;
            }
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (empty($body['photos'][0]['src']['large'])) {
                return;
            }
            $url = $body['photos'][0]['src']['large'];
        } elseif ($provider === 'dalle') {
            if ( ! aca_is_pro() ) {
                return;
            }
            $options     = get_option('aca_options');
            $api_key_enc = $options['openai_api_key'] ?? '';
            $api_key     = aca_safe_decrypt( $api_key_enc );
            if ( empty( $api_key ) ) {
                return;
            }
            $endpoint = 'https://api.openai.com/v1/images/generations';
            $response = wp_remote_post( $endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => wp_json_encode([
                    'model'  => 'dall-e-3',
                    'prompt' => $query,
                    'n'      => 1,
                    'size'   => '1024x1024',
                ]),
                'timeout' => 60,
            ] );
            if ( is_wp_error( $response ) ) {
                self::add_log( 'DALL-E request failed: ' . $response->get_error_message(), 'error' );
                return;
            }
            $body = json_decode( wp_remote_retrieve_body( $response ), true );
            if ( empty( $body['data'][0]['url'] ) ) {
                return;
            }
            $url = $body['data'][0]['url'];
        } else {
            return;
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($url);
        if (is_wp_error($tmp)) {
            self::add_log('Image download failed: ' . $tmp->get_error_message(), 'error');
            return;
        }

        $type = wp_check_filetype($url);
        if (function_exists('getimagesize')) {
            $info = @getimagesize($tmp);
            if ($info && ! empty($info['mime'])) {
                $type['type'] = $info['mime'];
            }
        }

        $size = filesize($tmp);
        $allowed_types = ['image/jpeg', 'image/png'];
        if ($size === false || $size > 2 * MB_IN_BYTES || empty($type['type']) || ! in_array($type['type'], $allowed_types, true)) {
            self::add_log('Downloaded image rejected due to size or type.', 'error');
            wp_delete_file( $tmp );
            return;
        }

        $file_array = [
            'name'     => basename($url) . '.jpg',
            'tmp_name' => $tmp,
        ];

        $id = media_handle_sideload($file_array, $post_id);
        if (is_wp_error($id)) {
            self::add_log('Failed to sideload image: ' . $id->get_error_message(), 'error');
        } else {
            set_post_thumbnail($post_id, $id);
        }
        wp_delete_file( $tmp );
    }

    /**
     * Check plagiarism via Copyscape API and store the result.
     */
    public static function check_plagiarism($post_id, $content) {
        if (!aca_is_pro()) {
            return; // Pro feature only
        }
        $options   = get_option('aca_options');
        $user      = $options['copyscape_username'] ?? '';
        $key_enc   = $options['copyscape_api_key'] ?? '';
        $key       = aca_safe_decrypt( $key_enc );
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

    /**
     * Generate and append a data-backed section with statistics.
     */
    public static function add_data_section($post_id, $title) {
        if (!aca_is_pro()) {
            return; // Pro feature only
        }

        $prompt = sprintf(
            "Provide a short HTML section containing recent statistics or data relevant to the article titled '%s'. Begin with <h3>Key Statistics</h3> and include a bulleted list or table with sources.",
            $title
        );

        $response = aca_call_gemini_api($prompt);

        if (is_wp_error($response)) {
            self::add_log('Failed to generate data section: ' . $response->get_error_message(), 'error');
            return;
        }

        $post = get_post($post_id);
        if (!$post) {
            return;
        }

        $content = $post->post_content . "\n\n" . $response;
        wp_update_post([
            'ID' => $post_id,
            'post_content' => $content
        ]);
    }

    /**
     * Generate a content cluster for strategic planning.
     */
    public static function generate_content_cluster($topic) {
        if (!aca_is_pro()) {
            return new WP_Error('pro_only', __('Content clusters are available in the Pro version.', 'aca-ai-content-agent'));
        }

        global $wpdb;
        $cluster_table = $wpdb->prefix . 'aca_clusters';
        $item_table    = $wpdb->prefix . 'aca_cluster_items';

        self::add_log('Generating content cluster for topic: ' . $topic);

        $prompt = sprintf(
            "Main topic: %s. Provide 5 related subtopics as short titles for a content cluster. Return each title on a new line without numbering.",
            $topic
        );

        $response = aca_call_gemini_api($prompt);

        if (is_wp_error($response)) {
            self::add_log('Failed to generate content cluster: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $subtopics = array_filter(array_map('trim', explode("\n", $response)));

        if (empty($subtopics)) {
            return new WP_Error('empty_cluster', __('No subtopics were returned.', 'aca-ai-content-agent'));
        }

        $wpdb->insert($cluster_table, [
            'topic'      => $topic,
            'created_at' => current_time('mysql'),
        ]);
        $cluster_id = $wpdb->insert_id;

        foreach ($subtopics as $sub) {
            $wpdb->insert($item_table, [
                'cluster_id'     => $cluster_id,
                'subtopic_title' => $sub,
                'created_at'     => current_time('mysql'),
            ]);
        }

        return $subtopics;
    }

    /**
     * Suggest updates for an existing post.
     */
    public static function suggest_content_update($post_id) {
        if (!aca_is_pro()) {
            return new WP_Error('pro_only', __('Update assistant is a Pro feature.', 'aca-ai-content-agent'));
        }

        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('post_not_found', __('Post not found.', 'aca-ai-content-agent'));
        }

        $prompt = sprintf(
            "You are an SEO assistant. Review the following article and suggest improvements or updates in bullet points.\n\n---ARTICLE---\n%s",
            $post->post_content
        );

        $response = aca_call_gemini_api($prompt);

        if (is_wp_error($response)) {
            self::add_log('Failed to generate update suggestions: ' . $response->get_error_message(), 'error');
            return $response;
        }

        update_post_meta($post_id, '_aca_update_suggestions', $response);
        return $response;
    }

    /**
     * Record user feedback for an idea.
     */
    public static function record_feedback($idea_id, $value) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ideas';
        $wpdb->update(
            $table_name,
            ['feedback' => intval($value)],
            ['id' => intval($idea_id)]
        );
    }

    /**
     * Fetch top queries from Google Search Console.
     */
    public static function fetch_gsc_data($site_url, $start_date, $end_date) {
        $options      = get_option('aca_options');
        $api_key_enc  = $options['gsc_api_key'] ?? '';
        $api_key      = aca_safe_decrypt( $api_key_enc );
        if (empty($api_key) || empty($site_url)) {
            return new WP_Error('missing_credentials', __('Search Console API key or site URL is missing.', 'aca-ai-content-agent'));
        }

        $endpoint = add_query_arg('key', $api_key, 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($site_url) . '/searchAnalytics/query');
        $body = [
            'startDate'  => $start_date,
            'endDate'    => $end_date,
            'dimensions' => ['query'],
            'rowLimit'   => 10,
        ];

        $response = wp_remote_post($endpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => wp_json_encode($body),
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    /**
     * Generate new ideas using Google Search Console queries.
     *
     * This analyzes search queries that bring traffic but are not yet covered
     * by existing posts and sends them to the AI for new title suggestions.
     *
     * @return array|WP_Error Array of inserted idea IDs on success or WP_Error.
     */
    public static function generate_ideas_from_gsc() {
        global $wpdb;

        $options     = get_option('aca_options');
        $site_url    = $options['gsc_site_url'] ?? '';
        $api_key_enc = $options['gsc_api_key'] ?? '';
        $api_key     = aca_safe_decrypt( $api_key_enc );

        if (empty($site_url) || empty($api_key)) {
            return new WP_Error('missing_credentials', __('Search Console credentials are missing.', 'aca-ai-content-agent'));
        }

        $end   = current_time( 'Y-m-d' );
        $start = gmdate( 'Y-m-d', strtotime( '-30 days', strtotime( $end ) ) );
        $data  = self::fetch_gsc_data($site_url, $start, $end);

        if (is_wp_error($data)) {
            return $data;
        }

        $queries = [];
        if (!empty($data['rows'])) {
            foreach ($data['rows'] as $row) {
                $query = $row['keys'][0] ?? '';
                if (!$query) {
                    continue;
                }
                $exists = get_posts([
                    's'              => $query,
                    'post_type'      => 'post',
                    'post_status'    => 'publish',
                    'fields'         => 'ids',
                    'posts_per_page' => 1,
                ]);
                if (empty($exists)) {
                    $queries[] = $query;
                }
            }
        }

        if (empty($queries)) {
            return new WP_Error('no_queries', __('No new query opportunities found.', 'aca-ai-content-agent'));
        }

        $limit  = $options['generation_limit'] ?? 5;
        $prompt = sprintf(
            'The following search queries are popular on Google but not well covered on our site: %s. Suggest %d SEO-friendly blog post titles to address them.',
            implode(', ', $queries),
            $limit
        );

        $response = aca_call_gemini_api($prompt);

        if (is_wp_error($response)) {
            self::add_log('Failed to generate GSC ideas: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $ideas = array_filter(array_map('trim', explode("\n", $response)));
        $inserted_ids = [];
        $table_name   = $wpdb->prefix . 'aca_ideas';

        foreach ($ideas as $idea) {
            $clean = preg_replace('/^\d+\.\s*/', '', $idea);
            if ($clean) {
                $wpdb->insert(
                    $table_name,
                    [
                        'idea_title' => $clean,
                        'created_at' => current_time('mysql'),
                    ]
                );
                $inserted_ids[] = $wpdb->insert_id;
            }
        }

        self::add_log(sprintf('%d ideas generated from Search Console data.', count($inserted_ids)), 'success');
        return $inserted_ids;
    }
}