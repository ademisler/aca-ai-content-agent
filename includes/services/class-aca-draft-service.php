<?php
/**
 * The draft service.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 */

/**
 * The draft service.
 *
 * This class defines all code necessary for draft writing and enrichment.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 * @author     Your Name <email@example.com>
 */
class ACA_Draft_Service {

    /**
     * Generate a full post draft from an idea.
     */
    public static function write_post_draft($idea_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $idea = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $idea_id ) );

        if (!$idea) {
            return new WP_Error('idea_not_found', __('Idea not found.', 'aca-ai-content-agent'));
        }

        if ( ! aca_ai_content_agent_is_pro() ) {
            $draft_count = get_option( 'aca_ai_content_agent_draft_count_current_month', 0 );
            if ( $draft_count >= 2 ) {
                return new WP_Error( 'limit_reached', __( 'Monthly draft limit reached for free version.', 'aca-ai-content-agent' ) );
            }
        }

        ACA_Log_Service::add(sprintf('Attempting to write draft for idea #%d: "%s"', $idea_id, $idea->title));

        $options   = get_option('aca_ai_content_agent_options');
        $prompts   = ACA_Style_Guide_Service::get_prompts();
        $profile   = $options['default_profile'] ?? '';
        $style_guide = ACA_Style_Guide_Service::get_style_guide_for_profile($profile);
        $prompt    = sprintf($prompts['content_writing'], $idea->title);

        $response = ACA_Gemini_Api::call($prompt, $style_guide);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to write draft: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $parts = self::parse_ai_response($response);

        $wpdb->query('START TRANSACTION');

        $post_data = [
            'post_title'  => $idea->title,
            'post_content'=> $parts['content'],
            'post_status' => 'draft',
            'post_author' => $options['default_author'] ?? get_current_user_id(),
        ];

        $post_id = wp_insert_post($post_data, true);

        if (is_wp_error($post_id)) {
            $wpdb->query('ROLLBACK');
            ACA_Log_Service::add('Failed to insert post into database: ' . $post_id->get_error_message(), 'error');
            return $post_id;
        }

        if ( ! aca_ai_content_agent_is_pro() ) {
            $draft_count = get_option( 'aca_ai_content_agent_draft_count_current_month', 0 );
            update_option( 'aca_ai_content_agent_draft_count_current_month', $draft_count + 1 );
        }

        // Update idea status in the database
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $updated = $wpdb->update(
            $table_name,
            ['status' => 'drafted', 'post_id' => $post_id],
            ['id' => $idea_id]
        );

        if (false === $updated) {
            $wpdb->query('ROLLBACK');
            $error_message = __( 'Failed to update idea status in the database.', 'aca-ai-content-agent' );
            ACA_Log_Service::add($error_message, 'error');
            return new WP_Error('database_error', $error_message);
        }

        $wpdb->query('COMMIT');

        self::enrich_draft($post_id, $response, $idea->title);

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
        $options = get_option('aca_ai_content_agent_options');
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
        $options = get_option('aca_ai_content_agent_options');
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
        $content .= "

