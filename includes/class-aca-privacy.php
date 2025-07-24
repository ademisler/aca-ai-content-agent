<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACA_AI_Content_Agent_Privacy {
    public static function init() {
        add_filter( 'wp_privacy_personal_data_exporters', [ 'ACA_AI_Content_Agent_Privacy', 'register_exporter' ] );
        add_filter( 'wp_privacy_personal_data_erasers', [ 'ACA_AI_Content_Agent_Privacy', 'register_eraser' ] );
    }

    public static function register_exporter( $exporters ) {
        $exporters['aca-ai-content-agent-data'] = [
            'exporter_friendly_name' => __( 'ACA Settings', 'aca-ai-content-agent' ),
            'callback'               => [ 'ACA_AI_Content_Agent_Privacy', 'export' ],
        ];
        return $exporters;
    }

    public static function export( $email_address, $page = 1 ) {
        $data  = [];
        $user  = get_user_by( 'email', $email_address );
        if ( $user && user_can( $user, 'manage_aca_ai_content_agent_settings' ) ) {
            $options  = get_option( 'aca_ai_content_agent_options', [] );
            $license  = get_option( 'aca_ai_content_agent_license_key', '' );
            $license  = $license ? aca_ai_content_agent_safe_decrypt( $license ) : '';
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

    public static function register_eraser( $erasers ) {
        $erasers['aca-ai-content-agent-data'] = [
            'eraser_friendly_name' => __( 'ACA Settings', 'aca-ai-content-agent' ),
            'callback'             => [ 'ACA_AI_Content_Agent_Privacy', 'erase' ],
        ];
        return $erasers;
    }

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
