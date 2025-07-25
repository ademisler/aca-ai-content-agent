<?php
/**
 * The log service.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/utils
 */

/**
 * The log service.
 *
 * This class defines all code necessary for logging.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/utils
 * @author     Your Name <email@example.com>
 */
class ACA_Log_Service {

    /**
     * Add a log entry.
     *
     * @param string $message The message to log.
     * @param string $level   The level of the log entry (e.g., 'info', 'error').
     */
    public static function add( $message, $level = 'info' ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'aca_ai_content_agent_logs';

        $wpdb->insert(
            $table_name,
            [
                'message'   => $message,
                'level'     => $level,
                'timestamp' => current_time( 'mysql' ),
            ]
        );
    }
}