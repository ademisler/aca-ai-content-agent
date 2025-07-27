<?php
/**
 * ACA - AI Content Agent
 *
 * AJAX Handler
 *
 * This class handles all AJAX requests from the admin dashboard.
 * It provides methods for testing connections, generating content,
 * managing ideas, and handling user interactions.
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * AJAX Handler Class
 *
 * Handles all AJAX requests for the ACA AI Content Agent plugin.
 * Provides secure endpoints for testing, content generation, and management.
 *
 * @since 1.2.0
 */
class ACA_Ajax_Handler {

    /**
     * Rate limit duration in seconds for idea generation.
     *
     * @since 1.2.0
     * @var int
     */
    const RATE_LIMIT_DURATION = 20;

    /**
     * Number of days to look back for GSC data in AJAX requests.
     *
     * @since 1.2.0
     * @var int
     */
    const GSC_LOOKBACK_DAYS = 7;

    /**
     * Constructor.
     *
     * Registers all AJAX action hooks for the plugin.
     *
     * @since 1.2.0
     */
    public function __construct() {
        $this->register_ajax_actions();
    }

    /**
     * Register all AJAX action hooks.
     *
     * @since 1.2.0
     */
    private function register_ajax_actions() {
        $actions = [
            'aca_ai_content_agent_test_connection' => 'handle_ajax_test_connection',
            'aca_ai_content_agent_generate_style_guide' => 'handle_ajax_generate_style_guide',
            'aca_ai_content_agent_generate_ideas' => 'handle_ajax_generate_ideas',
            'aca_ai_content_agent_write_draft' => 'handle_ajax_write_draft',
            'aca_ai_content_agent_reject_idea' => 'handle_ajax_reject_idea',
            'aca_ai_content_agent_validate_license' => 'handle_ajax_validate_license',
            'aca_ai_content_agent_generate_cluster' => 'handle_ajax_generate_cluster',
            'aca_ai_content_agent_submit_feedback' => 'handle_ajax_submit_feedback',
            'aca_ai_content_agent_suggest_update' => 'handle_ajax_suggest_update',
            'aca_ai_content_agent_fetch_gsc_data' => 'handle_ajax_fetch_gsc_data',
            'aca_ai_content_agent_generate_gsc_ideas' => 'handle_ajax_generate_gsc_ideas',
            'aca_ai_content_agent_reset_settings' => 'handle_ajax_reset_settings',
            'aca_ai_content_agent_refresh_capabilities' => 'handle_ajax_refresh_capabilities',
        ];

        foreach ($actions as $action => $method) {
            add_action("wp_ajax_{$action}", [$this, $method]);
        }
    }