<h3>" . __( 'Sources', 'aca-ai-content-agent' ) . "</h3>
<ul>";
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

        if (!empty($keywords)) {
            global $wpdb;
            $regexp = implode('|', array_map([$wpdb, 'esc_like'], $keywords));
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $targets = $wpdb->get_results($wpdb->prepare("SELECT ID, post_title, post_content FROM {$wpdb->posts} WHERE post_status='publish' AND post_type='post' AND ID != %d AND (post_title REGEXP %s OR post_content REGEXP %s)", $post_id, $regexp, $regexp));

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
        $keywords = ACA_Gemini_Api::extract_keywords_from_content($text);

        if (is_wp_error($keywords)) {
            ACA_Log_Service::add('Failed to extract keywords: ' . $keywords->get_error_message(), 'error');
            return [];
        }

        return array_slice($keywords, 0, $limit);
    }

    /**
     * Download and set a featured image using Unsplash.
     */
    public static function maybe_set_featured_image($post_id, $query, $provider) {
        if ($provider === 'unsplash') {
            $url = 'https://source.unsplash.com/1600x900/?' . urlencode($query);
        } elseif ($provider === 'pexels') {
            $options     = get_option('aca_ai_content_agent_options');
            $api_key_enc = $options['pexels_api_key'] ?? '';
            $api_key     = ACA_Encryption_Util::safe_decrypt( $api_key_enc );
            if (empty($api_key)) {
                return;
            }
            $endpoint = 'https://api.pexels.com/v1/search?per_page=1&query=' . urlencode($query);
            $response = wp_remote_get($endpoint, [
                'headers' => ['Authorization' => $api_key],
                'timeout' => 15,
            ]);
            if (is_wp_error($response)) {
                ACA_Log_Service::add('Pexels request failed: ' . $response->get_error_message(), 'error');
                return;
            }
            $body = json_decode(wp_remote_retrieve_body($response), true);
            if (empty($body['photos'][0]['src']['large'])) {
                return;
            }
            $url = $body['photos'][0]['src']['large'];
        } elseif ($provider === 'dalle') {
            if ( ! aca_ai_content_agent_is_pro() ) {
                return;
            }
            $options     = get_option('aca_ai_content_agent_options');
            $api_key_enc = $options['openai_api_key'] ?? '';
            $api_key     = ACA_Encryption_Util::safe_decrypt( $api_key_enc );
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
                ACA_Log_Service::add( 'DALL-E request failed: ' . $response->get_error_message(), 'error' );
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
            ACA_Log_Service::add(
                sprintf(
                    'Image download failed: %s',
                    $tmp->get_error_message()
                ),
                'error'
            );
            return;
        }

        $type = wp_check_filetype($tmp);
        if (function_exists('getimagesize')) {
            $info = @getimagesize($tmp);
            if ($info && ! empty($info['mime'])) {
                $type['type'] = $info['mime'];
            }
        }

        $size = filesize($tmp);
        $allowed_types = ['image/jpeg', 'image/png'];
        if ($size === false || $size > 2 * MB_IN_BYTES || empty($type['type']) || ! in_array($type['type'], $allowed_types, true)) {
            ACA_Log_Service::add(__( 'Downloaded image rejected due to size or type.', 'aca-ai-content-agent' ), 'error');
            wp_delete_file( $tmp );
            return;
        }

        $file_array = [
            'name'     => sanitize_title($title) . '.jpg',
            'tmp_name' => $tmp,
        ];

        $id = media_handle_sideload($file_array, $post_id);
        if (is_wp_error($id)) {
            ACA_Log_Service::add('Failed to sideload image: ' . $id->get_error_message(), 'error');
        } else {
            set_post_thumbnail($post_id, $id);
        }
        wp_delete_file( $tmp );
    }

    /**
     * Check plagiarism via Copyscape API and store the result.
     */
    public static function check_plagiarism($post_id, $content) {
        if (!aca_ai_content_agent_is_pro()) {
            return; // Pro feature only
        }
        $options   = get_option('aca_ai_content_agent_options');
        $user      = $options['copyscape_username'] ?? '';
        $key_enc   = $options['copyscape_api_key'] ?? '';
        $key       = ACA_Encryption_Util::safe_decrypt( $key_enc );
        if (empty($user) || empty($key)) {
            return;
        }

        $endpoint = add_query_arg([
            'c' => 'csearch',
            'u' => $user,
            'k' => $key,
            'o' => 'json',
            't' => wp_trim_words($content, 5000, '')
        ], 'https://www.copyscape.com/api/');

        $response = wp_remote_get($endpoint);
        if (is_wp_error($response)) {
            return;
        }
        $body = wp_remote_retrieve_body($response);
        update_post_meta($post_id, '_aca_ai_content_agent_plagiarism_raw', $body);
    }

    /**
     * Provide AI-powered update suggestions for an existing post.
     */
    public static function suggest_content_update($post_id) {
        if ( ! aca_ai_content_agent_is_pro() ) {
            return new WP_Error('pro_feature', __('This feature is available in the Pro version.', 'aca-ai-content-agent'));
        }

        $post = get_post( $post_id );
        if ( ! $post ) {
            return new WP_Error( 'post_not_found', __('Post not found.', 'aca-ai-content-agent') );
        }

        $content = wp_strip_all_tags( $post->post_content );
        $prompt  = sprintf(
            /* translators: 1: The post title, 2: The post content. */
            __( 'Suggest concise improvements to refresh and optimize the following blog post for SEO. Return a bullet list only.\n\nTitle: %1$s\n\n%2$s', 'aca-ai-content-agent' ),
            $post->post_title,
            $content
        );

        $response = ACA_Gemini_Api::call( $prompt );

        if ( is_wp_error( $response ) ) {
            ACA_Log_Service::add( 'Failed to generate update suggestions: ' . $response->get_error_message(), 'error' );
            return $response;
        }

        return $response;
    }

    /**
     * Generate and append a data-backed section with statistics.
     */
    public static function add_data_section($post_id, $title) {
        if (!aca_ai_content_agent_is_pro()) {
            return; // Pro feature only
        }

        $prompt = sprintf(
            "Provide a short HTML section containing recent statistics or data relevant to the article titled '%s'. Begin with <h3>Key Statistics</h3> and include a bulleted list or table with sources.",
            $title
        );

        $response = ACA_Gemini_Api::call($prompt);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to generate data section: ' . $response->get_error_message(), 'error');
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
}