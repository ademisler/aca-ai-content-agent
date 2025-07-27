<?php

/**
 * The admin-specific functionality of the plugin
 */
class ACA_Admin {

    private $plugin_name;
    private $version;

    public function __construct($plugin_name, $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area
     */
    public function enqueue_styles($hook) {
        if (strpos($hook, 'aca-ai-content-agent') === false) {
            return;
        }

        wp_enqueue_style($this->plugin_name, ACA_PLUGIN_URL . 'assets/css/aca-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('aca-tailwind', 'https://cdn.tailwindcss.com', array(), $this->version);
    }

    /**
     * Register the JavaScript for the admin area
     */
    public function enqueue_scripts($hook) {
        if (strpos($hook, 'aca-ai-content-agent') === false) {
            return;
        }

        wp_enqueue_script($this->plugin_name, ACA_PLUGIN_URL . 'assets/js/aca-admin.js', array('jquery'), $this->version, false);
        wp_localize_script($this->plugin_name, 'aca_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aca_nonce')
        ));
    }

    /**
     * Add the plugin admin menu
     */
    public function add_plugin_admin_menu() {
        add_menu_page(
            'ACA - AI Content Agent',
            'AI Content Agent',
            'manage_options',
            'aca-ai-content-agent',
            array($this, 'display_plugin_admin_page'),
            'dashicons-edit-page',
            30
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'aca-ai-content-agent',
            array($this, 'display_plugin_admin_page')
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Style Guide',
            'Style Guide',
            'manage_options',
            'aca-style-guide',
            array($this, 'display_style_guide_page')
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Ideas',
            'Ideas',
            'manage_options',
            'aca-ideas',
            array($this, 'display_ideas_page')
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Drafts',
            'Drafts',
            'manage_options',
            'aca-drafts',
            array($this, 'display_drafts_page')
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Calendar',
            'Calendar',
            'manage_options',
            'aca-calendar',
            array($this, 'display_calendar_page')
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Published',
            'Published',
            'manage_options',
            'aca-published',
            array($this, 'display_published_page')
        );

        add_submenu_page(
            'aca-ai-content-agent',
            'Settings',
            'Settings',
            'manage_options',
            'aca-settings',
            array($this, 'display_settings_page')
        );
    }

    /**
     * Display the main admin page
     */
    public function display_plugin_admin_page() {
        $stats = ACA_Database::get_stats();
        $activity_logs = ACA_Database::get_activity_logs(5);
        $style_guide = get_option('aca_style_guide', array());
        
        include ACA_PLUGIN_PATH . 'admin/partials/aca-admin-display.php';
    }

    /**
     * Display the style guide page
     */
    public function display_style_guide_page() {
        $style_guide = get_option('aca_style_guide', array());
        include ACA_PLUGIN_PATH . 'admin/partials/aca-style-guide-display.php';
    }

    /**
     * Display the calendar page
     */
    public function display_calendar_page() {
        $drafts = ACA_Database::get_drafts('draft');
        $published = ACA_Database::get_drafts('published');
        include ACA_PLUGIN_PATH . 'admin/partials/aca-calendar-display.php';
    }

    /**
     * Display the published page
     */
    public function display_published_page() {
        $published = ACA_Database::get_drafts('published');
        include ACA_PLUGIN_PATH . 'admin/partials/aca-published-display.php';
    }

    /**
     * Display the ideas page
     */
    public function display_ideas_page() {
        $ideas = ACA_Database::get_ideas();
        include ACA_PLUGIN_PATH . 'admin/partials/aca-ideas-display.php';
    }

    /**
     * Display the drafts page
     */
    public function display_drafts_page() {
        $drafts = ACA_Database::get_drafts('draft');
        $published = ACA_Database::get_drafts('published');
        include ACA_PLUGIN_PATH . 'admin/partials/aca-drafts-display.php';
    }

    /**
     * Display the settings page
     */
    public function display_settings_page() {
        $settings = get_option('aca_settings', array());
        $style_guide = get_option('aca_style_guide', array());
        include ACA_PLUGIN_PATH . 'admin/partials/aca-settings-display.php';
    }