    /**
     * Handle the AJAX request for testing the API connection.
     *
     * Tests the Google Gemini API connection with a simple prompt.
     *
     * @since 1.2.0
     */
    public function handle_ajax_test_connection() {
        $this->verify_nonce_and_capability('manage_options');

        $test_prompt = 'Hello.';
        $response = ACA_Gemini_Api::call($test_prompt);

        if (is_wp_error($response)) {
            $this->handle_api_error($response);
        } else {
            wp_send_json_success(esc_html__('Connection successful! API is working correctly.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Handle the AJAX request for generating the style guide.
     *
     * Generates a new style guide based on existing content.
     *
     * @since 1.2.0
     */
    public function handle_ajax_generate_style_guide() {
        $this->verify_nonce_and_capability('edit_posts');

        $result = ACA_Style_Guide_Service::generate_style_guide();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(esc_html__('Style guide updated successfully based on the latest content.', 'aca-ai-content-agent'));
    }

    /**
     * Handle the AJAX request for generating ideas.
     *
     * Generates new content ideas using AI analysis.
     *
     * @since 1.2.0
     */
    public function handle_ajax_generate_ideas() {
        $this->verify_nonce_and_capability('edit_posts');

        if ($this->is_rate_limited()) {
            wp_send_json_error(esc_html__('Please wait a moment before generating new ideas.', 'aca-ai-content-agent'));
        }

        $this->set_rate_limit();

        $result = ACA_Idea_Service::generate_ideas();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        $response_data = $this->build_ideas_response($result);
        wp_send_json_success($response_data);
    }

    /**
     * Handle the AJAX request for writing a post draft.
     *
     * Creates a WordPress post draft from an existing idea.
     *
     * @since 1.2.0
     */
    public function handle_ajax_write_draft() {
        $this->verify_nonce_and_capability('edit_posts');

        $idea_id = $this->validate_idea_id();
        if (is_wp_error($idea_id)) {
            wp_send_json_error($idea_id->get_error_message());
        }

        $result = ACA_Draft_Service::write_post_draft($idea_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success([
            'message' => esc_html__('Draft created successfully.', 'aca-ai-content-agent'),
            'edit_link' => get_edit_post_link($result, 'raw'),
        ]);
    }

    /**
     * Handle the AJAX request for rejecting an idea.
     *
     * Marks an idea as rejected in the database.
     *
     * @since 1.2.0
     */
    public function handle_ajax_reject_idea() {
        $this->verify_nonce_and_capability('edit_posts');

        $idea_id = $this->validate_idea_id();
        if (is_wp_error($idea_id)) {
            wp_send_json_error($idea_id->get_error_message());
        }

        $result = ACA_Idea_Service::reject_idea($idea_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(esc_html__('Idea rejected.', 'aca-ai-content-agent'));
    }

    /**
     * Handle the AJAX request for validating the license key.
     *
     * Validates a Gumroad license key and activates Pro features.
     *
     * @since 1.2.0
     */
    public function handle_ajax_validate_license() {
        $this->verify_nonce_and_capability('manage_options');

        $license_key = sanitize_text_field(wp_unslash($_POST['license_key']));
        
        if (empty($license_key)) {
            wp_send_json_error(esc_html__('Please enter a license key.', 'aca-ai-content-agent'));
        }

        $api_response = ACA_Gumroad_Api::verify_license_key($license_key);

        if (ACA_Gumroad_Api::is_license_valid($api_response)) {
            $license_details = ACA_Gumroad_Api::get_license_details($api_response);
            $this->activate_license($license_key, $license_details);
            
            ACA_Log_Service::info('License validated successfully via AJAX', $license_details);
            wp_send_json_success(esc_html__('License validated and activated successfully!', 'aca-ai-content-agent'));
        } else {
            $this->deactivate_license();
            
            if (is_wp_error($api_response)) {
                $message = $api_response->get_error_message();
            } else {
                $message = esc_html__('Invalid license key. Please check your key and try again.', 'aca-ai-content-agent');
            }
            
            ACA_Log_Service::error('License validation failed via AJAX: ' . $message);
            wp_send_json_error($message);
        }
    }

    /**
     * Handle AJAX request for generating a content cluster.
     *
     * Creates a content cluster around a specific topic.
     *
     * @since 1.2.0
     */
    public function handle_ajax_generate_cluster() {
        $this->verify_nonce_and_capability('edit_posts');

        $topic = $this->validate_topic();
        if (is_wp_error($topic)) {
            wp_send_json_error($topic->get_error_message());
        }

        $result = ACA_Idea_Service::generate_content_cluster($topic);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success($result);
    }

    /**
     * Handle AJAX request for submitting user feedback on an idea.
     *
     * Records user feedback for a specific idea.
     *
     * @since 1.2.0
     */
    public function handle_ajax_submit_feedback() {
        $this->verify_nonce_and_capability('edit_posts');

        $feedback_data = $this->validate_feedback_data();
        if (is_wp_error($feedback_data)) {
            wp_send_json_error($feedback_data->get_error_message());
        }

        $result = ACA_Idea_Service::record_feedback($feedback_data['idea_id'], $feedback_data['value']);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success();
    }

    /**
     * Handle AJAX request for suggesting updates to a post.
     *
     * Generates AI-powered suggestions for improving an existing post.
     *
     * @since 1.2.0
     */
    public function handle_ajax_suggest_update() {
        $this->verify_nonce_and_capability('edit_posts');

        $post_id = $this->validate_post_id();
        if (is_wp_error($post_id)) {
            wp_send_json_error($post_id->get_error_message());
        }

        $result = ACA_Draft_Service::suggest_content_update($post_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success($result);
    }

    /**
     * Handle AJAX request for fetching Search Console data.
     *
     * Fetches recent Google Search Console data for analysis.
     *
     * @since 1.2.0
     */
    public function handle_ajax_fetch_gsc_data() {
        $this->verify_nonce_and_capability('edit_posts');

        $gsc_params = $this->get_gsc_date_range();
        $result = ACA_Idea_Service::fetch_gsc_data($gsc_params['site_url'], $gsc_params['start'], $gsc_params['end']);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success($result);
    }

    /**
     * Generate ideas based on Search Console queries.
     *
     * Creates new content ideas based on GSC data analysis.
     *
     * @since 1.2.0
     */
    public function handle_ajax_generate_gsc_ideas() {
        $this->verify_nonce_and_capability('edit_posts');

        $result = ACA_Idea_Service::generate_ideas_from_gsc();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        $response_data = $this->build_ideas_response($result);
        wp_send_json_success($response_data);
    }

    /**
     * Handle the AJAX request for resetting all settings.
     *
     * Resets all plugin settings to their default values.
     *
     * @since 1.2.0
     */
    public function handle_ajax_reset_settings() {
        $this->verify_nonce_and_capability('manage_options');

        $this->delete_all_plugin_options();
        wp_send_json_success(esc_html__('All settings have been reset to their default values.', 'aca-ai-content-agent'));
    }
    
    /**
     * Handle the AJAX request for refreshing user capabilities.
     *
     * Refreshes user capabilities to fix permission issues.
     *
     * @since 1.3.0
     */
    public function handle_ajax_refresh_capabilities() {
        // Use a more lenient capability check for this specific function
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'aca_ai_content_agent_admin_nonce')) {
            wp_send_json_error(array(
                'message' => esc_html__('Security check failed. Please refresh the page and try again.', 'aca-ai-content-agent'),
                'code' => 'nonce_failed'
            ));
        }
        
        // Allow any logged-in user to refresh their own capabilities
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => esc_html__('You must be logged in to perform this action.', 'aca-ai-content-agent'),
                'code' => 'not_logged_in'
            ));
        }

        $result = ACA_Helper::refresh_user_capabilities();
        
        if ($result) {
            wp_send_json_success(array(
                'message' => esc_html__('User capabilities have been refreshed successfully. Please reload the page.', 'aca-ai-content-agent'),
                'reload_required' => true
            ));
        } else {
            wp_send_json_error(array(
                'message' => esc_html__('Failed to refresh user capabilities. Please contact your administrator.', 'aca-ai-content-agent'),
                'code' => 'refresh_failed'
            ));
        }
    }

    /**
     * Verify nonce and user capability for AJAX requests.
     *
     * @since 1.2.0
     * @param string $capability The required capability.
     */
    private function verify_nonce_and_capability($capability) {
        // FIX: Better error handling for nonce verification
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'aca_ai_content_agent_admin_nonce')) {
            wp_send_json_error(array(
                'message' => esc_html__('Security check failed. Please refresh the page and try again.', 'aca-ai-content-agent'),
                'code' => 'nonce_failed',
                'reload_required' => true
            ));
        }

        // FIX: More detailed capability error messages with troubleshooting
        if (!current_user_can($capability)) {
            $current_user = wp_get_current_user();
            $user_roles = implode(', ', $current_user->roles);
            
            $capability_names = array(
                'manage_options' => __('administrator privileges', 'aca-ai-content-agent'),
                'edit_posts' => __('content editing privileges', 'aca-ai-content-agent'),
                'edit_pages' => __('page editing privileges', 'aca-ai-content-agent')
            );
            
            $capability_name = $capability_names[$capability] ?? $capability;
            
            // Provide troubleshooting suggestions
            $troubleshooting = '';
            if ($capability === 'edit_posts' && !current_user_can('edit_posts')) {
                $troubleshooting = ' ' . esc_html__('Try logging out and back in, or contact your administrator to check your user role permissions.', 'aca-ai-content-agent');
            }
            
            wp_send_json_error(array(
                'message' => sprintf(
                    esc_html__('You do not have %s required for this action. Your current role(s): %s.%s', 'aca-ai-content-agent'),
                    $capability_name,
                    $user_roles,
                    $troubleshooting
                ),
                'code' => 'insufficient_capability',
                'required_capability' => $capability,
                'user_roles' => $user_roles,
                'troubleshooting' => true
            ));
        }
    }

