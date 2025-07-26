<?php
/**
 * ACA - AI Content Agent
 *
 * Main Admin Class
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin {

    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    private function includes() {
        require_once plugin_dir_path( __FILE__ ) . 'class-aca-admin-menu.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-aca-admin-notices.php';
        require_once plugin_dir_path( __FILE__ ) . 'class-aca-admin-assets.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-api.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-automation.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-analysis.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-enrichment.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-management.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-prompts.php';
        require_once plugin_dir_path( __FILE__ ) . 'settings/class-aca-settings-license.php';
    }

    private function init_hooks() {
        new ACA_Admin_Menu();
        new ACA_Admin_Notices();
        new ACA_Admin_Assets();
        new ACA_Settings_Api();
        new ACA_Settings_Automation();
        new ACA_Settings_Analysis();
        new ACA_Settings_Enrichment();
        new ACA_Settings_Management();
        new ACA_Settings_Prompts();
        new ACA_Settings_License();
    }
}
