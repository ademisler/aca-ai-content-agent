<?php
/**
 * The draft service.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 */

/**
 * Draft service class for ACA AI Content Agent.
 *
 * Handles draft writing and enrichment features including AI content generation,
 * featured image setting, internal linking, and plagiarism checking.
 *
 * @since 1.2.0
 * @author     Adem Isler <idemasler@gmail.com>
 */
class ACA_Draft_Service {

    /**
     * Maximum file size for downloaded images in bytes.
     *
     * @since 1.2.0
     * @var int
     */
    const MAX_IMAGE_SIZE = 2 * MB_IN_BYTES;

    /**
     * Allowed image types for featured images.
     *
     * @since 1.2.0
     * @var array
     */
    const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png'];

    /**
     * Maximum number of internal links to add.
     *
     * @since 1.2.0
     * @var int
     */
    const DEFAULT_MAX_INTERNAL_LINKS = 3;

    /**
     * Maximum number of keywords to extract.
     *
     * @since 1.2.0
     * @var int
     */
    const MAX_KEYWORDS_TO_EXTRACT = 10;

    /**
     * Default timeout for API requests in seconds.
     *
     * @since 1.2.0
     * @var int
     */
    const DEFAULT_API_TIMEOUT = 120;

    /**
     * Default timeout for image requests in seconds.
     *
     * @since 1.2.0
     * @var int
     */
    const IMAGE_REQUEST_TIMEOUT = 15;

    /**
     * Maximum content length for plagiarism check.
     *
     * @since 1.2.0
     * @var int
     */
    const MAX_CONTENT_LENGTH_FOR_PLAGIARISM = 5000;

    /**
     * Generate a full post draft from an idea.
     *
     * This method creates a complete WordPress post draft from an existing idea.
     * It handles content generation, database transactions, and post enrichment.
     *
     * @since 1.2.0
     * @param int $idea_id The ID of the idea to convert to a draft.
     * @return int|WP_Error The post ID on success, WP_Error on failure.
     */
    public static function write_post_draft($idea_id) {
        // SECURITY FIX: Enhanced security checks
        if (!is_admin() && !wp_doing_ajax()) {
            ACA_Log_Service::add('Unauthorized access to write_post_draft.', 'error');
            return new WP_Error('unauthorized', __('Unauthorized access.', 'aca-ai-content-agent'));
        }

        // SECURITY FIX: Add capability check
        if (!current_user_can('edit_posts') && !current_user_can('manage_aca_ai_content_agent_settings')) {
            ACA_Log_Service::add('Insufficient permissions for write_post_draft.', 'error');
            return new WP_Error('insufficient_permissions', __('You do not have permission to create drafts.', 'aca-ai-content-agent'));
        }

        $idea = self::get_idea_by_id($idea_id);
        if (is_wp_error($idea)) {
            return $idea;
        }

        $limit_check = self::check_draft_limit();
        if (is_wp_error($limit_check)) {
            return $limit_check;
        }

        ACA_Log_Service::add(sprintf('Attempting to write draft for idea #%d: "%s"', $idea_id, $idea->title), 'info');

        $ai_response = self::generate_ai_content($idea);
        if (is_wp_error($ai_response)) {
            return $ai_response;
        }

        $parts = self::parse_ai_response($ai_response);
        $post_id = self::create_post_in_database($idea, $parts);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }

        self::update_idea_status($idea_id, $post_id);
        self::increment_draft_count();
        self::enrich_draft($post_id, $ai_response, $idea->title);

