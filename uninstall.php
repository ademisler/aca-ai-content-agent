<?php
/**
 * ACA - AI Content Agent Uninstall
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/core/class-aca-uninstaller.php';
ACA_Uninstaller::uninstall();