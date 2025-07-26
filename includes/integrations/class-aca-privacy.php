<?php
/**
 * Privacy integration for ACA AI Content Agent.
 *
 * Handles GDPR compliance by providing data export and erasure capabilities
 * for user privacy requests.
 *
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Privacy compliance class for ACA AI Content Agent.
 *
 * Implements WordPress privacy API for data export and erasure,
 * ensuring GDPR compliance for user data stored by the plugin.
 *
 * @since 1.2.0
 */
class ACA_AI_Content_Agent_Privacy {

    /**
     * Initialize privacy functionality.
     *
     * Registers data exporters and erasers with WordPress privacy API.
     *
     * @since 1.2.0
     * @return void
     */
    public static function init() {
        add_filter( 'wp_privacy_personal_data_exporters', [ 'ACA_AI_Content_Agent_Privacy', 'register_exporter' ] );
        add_filter( 'wp_privacy_personal_data_erasers', [ 'ACA_AI_Content_Agent_Privacy', 'register_eraser' ] );
    }

    /**
     * Register the data exporter with WordPress privacy API.
     *
     * @since 1.2.0
     * @param array $exporters Array of registered exporters.
     * @return array Modified array of exporters.
     */
    public static function register_exporter( $exporters ) {
        $exporters['aca-ai-content-agent-data'] = [
            'exporter_friendly_name' => __( 'ACA Settings', 'aca-ai-content-agent' ),
            'callback'               => [ 'ACA_AI_Content_Agent_Privacy', 'export' ],
        ];
        return $exporters;
    }

    /**
     * Export user data for privacy requests.
     *
     * @since 1.2.0
     * @param string $email_address The email address of the user requesting data export.
     * @param int $page The page number for pagination.
     * @return array Export data array with 'data' and 'done' keys.
     */
    public static function export( $email_address, $page = 1 ) {
        $data  = [];
        $user  = get_user_by( 'email', $email_address );
        if ( $user && user_can( $user, 'manage_aca_ai_content_agent_settings' ) ) {
            $options  = get_option( 'aca_ai_content_agent_options', [] );
            $license  = get_option( 'aca_ai_content_agent_license_key', '' );
            $license  = $license ? ACA_Encryption_Util::safe_decrypt( $license ) : '';
            $api_key  = get_option( 'aca_ai_content_agent_gemini_api_key', '' );
            $data[] = [
                'name'  => __( 'ACA Options', 'aca-ai-content-agent' ),
                'value' => wp_json_encode( $options ),
            ];
            if ( $license ) {
                $data[] = [ 'name' => __( 'License Key', 'aca-ai-content-agent' ), 'value' => $license ];
            }
            if ( $api_key ) {
                $data[] = [ 'name' => __( 'Encrypted API Key', 'aca-ai-content-agent' ), 'value' => $api_key ];
            }
        }
        return [
            'data' => $data,
            'done' => true,
        ];
    }

    /**
     * Register the data eraser with WordPress privacy API.
     *
     * @since 1.2.0
     * @param array $erasers Array of registered erasers.
     * @return array Modified array of erasers.
     */
    public static function register_eraser( $erasers ) {
        $erasers['aca-ai-content-agent-data'] = [
            'eraser_friendly_name' => __( 'ACA Settings', 'aca-ai-content-agent' ),
            'callback'             => [ 'ACA_AI_Content_Agent_Privacy', 'erase' ],
        ];
        return $erasers;
    }

    /**
     * Erase user data for privacy requests.
     *
     * @since 1.2.0
     * @param string $email_address The email address of the user requesting data erasure.
     * @param int $page The page number for pagination.
     * @return array Erasure result array with status information.
     */
    public static function erase( $email_address, $page = 1 ) {
        $removed = false;
        $user    = get_user_by( 'email', $email_address );
        if ( $user && user_can( $user, 'manage_aca_ai_content_agent_settings' ) ) {
            delete_option( 'aca_ai_content_agent_gemini_api_key' );
            delete_option( 'aca_ai_content_agent_license_key' );
            delete_option( 'aca_ai_content_agent_options' );
            delete_option( 'aca_ai_content_agent_is_pro_active' );
            $removed = true;
        }
        return [
            'items_removed'  => $removed,
            'items_retained' => false,
            'messages'       => [],
            'done'           => true,
        ];
    }
}
