<?php
/**
 * The idea service.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 */

/**
 * Idea service class for ACA AI Content Agent.
 *
 * Handles idea generation and management, including Google Search Console integration.
 *
 * @since 1.2.0
 * @author     Adem Isler <idemasler@gmail.com>
 */
class ACA_Idea_Service {

    /**
     * Default number of posts to analyze for idea generation.
     *
     * @since 1.2.0
     * @var int
     */
    const DEFAULT_ANALYSIS_DEPTH = 20;

    /**
     * Default number of ideas to generate.
     *
     * @since 1.2.0
     * @var int
     */
    const DEFAULT_GENERATION_LIMIT = 5;

    /**
     * Number of days to look back for GSC data.
     *
     * @since 1.2.0
     * @var int
     */
    const GSC_LOOKBACK_DAYS = 30;

    /**
     * Maximum number of GSC queries to process.
     *
     * @since 1.2.0
     * @var int
     */
    const GSC_QUERY_LIMIT = 10;

    /**
     * Generate new post ideas.
     *
     * This method analyzes existing content and generates new post ideas
     * using AI. It respects monthly limits for free users.
     *
     * @since 1.2.0
     * @return array|WP_Error Array of inserted idea IDs on success, WP_Error on failure.
     */
    public static function generate_ideas() {
        if (!is_admin() && !defined('DOING_AJAX')) {
            ACA_Log_Service::add('Unauthorized access to generate_ideas.', 'error');
            return new WP_Error('unauthorized', __('Unauthorized access.', 'aca-ai-content-agent'));
        }

        $start_time = microtime(true);

        $limit_check = self::check_idea_limit();
        if (is_wp_error($limit_check)) {
            return $limit_check;
        }

        ACA_Log_Service::add('Attempting to generate new ideas.', 'info');

        $existing_titles = self::get_existing_post_titles();
        $ai_response = self::generate_ideas_with_ai($existing_titles);
        
        if (is_wp_error($ai_response)) {
            ACA_Log_Service::log_performance('idea_generation', $start_time, ['status' => 'failed']);
            return $ai_response;
        }

        $inserted_ids = self::save_generated_ideas($ai_response);
        self::increment_idea_count(count($inserted_ids));

        ACA_Log_Service::log_performance('idea_generation', $start_time, [
            'status' => 'success',
            'ideas_generated' => count($inserted_ids)
        ]);

        return $inserted_ids;
    }

    /**
     * Check if the user has reached the idea limit for the current month.
     *
     * @since 1.2.0
     * @return true|WP_Error True if within limits, WP_Error if limit reached.
     */
    private static function check_idea_limit() {
        if (!ACA_Helper::is_pro()) {
            $count = get_option('aca_ai_content_agent_idea_count_current_month', 0);
            if ($count >= 5) {
                ACA_Log_Service::add('Failed to generate ideas: Monthly idea limit reached for free version.', 'error');
                return new WP_Error('limit_reached', __('Monthly idea limit reached for free version.', 'aca-ai-content-agent'));
            }
        }
        return true;
    }

