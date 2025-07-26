<?php
/**
 * The Gumroad API client.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/api
 */

/**
 * Gumroad API client for license verification.
 *
 * This class defines all code necessary for communicating with the Gumroad API
 * to verify license keys and validate purchases.
 *
 * @since 1.2.0
 */
class ACA_Gumroad_Api {

    /**
     * Gumroad API base URL.
     *
     * @since 1.2.0
     * @var string
     */
    const API_BASE_URL = 'https://api.gumroad.com/v2';

    /**
     * API request timeout in seconds.
     *
     * @since 1.2.0
     * @var int
     */
    const API_TIMEOUT = 15;

    /**
     * Verify a license key with the Gumroad API.
     *
     * @since 1.2.0
     * @param string $license_key The license key to verify.
     * @return array|WP_Error The API response array or WP_Error on failure.
     */
    public static function verify_license_key( $license_key ) {
        // Validate license key format
        if ( empty( $license_key ) || strlen( $license_key ) < 10 ) {
            return new WP_Error( 'invalid_license_format', __( 'Invalid license key format.', 'aca-ai-content-agent' ) );
        }

        $request_args = [
            'method'  => 'POST',
            'timeout' => self::API_TIMEOUT,
            'body'    => [
                'product_id'  => ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID,
                'license_key' => sanitize_text_field( $license_key ),
            ],
            'headers' => [
                'User-Agent' => 'ACA-AI-Content-Agent/' . ACA_AI_CONTENT_AGENT_VERSION,
            ],
        ];

        $response = wp_remote_post( self::API_BASE_URL . '/licenses/verify', $request_args );

        if ( is_wp_error( $response ) ) {
            ACA_Log_Service::error( 'Gumroad API request failed: ' . $response->get_error_message() );
            return new WP_Error( 'api_request_failed', __( 'Failed to connect to license verification service.', 'aca-ai-content-agent' ) );
        }

        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );
        $response_data = json_decode( $response_body, true );

        // Log API response for debugging
        ACA_Log_Service::info( 'Gumroad API response', [
            'code' => $response_code,
            'body' => $response_data,
        ] );

        if ( $response_code !== 200 ) {
            $error_message = isset( $response_data['message'] ) ? $response_data['message'] : __( 'Unknown API error.', 'aca-ai-content-agent' );
            return new WP_Error( 'api_error', sprintf( __( 'License verification failed: %s', 'aca-ai-content-agent' ), $error_message ) );
        }

        if ( ! is_array( $response_data ) ) {
            return new WP_Error( 'invalid_response', __( 'Invalid response from license verification service.', 'aca-ai-content-agent' ) );
        }

        return $response_data;
    }

    /**
     * Check if a license is valid based on API response.
     *
     * @since 1.2.0
     * @param array $api_response The response from verify_license_key().
     * @return bool True if license is valid, false otherwise.
     */
    public static function is_license_valid( $api_response ) {
        if ( is_wp_error( $api_response ) ) {
            return false;
        }

        // Check for success flag
        if ( isset( $api_response['success'] ) && $api_response['success'] === true ) {
            return true;
        }

        // Check for specific Gumroad response patterns
        if ( isset( $api_response['purchase'] ) && ! empty( $api_response['purchase'] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Get license details from API response.
     *
     * @since 1.2.0
     * @param array $api_response The response from verify_license_key().
     * @return array License details or empty array on failure.
     */
    public static function get_license_details( $api_response ) {
        if ( is_wp_error( $api_response ) || ! is_array( $api_response ) ) {
            return [];
        }

        $details = [
            'valid'      => false,
            'email'      => '',
            'purchase_id' => '',
            'created_at' => '',
            'refunded'   => false,
        ];

        if ( isset( $api_response['purchase'] ) ) {
            $purchase = $api_response['purchase'];
            $details['valid'] = true;
            $details['email'] = isset( $purchase['email'] ) ? $purchase['email'] : '';
            $details['purchase_id'] = isset( $purchase['id'] ) ? $purchase['id'] : '';
            $details['created_at'] = isset( $purchase['created_at'] ) ? $purchase['created_at'] : '';
            $details['refunded'] = isset( $purchase['refunded'] ) ? (bool) $purchase['refunded'] : false;
        }

        return $details;
    }

    /**
     * Test API connectivity.
     *
     * @since 1.2.0
     * @return bool|WP_Error True if API is accessible, WP_Error on failure.
     */
    public static function test_api_connectivity() {
        $request_args = [
            'method'  => 'GET',
            'timeout' => 10,
            'headers' => [
                'User-Agent' => 'ACA-AI-Content-Agent/' . ACA_AI_CONTENT_AGENT_VERSION,
            ],
        ];

        $response = wp_remote_get( self::API_BASE_URL . '/products/' . ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID, $request_args );

        if ( is_wp_error( $response ) ) {
            return new WP_Error( 'connection_failed', __( 'Cannot connect to Gumroad API.', 'aca-ai-content-agent' ) );
        }

        $response_code = wp_remote_retrieve_response_code( $response );

        if ( $response_code === 200 ) {
            return true;
        } elseif ( $response_code === 404 ) {
            return new WP_Error( 'product_not_found', __( 'Product not found on Gumroad. Please check the product ID.', 'aca-ai-content-agent' ) );
        } else {
            return new WP_Error( 'api_error', sprintf( __( 'API returned status code: %d', 'aca-ai-content-agent' ), $response_code ) );
        }
    }
}