    /**
     * AJAX handler for analyzing style
     */
    public function ajax_analyze_style() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $gemini_service = new ACA_Gemini_Service();
        $result = $gemini_service->analyze_style();

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        // Parse and save the style guide
        $style_guide_data = json_decode($result, true);
        if ($style_guide_data) {
            $style_guide_data['last_analyzed'] = current_time('mysql');
            update_option('aca_style_guide', $style_guide_data);
            
            ACA_Database::add_activity_log('style_updated', 'Style guide updated based on recent posts', 'BookOpen');
            
            wp_send_json_success($style_guide_data);
        } else {
            wp_send_json_error('Failed to parse style guide data');
        }
    }

    /**
     * AJAX handler for generating ideas
     */
    public function ajax_generate_ideas() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $count = intval($_POST['count'] ?? 5);
        $style_guide = get_option('aca_style_guide', array());
        
        if (empty($style_guide) || empty($style_guide['tone'])) {
            wp_send_json_error('Please analyze your style guide first');
        }

        // Get existing titles to avoid duplicates
        $existing_ideas = ACA_Database::get_ideas();
        $existing_titles = array_map(function($idea) {
            return $idea->title;
        }, $existing_ideas);

        $gemini_service = new ACA_Gemini_Service();
        $result = $gemini_service->generate_ideas($style_guide, $existing_titles, $count);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        $ideas = json_decode($result, true);
        if ($ideas && is_array($ideas)) {
            $added_count = 0;
            foreach ($ideas as $title) {
                if (ACA_Database::add_idea($title, 'ai')) {
                    $added_count++;
                }
            }
            
            ACA_Database::add_activity_log('ideas_generated', "Generated {$added_count} new content ideas", 'Lightbulb');
            
            wp_send_json_success(array(
                'message' => "Generated {$added_count} new ideas",
                'ideas' => $ideas
            ));
        } else {
            wp_send_json_error('Failed to parse generated ideas');
        }
    }

    /**
     * AJAX handler for creating a draft
     */
    public function ajax_create_draft() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $title = sanitize_text_field($_POST['title'] ?? '');
        if (empty($title)) {
            wp_send_json_error('Title is required');
        }

        $style_guide = get_option('aca_style_guide', array());
        if (empty($style_guide) || empty($style_guide['tone'])) {
            wp_send_json_error('Please analyze your style guide first');
        }

        // Get existing published posts for internal linking
        $published_posts = get_posts(array(
            'numberposts' => 10,
            'post_status' => 'publish',
            'post_type' => 'post'
        ));

        $existing_posts = array();
        foreach ($published_posts as $post) {
            $existing_posts[] = array(
                'title' => $post->post_title,
                'url' => get_permalink($post->ID),
                'content' => wp_strip_all_tags($post->post_content)
            );
        }

        $gemini_service = new ACA_Gemini_Service();
        $result = $gemini_service->create_draft($title, $style_guide, $existing_posts);

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        $draft_data = json_decode($result, true);
        if ($draft_data) {
            $draft_id = ACA_Database::add_draft(array(
                'title' => $title,
                'content' => $draft_data['content'],
                'meta_title' => $draft_data['metaTitle'],
                'meta_description' => $draft_data['metaDescription'],
                'focus_keywords' => $draft_data['focusKeywords'],
                'featured_image_url' => ''
            ));

            if ($draft_id) {
                ACA_Database::add_activity_log('draft_created', "Created draft: {$title}", 'FileText');
                wp_send_json_success(array(
                    'message' => 'Draft created successfully',
                    'draft_id' => $draft_id
                ));
            } else {
                wp_send_json_error('Failed to save draft to database');
            }
        } else {
            wp_send_json_error('Failed to parse generated draft');
        }
    }

    /**
     * AJAX handler for saving settings
     */
    public function ajax_save_settings() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $settings = array(
            'mode' => sanitize_text_field($_POST['mode'] ?? 'manual'),
            'auto_publish' => isset($_POST['auto_publish']) ? (bool)$_POST['auto_publish'] : false,
            'gemini_api_key' => sanitize_text_field($_POST['gemini_api_key'] ?? ''),
            'image_source_provider' => sanitize_text_field($_POST['image_source_provider'] ?? 'ai'),
            'ai_image_style' => sanitize_text_field($_POST['ai_image_style'] ?? 'photorealistic'),
            'pexels_api_key' => sanitize_text_field($_POST['pexels_api_key'] ?? ''),
            'unsplash_api_key' => sanitize_text_field($_POST['unsplash_api_key'] ?? ''),
            'pixabay_api_key' => sanitize_text_field($_POST['pixabay_api_key'] ?? ''),
            'seo_plugin' => sanitize_text_field($_POST['seo_plugin'] ?? 'none')
        );

        update_option('aca_settings', $settings);
        
        ACA_Database::add_activity_log('settings_saved', 'Plugin settings updated', 'Settings');
        
        wp_send_json_success('Settings saved successfully');
    }

    /**
     * AJAX handler for publishing a draft
     */
    public function ajax_publish_draft() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $draft_id = intval($_POST['draft_id'] ?? 0);
        if (!$draft_id) {
            wp_send_json_error('Invalid draft ID');
        }

        $draft = ACA_Database::get_draft($draft_id);
        if (!$draft) {
            wp_send_json_error('Draft not found');
        }

        // Create WordPress post
        $post_data = array(
            'post_title' => $draft->title,
            'post_content' => $draft->content,
            'post_status' => 'publish',
            'post_type' => 'post',
            'meta_input' => array(
                '_yoast_wpseo_title' => $draft->meta_title,
                '_yoast_wpseo_metadesc' => $draft->meta_description,
                '_yoast_wpseo_focuskw' => $draft->focus_keywords
            )
        );

        $post_id = wp_insert_post($post_data);

        if (is_wp_error($post_id)) {
            wp_send_json_error('Failed to create WordPress post');
        }

        // Update draft status
        ACA_Database::update_draft($draft_id, array(
            'status' => 'published',
            'post_id' => $post_id
        ));

        ACA_Database::add_activity_log('post_published', "Published post: {$draft->title}", 'Send');

        wp_send_json_success(array(
            'message' => 'Post published successfully',
            'post_id' => $post_id,
            'post_url' => get_permalink($post_id)
        ));
    }

    /**
     * AJAX handler for deleting an idea
     */
    public function ajax_delete_idea() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $idea_id = intval($_POST['idea_id'] ?? 0);
        if (!$idea_id) {
            wp_send_json_error('Invalid idea ID');
        }

        if (ACA_Database::delete_idea($idea_id)) {
            wp_send_json_success('Idea deleted successfully');
        } else {
            wp_send_json_error('Failed to delete idea');
        }
    }

    /**
     * AJAX handler for archiving an idea
     */
    public function ajax_archive_idea() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $idea_id = intval($_POST['idea_id'] ?? 0);
        if (!$idea_id) {
            wp_send_json_error('Invalid idea ID');
        }

        if (ACA_Database::update_idea_status($idea_id, 'archived')) {
            wp_send_json_success('Idea archived successfully');
        } else {
            wp_send_json_error('Failed to archive idea');
        }
    }

    /**
     * AJAX handler for saving a draft
     */
    public function ajax_save_draft() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $draft_id = intval($_POST['draft_id'] ?? 0);
        if (!$draft_id) {
            wp_send_json_error('Invalid draft ID');
        }

        $update_data = array();
        if (isset($_POST['title'])) {
            $update_data['title'] = sanitize_text_field($_POST['title']);
        }
        if (isset($_POST['content'])) {
            $update_data['content'] = wp_kses_post($_POST['content']);
        }
        if (isset($_POST['meta_title'])) {
            $update_data['meta_title'] = sanitize_text_field($_POST['meta_title']);
        }
        if (isset($_POST['meta_description'])) {
            $update_data['meta_description'] = sanitize_textarea_field($_POST['meta_description']);
        }

        if (ACA_Database::update_draft($draft_id, $update_data)) {
            ACA_Database::add_activity_log('draft_updated', 'Draft updated', 'Pencil');
            wp_send_json_success('Draft saved successfully');
        } else {
            wp_send_json_error('Failed to save draft');
        }
    }

    /**
     * AJAX handler for getting a single draft
     */
    public function ajax_get_draft() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $draft_id = intval($_POST['draft_id'] ?? 0);
        if (!$draft_id) {
            wp_send_json_error('Invalid draft ID');
        }

        $draft = ACA_Database::get_draft($draft_id);
        if ($draft) {
            wp_send_json_success($draft);
        } else {
            wp_send_json_error('Draft not found');
        }
    }

    /**
     * AJAX handler for adding a manual idea
     */
    public function ajax_add_manual_idea() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $title = sanitize_text_field($_POST['title'] ?? '');
        if (empty($title)) {
            wp_send_json_error('Title is required');
        }

        if (ACA_Database::add_idea($title, 'manual')) {
            ACA_Database::add_activity_log('idea_added', "Added manual idea: {$title}", 'PlusCircle');
            wp_send_json_success('Idea added successfully');
        } else {
            wp_send_json_error('Failed to add idea');
        }
    }

    /**
     * AJAX handler for deleting a draft
     */
    public function ajax_delete_draft() {
        check_ajax_referer('aca_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $draft_id = intval($_POST['draft_id'] ?? 0);
        if (!$draft_id) {
            wp_send_json_error('Invalid draft ID');
        }

        if (ACA_Database::delete_draft($draft_id)) {
            wp_send_json_success('Draft deleted successfully');
        } else {
            wp_send_json_error('Failed to delete draft');
        }
    }
}