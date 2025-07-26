<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Menu
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
    }

    public function add_admin_menu() {
        add_menu_page(
            esc_html__( 'ACA - AI Content Agent', 'aca-ai-content-agent' ),
            esc_html__( 'ACA', 'aca-ai-content-agent' ),
            'view_aca_ai_content_agent_dashboard',
            'aca-ai-content-agent',
            [ $this, 'render_settings_page' ],
            'dashicons-robot'
        );
    }

    public function render_settings_page() {
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'dashboard';
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <h2 class="nav-tab-wrapper">
                <a href="?page=aca-ai-content-agent&tab=dashboard" class="nav-tab <?php echo esc_attr( $active_tab === 'dashboard' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Dashboard', 'aca-ai-content-agent' ); ?></a>
                <a href="?page=aca-ai-content-agent&tab=settings" class="nav-tab <?php echo esc_attr( $active_tab === 'settings' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Settings', 'aca-ai-content-agent' ); ?></a>
                <a href="?page=aca-ai-content-agent&tab=prompts" class="nav-tab <?php echo esc_attr( $active_tab === 'prompts' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Prompt Editor', 'aca-ai-content-agent' ); ?></a>
                <a href="?page=aca-ai-content-agent&tab=license" class="nav-tab <?php echo esc_attr( $active_tab === 'license' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'License', 'aca-ai-content-agent' ); ?></a>
                <a href="?page=aca-ai-content-agent-diagnostics" class="nav-tab <?php echo esc_attr( isset($_GET['page']) && $_GET['page'] === 'aca-ai-content-agent-diagnostics' ? 'nav-tab-active' : '' ); ?>"><?php esc_html_e( 'Diagnostics', 'aca-ai-content-agent' ); ?></a>
            </h2>

            <?php
            if ($active_tab === 'dashboard') {
                ACA_AI_Content_Agent_Dashboard::render();
            } elseif ($active_tab === 'settings') {
                ?>
                <form action="options.php" method="post">
                    <?php
                    settings_fields( 'aca_ai_content_agent_settings_group' );
                    do_settings_sections( 'aca-ai-content-agent' );
                    submit_button();
                    ?>
                </form>
                <?php
            } elseif ($active_tab === 'prompts') {
                $prompts = new ACA_Settings_Prompts();
                $prompts->render_prompts_page();
            } elseif ($active_tab === 'license') {
                $license = new ACA_Settings_License();
                $license->render_license_page();
            } elseif (isset($_GET['page']) && $_GET['page'] === 'aca-ai-content-agent-diagnostics') {
                echo '<div class="notice notice-info"><p>' . esc_html__('This diagnostics page helps you identify issues with database tables, API keys, user permissions, and scheduled tasks for the ACA Content Agent plugin.', 'aca-ai-content-agent') . '</p></div>';
            }
            ?>
        </div>
        <?php
    }
}
