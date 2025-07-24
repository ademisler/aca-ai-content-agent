<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Check license status with Gumroad.
 *
 * @param bool $force Force verification even if cached.
 * @return bool True if license is valid, false otherwise.
 */
function aca_ai_content_agent_maybe_check_license( $force = false ) {
    $cached = get_transient( 'aca_ai_content_agent_license_status' );
    if ( false !== $cached && ! $force ) {
        return $cached === 'valid';
    }

    $license_key_enc = get_option( 'aca_ai_content_agent_license_key' );
    $license_key     = ! empty( $license_key_enc ) ? aca_ai_content_agent_safe_decrypt( $license_key_enc ) : '';
    if ( empty( $license_key ) ) {
        set_transient( 'aca_ai_content_agent_license_status', 'invalid', WEEK_IN_SECONDS );
        update_option( 'aca_ai_content_agent_is_pro_active', 'false' );
        return false;
    }

    $api_url  = 'https://api.gumroad.com/v2/licenses/verify';
    $response = wp_remote_post( $api_url, [
        'method'  => 'POST',
        'timeout' => 15,
        'body'    => [
            'product_id'  => ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID,
            'license_key' => $license_key,
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        // Fail open: keep current status if API unreachable.
        return get_option( 'aca_ai_content_agent_is_pro_active' ) === 'true';
    }

    $body  = json_decode( wp_remote_retrieve_body( $response ), true );
    $valid = isset( $body['success'] ) && true === $body['success'] && empty( $body['purchase']['refunded'] ) && empty( $body['purchase']['chargebacked'] );

    set_transient( 'aca_ai_content_agent_license_status', $valid ? 'valid' : 'invalid', WEEK_IN_SECONDS );

    if ( $valid ) {
        update_option( 'aca_ai_content_agent_is_pro_active', 'true' );
        update_option( 'aca_ai_content_agent_license_data', $body['purchase'] );
        $sale_time   = isset( $body['purchase']['sale_timestamp'] ) ? strtotime( $body['purchase']['sale_timestamp'] ) : time();
        $valid_until = $sale_time + YEAR_IN_SECONDS;
        update_option( 'aca_ai_content_agent_license_valid_until', $valid_until );
    } else {
        update_option( 'aca_ai_content_agent_is_pro_active', 'false' );
        delete_option( 'aca_ai_content_agent_license_valid_until' );
    }

    return $valid;
}
