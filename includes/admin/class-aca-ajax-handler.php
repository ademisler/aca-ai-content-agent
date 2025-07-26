<?php
/**
 * ACA - AI Content Agent
 *
 * AJAX Handler
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Ajax_Handler {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('wp_ajax_aca_ai_content_agent_test_connection', [$this, 'handle_ajax_test_connection']);
        add_action('wp_ajax_aca_ai_content_agent_generate_style_guide', [$this, 'handle_ajax_generate_style_guide']);
        add_action('wp_ajax_aca_ai_content_agent_generate_ideas', [$this, 'handle_ajax_generate_ideas']);
        add_action('wp_ajax_aca_ai_content_agent_write_draft', [$this, 'handle_ajax_write_draft']);
        add_action('wp_ajax_aca_ai_content_agent_reject_idea', [$this, 'handle_ajax_reject_idea']);
        add_action('wp_ajax_aca_ai_content_agent_validate_license', [$this, 'handle_ajax_validate_license']);
        add_action('wp_ajax_aca_ai_content_agent_generate_cluster', [$this, 'handle_ajax_generate_cluster']);
        add_action('wp_ajax_aca_ai_content_agent_submit_feedback', [$this, 'handle_ajax_submit_feedback']);
        add_action('wp_ajax_aca_ai_content_agent_suggest_update', [$this, 'handle_ajax_suggest_update']);
        add_action('wp_ajax_aca_ai_content_agent_fetch_gsc_data', [$this, 'handle_ajax_fetch_gsc_data']);
        add_action('wp_ajax_aca_ai_content_agent_generate_gsc_ideas', [$this, 'handle_ajax_generate_gsc_ideas']);
        add_action('wp_ajax_aca_ai_content_agent_reset_settings', [$this, 'handle_ajax_reset_settings']);
    }

    /**
     * Handle the AJAX request for testing the API connection.
     */
    public function handle_ajax_test_connection() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        // Use a simple prompt to test the connection
        $test_prompt = 'Hello.'; 
        $response = ACA_Gemini_Api::call($test_prompt);

        if (is_wp_error($response)) {
            if ('api_key_missing' === $response->get_error_code()) {
                wp_send_json_error(esc_html__('Google Gemini API key is missing or invalid. Please check your settings.', 'aca-ai-content-agent'));
            } else {
                wp_send_json_error($response->get_error_message());
            }
        } else {
            wp_send_json_success(esc_html__('Connection successful! API is working correctly.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Handle the AJAX request for generating the style guide.
     */
    public function handle_ajax_generate_style_guide() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $result = ACA_Style_Guide_Service::generate_style_guide();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(esc_html__('Style guide updated successfully based on the latest content.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Handle the AJAX request for generating ideas.
     */
    public function handle_ajax_generate_ideas() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_ai_content_agent_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $transient_key = 'aca_ai_content_agent_rate_limit_' . get_current_user_id();
        if ( get_transient( $transient_key ) ) {
            wp_send_json_error( esc_html__( 'Please wait a moment before generating new ideas.', 'aca-ai-content-agent' ) );
        }

        set_transient( $transient_key, true, 20 );

        $result = ACA_Idea_Service::generate_ideas();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            global $wpdb;
            $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
            $ideas_html = '';
            if ( ! empty( $result ) ) {
                foreach ($result as $idea_id) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $idea = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ideas_table} WHERE id = %d", $idea_id ) );
                    if ($idea) {
                        $ideas_html .= '<li data-id="' . esc_attr( $idea->id ) . '">' . esc_html( $idea->title ) .
                             ' <button class="button-primary aca-ai-content-agent-write-draft" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Write Draft', 'aca-ai-content-agent' ) . '</button>' .
                             ' <span class="aca-ai-content-agent-draft-status"></span>' .
                             ' <button class="button-secondary aca-ai-content-agent-reject-idea" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Reject', 'aca-ai-content-agent' ) . '</button>' .
                             ' <button class="button aca-ai-content-agent-feedback-btn" data-value="1">👍</button>' .
                             ' <button class="button aca-ai-content-agent-feedback-btn" data-value="-1">👎</button>' .
                             '</li>';
                    }
                }
            }
            /* translators: %d: number of ideas */
            $message = sprintf(esc_html(_n('%d new idea generated.', '%d new ideas generated.', count($result), 'aca-ai-content-agent')), count($result));
            wp_send_json_success(['message' => $message, 'ideas_html' => $ideas_html]);
        }
    }

    /**
     * Handle the AJAX request for writing a post draft.
     */
    public function handle_ajax_write_draft() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_ai_content_agent_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        if (!isset($_POST['id']) || !absint($_POST['id'])) {
            wp_send_json_error(esc_html__('Invalid idea ID.', 'aca-ai-content-agent'));
        }

        $idea_id = absint($_POST['id']);
        $result = ACA_Draft_Service::write_post_draft($idea_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success([
                'message' => esc_html__('Draft created successfully.', 'aca-ai-content-agent'),
                'edit_link' => get_edit_post_link($result, 'raw'),
            ]);
        }
    }

    /**
     * Handle the AJAX request for rejecting an idea.
     */
    public function handle_ajax_reject_idea() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_ai_content_agent_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $idea_id = absint($_POST['id']);
        $result = ACA_Idea_Service::reject_idea($idea_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(esc_html__('Idea rejected.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Handle the AJAX request for validating the license key.
     */
    public function handle_ajax_validate_license() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $license_key = sanitize_text_field( wp_unslash( $_POST['license_key'] ) );

        $body = ACA_Gumroad_Api::verify_license_key($license_key);

        if ( isset($body['success']) && $body['success'] === true ) {
            // License is valid. Optionally check for refunds or chargebacks
            $purchase = $body['purchase'];

            update_option('aca_ai_content_agent_license_key', ACA_Encryption_Util::encrypt( $license_key ));
            update_option('aca_ai_content_agent_is_pro_active', 'true');
            update_option('aca_ai_content_agent_license_data', $purchase);

            // Calculate license validity (1 year from purchase date)
            $sale_time   = isset( $purchase['sale_timestamp'] ) ? strtotime( $purchase['sale_timestamp'] ) : time();
            $valid_until = $sale_time + YEAR_IN_SECONDS;
            update_option( 'aca_ai_content_agent_license_valid_until', $valid_until );

            set_transient( 'aca_ai_content_agent_license_status', 'valid', WEEK_IN_SECONDS );

            wp_send_json_success( esc_html__( 'License activated successfully!', 'aca-ai-content-agent' ) );
        } else {
            // License is invalid or another error occurred
            $message = isset($body['message']) ? $body['message'] : esc_html__('Invalid license key or API error.', 'aca-ai-content-agent');
            update_option('aca_ai_content_agent_is_pro_active', 'false');
            delete_option('aca_ai_content_agent_license_valid_until');
            set_transient( 'aca_ai_content_agent_license_status', 'invalid', WEEK_IN_SECONDS );
            wp_send_json_error($message);
        }
    }

    /**
     * Handle AJAX request for generating a content cluster.
     */
    public function handle_ajax_generate_cluster() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        if ( empty( $_POST['topic'] ) ) {
            wp_send_json_error( __( 'Topic is required.', 'aca-ai-content-agent' ) );
        }

        $topic = sanitize_text_field( wp_unslash( $_POST['topic'] ) );
        $result = ACA_Idea_Service::generate_content_cluster($topic);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success($result);
        }
    }

    /**
     * Handle AJAX request for submitting user feedback on an idea.
     */
    public function handle_ajax_submit_feedback() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_ai_content_agent_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $idea_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
        $value   = isset( $_POST['value'] ) ? intval( wp_unslash( $_POST['value'] ) ) : 0;

        if ($idea_id) {
            ACA_Idea_Service::record_feedback($idea_id, $value);
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Invalid idea ID.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Handle AJAX request for suggesting updates to a post.
     */
    public function handle_ajax_suggest_update() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(__('Invalid post ID.', 'aca-ai-content-agent'));
        }

        $result = ACA_Draft_Service::suggest_content_update($post_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success($result);
        }
    }

    /**
     * Handle AJAX request for fetching Search Console data.
     */
    public function handle_ajax_fetch_gsc_data() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $options = get_option('aca_ai_content_agent_options');
        $site_url = $options['gsc_site_url'] ?? '';
        $end      = current_time( 'Y-m-d' );
        $start    = gmdate( 'Y-m-d', strtotime( '-7 days', strtotime( $end ) ) );

        $result = ACA_Idea_Service::fetch_gsc_data($site_url, $start, $end);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success($result);
        }
    }

    /**
     * Generate ideas based on Search Console queries.
     */
    public function handle_ajax_generate_gsc_ideas() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        $result = ACA_Idea_Service::generate_ideas_from_gsc();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            global $wpdb;
            $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
            $ideas_html = '';
            if ( ! empty( $result ) ) {
                foreach ($result as $idea_id) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                    $idea = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$ideas_table} WHERE id = %d", $idea_id ) );
                    if ($idea) {
                        $ideas_html .= '<li data-id="' . esc_attr( $idea->id ) . '">' . esc_html( $idea->title ) .
                             ' <button class="button-primary aca-ai-content-agent-write-draft" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Write Draft', 'aca-ai-content-agent' ) . '</button>' .
                             ' <span class="aca-ai-content-agent-draft-status"></span>' .
                             ' <button class="button-secondary aca-ai-content-agent-reject-idea" data-id="' . esc_attr( $idea->id ) . '">' . esc_html__( 'Reject', 'aca-ai-content-agent' ) . '</button>' .
                             ' <button class="button aca-ai-content-agent-feedback-btn" data-value="1">👍</button>' .
                             ' <button class="button aca-ai-content-agent-feedback-btn" data-value="-1">👎</button>' .
                             '</li>';
                    }
                }
            }
            /* translators: %d: number of ideas */
            $message = sprintf(esc_html(_n('%d new idea generated.', '%d new ideas generated.', count($result), 'aca-ai-content-agent')), count($result));
            wp_send_json_success(['message' => $message, 'ideas_html' => $ideas_html]);
        }
    }

    /**
     * Handle the AJAX request for resetting all settings.
     */
    public function handle_ajax_reset_settings() {
        check_ajax_referer('aca_ai_content_agent_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_ai_content_agent_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'aca-ai-content-agent'));
        }

        delete_option('aca_ai_content_agent_options');
        delete_option('aca_ai_content_agent_prompts');
        delete_option('aca_ai_content_agent_gemini_api_key');
        delete_option('aca_ai_content_agent_license_key');
        delete_option('aca_ai_content_agent_is_pro_active');
        delete_option('aca_ai_content_agent_license_data');
        delete_option('aca_ai_content_agent_license_valid_until');

        wp_send_json_success(esc_html__('All settings have been reset to their default values.', 'aca-ai-content-agent'));
    }
