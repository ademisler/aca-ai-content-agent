<?php

/**
 * The core plugin class
 */
class ACA_Core {

    /**
     * The loader that's responsible for maintaining and registering all hooks
     */
    protected $loader;

    /**
     * The unique identifier of this plugin
     */
    protected $plugin_name;

    /**
     * The current version of the plugin
     */
    protected $version;

    public function __construct() {
        $this->version = ACA_PLUGIN_VERSION;
        $this->plugin_name = 'aca-ai-content-agent';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin
     */
    private function load_dependencies() {
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-loader.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-i18n.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-gemini-service.php';
        require_once ACA_PLUGIN_PATH . 'includes/class-aca-database.php';
        require_once ACA_PLUGIN_PATH . 'admin/class-aca-admin.php';

        $this->loader = new ACA_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization
     */
    private function set_locale() {
        $plugin_i18n = new ACA_i18n();
        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the admin area functionality
     */
    private function define_admin_hooks() {
        $plugin_admin = new ACA_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        $this->loader->add_action('wp_ajax_aca_analyze_style', $plugin_admin, 'ajax_analyze_style');
        $this->loader->add_action('wp_ajax_aca_generate_ideas', $plugin_admin, 'ajax_generate_ideas');
        $this->loader->add_action('wp_ajax_aca_create_draft', $plugin_admin, 'ajax_create_draft');
        $this->loader->add_action('wp_ajax_aca_save_settings', $plugin_admin, 'ajax_save_settings');
        $this->loader->add_action('wp_ajax_aca_publish_draft', $plugin_admin, 'ajax_publish_draft');
        $this->loader->add_action('wp_ajax_aca_delete_idea', $plugin_admin, 'ajax_delete_idea');
        $this->loader->add_action('wp_ajax_aca_archive_idea', $plugin_admin, 'ajax_archive_idea');
        $this->loader->add_action('wp_ajax_aca_save_draft', $plugin_admin, 'ajax_save_draft');
        $this->loader->add_action('wp_ajax_aca_get_draft', $plugin_admin, 'ajax_get_draft');
        $this->loader->add_action('wp_ajax_aca_add_manual_idea', $plugin_admin, 'ajax_add_manual_idea');
        $this->loader->add_action('wp_ajax_aca_delete_draft', $plugin_admin, 'ajax_delete_draft');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     */
    private function define_public_hooks() {
        // Add any public hooks here if needed
    }

    /**
     * Run the loader to execute all of the hooks with WordPress
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin
     */
    public function get_version() {
        return $this->version;
    }
}