        return $post_id;
    }

    /**
     * Retrieve an idea by its ID.
     *
     * @since 1.2.0
     * @param int $idea_id The idea ID.
     * @return object|WP_Error The idea object or WP_Error on failure.
     */
    private static function get_idea_by_id($idea_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $idea = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE id = %d", $idea_id));

        if (!$idea) {
            ACA_Log_Service::add('Failed to write draft: Idea not found.', 'error');
            return new WP_Error('idea_not_found', __('Idea not found.', 'aca-ai-content-agent'));
        }

        return $idea;
    }

    /**
     * Check if the user has reached the draft limit for the current month.
     *
     * @since 1.2.0
     * @return true|WP_Error True if within limits, WP_Error if limit reached.
     */
    private static function check_draft_limit() {
        if (!aca_ai_content_agent_is_pro()) {
            $draft_count = get_option('aca_ai_content_agent_draft_count_current_month', 0);
            if ($draft_count >= 2) {
                ACA_Log_Service::add('Failed to write draft: Monthly draft limit reached for free version.', 'error');
                return new WP_Error('limit_reached', __('Monthly draft limit reached for free version.', 'aca-ai-content-agent'));
            }
        }
        return true;
    }

    /**
     * Generate AI content for the given idea.
     *
     * @since 1.2.0
     * @param object $idea The idea object.
     * @return string|WP_Error The AI response or WP_Error on failure.
     */
    private static function generate_ai_content($idea) {
        $options = get_option('aca_ai_content_agent_options');
        $prompts = ACA_Style_Guide_Service::get_prompts();
        $profile = $options['default_profile'] ?? '';
        $style_guide = ACA_Style_Guide_Service::get_style_guide_for_profile($profile);
        $prompt = sprintf($prompts['content_writing'], $idea->title);

        $response = ACA_Gemini_Api::call($prompt, $style_guide);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to write draft: ' . $response->get_error_message(), 'error');
            return $response;
        }

        return $response;
    }

    /**
     * Create the post in the WordPress database.
     *
     * @since 1.2.0
     * @param object $idea The idea object.
     * @param array $parts The parsed AI response parts.
     * @return int|WP_Error The post ID or WP_Error on failure.
     */
    private static function create_post_in_database($idea, $parts) {
        global $wpdb;
        $options = get_option('aca_ai_content_agent_options');

        // SECURITY FIX: Proper transaction handling with error management
        $wpdb->query('START TRANSACTION');

        try {
            $post_data = [
                'post_title' => $idea->title,
                'post_content' => $parts['content'],
                'post_status' => 'draft',
                'post_author' => $options['default_author'] ?? get_current_user_id(),
            ];

            $post_id = wp_insert_post($post_data, true);

            if (is_wp_error($post_id)) {
                $wpdb->query('ROLLBACK');
                ACA_Log_Service::add('Failed to insert post into database: ' . $post_id->get_error_message(), 'error');
                return $post_id;
            }

            // Additional validation
            if (!$post_id || $post_id === 0) {
                $wpdb->query('ROLLBACK');
                ACA_Log_Service::add('Failed to insert post: Invalid post ID returned', 'error');
                return new WP_Error('invalid_post_id', __('Failed to create post: Invalid post ID returned.', 'aca-ai-content-agent'));
            }

            $wpdb->query('COMMIT');
            return $post_id;

        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            ACA_Log_Service::add('Exception during post creation: ' . $e->getMessage(), 'error');
            return new WP_Error('database_exception', __('Database error occurred during post creation.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Update the idea status in the database.
     *
     * @since 1.2.0
     * @param int $idea_id The idea ID.
     * @param int $post_id The post ID.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    private static function update_idea_status($idea_id, $post_id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $updated = $wpdb->update(
            $table_name,
            ['status' => 'drafted', 'post_id' => $post_id],
            ['id' => $idea_id]
        );

        if (false === $updated) {
            $error_message = __('Failed to update idea status in the database.', 'aca-ai-content-agent');
            ACA_Log_Service::add($error_message, 'error');
            return new WP_Error('database_error', $error_message);
        }

        return true;
    }

    /**
     * Increment the draft count for the current month.
     *
     * @since 1.2.0
     */
    private static function increment_draft_count() {
        if (!aca_ai_content_agent_is_pro()) {
            $draft_count = get_option('aca_ai_content_agent_draft_count_current_month', 0);
            update_option('aca_ai_content_agent_draft_count_current_month', $draft_count + 1);
        }
    }

    /**
     * Enrich the generated draft with additional features.
     *
     * This method adds various enhancements to the draft including sources,
     * internal links, featured images, plagiarism checks, and data sections.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $ai_response The raw AI response.
     * @param string $title The post title.
     * @return void
     */
    public static function enrich_draft($post_id, $ai_response, $title) {
        $parts = self::parse_ai_response($ai_response);
        if (empty($parts['content'])) {
            $msg = __('Draft enrichment failed: AI response did not contain content.', 'aca-ai-content-agent');
            ACA_Log_Service::add($msg, 'error');
            return new WP_Error('enrich_failed', $msg);
        }

        self::append_sources_if_available($post_id, $parts['sources']);
        self::add_internal_links_if_enabled($post_id);
        self::set_featured_image_if_enabled($post_id, $title);
        self::check_plagiarism_if_enabled($post_id, $parts['content']);
        self::add_data_section_if_enabled($post_id, $title);
    }

    /**
     * Append sources to the post if available.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $sources The sources text.
     */
    private static function append_sources_if_available($post_id, $sources) {
        if (!empty($sources)) {
            self::append_sources($post_id, $sources);
        }
    }

    /**
     * Add internal links if the feature is enabled.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     */
    private static function add_internal_links_if_enabled($post_id) {
        $options = get_option('aca_ai_content_agent_options');
        $max_links = $options['internal_links_max'] ?? self::DEFAULT_MAX_INTERNAL_LINKS;
        self::add_internal_links($post_id, $max_links);
    }

    /**
     * Set featured image if the feature is enabled.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $title The post title.
     */
    private static function set_featured_image_if_enabled($post_id, $title) {
        $options = get_option('aca_ai_content_agent_options');
        $provider = $options['featured_image_provider'] ?? 'none';
        if ($provider !== 'none') {
            self::maybe_set_featured_image($post_id, $title, $provider);
        }
    }

    /**
     * Check plagiarism if the feature is enabled.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $content The post content.
     */
    private static function check_plagiarism_if_enabled($post_id, $content) {
        if (!empty($content)) {
            self::check_plagiarism($post_id, $content);
        }
    }

    /**
     * Add data section if the feature is enabled.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $title The post title.
     */
    private static function add_data_section_if_enabled($post_id, $title) {
        $options = get_option('aca_ai_content_agent_options');
        if (!empty($options['add_data_sections'])) {
            self::add_data_section($post_id, $title);
        }
    }

    /**
     * Parse AI response into sections.
     *
     * This method attempts to parse the AI response in JSON format first,
     * then falls back to legacy parsing if JSON parsing fails.
     *
     * @since 1.2.0
     * @param string $response The AI response.
     * @return array The parsed sections.
     */
    public static function parse_ai_response($response) {
        $sections = [
            'content' => '',
            'tags' => '',
            'meta_description' => '',
            'sources' => ''
        ];

        $json = json_decode($response, true);
        if (null !== $json && json_last_error() === JSON_ERROR_NONE) {
            return self::parse_json_response($json);
        }

        return self::parse_legacy_response($response);
    }

    /**
     * Parse JSON response format.
     *
     * @since 1.2.0
     * @param array $json The JSON response.
     * @return array The parsed sections.
     */
    private static function parse_json_response($json) {
        return [
            'content' => $json['postContent'] ?? '',
            'tags' => is_array($json['tags'] ?? null) ? implode(',', $json['tags']) : ($json['tags'] ?? ''),
            'meta_description' => $json['metaDescription'] ?? '',
            'sources' => is_array($json['sources'] ?? null) ? implode("\n", $json['sources']) : ($json['sources'] ?? '')
        ];
    }

    /**
     * Parse legacy response format.
     *
     * @since 1.2.0
     * @param string $response The legacy response.
     * @return array The parsed sections.
     */
    private static function parse_legacy_response($response) {
        $sections = [
            'content' => '',
            'tags' => '',
            'meta_description' => '',
            'sources' => ''
        ];

        if (!preg_match('/---POST CONTENT---/', $response)) {
            $msg = __('AI response could not be parsed for content.', 'aca-ai-content-agent');
            ACA_Log_Service::add($msg, 'error');
        }

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
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $sources The sources text.
     */
    public static function append_sources($post_id, $sources) {
        $post = get_post($post_id);
        if (!$post) {
            return;
        }

        $content = $post->post_content;
        $content .= "\n\n<h3>" . __('Sources', 'aca-ai-content-agent') . "</h3>\n<ul>";

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
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param int $max_links The maximum number of links to add.
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
        $keywords = self::extract_keywords($post->post_title . ' ' . wp_strip_all_tags($content), self::MAX_KEYWORDS_TO_EXTRACT);
        
        if (empty($keywords)) {
            return;
        }

        $targets = self::find_related_posts($keywords, $post_id);
        $content = self::insert_internal_links($content, $keywords, $targets, $max_links);

        wp_update_post([
            'ID' => $post_id,
            'post_content' => $content,
        ]);
    }

    /**
     * Find related posts based on keywords.
     *
     * @since 1.2.0
     * @param array $keywords The keywords to search for.
     * @param int $exclude_post_id The post ID to exclude from results.
     * @return array The related posts.
     */
    private static function find_related_posts($keywords, $exclude_post_id) {
        global $wpdb;
        
        // SECURITY FIX: Optimize query to prevent memory leaks - only fetch necessary fields
        $regexp = implode('|', array_map([$wpdb, 'esc_like'], $keywords));
        
        // Only fetch ID and title to prevent memory issues with large content
        $related_posts = $wpdb->get_results($wpdb->prepare(
            "SELECT ID, post_title FROM {$wpdb->posts} 
            WHERE post_status='publish' AND post_type='post' AND ID != %d 
            AND (post_title REGEXP %s)
            LIMIT 10", // Add limit to prevent excessive results
            $exclude_post_id, $regexp
        ));

        // If we need content-based matching, do it in smaller batches
        if (empty($related_posts) && !empty($keywords)) {
            $related_posts = $wpdb->get_results($wpdb->prepare(
                "SELECT ID, post_title FROM {$wpdb->posts} 
                WHERE post_status='publish' AND post_type='post' AND ID != %d 
                LIMIT 5", // Small batch for content checking
                $exclude_post_id
            ));
        }

        return $related_posts;
    }

    /**
     * Insert internal links into content.
     *
     * @since 1.2.0
     * @param string $content The post content.
     * @param array $keywords The keywords to link.
     * @param array $targets The target posts.
     * @param int $max_links The maximum number of links to insert.
     * @return string The modified content.
     */
    private static function insert_internal_links($content, $keywords, $targets, $max_links) {
        $inserted = 0;

        foreach ($keywords as $keyword) {
            foreach ($targets as $target) {
                if (stripos($target->post_content, $keyword) !== false || stripos($target->post_title, $keyword) !== false) {
                    if (preg_match('/(' . preg_quote($keyword, '/') . ')/i', $content, $match)) {
                        $link = get_permalink($target->ID);
                        $content = preg_replace(
                            '/' . preg_quote($match[1], '/') . '/i',
                            '<a href="' . esc_url($link) . '">' . $match[1] . '</a>',
                            $content,
                            1
                        );
                        $inserted++;
                        break;
                    }
                }
            }

            if ($inserted >= $max_links) {
                break;
            }
        }

        return $content;
    }

    /**
     * Extract simple keywords from text.
     *
     * @since 1.2.0
     * @param string $text The text to extract keywords from.
     * @param int $limit The maximum number of keywords to return.
     * @return array The extracted keywords.
     */
    private static function extract_keywords($text, $limit = self::MAX_KEYWORDS_TO_EXTRACT) {
        $keywords = ACA_Gemini_Api::extract_keywords_from_content($text);

        if (is_wp_error($keywords)) {
            ACA_Log_Service::add('Failed to extract keywords: ' . $keywords->get_error_message(), 'error');
            return [];
        }

        return array_slice($keywords, 0, $limit);
    }

    /**
     * Download and set a featured image using various providers.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $query The search query for the image.
     * @param string $provider The image provider (unsplash, pexels, dalle).
     */
    public static function maybe_set_featured_image($post_id, $query, $provider) {
        $image_url = self::get_image_url_from_provider($query, $provider);
        
        if (empty($image_url)) {
            return;
        }

        self::download_and_set_image($post_id, $image_url, $query);
    }

    /**
     * Get image URL from the specified provider.
     *
     * @since 1.2.0
     * @param string $query The search query.
     * @param string $provider The image provider.
     * @return string|false The image URL or false on failure.
     */
    private static function get_image_url_from_provider($query, $provider) {
        switch ($provider) {
            case 'unsplash':
                return 'https://source.unsplash.com/1600x900/?' . urlencode($query);
            
            case 'pexels':
                return self::get_pexels_image_url($query);
            
            case 'dalle':
                return self::get_dalle_image_url($query);
            
            default:
                return false;
        }
    }

    /**
     * Get image URL from Pexels API.
     *
     * @since 1.2.0
     * @param string $query The search query.
     * @return string|false The image URL or false on failure.
     */
    private static function get_pexels_image_url($query) {
        $options = get_option('aca_ai_content_agent_options');
        $api_key_enc = $options['pexels_api_key'] ?? '';
        $api_key = ACA_Encryption_Util::safe_decrypt($api_key_enc);
        
        if (empty($api_key)) {
            return false;
        }

        $endpoint = 'https://api.pexels.com/v1/search?per_page=1&query=' . urlencode($query);
        $response = wp_remote_get($endpoint, [
            'headers' => ['Authorization' => $api_key],
            'timeout' => self::IMAGE_REQUEST_TIMEOUT,
        ]);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Pexels request failed: ' . $response->get_error_message(), 'error');
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['photos'][0]['src']['large'] ?? false;
    }

    /**
     * Get image URL from DALL-E API.
     *
     * @since 1.2.0
     * @param string $query The search query.
     * @return string|false The image URL or false on failure.
     */
    private static function get_dalle_image_url($query) {
        if (!aca_ai_content_agent_is_pro()) {
            return false;
        }

        $options = get_option('aca_ai_content_agent_options');
        $api_key_enc = $options['openai_api_key'] ?? '';
        $api_key = ACA_Encryption_Util::safe_decrypt($api_key_enc);
        
        if (empty($api_key)) {
            return false;
        }

        $endpoint = 'https://api.openai.com/v1/images/generations';
        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'model' => 'dall-e-3',
                'prompt' => $query,
                'n' => 1,
                'size' => '1024x1024',
            ]),
            'timeout' => self::DEFAULT_API_TIMEOUT,
        ]);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('DALL-E request failed: ' . $response->get_error_message(), 'error');
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return $body['data'][0]['url'] ?? false;
    }

    /**
     * Download and set the image as featured image.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $image_url The image URL.
     * @param string $title The post title.
     */
    private static function download_and_set_image($post_id, $image_url, $title) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $tmp = download_url($image_url);
        if (is_wp_error($tmp)) {
            ACA_Log_Service::add(
                sprintf('Image download failed: %s', $tmp->get_error_message()),
                'error'
            );
            return;
        }

        if (!self::validate_downloaded_image($tmp)) {
            wp_delete_file($tmp);
            return;
        }

        $file_array = [
            'name' => sanitize_title($title) . '.jpg',
            'tmp_name' => $tmp,
        ];

        $id = media_handle_sideload($file_array, $post_id);
        if (is_wp_error($id)) {
            ACA_Log_Service::add('Failed to sideload image: ' . $id->get_error_message(), 'error');
        } else {
            set_post_thumbnail($post_id, $id);
        }

        wp_delete_file($tmp);
    }

    /**
     * Validate the downloaded image file.
     *
     * @since 1.2.0
     * @param string $tmp_file The temporary file path.
     * @return bool True if valid, false otherwise.
     */
    private static function validate_downloaded_image($tmp_file) {
        $type = wp_check_filetype($tmp_file);
        
        if (function_exists('getimagesize')) {
            $info = @getimagesize($tmp_file);
            if ($info && !empty($info['mime'])) {
                $type['type'] = $info['mime'];
            }
        }

        $size = filesize($tmp_file);
        
        if ($size === false || $size > self::MAX_IMAGE_SIZE || empty($type['type']) || !in_array($type['type'], self::ALLOWED_IMAGE_TYPES, true)) {
            ACA_Log_Service::add(__('Downloaded image rejected due to size or type.', 'aca-ai-content-agent'), 'error');
            return false;
        }

        return true;
    }

    /**
     * Check plagiarism via Copyscape API and store the result.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $content The post content.
     */
    public static function check_plagiarism($post_id, $content) {
        if (!aca_ai_content_agent_is_pro()) {
            return; // Pro feature only
        }

        $credentials = self::get_copyscape_credentials();
        if (empty($credentials)) {
            ACA_Log_Service::add(__('Plagiarism check failed: Copyscape credentials missing.', 'aca-ai-content-agent'), 'error');
            return;
        }

        $endpoint = self::build_copyscape_endpoint($credentials, $content);
        $response = wp_remote_get($endpoint);
        
        if (is_wp_error($response)) {
            ACA_Log_Service::add(__('Plagiarism check failed: ', 'aca-ai-content-agent') . $response->get_error_message(), 'error');
            return;
        }

        $body = wp_remote_retrieve_body($response);
        update_post_meta($post_id, '_aca_ai_content_agent_plagiarism_raw', $body);
    }

    /**
     * Get Copyscape API credentials.
     *
     * @since 1.2.0
     * @return array|false The credentials array or false if not available.
     */
    private static function get_copyscape_credentials() {
        $options = get_option('aca_ai_content_agent_options');
        $user = $options['copyscape_username'] ?? '';
        $key_enc = $options['copyscape_api_key'] ?? '';
        $key = ACA_Encryption_Util::safe_decrypt($key_enc);
        
        if (empty($user) || empty($key)) {
            return false;
        }

        return ['user' => $user, 'key' => $key];
    }

    /**
     * Build Copyscape API endpoint URL.
     *
     * @since 1.2.0
     * @param array $credentials The API credentials.
     * @param string $content The content to check.
     * @return string The endpoint URL.
     */
    private static function build_copyscape_endpoint($credentials, $content) {
        return add_query_arg([
            'c' => 'csearch',
            'u' => $credentials['user'],
            'k' => $credentials['key'],
            'o' => 'json',
            't' => wp_trim_words($content, self::MAX_CONTENT_LENGTH_FOR_PLAGIARISM, '')
        ], 'https://www.copyscape.com/api/');
    }

    /**
     * Provide AI-powered update suggestions for an existing post.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @return string|WP_Error The suggestions or WP_Error on failure.
     */
    public static function suggest_content_update($post_id) {
        if (!aca_ai_content_agent_is_pro()) {
            return new WP_Error('pro_feature', __('This feature is available in the Pro version.', 'aca-ai-content-agent'));
        }

        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('post_not_found', __('Post not found.', 'aca-ai-content-agent'));
        }

        $content = wp_strip_all_tags($post->post_content);
        $prompt = sprintf(
            /* translators: 1: The post title, 2: The post content. */
            __('Suggest concise improvements to refresh and optimize the following blog post for SEO. Return a bullet list only.\n\nTitle: %1$s\n\n%2$s', 'aca-ai-content-agent'),
            $post->post_title,
            $content
        );

        $response = ACA_Gemini_Api::call($prompt);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to generate update suggestions: ' . $response->get_error_message(), 'error');
            return $response;
        }

        return $response;
    }

    /**
     * Generate and append a data-backed section with statistics.
     *
     * @since 1.2.0
     * @param int $post_id The post ID.
     * @param string $title The post title.
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