    /**
     * Get existing post titles for analysis.
     *
     * @since 1.2.0
     * @return string Comma-separated list of existing titles.
     */
    private static function get_existing_post_titles() {
        $options = get_option('aca_ai_content_agent_options');
        $post_types = $options['analysis_post_types'] ?? ['post'];
        
        $posts = get_posts([
            'post_type' => $post_types,
            'post_status' => 'publish',
            'posts_per_page' => self::DEFAULT_ANALYSIS_DEPTH,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        $titles = array_map(function($post) {
            return $post->post_title;
        }, $posts);

        return implode(', ', $titles);
    }

    /**
     * Generate ideas using AI based on existing content.
     *
     * @since 1.2.0
     * @param string $existing_titles Comma-separated list of existing titles.
     * @return string|WP_Error The AI response or WP_Error on failure.
     */
    private static function generate_ideas_with_ai($existing_titles) {
        $prompts = ACA_Style_Guide_Service::get_default_prompts();
        $limit = 5;
        
        $prompt = sprintf($prompts['idea_generation'], $existing_titles, $limit);
        
        $response = ACA_Gemini_Api::call($prompt);
        
        if (is_wp_error($response)) {
            ACA_Log_Service::add('AI idea generation failed: ' . $response->get_error_message(), 'error');
            
            // ERROR RECOVERY: Use fallback idea generation
            $fallback_ideas = ACA_Error_Recovery::provide_fallback('idea_generation', [
                'existing_titles' => $existing_titles,
                'limit' => $limit
            ]);
            
            if (is_wp_error($fallback_ideas)) {
                return $fallback_ideas;
            }
            
            return implode("\n", $fallback_ideas);
        }
        
        return $response;
    }

    /**
     * Save generated ideas to the database.
     *
     * @since 1.2.0
     * @param string $ai_response The AI response containing ideas.
     * @return array Array of inserted idea IDs.
     */
    private static function save_generated_ideas($ai_response) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $ideas = explode("\n", trim($ai_response));
        $cleaned_ideas = [];
        $inserted_ids = [];
        
        ACA_Log_Service::add('Processing ' . count($ideas) . ' ideas from AI response', 'info');
        
        // SECURITY FIX: Add database transaction for data consistency
        $wpdb->query('START TRANSACTION');
        
        try {
            foreach ($ideas as $idea) {
                $cleaned_idea = preg_replace('/^\d+\.\s*/', '', trim($idea));
                if (!empty($cleaned_idea)) {
                    $result = $wpdb->insert(
                        $table_name,
                        [
                            'title' => $cleaned_idea,
                            'generated_date' => current_time('mysql'),
                        ]
                    );
                    
                    if ($result === false) {
                        ACA_Log_Service::add('Failed to insert idea into database: ' . $wpdb->last_error, 'error');
                        $wpdb->query('ROLLBACK');
                        return new WP_Error('database_error', __('Failed to save ideas to database.', 'aca-ai-content-agent'));
                    } else {
                        $inserted_ids[] = $wpdb->insert_id;
                        $cleaned_ideas[] = $cleaned_idea;
                    }
                }
            }
            
            $wpdb->query('COMMIT');
            ACA_Log_Service::add('Successfully saved ' . count($inserted_ids) . ' ideas to database', 'info');
            return $inserted_ids;
            
        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            ACA_Log_Service::add('Exception during idea saving: ' . $e->getMessage(), 'error');
            return new WP_Error('database_exception', __('Database error occurred while saving ideas.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Increment the idea count for the current month.
     *
     * @since 1.2.0
     * @param int $count The number of ideas to add to the count.
     */
    private static function increment_idea_count($count) {
        if (!ACA_Helper::is_pro()) {
            $current_count = get_option('aca_ai_content_agent_idea_count_current_month', 0);
            update_option('aca_ai_content_agent_idea_count_current_month', $current_count + $count);
        }
    }

    /**
     * Generate new ideas using Google Search Console queries.
     *
     * This analyzes search queries that bring traffic but are not yet covered
     * by existing posts and sends them to the AI for new title suggestions.
     *
     * @since 1.2.0
     * @return array|WP_Error Array of inserted idea IDs on success or WP_Error.
     */
    public static function generate_ideas_from_gsc() {
        if (!is_admin() && !defined('DOING_AJAX')) {
            ACA_Log_Service::add('Unauthorized access to generate_ideas_from_gsc.', 'error');
            return new WP_Error('unauthorized', __('Unauthorized access.', 'aca-ai-content-agent'));
        }

        $credentials = self::get_gsc_credentials();
        if (is_wp_error($credentials)) {
            return $credentials;
        }

        $gsc_data = self::fetch_gsc_data($credentials['site_url'], $credentials['start_date'], $credentials['end_date']);
        if (is_wp_error($gsc_data)) {
            return $gsc_data;
        }

        $queries = self::extract_uncovered_queries($gsc_data);
        if (empty($queries)) {
            ACA_Log_Service::add('Failed to generate GSC ideas: No new query opportunities found.', 'error');
            return new WP_Error('no_queries', __('No new query opportunities found.', 'aca-ai-content-agent'));
        }

        $ai_response = self::generate_gsc_ideas_with_ai($queries);
        if (is_wp_error($ai_response)) {
            return $ai_response;
        }

        $inserted_ids = self::save_gsc_ideas($ai_response);
        ACA_Log_Service::add(sprintf('%d ideas generated from Search Console data.', count($inserted_ids)), 'info');

        return $inserted_ids;
    }

    /**
     * Get Google Search Console credentials and date range.
     *
     * @since 1.2.0
     * @return array|WP_Error The credentials array or WP_Error on failure.
     */
    private static function get_gsc_credentials() {
        $options = get_option('aca_ai_content_agent_options');
        $site_url = $options['gsc_site_url'] ?? '';
        $api_key_enc = $options['gsc_api_key'] ?? '';
        $api_key = ACA_Encryption_Util::safe_decrypt($api_key_enc);

        if (empty($api_key) || empty($site_url)) {
            ACA_Log_Service::add('Failed to generate GSC ideas: Search Console API key or site URL is missing.', 'error');
            return new WP_Error('missing_credentials', __('Search Console API key or site URL is missing.', 'aca-ai-content-agent'));
        }

        $end = current_time('Y-m-d');
        $start = gmdate('Y-m-d', strtotime('-' . self::GSC_LOOKBACK_DAYS . ' days', strtotime($end)));

        return [
            'site_url' => $site_url,
            'api_key' => $api_key,
            'start_date' => $start,
            'end_date' => $end
        ];
    }

    /**
     * Extract queries that are not covered by existing posts.
     *
     * @since 1.2.0
     * @param array $gsc_data The GSC data response.
     * @return array Array of uncovered queries.
     */
    private static function extract_uncovered_queries($gsc_data) {
        $queries = [];

        if (!empty($gsc_data['rows'])) {
            foreach ($gsc_data['rows'] as $row) {
                $query = $row['keys'][0] ?? '';
                if (!$query) {
                    continue;
                }

                $exists = get_posts([
                    's' => $query,
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'fields' => 'ids',
                    'posts_per_page' => 1,
                ]);

                if (empty($exists)) {
                    $queries[] = $query;
                }
            }
        }

        return $queries;
    }

    /**
     * Generate ideas using AI based on GSC queries.
     *
     * @since 1.2.0
     * @param array $queries Array of uncovered queries.
     * @return string|WP_Error The AI response or WP_Error on failure.
     */
    private static function generate_gsc_ideas_with_ai($queries) {
        $options = get_option('aca_ai_content_agent_options');
        $limit = $options['generation_limit'] ?? self::DEFAULT_GENERATION_LIMIT;
        
        $prompt = sprintf(
            'The following search queries are popular on Google but not well covered on our site: %s. Suggest %d SEO-friendly blog post titles to address them.',
            implode(', ', $queries),
            $limit
        );

        $response = ACA_Gemini_Api::call($prompt);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to generate GSC ideas: ' . $response->get_error_message(), 'error');
            return $response;
        }

        return $response;
    }

    /**
     * Save GSC-generated ideas to the database.
     *
     * @since 1.2.0
     * @param string $ai_response The AI response containing ideas.
     * @return array Array of inserted idea IDs.
     */
    private static function save_gsc_ideas($ai_response) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $ideas = array_filter(array_map('trim', explode("\n", $ai_response)));
        $inserted_ids = [];

        foreach ($ideas as $idea) {
            $clean = preg_replace('/^\d+\.\s*/', '', $idea);
            if ($clean) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->insert(
                    $table_name,
                    [
                        'title' => $clean,
                        'generated_date' => current_time('mysql'),
                    ]
                );
                $inserted_ids[] = $wpdb->insert_id;
            }
        }

        return $inserted_ids;
    }

    /**
     * Record user feedback for an idea.
     *
     * @since 1.2.0
     * @param int $idea_id The idea ID.
     * @param int $value The feedback value.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    public static function record_feedback($idea_id, $value) {
        if (!is_admin() && !defined('DOING_AJAX')) {
            ACA_Log_Service::add('Unauthorized access to record_feedback.', 'error');
            return new WP_Error('unauthorized', __('Unauthorized access.', 'aca-ai-content-agent'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $result = $wpdb->update(
            $table_name,
            ['feedback' => intval($value)],
            ['id' => intval($idea_id)]
        );

        if (false === $result) {
            return new WP_Error('database_error', __('Failed to record feedback.', 'aca-ai-content-agent'));
        }

        return true;
    }

    /**
     * Reject an idea by updating its status.
     *
     * @since 1.2.0
     * @param int $idea_id The idea ID.
     * @return bool|WP_Error True on success, WP_Error on failure.
     */
    public static function reject_idea($idea_id) {
        if (!is_admin() && !defined('DOING_AJAX')) {
            ACA_Log_Service::add('Unauthorized access to reject_idea.', 'error');
            return new WP_Error('unauthorized', __('Unauthorized access.', 'aca-ai-content-agent'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $result = $wpdb->update(
            $table_name,
            ['status' => 'rejected'],
            ['id' => $idea_id],
            ['%s'],
            ['%d']
        );

        if (false === $result) {
            return new WP_Error('database_error', __('Failed to reject idea in the database.', 'aca-ai-content-agent'));
        }

        ACA_Log_Service::add(sprintf('Idea #%d rejected by user.', $idea_id), 'info');
        return true;
    }

    /**
     * Fetch top queries from Google Search Console.
     *
     * @since 1.2.0
     * @param string $site_url The site URL.
     * @param string $start_date The start date (Y-m-d format).
     * @param string $end_date The end date (Y-m-d format).
     * @return array|WP_Error The GSC data or WP_Error on failure.
     */
    public static function fetch_gsc_data($site_url, $start_date, $end_date) {
        $options = get_option('aca_ai_content_agent_options');
        $api_key_enc = $options['gsc_api_key'] ?? '';
        $api_key = ACA_Encryption_Util::safe_decrypt($api_key_enc);

        if (empty($api_key) || empty($site_url)) {
            ACA_Log_Service::add('Failed to fetch GSC data: API key or site URL is missing.', 'error');
            return new WP_Error('missing_credentials', __('Search Console API key or site URL is missing.', 'aca-ai-content-agent'));
        }

        $endpoint = add_query_arg('key', $api_key, 'https://www.googleapis.com/webmasters/v3/sites/' . rawurlencode($site_url) . '/searchAnalytics/query');
        $body = [
            'startDate' => $start_date,
            'endDate' => $end_date,
            'dimensions' => ['query'],
            'rowLimit' => self::GSC_QUERY_LIMIT,
        ];

        $response = wp_remote_post($endpoint, [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode($body),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to fetch GSC data: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            ACA_Log_Service::add('GSC API returned error code: ' . $response_code, 'error');
            return new WP_Error('gsc_api_error', sprintf(__('Google Search Console API error: %d', 'aca-ai-content-agent'), $response_code));
        }

        $data = json_decode($response_body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            ACA_Log_Service::add('Failed to decode GSC response: ' . json_last_error_msg(), 'error');
            return new WP_Error('gsc_decode_error', __('Failed to decode Google Search Console response.', 'aca-ai-content-agent'));
        }

        return $data;
    }

    /**
     * Generate a content cluster around a specific topic.
     *
     * This is a Pro feature that creates a group of related content ideas
     * that can be developed into a comprehensive content cluster.
     *
     * @since 1.2.0
     * @param string $topic The main topic for the content cluster.
     * @return array|WP_Error The cluster data or WP_Error on failure.
     */
    public static function generate_content_cluster($topic) {
        if (!is_admin() && !defined('DOING_AJAX')) {
            ACA_Log_Service::add('Unauthorized access to generate_content_cluster.', 'error');
            return new WP_Error('unauthorized', __('Unauthorized access.', 'aca-ai-content-agent'));
        }

        if (!aca_ai_content_agent_is_pro()) {
            ACA_Log_Service::add('Failed to generate content cluster: Pro feature required.', 'error');
            return new WP_Error('pro_feature', __('This feature is available in the Pro version.', 'aca-ai-content-agent'));
        }

        $topic = sanitize_text_field($topic);
        if (empty($topic)) {
            ACA_Log_Service::add('Failed to generate content cluster: Topic is required.', 'error');
            return new WP_Error('invalid_topic', __('Topic is required.', 'aca-ai-content-agent'));
        }

        $cluster_id = self::create_cluster_record($topic);
        if (is_wp_error($cluster_id)) {
            return $cluster_id;
        }

        $ai_response = self::generate_cluster_subtopics_with_ai($topic);
        if (is_wp_error($ai_response)) {
            self::mark_cluster_as_failed($cluster_id);
            return $ai_response;
        }

        $subtopics = self::save_cluster_subtopics($cluster_id, $ai_response);
        ACA_Log_Service::add(sprintf('Content cluster generated for topic "%s".', $topic), 'info');

        return [
            'cluster_id' => $cluster_id,
            'subtopics' => $subtopics,
        ];
    }

    /**
     * Create a new cluster record in the database.
     *
     * @since 1.2.0
     * @param string $topic The cluster topic.
     * @return int|WP_Error The cluster ID or WP_Error on failure.
     */
    private static function create_cluster_record($topic) {
        global $wpdb;
        $clusters_table = $wpdb->prefix . 'aca_ai_content_agent_clusters';

        $result = $wpdb->insert(
            $clusters_table,
            [
                'topic' => $topic,
                'status' => 'generated',
                'generated_date' => current_time('mysql'),
            ]
        );

        if (false === $result) {
            return new WP_Error('database_error', __('Failed to create cluster record.', 'aca-ai-content-agent'));
        }

        return $wpdb->insert_id;
    }

    /**
     * Generate cluster subtopics using AI.
     *
     * @since 1.2.0
     * @param string $topic The main topic.
     * @return string|WP_Error The AI response or WP_Error on failure.
     */
    private static function generate_cluster_subtopics_with_ai($topic) {
        $prompt = sprintf(
            'Create a list of 5 related blog post titles that would form a content cluster around the topic "%s". Return only the list.',
            $topic
        );

        $response = ACA_Gemini_Api::call($prompt);

        if (is_wp_error($response)) {
            ACA_Log_Service::add('Failed to generate content cluster: ' . $response->get_error_message(), 'error');
            return $response;
        }

        return $response;
    }

    /**
     * Mark a cluster as failed in the database.
     *
     * @since 1.2.0
     * @param int $cluster_id The cluster ID.
     */
    private static function mark_cluster_as_failed($cluster_id) {
        global $wpdb;
        $clusters_table = $wpdb->prefix . 'aca_ai_content_agent_clusters';
        $wpdb->update($clusters_table, ['status' => 'failed'], ['id' => $cluster_id]);
    }

    /**
     * Save cluster subtopics to the database.
     *
     * @since 1.2.0
     * @param int $cluster_id The cluster ID.
     * @param string $ai_response The AI response containing subtopics.
     * @return array Array of subtopic titles.
     */
    private static function save_cluster_subtopics($cluster_id, $ai_response) {
        global $wpdb;
        $items_table = $wpdb->prefix . 'aca_ai_content_agent_cluster_items';

        $lines = array_filter(array_map('trim', explode("\n", $ai_response)));
        $subtopics = [];

        foreach ($lines as $line) {
            $title = preg_replace('/^\d+\.\s*/', '', $line);
            if (!empty($title)) {
                $wpdb->insert(
                    $items_table,
                    [
                        'cluster_id' => $cluster_id,
                        'subtopic_title' => $title,
                        'status' => 'suggested',
                    ]
                );
                $subtopics[] = $title;
            }
        }

        return $subtopics;
    }
}