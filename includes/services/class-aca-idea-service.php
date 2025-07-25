<?php
/**
 * The idea service.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 */

/**
 * The idea service.
 *
 * This class defines all code necessary for idea generation and management.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/services
 * @author     Your Name <email@example.com>
 */
class ACA_Idea_Service {

    /**
     * Generate new post ideas.
     */
    public static function generate_ideas() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        ACA_Log_Service::add('Attempting to generate new ideas.');

        $options = get_option('aca_ai_content_agent_options');

        // Free version monthly limit
        if ( ! ACA_Helper::is_pro() ) {
            $count = get_option( 'aca_ai_content_agent_idea_count_current_month', 0 );
            if ( $count >= 5 ) {
                return new WP_Error( 'limit_reached', __( 'Monthly idea limit reached for free version.', 'aca-ai-content-agent' ) );
            }
        }

        $prompts    = ACA_Style_Guide_Service::get_default_prompts();
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

        $response = ACA_Gemini_Api::call($prompt);

        if (is_wp_error($response)) {
            // ACA_AI_Content_Agent_Engine::add_log('Failed to generate ideas: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $ideas = explode("\n", trim($response));
        $cleaned_ideas = [];
        $inserted_ids   = [];
        foreach ($ideas as $idea) {
            $cleaned_idea = preg_replace('/^\d+\.\s*/', '', trim($idea));
            if (!empty($cleaned_idea)) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
                $wpdb->insert(
                    $table_name,
                    [
                        'title' => $cleaned_idea,
                    'generated_date' => current_time('mysql'),
                    ]
                );
                $inserted_ids[] = $wpdb->insert_id;
                $cleaned_ideas[] = $cleaned_idea;
            }
        }

        if ( ! ACA_Helper::is_pro() ) {
            $count = get_option( 'aca_ai_content_agent_idea_count_current_month', 0 );
            update_option( 'aca_ai_content_agent_idea_count_current_month', $count + count( $inserted_ids ) );
        }
        // ACA_AI_Content_Agent_Engine::add_log(sprintf('%d new ideas generated and saved.', count($inserted_ids)), 'success');
        return $inserted_ids;
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

        $options     = get_option('aca_ai_content_agent_options');
        $site_url    = $options['gsc_site_url'] ?? '';
        $api_key_enc = $options['gsc_api_key'] ?? '';
        $api_key     = ACA_Encryption_Util::safe_decrypt( $api_key_enc );

        if (empty($api_key) || empty($site_url)) {
            return new WP_Error('missing_credentials', __('Search Console API key or site URL is missing.', 'aca-ai-content-agent'));
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

        $response = ACA_Gemini_Api::call($prompt);

        if (is_wp_error($response)) {
            // ACA_AI_Content_Agent_Engine::add_log('Failed to generate GSC ideas: ' . $response->get_error_message(), 'error');
            return $response;
        }

        $ideas = array_filter(array_map('trim', explode("\n", $response)));
        $inserted_ids = [];
        $table_name   = $wpdb->prefix . 'aca_ai_content_agent_ideas';

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

        ACA_Log_Service::add(sprintf('%d ideas generated from Search Console data.', count($inserted_ids)), 'success');
        return $inserted_ids;
    }

    /**
     * Record user feedback for an idea.
     */
    public static function record_feedback($idea_id, $value) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $table_name,
            ['feedback' => intval($value)],
            ['id' => intval($idea_id)]
        );
    }


    public static function reject_idea($idea_id) {
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
            return new WP_Error('database_error', 'Failed to reject idea in the database.');
        }

        ACA_Log_Service::add(sprintf('Idea #%d rejected by user.', $idea_id));

        return true;
    }

    /**
     * Fetch top queries from Google Search Console.
     */
    public static function fetch_gsc_data($site_url, $start_date, $end_date) {
        $options      = get_option('aca_ai_content_agent_options');
        $api_key_enc  = $options['gsc_api_key'] ?? '';
        $api_key      = ACA_Encryption_Util::safe_decrypt( $api_key_enc );
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

    public static function generate_content_cluster($topic) {
        if ( ! aca_ai_content_agent_is_pro() ) {
            return new WP_Error('pro_feature', __('This feature is available in the Pro version.', 'aca-ai-content-agent'));
        }

        $topic = sanitize_text_field( $topic );
        if ( empty( $topic ) ) {
            return new WP_Error('invalid_topic', __('Topic is required.', 'aca-ai-content-agent'));
        }

        global $wpdb;
        $clusters_table = $wpdb->prefix . 'aca_ai_content_agent_clusters';
        $items_table    = $wpdb->prefix . 'aca_ai_content_agent_cluster_items';

        // Insert the main cluster record.
        $wpdb->insert(
            $clusters_table,
            [
                'topic'         => $topic,
                'status'        => 'generated',
                'generated_date'=> current_time('mysql'),
            ]
        );
        $cluster_id = $wpdb->insert_id;

        $prompt = sprintf(
            'Create a list of 5 related blog post titles that would form a content cluster around the topic "%s". Return only the list.',
            $topic
        );

        $response = ACA_Gemini_Api::call( $prompt );

        if ( is_wp_error( $response ) ) {
            // Mark cluster as failed and return the error.
            $wpdb->update( $clusters_table, [ 'status' => 'failed' ], [ 'id' => $cluster_id ] );
            return $response;
        }

        $lines = array_filter( array_map( 'trim', explode( "\n", $response ) ) );
        $subtopics = [];
        foreach ( $lines as $line ) {
            $title = preg_replace( '/^\d+\.\s*/', '', $line );
            if ( ! empty( $title ) ) {
                $wpdb->insert(
                    $items_table,
                    [
                        'cluster_id'    => $cluster_id,
                        'subtopic_title'=> $title,
                        'status'        => 'suggested',
                    ]
                );
                $subtopics[] = $title;
            }
        }

        ACA_Log_Service::add( sprintf( 'Content cluster generated for topic "%s".', $topic ) );

        return [
            'cluster_id' => $cluster_id,
            'subtopics'  => $subtopics,
        ];
    }
}