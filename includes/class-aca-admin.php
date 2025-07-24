<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Settings Page
 *
 * @package ACA
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_filter('post_row_actions', [$this, 'add_update_link'], 10, 2);
        add_action('wp_ajax_aca_test_connection', [$this, 'handle_ajax_test_connection']);
        add_action('wp_ajax_aca_generate_style_guide', [$this, 'handle_ajax_generate_style_guide']);
        add_action('wp_ajax_aca_generate_ideas', [$this, 'handle_ajax_generate_ideas']);
        add_action('wp_ajax_aca_write_draft', [$this, 'handle_ajax_write_draft']);
        add_action('wp_ajax_aca_reject_idea', [$this, 'handle_ajax_reject_idea']);
        add_action('wp_ajax_aca_validate_license', [$this, 'handle_ajax_validate_license']);
        add_action('wp_ajax_aca_generate_cluster', [$this, 'handle_ajax_generate_cluster']);
        add_action('wp_ajax_aca_submit_feedback', [$this, 'handle_ajax_submit_feedback']);
        add_action('wp_ajax_aca_suggest_update', [$this, 'handle_ajax_suggest_update']);
        add_action('wp_ajax_aca_fetch_gsc_data', [$this, 'handle_ajax_fetch_gsc_data']);
        add_action('wp_ajax_aca_generate_gsc_ideas', [$this, 'handle_ajax_generate_gsc_ideas']);
        add_action('admin_notices', [$this, 'display_admin_notices']);
        add_action('add_meta_boxes', [$this, 'add_plagiarism_metabox']);
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'page_asa-ai-content-agent') === false && $hook !== 'edit.php') {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'aca-admin-css',
            plugin_dir_url(__FILE__) . '../admin/css/aca-admin.css',
            [],
            ACA_VERSION
        );

        // Enqueue JS
        wp_enqueue_script(
            'aca-admin-js',
            plugin_dir_url(__FILE__) . '../admin/js/aca-admin.js',
            ['jquery'], // Add jquery dependency if needed
            ACA_VERSION,
            true
        );

        wp_localize_script('aca-admin-js', 'aca_admin_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('aca_admin_nonce'),
        ]);
    }

    /**
     * Handle the AJAX request for testing the API connection.
     */
    public function handle_ajax_test_connection() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        // Use a simple prompt to test the connection
        $test_prompt = 'Hello.'; 
        $response = aca_call_gemini_api($test_prompt);

        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        } else {
            wp_send_json_success(esc_html__('Connection successful! API is working correctly.', 'asa-ai-content-agent'));
        }
    }

    /**
     * Handle the AJAX request for generating the style guide.
     */
    public function handle_ajax_generate_style_guide() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        $result = ACA_Engine::generate_style_guide();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success(esc_html__('Style guide updated successfully based on the latest content.', 'asa-ai-content-agent'));
        }
    }

    /**
     * Handle the AJAX request for generating ideas.
     */
    public function handle_ajax_generate_ideas() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        $result = ACA_Engine::generate_ideas();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            /* translators: %d: number of ideas */
            $message = sprintf(esc_html(_n('%d new idea generated.', '%d new ideas generated.', count($result), 'asa-ai-content-agent')), count($result));
            wp_send_json_success(['message' => $message]);
        }
    }

    /**
     * Handle the AJAX request for writing a post draft.
     */
    public function handle_ajax_write_draft() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        if (!isset($_POST['id']) || !absint($_POST['id'])) {
            wp_send_json_error(esc_html__('Invalid idea ID.', 'asa-ai-content-agent'));
        }

        $idea_id = absint($_POST['id']);
        $result = ACA_Engine::write_post_draft($idea_id);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success([
                'message' => esc_html__('Draft created successfully.', 'asa-ai-content-agent'),
                'edit_link' => get_edit_post_link($result, 'raw'),
            ]);
        }
    }

    /**
     * Handle the AJAX request for rejecting an idea.
     */
    public function handle_ajax_reject_idea() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        if (!isset($_POST['id']) || !absint($_POST['id'])) {
            wp_send_json_error(esc_html__('Invalid idea ID.', 'asa-ai-content-agent'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ideas';
        $idea_id = absint($_POST['id']);

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->update(
            $table_name,
            ['status' => 'rejected'],
            ['id' => $idea_id],
            ['%s'],
            ['%d']
        );

        /* translators: %d: idea ID */
        ACA_Engine::add_log(sprintf(esc_html__('Idea #%d rejected by user.', 'asa-ai-content-agent'), $idea_id));
        wp_send_json_success(esc_html__('Idea rejected.', 'asa-ai-content-agent'));
    }

    /**
     * Handle the AJAX request for validating the license key.
     */
    public function handle_ajax_validate_license() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        if ( ! defined('ACA_GUMROAD_PRODUCT_ID') || empty(ACA_GUMROAD_PRODUCT_ID) ) {
            wp_send_json_error(esc_html__('Product ID is not configured in the plugin.', 'asa-ai-content-agent'));
        }

                        if ( ! isset( $_POST['license_key'] ) ) {
            wp_send_json_error( esc_html__( 'License key cannot be empty.', 'asa-ai-content-agent' ) );
        }

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $license_key = sanitize_text_field( wp_unslash( $_POST['license_key'] ) );
        $api_url = 'https://api.gumroad.com/v2/licenses/verify';

        $response = wp_remote_post($api_url, [
            'method'    => 'POST',
            'timeout'   => 15,
            'body'      => [
                'product_id'  => ACA_GUMROAD_PRODUCT_ID,
                'license_key' => $license_key,
            ],
        ]);

        if ( is_wp_error($response) ) {
            wp_send_json_error($response->get_error_message());
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if ( isset($body['success']) && $body['success'] === true ) {
            // License is valid. Optionally check for refunds or chargebacks
            $purchase = $body['purchase'];

            update_option('aca_license_key', aca_encrypt( $license_key ));
            update_option('aca_is_pro_active', 'true');
            update_option('aca_license_data', $purchase);

            // Calculate license validity (1 year from purchase date)
            $sale_time   = isset( $purchase['sale_timestamp'] ) ? strtotime( $purchase['sale_timestamp'] ) : time();
            $valid_until = $sale_time + YEAR_IN_SECONDS;
            update_option( 'aca_license_valid_until', $valid_until );

            set_transient( 'aca_license_status', 'valid', WEEK_IN_SECONDS );

            wp_send_json_success( esc_html__( 'License activated successfully!', 'asa-ai-content-agent' ) );
        } else {
            // License is invalid or another error occurred
            $message = isset($body['message']) ? $body['message'] : esc_html__('Invalid license key or API error.', 'asa-ai-content-agent');
            update_option('aca_is_pro_active', 'false');
            delete_option('aca_license_valid_until');
            set_transient( 'aca_license_status', 'invalid', WEEK_IN_SECONDS );
            wp_send_json_error($message);
        }
    }

    /**
     * Handle AJAX request for generating a content cluster.
     */
    public function handle_ajax_generate_cluster() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        if ( empty( $_POST['topic'] ) ) {
            wp_send_json_error( __( 'Topic is required.', 'asa-ai-content-agent' ) );
        }

        $topic = sanitize_text_field( wp_unslash( $_POST['topic'] ) );
        $result = ACA_Engine::generate_content_cluster($topic);

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
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('view_aca_dashboard') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        $idea_id = isset( $_POST['id'] ) ? absint( wp_unslash( $_POST['id'] ) ) : 0;
        $value   = isset( $_POST['value'] ) ? intval( wp_unslash( $_POST['value'] ) ) : 0;

        if ($idea_id) {
            ACA_Engine::record_feedback($idea_id, $value);
            wp_send_json_success();
        } else {
            wp_send_json_error(__('Invalid idea ID.', 'asa-ai-content-agent'));
        }
    }

    /**
     * Handle AJAX request for suggesting updates to a post.
     */
    public function handle_ajax_suggest_update() {
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        $post_id = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
        if (!$post_id) {
            wp_send_json_error(__('Invalid post ID.', 'asa-ai-content-agent'));
        }

        $result = ACA_Engine::suggest_content_update($post_id);

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
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        $options = get_option('aca_options');
        $site_url = $options['gsc_site_url'] ?? '';
        $end      = current_time( 'Y-m-d' );
        $start    = gmdate( 'Y-m-d', strtotime( '-7 days', strtotime( $end ) ) );

        $result = ACA_Engine::fetch_gsc_data($site_url, $start, $end);

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
        check_ajax_referer('aca_admin_nonce', 'nonce');

        if ( ! current_user_can('manage_aca_settings') ) {
            wp_send_json_error(esc_html__('You do not have permission to do this.', 'asa-ai-content-agent'));
        }

        $result = ACA_Engine::generate_ideas_from_gsc();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            /* translators: %d: number of ideas */
            $message = sprintf(esc_html(_n('%d new idea generated.', '%d new ideas generated.', count($result), 'asa-ai-content-agent')), count($result));
            wp_send_json_success(['message' => $message]);
        }
    }

    /**
     * Add the admin menu item for ACA.
     */
    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'ACA - AI Content Agent', 'asa-ai-content-agent' ),
            esc_html__( 'ACA', 'asa-ai-content-agent' ),
            'view_aca_dashboard',
            'asa-ai-content-agent',
            [ $this, 'render_settings_page' ],
            'dashicons-robot'
        );
    }

    /**
     * Render the main settings page with tabs.
     */
    public function render_settings_page() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'dashboard';
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=asa-ai-content-agent&amp;tab=dashboard" class="nav-tab <?php echo esc_attr( $active_tab === 'dashboard' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Dashboard', 'asa-ai-content-agent'); ?></a>
                <a href="?page=asa-ai-content-agent&amp;tab=settings" class="nav-tab <?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Settings', 'asa-ai-content-agent'); ?></a>
                <a href="?page=asa-ai-content-agent&amp;tab=prompts" class="nav-tab <?php echo esc_attr( $active_tab === 'prompts' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Prompt Editor', 'asa-ai-content-agent'); ?></a>
                <a href="?page=asa-ai-content-agent&amp;tab=license" class="nav-tab <?php echo esc_attr( $active_tab === 'license' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'License', 'asa-ai-content-agent'); ?></a>
            </h2>

            <?php
            if ($active_tab === 'dashboard') {
                ACA_Dashboard::render();
            } elseif ($active_tab === 'settings') {
                ?>
                <form action="options.php" method="post">
                    <?php
                    settings_fields('aca_settings_group');
                    do_settings_sections('asa-ai-content-agent');
                    submit_button(esc_html__('Save Settings', 'asa-ai-content-agent'));
                    ?>
                </form>
                <div class="aca-disclaimer">
                    <h3><?php esc_html_e( 'Legal Disclaimer', 'asa-ai-content-agent'); ?></h3>
                    <p><?php esc_html_e( 'All content generated by ACA is a "draft". It is essential that you review, edit, and verify all content before publishing. The final responsibility for the published content belongs to you.', 'asa-ai-content-agent'); ?></p>
                </div>
                <?php
            } elseif ($active_tab === 'prompts') {
                $this->render_prompts_page();
            } elseif ($active_tab === 'license') {
                $this->render_license_page();
            }
            ?>
        </div>
        <?php
    }

    /**
     * Display admin notices.
     */
    public function display_admin_notices() {
        if ( empty( get_option( 'aca_gemini_api_key' ) ) ) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . esc_html__( 'ACA: API key is not set. Please set it in the', 'asa-ai-content-agent' ) . ' <a href="?page=asa-ai-content-agent&amp;tab=settings">' . esc_html__( 'settings', 'asa-ai-content-agent' ) . '</a>.</p></div>';
        }

        $options = get_option('aca_options');
        $limit = $options['api_monthly_limit'] ?? 0;
        $usage = get_option('aca_api_usage_current_month', 0);

        if ( $limit > 0 && $usage / $limit >= 0.8 ) {
            /* translators: %s: percentage of usage */
            echo '<div class="notice notice-warning is-dismissible"><p>' . sprintf( esc_html__( 'ACA: You have used %s%% or more of your monthly API call limit.', 'asa-ai-content-agent' ), '80' ) . '</p></div>';
        }

        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $pending = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM `{$ideas_table}` WHERE status = %s", 'pending' ) );
        if ($pending > 0) {
            /* translators: %d: number of pending ideas */
            echo '<div class="notice notice-info is-dismissible"><p>' . sprintf( esc_html__( 'ACA: %d new ideas are awaiting your review.', 'asa-ai-content-agent' ), esc_html( $pending ) ) . ' <a href="?page=asa-ai-content-agent&amp;tab=dashboard">' . esc_html__( 'Open Dashboard', 'asa-ai-content-agent' ) . '</a></p></div>';
        }

        $logs_table = $wpdb->prefix . 'aca_logs';
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $latest_error = $wpdb->get_row( $wpdb->prepare( "SELECT log_message FROM `{$logs_table}` WHERE log_type = %s AND created_at >= %s ORDER BY id DESC LIMIT 1", 'error', gmdate( 'Y-m-d H:i:s', strtotime( '-1 day' ) ) ) );
        if ( $latest_error ) {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $latest_error->log_message ) . '</p></div>';
        }
    }

    /**
     * Register the settings, sections, and fields for the admin page.
     */
    public function register_settings() {
        // Settings Group
        $group = 'aca_settings_group';

        // Register Settings
        register_setting($group, 'aca_gemini_api_key', ['sanitize_callback' => [$this, 'sanitize_and_obfuscate_api_key']]);
        register_setting($group, 'aca_options', ['sanitize_callback' => [$this, 'sanitize_options']]);

        // API Settings Section
        add_settings_section('aca_api_settings_section', esc_html__('API and Connection Settings', 'asa-ai-content-agent'), null, 'asa-ai-content-agent');
        add_settings_field('aca_gemini_api_key', esc_html__('Google Gemini API Key', 'asa-ai-content-agent'), [$this, 'render_api_key_field'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_api_test', esc_html__('Connection Test', 'asa-ai-content-agent'), [$this, 'render_api_test_button'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_copyscape_username', esc_html__('Copyscape Username', 'asa-ai-content-agent'), [$this, 'render_copyscape_username_field'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_copyscape_api_key', esc_html__('Copyscape API Key', 'asa-ai-content-agent'), [$this, 'render_copyscape_api_key_field'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_gsc_site_url', esc_html__('Search Console Site URL', 'asa-ai-content-agent'), [$this, 'render_gsc_site_url_field'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_gsc_api_key', esc_html__('Search Console API Key', 'asa-ai-content-agent'), [$this, 'render_gsc_api_key_field'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_pexels_api_key', esc_html__('Pexels API Key', 'asa-ai-content-agent'), [$this, 'render_pexels_api_key_field'], 'asa-ai-content-agent', 'aca_api_settings_section');
        add_settings_field('aca_openai_api_key', esc_html__('OpenAI API Key', 'asa-ai-content-agent'), [$this, 'render_openai_api_key_field'], 'asa-ai-content-agent', 'aca_api_settings_section');

        // Automation Settings Section
        add_settings_section('aca_automation_settings_section', esc_html__('Automation Settings', 'asa-ai-content-agent'), null, 'asa-ai-content-agent');
        add_settings_field('aca_working_mode', esc_html__('Working Mode', 'asa-ai-content-agent'), [$this, 'render_working_mode_field'], 'asa-ai-content-agent', 'aca_automation_settings_section');
        add_settings_field('aca_automation_frequency', esc_html__('Automation Frequency', 'asa-ai-content-agent'), [$this, 'render_automation_frequency_field'], 'asa-ai-content-agent', 'aca_automation_settings_section');
        add_settings_field('aca_generation_limit', esc_html__('Generation Limit', 'asa-ai-content-agent'), [$this, 'render_generation_limit_field'], 'asa-ai-content-agent', 'aca_automation_settings_section');
        add_settings_field('aca_default_author', esc_html__('Default Author', 'asa-ai-content-agent'), [$this, 'render_default_author_field'], 'asa-ai-content-agent', 'aca_automation_settings_section');
        add_settings_field('aca_default_profile', esc_html__('Brand Voice Profile', 'asa-ai-content-agent'), [$this, 'render_default_profile_field'], 'asa-ai-content-agent', 'aca_automation_settings_section');

        // Content Analysis Section
        add_settings_section('aca_analysis_settings_section', esc_html__('Content Analysis & Learning Rules', 'asa-ai-content-agent'), null, 'asa-ai-content-agent');
        add_settings_field('aca_analysis_post_types', esc_html__('Analyze Content Types', 'asa-ai-content-agent'), [$this, 'render_analysis_post_types_field'], 'asa-ai-content-agent', 'aca_analysis_settings_section');
        add_settings_field('aca_analysis_depth', esc_html__('Analysis Depth', 'asa-ai-content-agent'), [$this, 'render_analysis_depth_field'], 'asa-ai-content-agent', 'aca_analysis_settings_section');
        add_settings_field('aca_analysis_categories', esc_html__('Analysis Categories', 'asa-ai-content-agent'), [$this, 'render_analysis_categories_field'], 'asa-ai-content-agent', 'aca_analysis_settings_section');
        add_settings_field('aca_style_guide_frequency', esc_html__('Style Guide Refresh', 'asa-ai-content-agent'), [$this, 'render_style_guide_frequency_field'], 'asa-ai-content-agent', 'aca_analysis_settings_section');

        // Content Enrichment Section
        add_settings_section('aca_enrichment_settings_section', esc_html__('Content Enrichment', 'asa-ai-content-agent'), null, 'asa-ai-content-agent');
        add_settings_field('aca_internal_links_max', esc_html__('Max Internal Links', 'asa-ai-content-agent'), [$this, 'render_internal_links_max_field'], 'asa-ai-content-agent', 'aca_enrichment_settings_section');
        add_settings_field('aca_featured_image_provider', esc_html__('Featured Image Provider', 'asa-ai-content-agent'), [$this, 'render_featured_image_provider_field'], 'asa-ai-content-agent', 'aca_enrichment_settings_section');
        add_settings_field('aca_add_data_sections', esc_html__('Add Data Sections', 'asa-ai-content-agent'), [$this, 'render_add_data_sections_field'], 'asa-ai-content-agent', 'aca_enrichment_settings_section');

        // Management and Cost Control Section
        add_settings_section('aca_management_settings_section', esc_html__('Management and Cost Control', 'asa-ai-content-agent'), null, 'asa-ai-content-agent');
        add_settings_field('aca_api_monthly_limit', esc_html__('Monthly API Limit', 'asa-ai-content-agent'), [$this, 'render_api_monthly_limit_field'], 'asa-ai-content-agent', 'aca_management_settings_section');
        add_settings_field('aca_api_usage_display', esc_html__('Current API Usage', 'asa-ai-content-agent'), [$this, 'render_api_usage_display_field'], 'asa-ai-content-agent', 'aca_management_settings_section');
        add_settings_field('aca_log_cleanup_enabled', esc_html__('Enable Log Cleanup', 'asa-ai-content-agent'), [$this, 'render_log_cleanup_enabled_field'], 'asa-ai-content-agent', 'aca_management_settings_section');
        add_settings_field('aca_log_retention_days', esc_html__('Log Retention (days)', 'asa-ai-content-agent'), [$this, 'render_log_retention_days_field'], 'asa-ai-content-agent', 'aca_management_settings_section');


        // Prompts Section (for saving)
        register_setting('aca_prompts_group', 'aca_prompts', ['sanitize_callback' => [$this, 'sanitize_prompts']]);
        register_setting('aca_prompts_group', 'aca_brand_profiles', ['sanitize_callback' => [$this, 'sanitize_brand_profiles']]);

        // License Section (for saving)
        register_setting('aca_license_group', 'aca_license_key', ['sanitize_callback' => [$this, 'sanitize_license_key']]);
    }

    /**
     * Sanitize the options array.
     */
    public function sanitize_options($input) {
        $new_input = [];
        $options = get_option('aca_options', []);
        
        $new_input['working_mode'] = isset($input['working_mode']) ? sanitize_key($input['working_mode']) : ($options['working_mode'] ?? 'manual');
        $new_input['automation_frequency'] = isset($input['automation_frequency']) ? sanitize_key($input['automation_frequency']) : ($options['automation_frequency'] ?? 'daily');
        $new_input['generation_limit'] = isset($input['generation_limit']) ? absint($input['generation_limit']) : ($options['generation_limit'] ?? 5);
        $new_input['default_author'] = isset($input['default_author']) ? absint($input['default_author']) : ($options['default_author'] ?? get_current_user_id());
        $new_input['default_profile'] = isset($input['default_profile']) ? sanitize_key($input['default_profile']) : ($options['default_profile'] ?? '');
        $new_input['analysis_post_types'] = isset($input['analysis_post_types']) ? array_map('sanitize_text_field', $input['analysis_post_types']) : ($options['analysis_post_types'] ?? ['post']);
        $new_input['analysis_depth'] = isset($input['analysis_depth']) ? absint($input['analysis_depth']) : ($options['analysis_depth'] ?? 20);
        $new_input['analysis_include_categories'] = isset($input['analysis_include_categories']) ? array_map('absint', $input['analysis_include_categories']) : ($options['analysis_include_categories'] ?? []);
        $new_input['analysis_exclude_categories'] = isset($input['analysis_exclude_categories']) ? array_map('absint', $input['analysis_exclude_categories']) : ($options['analysis_exclude_categories'] ?? []);
        $new_input['internal_links_max'] = isset($input['internal_links_max']) ? absint($input['internal_links_max']) : ($options['internal_links_max'] ?? 3);
        $new_input['featured_image_provider'] = isset($input['featured_image_provider']) ? sanitize_key($input['featured_image_provider']) : ($options['featured_image_provider'] ?? 'none');
        $new_input['add_data_sections'] = isset($input['add_data_sections']) ? 1 : ($options['add_data_sections'] ?? 0);
        $new_input['gsc_site_url'] = isset($input['gsc_site_url']) ? esc_url_raw($input['gsc_site_url']) : ($options['gsc_site_url'] ?? '');
        // Encrypt API keys if new values are provided; otherwise keep existing encrypted values.
        if ( isset( $input['gsc_api_key'] ) && '' !== trim( $input['gsc_api_key'] ) ) {
            $new_input['gsc_api_key'] = aca_encrypt( sanitize_text_field( $input['gsc_api_key'] ) );
        } else {
            $new_input['gsc_api_key'] = $options['gsc_api_key'] ?? '';
        }

        if ( isset( $input['pexels_api_key'] ) && '' !== trim( $input['pexels_api_key'] ) ) {
            $new_input['pexels_api_key'] = aca_encrypt( sanitize_text_field( $input['pexels_api_key'] ) );
        } else {
            $new_input['pexels_api_key'] = $options['pexels_api_key'] ?? '';
        }

        if ( isset( $input['openai_api_key'] ) && '' !== trim( $input['openai_api_key'] ) ) {
            $new_input['openai_api_key'] = aca_encrypt( sanitize_text_field( $input['openai_api_key'] ) );
        } else {
            $new_input['openai_api_key'] = $options['openai_api_key'] ?? '';
        }
        $new_input['style_guide_frequency'] = isset($input['style_guide_frequency']) ? sanitize_key($input['style_guide_frequency']) : ($options['style_guide_frequency'] ?? 'weekly');
        $new_input['api_monthly_limit'] = isset($input['api_monthly_limit']) ? absint($input['api_monthly_limit']) : ($options['api_monthly_limit'] ?? 0);
        $new_input['copyscape_username'] = isset($input['copyscape_username']) ? sanitize_text_field($input['copyscape_username']) : ($options['copyscape_username'] ?? '');
        if ( isset( $input['copyscape_api_key'] ) && '' !== trim( $input['copyscape_api_key'] ) ) {
            $new_input['copyscape_api_key'] = aca_encrypt( sanitize_text_field( $input['copyscape_api_key'] ) );
        } else {
            $new_input['copyscape_api_key'] = $options['copyscape_api_key'] ?? '';
        }
        $new_input['log_cleanup_enabled'] = isset($input['log_cleanup_enabled']) ? 1 : ($options['log_cleanup_enabled'] ?? 0);
        $new_input['log_retention_days'] = isset($input['log_retention_days']) ? absint($input['log_retention_days']) : ($options['log_retention_days'] ?? 60);

        return $new_input;
    }

    /**
     * Sanitize and obfuscate the API key before saving.
     */
    public function sanitize_and_obfuscate_api_key($input) {
        $existing = get_option('aca_gemini_api_key');

        if ( ! isset( $input ) || '' === trim( $input ) ) {
            // Keep existing value when no new key is supplied.
            return $existing;
        }

        $sanitized_key = sanitize_text_field( $input );

        return aca_encrypt( $sanitized_key );
    }

    /**
     * Sanitize and encrypt the license key before saving.
     */
    public function sanitize_license_key( $input ) {
        $existing = get_option( 'aca_license_key' );

        if ( ! isset( $input ) || '' === trim( $input ) ) {
            return $existing;
        }

        $sanitized_key = sanitize_text_field( $input );

        return aca_encrypt( $sanitized_key );
    }

    /**
     * Render the API Key input field.
     */
    public function render_api_key_field() {
        $api_key = get_option('aca_gemini_api_key');
        $placeholder = !empty($api_key) ? esc_html__('***************** (already saved)', 'asa-ai-content-agent') : '';
        echo '<input type="password" id="aca_gemini_api_key" name="aca_gemini_api_key" value="" placeholder="' . esc_attr($placeholder) . '" class="regular-text">';
        echo '<p class="description">' . esc_html__('Enter your Google Gemini API key. Your key is obfuscated for security.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the API Test button.
     */
    public function render_api_test_button() {
        echo '<button type="button" class="button" id="aca-test-connection">' . esc_html__('Test Connection', 'asa-ai-content-agent') . '</button>';
        echo '<span id="aca-test-status" style="margin-left: 10px;"></span>';
    }

    /**
     * Render the Copyscape Username field.
     */
    public function render_copyscape_username_field() {
        $options = get_option('aca_options');
        $username = isset($options['copyscape_username']) ? $options['copyscape_username'] : '';
        echo '<input type="text" name="aca_options[copyscape_username]" value="' . esc_attr($username) . '" class="regular-text">';
    }

    /**
     * Render the Copyscape API key field.
     */
    public function render_copyscape_api_key_field() {
        $options     = get_option('aca_options');
        $key         = $options['copyscape_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__('***************** (already saved)', 'asa-ai-content-agent') : '';
        echo '<input type="password" name="aca_options[copyscape_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    /**
     * Render the Search Console Site URL field.
     */
    public function render_gsc_site_url_field() {
        $options = get_option('aca_options');
        $url = isset($options['gsc_site_url']) ? $options['gsc_site_url'] : '';
        echo '<input type="text" name="aca_options[gsc_site_url]" value="' . esc_attr($url) . '" class="regular-text">';
    }

    /**
     * Render the Search Console API key field.
     */
    public function render_gsc_api_key_field() {
        $options     = get_option('aca_options');
        $key         = $options['gsc_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__('***************** (already saved)', 'asa-ai-content-agent') : '';
        echo '<input type="password" name="aca_options[gsc_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    /**
     * Render the Pexels API key field.
     */
    public function render_pexels_api_key_field() {
        $options     = get_option('aca_options');
        $key         = $options['pexels_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__('***************** (already saved)', 'asa-ai-content-agent') : '';
        echo '<input type="password" name="aca_options[pexels_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    /**
     * Render the OpenAI API key field.
     */
    public function render_openai_api_key_field() {
        $options     = get_option('aca_options');
        $key         = $options['openai_api_key'] ?? '';
        $placeholder = ! empty( $key ) ? esc_html__('***************** (already saved)', 'asa-ai-content-agent') : '';
        echo '<input type="password" name="aca_options[openai_api_key]" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">';
    }

    /**
     * Render the Working Mode dropdown.
     */
    public function render_working_mode_field() {
        $options = get_option('aca_options');
        $current_mode = isset($options['working_mode']) ? $options['working_mode'] : 'manual';
        if ( ! aca_is_pro() ) {
            echo '<select name="aca_options[working_mode]" disabled>';
            echo '<option value="manual" selected>' . esc_html__( 'Manual Mode', 'asa-ai-content-agent' ) . '</option>';
            echo '</select>';
            echo '<p class="description">' . esc_html__( 'Automation modes require ACA Pro.', 'asa-ai-content-agent' ) . '</p>';
            echo '<input type="hidden" name="aca_options[working_mode]" value="manual">';
            return;
        }

        $modes = [
            'manual'    => esc_html__('Manual Mode', 'asa-ai-content-agent'),
            'semi-auto' => esc_html__('Semi-Automatic (Ideas & Approval)', 'asa-ai-content-agent'),
            'full-auto' => esc_html__('Fully Automatic (Draft Creation)', 'asa-ai-content-agent'),
        ];

        echo '<select name="aca_options[working_mode]">';
        foreach ($modes as $key => $label) {
            $selected = selected($current_mode, $key, false);
            echo '<option value="' . esc_attr($key) . '" ' . esc_attr( $selected ) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Choose how ACA operates. Note: All content is always saved as a draft.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Automation Frequency dropdown.
     */
    public function render_automation_frequency_field() {
        $options = get_option('aca_options');
        $current_freq = isset($options['automation_frequency']) ? $options['automation_frequency'] : 'daily';
        $schedules = wp_get_schedules();
        $frequencies = [];
        foreach ($schedules as $key => $schedule) {
            $frequencies[$key] = $schedule['display'];
        }

        echo '<select name="aca_options[automation_frequency]">';
        foreach ($frequencies as $key => $label) {
            $selected = selected($current_freq, $key, false);
            echo '<option value="' . esc_attr($key) . '" ' . esc_attr( $selected ) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('How often the automatic tasks should run.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Generation Limit input.
     */
    public function render_generation_limit_field() {
        $options = get_option('aca_options');
        $limit = isset($options['generation_limit']) ? $options['generation_limit'] : 5;
        echo '<input type="number" name="aca_options[generation_limit]" value="' . esc_attr($limit) . '" class="small-text" min="1">';
        echo '<p class="description">' . esc_html__('Max number of ideas/drafts per cycle to control API costs.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Default Author dropdown.
     */
    public function render_default_author_field() {
        $options = get_option('aca_options');
        $selected_author = isset($options['default_author']) ? $options['default_author'] : get_current_user_id();
        wp_dropdown_users([
            'name' => 'aca_options[default_author]',
            'selected' => $selected_author,
            'show_option_none' => esc_html__('Select an Author', 'asa-ai-content-agent'),
        ]);
    }

    /**
     * Render the default brand voice profile dropdown.
     */
    public function render_default_profile_field() {
        $options  = get_option('aca_options');
        $profiles = ACA_Engine::get_brand_profiles();
        $current  = $options['default_profile'] ?? '';

        echo '<select name="aca_options[default_profile]">';
        echo '<option value="">' . esc_html__('Default', 'asa-ai-content-agent') . '</option>';
        foreach ($profiles as $key => $label) {
            $selected = selected($current, $key, false);
            echo '<option value="' . esc_attr($key) . '" ' . esc_attr( $selected ) . '>' . esc_html($key) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Select which brand voice profile to use for new drafts.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Analysis Post Types checkboxes.
     */
    public function render_analysis_post_types_field() {
        $options = get_option('aca_options');
        $post_types = get_post_types(['public' => true], 'objects');
        $checked_post_types = isset($options['analysis_post_types']) ? $options['analysis_post_types'] : ['post'];

        foreach ($post_types as $post_type) {
            if (in_array($post_type->name, ['attachment', 'page'])) {
                continue;
            }
            $checked = in_array($post_type->name, $checked_post_types) ? 'checked' : '';
            echo '<label><input type="checkbox" name="aca_options[analysis_post_types][]" value="' . esc_attr($post_type->name) . '" ' . esc_attr( $checked ) . '> ' . esc_html($post_type->label) . '</label><br>';
        }
        echo '<p class="description">' . esc_html__('Select the content types ACA should analyze to learn the writing style.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Analysis Depth input.
     */
    public function render_analysis_depth_field() {
        $options = get_option('aca_options');
        $depth = isset($options['analysis_depth']) ? $options['analysis_depth'] : 20;
        echo '<input type="number" name="aca_options[analysis_depth]" value="' . esc_attr($depth) . '" class="small-text" min="5" max="100">';
        echo '<p class="description">' . esc_html__('Number of recent posts (5-100) to analyze for learning the writing style.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Analysis Categories checkboxes.
     */
    public function render_analysis_categories_field() {
        $options = get_option('aca_options');
        $include_categories = isset($options['analysis_include_categories']) ? $options['analysis_include_categories'] : [];
        $exclude_categories = isset($options['analysis_exclude_categories']) ? $options['analysis_exclude_categories'] : [];
        $categories = get_categories(['hide_empty' => 0]);

        echo '<div style="display: flex; gap: 20px;">';
        echo '<div style="flex: 1;">';
        echo '<strong>' . esc_html__('Include Categories', 'asa-ai-content-agent') . '</strong>';
        echo '<div style="height: 150px; overflow-y: scroll; border: 1px solid #ddd; padding: 5px; background: #fff;">';
        foreach ($categories as $category) {
            $checked = in_array($category->term_id, $include_categories) ? 'checked' : '';
            echo '<label><input type="checkbox" name="aca_options[analysis_include_categories][]" value="' . esc_attr($category->term_id) . '" ' . esc_attr( $checked ) . '> ' . esc_html($category->name) . '</label><br>';
        }
        echo '</div>';
        echo '</div>';

        echo '<div style="flex: 1;">';
        echo '<strong>' . esc_html__('Exclude Categories', 'asa-ai-content-agent') . '</strong>';
        echo '<div style="height: 150px; overflow-y: scroll; border: 1px solid #ddd; padding: 5px; background: #fff;">';
        foreach ($categories as $category) {
            $checked = in_array($category->term_id, $exclude_categories) ? 'checked' : '';
            echo '<label><input type="checkbox" name="aca_options[analysis_exclude_categories][]" value="' . esc_attr($category->term_id) . '" ' . esc_attr( $checked ) . '> ' . esc_html($category->name) . '</label><br>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<p class="description">' . esc_html__('Fine-tune the style analysis by including or excluding specific categories. If any category is included, only posts from those categories will be analyzed.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Style Guide refresh frequency dropdown.
     */
    public function render_style_guide_frequency_field() {
        $options = get_option('aca_options');
        $current = isset($options['style_guide_frequency']) ? $options['style_guide_frequency'] : 'weekly';
        $schedules = wp_get_schedules();
        $frequencies = ['manual' => esc_html__('Manual', 'asa-ai-content-agent')];
        foreach ($schedules as $key => $label) {
            $selected = selected($current, $key, false);
            echo '<option value="' . esc_attr($key) . '" ' . esc_attr( $selected ) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('How often ACA should regenerate the style guide automatically.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Max Internal Links input.
     */
    public function render_internal_links_max_field() {
        $options = get_option('aca_options');
        $max = isset($options['internal_links_max']) ? $options['internal_links_max'] : 3;
        echo '<input type="number" name="aca_options[internal_links_max]" value="' . esc_attr($max) . '" class="small-text" min="0" max="10">';
        echo '<p class="description">' . esc_html__('Maximum number of internal links (0-10) to add to each new draft.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Featured Image Provider dropdown.
     */
    public function render_featured_image_provider_field() {
        $options = get_option('aca_options');
        $provider = isset($options['featured_image_provider']) ? $options['featured_image_provider'] : 'none';
        $providers = [
            'none'    => esc_html__('None', 'asa-ai-content-agent'),
            'unsplash'=> esc_html__('Unsplash', 'asa-ai-content-agent'),
            'pexels'  => esc_html__('Pexels', 'asa-ai-content-agent'),
            'dalle'   => esc_html__('DALL-E 3', 'asa-ai-content-agent'),
        ];

        echo '<select name="aca_options[featured_image_provider]">';
        foreach ($providers as $key => $label) {
            $selected = selected($provider, $key, false);
            echo '<option value="' . esc_attr($key) . '" ' . esc_attr( $selected ) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__('Select a provider for automatically fetching a featured image for each draft.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Add Data Sections checkbox.
     */
    public function render_add_data_sections_field() {
        $options = get_option('aca_options');
        $checked = isset($options['add_data_sections']) ? (bool)$options['add_data_sections'] : false;
        echo '<label><input type="checkbox" name="aca_options[add_data_sections]" value="1" ' . checked($checked, true, false) . '> ' . esc_html__('Append statistics section to drafts (Pro only)', 'asa-ai-content-agent') . '</label>';
    }

    /**
     * Render the API Monthly Limit input.
     */
    public function render_api_monthly_limit_field() {
        $options = get_option('aca_options');
        $limit = isset($options['api_monthly_limit']) ? $options['api_monthly_limit'] : 0;
        echo '<input type="number" name="aca_options[api_monthly_limit]" value="' . esc_attr($limit) . '" class="small-text" min="0">';
        echo '<p class="description">' . esc_html__('Set a monthly API call limit to control costs. Set to 0 for no limit.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the API Usage Display.
     */
    public function render_api_usage_display_field() {
        $usage = get_option('aca_api_usage_current_month', 0);
        /* translators: %d: number of API calls */
        echo '<span>' . sprintf(esc_html__('%d calls this month.', 'asa-ai-content-agent'), esc_html( $usage )) . '</span>';
        echo '<p class="description">' . esc_html__('This counter resets on the first day of each month.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the log cleanup enable checkbox.
     */
    public function render_log_cleanup_enabled_field() {
        $options = get_option('aca_options');
        $checked = ! empty( $options['log_cleanup_enabled'] );
        echo '<label><input type="checkbox" name="aca_options[log_cleanup_enabled]" value="1" ' . checked( $checked, true, false ) . '> ' . esc_html__('Delete old log entries automatically', 'asa-ai-content-agent') . '</label>';
    }

    /**
     * Render the log retention days field.
     */
    public function render_log_retention_days_field() {
        $options = get_option('aca_options');
        $days = isset( $options['log_retention_days'] ) ? $options['log_retention_days'] : 60;
        echo '<input type="number" name="aca_options[log_retention_days]" value="' . esc_attr( $days ) . '" class="small-text" min="1">';
        echo '<p class="description">' . esc_html__('Remove logs older than this number of days.', 'asa-ai-content-agent') . '</p>';
    }

    /**
     * Render the Prompts Editor page.
     */
    public function render_prompts_page() {
        if ( isset( $_POST['submit'] ) ) {
            check_admin_referer( 'aca_prompts_group-options' );
        }
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('aca_prompts_group');
            $prompts = ACA_Engine::get_prompts();
            $profiles = ACA_Engine::get_brand_profiles();
            ?>
            <h3><?php esc_html_e('Style Guide Prompt', 'asa-ai-content-agent'); ?></h3>
            <textarea name="aca_prompts[style_guide]" rows="10" cols="50" class="large-text"><?php echo esc_textarea($prompts['style_guide']); ?></textarea>
            <p class="description"><?php esc_html_e('This prompt is used to create the style guide from your existing content. Available variables: <code>{{content_corpus}}</code>', 'asa-ai-content-agent'); ?></p>

            <h3><?php esc_html_e('Idea Generation Prompt', 'asa-ai-content-agent'); ?></h3>
            <textarea name="aca_prompts[idea_generation]" rows="10" cols="50" class="large-text"><?php echo esc_textarea($prompts['idea_generation']); ?></textarea>
            <p class="description"><?php esc_html_e('This prompt is used to generate new article ideas. Available variables: <code>{{existing_titles}}</code>, <code>{{limit}}</code>', 'asa-ai-content-agent'); ?></p>

            <h3><?php esc_html_e('Content Writing Prompt', 'asa-ai-content-agent'); ?></h3>
            <textarea name="aca_prompts[content_writing]" rows="10" cols="50" class="large-text"><?php echo esc_textarea($prompts['content_writing']); ?></textarea>
            <p class="description"><?php esc_html_e('This prompt is used to write the full article draft. Available variables: <code>{{style_guide}}</code>, <code>{{title}}</code>', 'asa-ai-content-agent'); ?></p>

            <?php submit_button(esc_html__('Save Prompts', 'asa-ai-content-agent')); ?>
            <h3><?php esc_html_e('Brand Voice Profiles', 'asa-ai-content-agent'); ?></h3>
            <table class="widefat">
                <thead><tr><th><?php esc_html_e('Profile Key', 'asa-ai-content-agent'); ?></th><th><?php esc_html_e('Style Guide', 'asa-ai-content-agent'); ?></th></tr></thead>
                <tbody>
                    <?php foreach ($profiles as $key => $guide) : ?>
                        <tr><td><?php echo esc_html($key); ?></td><td><textarea name="aca_brand_profiles[<?php echo esc_attr($key); ?>]" rows="4" class="large-text"><?php echo esc_textarea($guide); ?></textarea></td></tr>
                    <?php endforeach; ?>
                    <tr><td><input type="text" name="aca_brand_profiles_new_key" placeholder="<?php esc_attr_e('new-profile', 'asa-ai-content-agent'); ?>"></td><td><textarea name="aca_brand_profiles_new_value" rows="4" class="large-text"></textarea></td></tr>
                </tbody>
            </table>
            <p class="description">' . esc_html__('Define additional style guides for different content types.', 'asa-ai-content-agent') . '</p>
            <?php submit_button(esc_html__('Save Profiles', 'asa-ai-content-agent')); ?>
        </form>
        <?php
    }

    /**
     * Sanitize the prompts array.
     */
    public function sanitize_prompts($input) {
        $new_input = [];
        $default_prompts = ACA_Engine::get_default_prompts();

        foreach ($default_prompts as $key => $value) {
            if (isset($input[$key]) && !empty(trim($input[$key]))) {
                $new_input[$key] = sanitize_textarea_field($input[$key]);
            } else {
                $new_input[$key] = $value; // Restore default if empty
            }
        }
        return $new_input;
    }

    /**
     * Sanitize brand voice profiles.
     */
    public function sanitize_brand_profiles($input) {
        $clean = [];
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $clean[sanitize_key($key)] = sanitize_textarea_field($value);
            }
        }
        if ( ! empty( $_POST['aca_brand_profiles_new_key'] ) && ! empty( $_POST['aca_brand_profiles_new_value'] ) ) {
            check_admin_referer( 'aca_prompts_group-options' );
            $clean[ sanitize_key( wp_unslash( $_POST['aca_brand_profiles_new_key'] ) ) ] = sanitize_textarea_field( wp_unslash( $_POST['aca_brand_profiles_new_value'] ) );
        }
        return $clean;
    }

    /**
     * Render the License page.
     */
    public function render_license_page() {
        ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('aca_license_group');
            ?>
            <h3><?php esc_html_e('ACA Pro License', 'asa-ai-content-agent'); ?></h3>
            <p><?php esc_html_e('Enter your license key to unlock all features and receive updates.', 'asa-ai-content-agent'); ?></p>
            <?php $lic_key = get_option('aca_license_key'); ?>
            <?php $placeholder = ! empty( $lic_key ) ? esc_html__('***************** (already saved)', 'asa-ai-content-agent') : ''; ?>
            <input type="text" id="aca_license_key" name="aca_license_key" value="" placeholder="' . esc_attr( $placeholder ) . '" class="regular-text">
            <button type="button" class="button" id="aca-validate-license"><?php esc_html_e('Validate License', 'asa-ai-content-agent'); ?></button>
            <span id="aca-license-status" style="margin-left: 10px;"></span>
            <?php submit_button(esc_html__('Activate License', 'asa-ai-content-agent')); ?>
        </form>
        <?php
    }

    /**
     * Add plagiarism meta box on post edit screen.
     */
    public function add_plagiarism_metabox() {
        add_meta_box('aca_plagiarism', esc_html__('Plagiarism Check', 'asa-ai-content-agent'), [$this, 'render_plagiarism_metabox'], 'post', 'side');
    }

    /**
     * Render plagiarism meta box content.
     */
    public function render_plagiarism_metabox($post) {
        $raw = get_post_meta($post->ID, '_aca_plagiarism_raw', true);
        if (empty($raw)) {
            echo '<p>' . esc_html__('No plagiarism data available.', 'asa-ai-content-agent') . '</p>';
            return;
        }
        $data = json_decode($raw, true);
        if (isset($data['count'])) {
            /* translators: %d: number of matches */
            echo '<p>' . sprintf(esc_html__('Matches found: %d', 'asa-ai-content-agent'), intval($data['count'])) . '</p>';
            return;
        }
        echo '<pre style="overflow:auto; max-height:150px;">' . esc_html($raw) . '</pre>';
    }

    /**
     * Add update suggestion link on post list rows.
     */
    public function add_update_link($actions, $post) {
        if ($post->post_type === 'post' && $post->post_status === 'publish' && current_user_can('manage_aca_settings')) {
            $actions['aca_update'] = '<a href="#" class="aca-suggest-update" data-post-id="' . $post->ID . '">' . esc_html__( 'ACA ile Güncelleme Önerisi Al', 'asa-ai-content-agent' ) . '</a>';
        }
        return $actions;
    }
}

new ACA_Admin();