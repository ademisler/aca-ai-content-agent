<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class ACA_Privacy {
    public static function init() {
        add_filter( 'wp_privacy_personal_data_exporters', [ __CLASS__, 'register_exporter' ] );
        add_filter( 'wp_privacy_personal_data_erasers', [ __CLASS__, 'register_eraser' ] );
    }

    public static function register_exporter( $exporters ) {
        $exporters['aca-data'] = [
            'exporter_friendly_name' => __( 'ACA Settings', 'aca' ),
            'callback'               => [ __CLASS__, 'export' ],
        ];
        return $exporters;
    }

    public static function export( $email_address, $page = 1 ) {
        $data  = [];
        $user  = get_user_by( 'email', $email_address );
        if ( $user && user_can( $user, 'manage_aca_settings' ) ) {
            $options  = get_option( 'aca_options', [] );
            $license  = get_option( 'aca_license_key', '' );
            $license  = $license ? aca_safe_decrypt( $license ) : '';
            $api_key  = get_option( 'aca_gemini_api_key', '' );
            $data[] = [
                'name'  => __( 'ACA Options', 'aca' ),
                'value' => wp_json_encode( $options ),
            ];
            if ( $license ) {
                $data[] = [ 'name' => __( 'License Key', 'aca' ), 'value' => $license ];
            }
            if ( $api_key ) {
                $data[] = [ 'name' => __( 'Encrypted API Key', 'aca' ), 'value' => $api_key ];
            }
        }
        return [
            'data' => $data,
            'done' => true,
        ];
    }

    public static function register_eraser( $erasers ) {
        $erasers['aca-data'] = [
            'eraser_friendly_name' => __( 'ACA Settings', 'aca' ),
            'callback'             => [ __CLASS__, 'erase' ],
        ];
        return $erasers;
    }

    public static function erase( $email_address, $page = 1 ) {
        $removed = false;
        $user    = get_user_by( 'email', $email_address );
        if ( $user && user_can( $user, 'manage_aca_settings' ) ) {
            delete_option( 'aca_gemini_api_key' );
            delete_option( 'aca_license_key' );
            delete_option( 'aca_options' );
            delete_option( 'aca_is_pro_active' );
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
