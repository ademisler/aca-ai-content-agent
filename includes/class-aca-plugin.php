<?php
/**
 * ACA - AI Content Agent
 *
 * Main Plugin Class
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Plugin {

    /**
     * The single instance of the class.
     */
    protected static $_instance = null;

    /**
     * Main ACA_Plugin Instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
        add_action('admin_init', array($this, 'handle_activation_redirect'));
    }

    public function handle_activation_redirect() {
        if ( get_transient( 'aca_ai_content_agent_activation_redirect' ) ) {
            delete_transient( 'aca_ai_content_agent_activation_redirect' );
            if ( ! is_multisite() ) {
                wp_safe_redirect( admin_url( 'admin.php?page=aca-ai-content-agent-onboarding' ) );
                exit;
            }
        }
    }

    

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        require_once plugin_dir_path( __FILE__ ) . '/core/class-aca-activator.php';
        require_once plugin_dir_path( __FILE__ ) . '/core/class-aca-deactivator.php';

        if (is_admin()) {
            require_once plugin_dir_path( __FILE__ ) . '/admin/class-aca-admin.php';
            require_once plugin_dir_path( __FILE__ ) . '/admin/class-aca-ajax-handler.php';
            require_once plugin_dir_path( __FILE__ ) . '/admin/class-aca-dashboard.php';
            require_once plugin_dir_path( __FILE__ ) . '/admin/class-aca-onboarding.php';
        }

        // Services
        require_once plugin_dir_path( __FILE__ ) . '/services/class-aca-idea-service.php';
        require_once plugin_dir_path( __FILE__ ) . '/services/class-aca-draft-service.php';
        require_once plugin_dir_path( __FILE__ ) . '/services/class-aca-style-guide-service.php';

        // API
        require_once plugin_dir_path( __FILE__ ) . '/api/class-aca-gemini-api.php';
        require_once plugin_dir_path( __FILE__ ) . '/api/class-aca-gumroad-api.php';

        // Integrations
        require_once plugin_dir_path( __FILE__ ) . '/integrations/class-aca-privacy.php';
        require_once plugin_dir_path( __FILE__ ) . '/integrations/class-aca-post-hooks.php';

        // Utils
        require_once plugin_dir_path( __FILE__ ) . '/utils/class-aca-encryption-util.php';
        require_once plugin_dir_path( __FILE__ ) . '/utils/class-aca-helper.php';
        require_once plugin_dir_path( __FILE__ ) . '/utils/class-aca-log-service.php';

        // Cron
        require_once plugin_dir_path( __FILE__ ) . '/class-aca-cron.php';
    }

    /**
     * Hook into actions and filters.
     */
    private function init_hooks() {

        add_action( 'plugins_loaded', [$this, 'init'] );
    }

    /**
     * Init the plugin.
     */
    public function init() {
        // Init classes
        if ( is_admin() ) {
            new ACA_Admin();
        }
        new ACA_AI_Content_Agent_Cron();
        new ACA_Privacy();

    }
}