    /**
     * Handle API errors with specific error codes.
     *
     * @since 1.2.0
     * @param WP_Error $error The error object.
     */
    private function handle_api_error($error) {
        if ('api_key_missing' === $error->get_error_code()) {
            wp_send_json_error(esc_html__('Google Gemini API key is missing or invalid. Please check your settings.', 'aca-ai-content-agent'));
        } else {
            wp_send_json_error($error->get_error_message());
        }
    }

    /**
     * Check if the current user is rate limited.
     *
     * @since 1.2.0
     * @return bool True if rate limited, false otherwise.
     */
    private function is_rate_limited() {
        $transient_key = 'aca_ai_content_agent_rate_limit_' . get_current_user_id();
        return (bool) get_transient($transient_key);
    }

    /**
     * Set rate limit for the current user.
     *
     * @since 1.2.0
     */
    private function set_rate_limit() {
        $transient_key = 'aca_ai_content_agent_rate_limit_' . get_current_user_id();
        set_transient($transient_key, true, self::RATE_LIMIT_DURATION);
    }

    /**
     * Build response data for idea generation.
     *
     * @since 1.2.0
     * @param array $idea_ids Array of idea IDs.
     * @return array The response data.
     */
    private function build_ideas_response($idea_ids) {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        $ideas_html = '';

        if (!empty($idea_ids)) {
            foreach ($idea_ids as $idea_id) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $idea = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$ideas_table} WHERE id = %d", $idea_id));
                if ($idea) {
                    $ideas_html .= $this->build_idea_html($idea);
                }
            }
        }

        /* translators: %d: number of ideas */
        $message = sprintf(esc_html(_n('%d new idea generated.', '%d new ideas generated.', count($idea_ids), 'aca-ai-content-agent')), count($idea_ids));

        return [
            'message' => $message,
            'ideas_html' => $ideas_html
        ];
    }

    /**
     * Build HTML for a single idea.
     *
     * @since 1.2.0
     * @param object $idea The idea object.
     * @return string The HTML string.
     */
    private function build_idea_html($idea) {
        return '<li data-id="' . esc_attr($idea->id) . '">' . esc_html($idea->title) .
            ' <button class="button-primary aca-ai-content-agent-write-draft" data-id="' . esc_attr($idea->id) . '">' . esc_html__('Write Draft', 'aca-ai-content-agent') . '</button>' .
            ' <span class="aca-ai-content-agent-draft-status"></span>' .
            ' <button class="button-secondary aca-ai-content-agent-reject-idea" data-id="' . esc_attr($idea->id) . '">' . esc_html__('Reject', 'aca-ai-content-agent') . '</button>' .
            ' <button class="button aca-ai-content-agent-feedback-btn" data-value="1">👍</button>' .
            ' <button class="button aca-ai-content-agent-feedback-btn" data-value="-1">👎</button>' .
            '</li>';
    }

    /**
     * Validate idea ID from POST data.
     *
     * @since 1.2.0
     * @return int|WP_Error The idea ID or WP_Error on failure.
     */
    private function validate_idea_id() {
        if (!isset($_POST['id'])) {
            return new WP_Error('missing_id', __('Idea ID is missing.', 'aca-ai-content-agent'));
        }

        $idea_id = absint($_POST['id']);
        if (!$idea_id) {
            return new WP_Error('invalid_id', __('Invalid idea ID.', 'aca-ai-content-agent'));
        }

        return $idea_id;
    }

    /**
     * Validate topic from POST data.
     *
     * @since 1.2.0
     * @return string|WP_Error The topic or WP_Error on failure.
     */
    private function validate_topic() {
        if (empty($_POST['topic'])) {
            return new WP_Error('missing_topic', __('Topic is required.', 'aca-ai-content-agent'));
        }

        return sanitize_text_field(wp_unslash($_POST['topic']));
    }

    /**
     * Validate feedback data from POST.
     *
     * @since 1.2.0
     * @return array|WP_Error The feedback data or WP_Error on failure.
     */
    private function validate_feedback_data() {
        if (!isset($_POST['id']) || !isset($_POST['value'])) {
            return new WP_Error('missing_data', __('Idea ID or feedback value is missing.', 'aca-ai-content-agent'));
        }

        $idea_id = absint(wp_unslash($_POST['id']));
        $value = intval(wp_unslash($_POST['value']));

        if (!$idea_id) {
            return new WP_Error('invalid_id', __('Invalid idea ID.', 'aca-ai-content-agent'));
        }

        if (!in_array($value, [-1, 0, 1], true)) {
            return new WP_Error('invalid_value', __('Invalid feedback value.', 'aca-ai-content-agent'));
        }

        return [
            'idea_id' => $idea_id,
            'value' => $value
        ];
    }

    /**
     * Validate post ID from POST data.
     *
     * @since 1.2.0
     * @return int|WP_Error The post ID or WP_Error on failure.
     */
    private function validate_post_id() {
        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        
        if (!$post_id) {
            return new WP_Error('invalid_post_id', __('Invalid post ID.', 'aca-ai-content-agent'));
        }

        return $post_id;
    }

    /**
     * Get GSC date range for AJAX requests.
     *
     * @since 1.2.0
     * @return array The date range parameters.
     */
    private function get_gsc_date_range() {
        $options = get_option('aca_ai_content_agent_options');
        $site_url = $options['gsc_site_url'] ?? '';
        $end = current_time('Y-m-d');
        $start = gmdate('Y-m-d', strtotime('-' . self::GSC_LOOKBACK_DAYS . ' days', strtotime($end)));

        return [
            'site_url' => $site_url,
            'start' => $start,
            'end' => $end
        ];
    }

    /**
     * Activate a license key.
     *
     * @since 1.2.0
     * @param string $license_key The license key to activate.
     * @param array $license_details The license details from Gumroad API.
     */
    private function activate_license($license_key, $license_details) {
        // Encrypt and save the license key
        $encrypted_key = ACA_Encryption_Util::encrypt($license_key);
        update_option('aca_ai_content_agent_license_key', $encrypted_key);
        
        // Set Pro status
        update_option('aca_ai_content_agent_is_pro_active', 'true');
        
        // Save license details
        update_option('aca_ai_content_agent_license_data', $license_details);
        
        // Set license status cache
        set_transient('aca_ai_content_agent_license_status', 'valid', WEEK_IN_SECONDS);
        
        // Clear any error notices
        delete_transient('aca_ai_content_agent_license_error');
        
        ACA_Log_Service::info('License activated successfully', [
            'email' => isset($license_details['email']) ? $license_details['email'] : 'unknown',
            'purchase_id' => isset($license_details['purchase_id']) ? $license_details['purchase_id'] : 'unknown'
        ]);
    }

    /**
     * Deactivate the current license.
     *
     * @since 1.2.0
     */
    private function deactivate_license() {
        // Remove license key
        delete_option('aca_ai_content_agent_license_key');
        
        // Set Pro status to false
        update_option('aca_ai_content_agent_is_pro_active', 'false');
        
        // Remove license details
        delete_option('aca_ai_content_agent_license_data');
        
        // Set license status cache
        set_transient('aca_ai_content_agent_license_status', 'invalid', WEEK_IN_SECONDS);
        
        // Clear any success notices
        delete_transient('aca_ai_content_agent_license_success');
        
        ACA_Log_Service::info('License deactivated');
    }

    /**
     * Delete all plugin options.
     *
     * @since 1.2.0
     */
    private function delete_all_plugin_options() {
        $options = [
            'aca_ai_content_agent_options',
            'aca_ai_content_agent_prompts',
            'aca_ai_content_agent_gemini_api_key',
            'aca_ai_content_agent_license_key',
            'aca_ai_content_agent_is_pro_active',
            'aca_ai_content_agent_license_data',
            'aca_ai_content_agent_license_valid_until',
        ];

        foreach ($options as $option) {
            delete_option($option);
        }
    }
}