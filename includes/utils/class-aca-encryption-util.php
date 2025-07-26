<?php
/**
 * ACA - AI Content Agent
 *
 * Encryption Utility
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Encryption_Util {

    /**
     * Encrypt a string using the AUTH_KEY as a secret.
     *
     * @param string $data Plain text to encrypt.
     * @return string|WP_Error Encrypted and base64-encoded string or WP_Error on failure.
     */
    public static function encrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        if ( ! defined( 'AUTH_KEY' ) || 'put your unique phrase here' === AUTH_KEY ) {
            return new WP_Error( 'auth_key_not_defined', __( 'AUTH_KEY is not defined in wp-config.php. Please define it to use encryption.', 'aca-ai-content-agent' ) );
        }
        $key    = AUTH_KEY;
        $method = 'AES-256-CBC';
        $iv_len = openssl_cipher_iv_length( $method );
        $iv     = random_bytes( $iv_len );
        $cipher = openssl_encrypt( $data, $method, substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        if ( false === $cipher ) {
            return new WP_Error( 'encryption_failed', __( 'Encryption failed.', 'aca-ai-content-agent' ) );
        }
        return base64_encode( $iv . $cipher );
    }

    /**
     * Decrypt a string that was encrypted with aca_ai_content_agent_encrypt().
     *
     * @param string $data Encrypted string.
     * @return string|WP_Error Decrypted plain text or WP_Error on failure.
     */
    public static function decrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        if ( ! defined( 'AUTH_KEY' ) || 'put your unique phrase here' === AUTH_KEY ) {
            return new WP_Error( 'auth_key_not_defined', __( 'AUTH_KEY is not defined in wp-config.php. Please define it to use encryption.', 'aca-ai-content-agent' ) );
        }
        $key    = AUTH_KEY;
        $method = 'AES-256-CBC';
        $raw    = base64_decode( $data );
        $iv_len = openssl_cipher_iv_length( $method );
        $iv     = substr( $raw, 0, $iv_len );
        $cipher = substr( $raw, $iv_len );
        $plain  = openssl_decrypt( $cipher, $method, substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        return $plain ?: new WP_Error( 'decryption_failed', __( 'Decryption failed.', 'aca-ai-content-agent' ) );
    }

    /**
     * Attempt to decrypt a value, falling back to the raw string if decryption
     * fails. This helps maintain compatibility with previously stored plain text
     * values.
     *
     * @param string $data Encrypted or plain text value.
     * @return string Decrypted value or original string on failure.
     */
    public static function safe_decrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        $decrypted = self::decrypt( $data );
        return is_wp_error( $decrypted ) ? $data : $decrypted;
    }
}
