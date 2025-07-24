<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$tables = [
    $wpdb->prefix . 'aca_ai_content_agent_ideas',
    $wpdb->prefix . 'aca_ai_content_agent_logs',
    $wpdb->prefix . 'aca_ai_content_agent_clusters',
    $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
];

foreach ( $tables as $table ) {
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
}

if ( function_exists( 'as_unschedule_all_actions' ) ) {
    as_unschedule_all_actions( 'aca_ai_content_agent_run_main_automation' );
    as_unschedule_all_actions( 'aca_ai_content_agent_reset_api_usage_counter' );
    as_unschedule_all_actions( 'aca_ai_content_agent_generate_style_guide' );
    as_unschedule_all_actions( 'aca_ai_content_agent_verify_license' );
    as_unschedule_all_actions( 'aca_ai_content_agent_clean_logs' );
}

// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'aca_ai_content_agent_%' ) );

$roles = [ 'administrator', 'editor', 'author' ];
foreach ( $roles as $role_name ) {
    $role = get_role( $role_name );
    if ( $role ) {
        $role->remove_cap( 'manage_aca_ai_content_agent_settings' );
        $role->remove_cap( 'view_aca_ai_content_agent_dashboard' );
    }
}