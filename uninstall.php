<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

$tables = [
    $wpdb->prefix . 'aca_ideas',
    $wpdb->prefix . 'aca_logs',
    $wpdb->prefix . 'aca_clusters',
    $wpdb->prefix . 'aca_cluster_items',
];

foreach ( $tables as $table ) {
    $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
}

if ( function_exists( 'as_unschedule_all_actions' ) ) {
    as_unschedule_all_actions( 'aca_run_main_automation' );
    as_unschedule_all_actions( 'aca_reset_api_usage_counter' );
    as_unschedule_all_actions( 'aca_generate_style_guide' );
    as_unschedule_all_actions( 'aca_verify_license' );
}

$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'aca_%' ) );

$roles = [ 'administrator', 'editor', 'author' ];
foreach ( $roles as $role_name ) {
    $role = get_role( $role_name );
    if ( $role ) {
        $role->remove_cap( 'manage_aca_settings' );
        $role->remove_cap( 'view_aca_dashboard' );
    }
}
