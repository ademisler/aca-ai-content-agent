<?php
/**
 * ACA - AI Content Agent
 *
 * Admin Menu
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin_Menu {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_head', [ $this, 'add_menu_icon_styles' ] );
    }

    public function add_admin_menu() {
        // Main menu page - FIX: Use edit_posts for consistency with content management
        add_menu_page(
            esc_html__( 'ACA - AI Content Agent', 'aca-ai-content-agent' ),
            esc_html__( 'ACA Agent', 'aca-ai-content-agent' ),
            'edit_posts', // Changed from manage_options to edit_posts for consistent access
            'aca-ai-content-agent',
            [ $this, 'render_main_page' ],
            'dashicons-robot',
            30
        );

        // Submenu pages
        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Dashboard', 'aca-ai-content-agent' ),
            esc_html__( 'Dashboard', 'aca-ai-content-agent' ),
            'edit_posts', // Changed from manage_options to edit_posts for consistency
            'aca-ai-content-agent',
            [ $this, 'render_main_page' ]
        );

        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Content Ideas', 'aca-ai-content-agent' ),
            esc_html__( 'Content Ideas', 'aca-ai-content-agent' ),
            'edit_posts', // Keep edit_posts for content-related operations
            'aca-ai-content-agent-ideas',
            [ $this, 'render_ideas_page' ]
        );

        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Settings', 'aca-ai-content-agent' ),
            esc_html__( 'Settings', 'aca-ai-content-agent' ),
            'manage_options', // Keep manage_options for settings
            'aca-ai-content-agent-settings',
            [ $this, 'render_settings_page' ]
        );

        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Prompt Editor', 'aca-ai-content-agent' ),
            esc_html__( 'Prompt Editor', 'aca-ai-content-agent' ),
            'edit_posts', // Keep edit_posts for content operations
            'aca-ai-content-agent-prompts',
            [ $this, 'render_prompts_page' ]
        );

        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'License', 'aca-ai-content-agent' ),
            esc_html__( 'License', 'aca-ai-content-agent' ),
            'manage_options', // Keep manage_options for license
            'aca-ai-content-agent-license',
            [ $this, 'render_license_page' ]
        );

        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Diagnostics', 'aca-ai-content-agent' ),
            esc_html__( 'Diagnostics', 'aca-ai-content-agent' ),
            'manage_options', // Keep manage_options for diagnostics
            'aca-ai-content-agent-diagnostics',
            [ $this, 'render_diagnostics_page' ]
        );

        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Activity Logs', 'aca-ai-content-agent' ),
            esc_html__( 'Activity Logs', 'aca-ai-content-agent' ),
            'edit_posts', // Keep edit_posts for viewing logs
            'aca-ai-content-agent-logs',
            [ $this, 'render_logs_page' ]
        );

        // Add onboarding page as submenu
        add_submenu_page(
            'aca-ai-content-agent',
            esc_html__( 'Welcome to ACA', 'aca-ai-content-agent' ),
            esc_html__( 'Welcome', 'aca-ai-content-agent' ),
            'edit_posts', // Changed from manage_options to edit_posts for consistency
            'aca-ai-content-agent-onboarding',
            [ $this, 'render_onboarding_page' ]
        );
    }

    public function add_menu_icon_styles() {
        ?>
        <style>
        #adminmenu .dashicons-robot:before {
            color: #667eea !important;
        }
        #adminmenu .wp-menu-image.dashicons-robot {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        </style>
        <?php
    }

    public function render_main_page() {
        // Check if user has permission - use 'edit_posts' to match menu capability
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        // Use the new dashboard system
        ACA_Dashboard::render();
    }

    public function render_ideas_page() {
        // Check if user has permission - use 'edit_posts' to match menu capability
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        ?>
        <div class="wrap aca-admin-page">
            <div class="aca-page-header">
                <h1>
                    <span class="dashicons dashicons-lightbulb" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e( 'Content Ideas', 'aca-ai-content-agent' ); ?>
                </h1>
                <div class="aca-page-actions">
                    <button class="aca-action-button" id="aca-ai-content-agent-generate-ideas">
                        <span class="dashicons dashicons-plus"></span>
                        <?php esc_html_e( 'Generate Ideas', 'aca-ai-content-agent' ); ?>
                    </button>
                </div>
            </div>
            
            <div class="aca-ideas-page">
                <?php $this->render_ideas_list(); ?>
            </div>
        </div>
        <?php
    }

    public function render_settings_page() {
        // Check if user has permission - use 'manage_options' to match menu capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        ?>
        <div class="wrap aca-admin-page aca-settings-page">
            <div class="aca-page-header">
                <h1>
                    <span class="dashicons dashicons-admin-settings" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e( 'Settings', 'aca-ai-content-agent' ); ?>
                </h1>
            </div>
            
            <div class="aca-settings-content">
                <?php
                // Render settings form using static approach to avoid multiple instances
                if (class_exists('ACA_Settings_Api')) {
                    echo '<form method="post" action="options.php">';
                    settings_fields( 'aca_ai_content_agent_settings_group' );
                    do_settings_sections( 'aca-ai-content-agent' );
                    submit_button();
                    echo '</form>';
                } else {
                    echo '<p>' . esc_html__('Settings API not available.', 'aca-ai-content-agent') . '</p>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function render_prompts_page() {
        // Check if user has permission - use 'edit_posts' to match menu capability
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        ?>
        <div class="wrap aca-admin-page">
            <div class="aca-page-header">
                <h1>
                    <span class="dashicons dashicons-edit" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e( 'Prompt Editor', 'aca-ai-content-agent' ); ?>
                </h1>
            </div>
            
            <div class="aca-prompts-content">
                <p><?php esc_html_e( 'Prompt editor functionality will be available in a future update.', 'aca-ai-content-agent' ); ?></p>
            </div>
        </div>
        <?php
    }

    public function render_license_page() {
        // Check if user has permission - use 'manage_options' to match menu capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        ?>
        <div class="wrap aca-admin-page">
            <div class="aca-page-header">
                <h1>
                    <span class="dashicons dashicons-admin-network" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e( 'License', 'aca-ai-content-agent' ); ?>
                </h1>
            </div>
            
            <div class="aca-license-content">
                <p><?php esc_html_e( 'License management functionality will be available in a future update.', 'aca-ai-content-agent' ); ?></p>
            </div>
        </div>
        <?php
    }

    public function render_diagnostics_page() {
        // Check if user has permission - use 'manage_options' to match menu capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        ?>
        <div class="wrap aca-admin-page">
            <div class="aca-page-header">
                <h1>
                    <span class="dashicons dashicons-admin-tools" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e( 'Diagnostics', 'aca-ai-content-agent' ); ?>
                </h1>
            </div>
            
            <div class="aca-diagnostics-content">
                <?php $this->render_diagnostics_info(); ?>
            </div>
        </div>
        <?php
    }

    public function render_logs_page() {
        // Check if user has permission - use 'edit_posts' to match menu capability
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        ?>
        <div class="wrap aca-admin-page">
            <div class="aca-page-header">
                <h1>
                    <span class="dashicons dashicons-list-view" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e( 'Activity Logs', 'aca-ai-content-agent' ); ?>
                </h1>
            </div>
            
            <div class="aca-logs-content">
                <?php $this->render_logs_list(); ?>
            </div>
        </div>
        <?php
    }

    private function render_ideas_tab() {
        echo '<div class="aca-ideas-tab">';
        echo '<h2>' . esc_html__( 'Content Ideas', 'aca-ai-content-agent' ) . '</h2>';
        $this->render_ideas_list();
        echo '</div>';
    }

    private function render_clusters_tab() {
        echo '<div class="aca-clusters-tab">';
        echo '<h2>' . esc_html__( 'Content Clusters', 'aca-ai-content-agent' ) . '</h2>';
        echo '<p>' . esc_html__( 'Manage your content clusters and topic groups.', 'aca-ai-content-agent' ) . '</p>';
        // Cluster management content will be implemented here
        echo '</div>';
    }

    private function render_analytics_tab() {
        echo '<div class="aca-analytics-tab">';
        echo '<h2>' . esc_html__( 'Analytics', 'aca-ai-content-agent' ) . '</h2>';
        echo '<p>' . esc_html__( 'View detailed analytics and performance metrics.', 'aca-ai-content-agent' ) . '</p>';
        // Analytics content will be implemented here
        echo '</div>';
    }

    private function render_ideas_list() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';
        
        // Get ideas with pagination
        $per_page = 20;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $ideas = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$ideas_table} ORDER BY generated_date DESC LIMIT %d OFFSET %d", $per_page, $offset ) );
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $total_ideas = $wpdb->get_var( "SELECT COUNT(*) FROM {$ideas_table}" );
        $total_pages = ceil($total_ideas / $per_page);

        if (!empty($ideas)) {
            echo '<div class="aca-ideas-table-container">';
            echo '<table class="wp-list-table widefat fixed striped aca-ideas-table">';
            echo '<thead><tr>';
            echo '<th>' . esc_html__( 'Title', 'aca-ai-content-agent' ) . '</th>';
            echo '<th>' . esc_html__( 'Status', 'aca-ai-content-agent' ) . '</th>';
            echo '<th>' . esc_html__( 'Keywords', 'aca-ai-content-agent' ) . '</th>';
            echo '<th>' . esc_html__( 'Generated', 'aca-ai-content-agent' ) . '</th>';
            echo '<th>' . esc_html__( 'Actions', 'aca-ai-content-agent' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            
            foreach ($ideas as $idea) {
                $generated_date = new DateTime($idea->generated_date);
                $time_ago = human_time_diff($generated_date->getTimestamp(), current_time('timestamp'));
                
                echo '<tr>';
                echo '<td><strong>' . esc_html($idea->title) . '</strong></td>';
                echo '<td><span class="aca-status-badge aca-status-' . esc_attr($idea->status) . '">' . esc_html(ucfirst($idea->status)) . '</span></td>';
                echo '<td>' . esc_html($idea->keywords ?: '-') . '</td>';
                echo '<td>' . esc_html($time_ago) . ' ago</td>';
                echo '<td>';
                echo '<div class="aca-row-actions">';
                if ($idea->status === 'pending') {
                    echo '<button class="button-primary aca-ai-content-agent-write-draft" data-id="' . esc_attr($idea->id) . '">' . esc_html__( 'Write Draft', 'aca-ai-content-agent' ) . '</button> ';
                    echo '<button class="button-secondary aca-ai-content-agent-reject-idea" data-id="' . esc_attr($idea->id) . '">' . esc_html__( 'Reject', 'aca-ai-content-agent' ) . '</button>';
                } else {
                    echo '<button class="button-secondary" disabled>' . esc_html__( 'Processed', 'aca-ai-content-agent' ) . '</button>';
                }
                echo '</div>';
                echo '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            echo '</div>';
            
            // Pagination
            if ($total_pages > 1) {
                echo '<div class="aca-pagination">';
                echo paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo; Previous'),
                    'next_text' => __('Next &raquo;'),
                    'total' => $total_pages,
                    'current' => $current_page
                ]);
                echo '</div>';
            }
        } else {
            echo '<div class="aca-empty-state">';
            echo '<div class="aca-empty-state-icon">💡</div>';
            echo '<h3>' . esc_html__( 'No Ideas Found', 'aca-ai-content-agent' ) . '</h3>';
            echo '<p>' . esc_html__( 'Generate some content ideas to get started!', 'aca-ai-content-agent' ) . '</p>';
            echo '</div>';
        }
    }

    private function render_logs_list() {
        global $wpdb;
        $logs_table = $wpdb->prefix . 'aca_ai_content_agent_logs';
        
        // Get logs with pagination
        $per_page = 50;
        $current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
        $offset = ($current_page - 1) * $per_page;
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $logs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$logs_table} ORDER BY timestamp DESC LIMIT %d OFFSET %d", $per_page, $offset ) );
        
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $total_logs = $wpdb->get_var( "SELECT COUNT(*) FROM {$logs_table}" );
        $total_pages = ceil($total_logs / $per_page);

        if (!empty($logs)) {
            echo '<div class="aca-logs-table-container">';
            echo '<table class="wp-list-table widefat fixed striped aca-logs-table">';
            echo '<thead><tr>';
            echo '<th>' . esc_html__( 'Timestamp', 'aca-ai-content-agent' ) . '</th>';
            echo '<th>' . esc_html__( 'Level', 'aca-ai-content-agent' ) . '</th>';
            echo '<th>' . esc_html__( 'Message', 'aca-ai-content-agent' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            
            foreach ($logs as $log) {
                $timestamp = new DateTime($log->timestamp);
                $formatted_time = $timestamp->format('Y-m-d H:i:s');
                
                echo '<tr class="log-' . esc_attr($log->level) . '">';
                echo '<td>' . esc_html($formatted_time) . '</td>';
                echo '<td><span class="aca-log-level aca-log-level-' . esc_attr($log->level) . '">' . esc_html(ucfirst($log->level)) . '</span></td>';
                echo '<td>' . esc_html($log->message) . '</td>';
                echo '</tr>';
            }
            
            echo '</tbody></table>';
            echo '</div>';
            
            // Pagination
            if ($total_pages > 1) {
                echo '<div class="aca-pagination">';
                echo paginate_links([
                    'base' => add_query_arg('paged', '%#%'),
                    'format' => '',
                    'prev_text' => __('&laquo; Previous'),
                    'next_text' => __('Next &raquo;'),
                    'total' => $total_pages,
                    'current' => $current_page
                ]);
                echo '</div>';
            }
        } else {
            echo '<div class="aca-empty-state">';
            echo '<div class="aca-empty-state-icon">📊</div>';
            echo '<h3>' . esc_html__( 'No Logs Found', 'aca-ai-content-agent' ) . '</h3>';
            echo '<p>' . esc_html__( 'No activity logs available yet.', 'aca-ai-content-agent' ) . '</p>';
            echo '</div>';
        }
    }

    private function render_diagnostics_info() {
        global $wpdb;
        
        $diagnostics = [
            'WordPress Version' => get_bloginfo('version'),
            'PHP Version' => PHP_VERSION,
            'Plugin Version' => ACA_AI_CONTENT_AGENT_VERSION,
            'Database Tables' => $this->check_database_tables(),
            'API Connection' => $this->check_api_connection(),
            'User Permissions' => $this->check_user_permissions(),
            'Scheduled Tasks' => $this->check_scheduled_tasks(),
        ];

        foreach ($diagnostics as $label => $value) {
            echo '<div class="aca-diagnostic-item">';
            echo '<h4>' . esc_html($label) . '</h4>';
            echo '<div class="aca-diagnostic-value">' . wp_kses_post($value) . '</div>';
            echo '</div>';
        }
    }

    private function check_database_tables() {
        global $wpdb;
        
        $tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
        ];
        
        $missing_tables = [];
        foreach ($tables as $table) {
            // SECURITY FIX: Use prepared statement to prevent SQL injection
            $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
            if (!$exists) {
                $missing_tables[] = $table;
            }
        }
        
        if (empty($missing_tables)) {
            return '<span class="aca-status-success">✅ All tables exist</span>';
        } else {
            return '<span class="aca-status-error">❌ Missing: ' . esc_html(implode(', ', $missing_tables)) . '</span>';
        }
    }

    private function check_api_connection() {
        $api_key = get_option('aca_ai_content_agent_gemini_api_key');
        if (empty($api_key)) {
            return '<span class="aca-status-error">❌ API key not configured</span>';
        }
        return '<span class="aca-status-success">✅ API key configured</span>';
    }

    private function check_user_permissions() {
        if (current_user_can('edit_posts')) {
            return '<span class="aca-status-success">✅ Permissions OK</span>';
        } else {
            return '<span class="aca-status-error">❌ Insufficient permissions</span>';
        }
    }

    private function check_scheduled_tasks() {
        $cron_jobs = [
            'aca_ai_content_agent_generate_ideas_cron',
            'aca_ai_content_agent_cleanup_logs_cron',
        ];
        
        $active_jobs = [];
        foreach ($cron_jobs as $job) {
            if (wp_next_scheduled($job)) {
                $active_jobs[] = $job;
            }
        }
        
        if (count($active_jobs) === count($cron_jobs)) {
            return '<span class="aca-status-success">✅ All tasks scheduled</span>';
        } else {
            return '<span class="aca-status-warning">⚠️ Some tasks not scheduled</span>';
        }
    }

    public function render_onboarding_page() {
        // Check if user has permission - use 'edit_posts' to match menu capability
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'aca-ai-content-agent'));
        }
        
        // Render onboarding page directly without creating new instance
        if (class_exists('ACA_Onboarding')) {
            // Create a temporary instance just for rendering
            $onboarding = new ACA_Onboarding();
            $onboarding->render_onboarding_page();
        } else {
            wp_die(__('Onboarding class not found.', 'aca-ai-content-agent'));
        }
    }
}
