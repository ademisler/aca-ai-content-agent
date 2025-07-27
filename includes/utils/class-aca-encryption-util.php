<?php
/**
 * ACA - AI Content Agent
 *
 * Encryption Utility
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Utility class for encryption and decryption using AUTH_KEY.
 *
 * @since 1.2.0
 */
class ACA_Encryption_Util {

    /**
     * Check if AUTH_KEY is valid and unique.
     *
     * @since 1.2.0
     * @return bool True if AUTH_KEY is valid, false otherwise.
     */
    public static function is_auth_key_valid() {
        // FIX: More comprehensive AUTH_KEY validation
        if (!defined('AUTH_KEY')) {
            return false;
        }
        
        $auth_key = AUTH_KEY;
        
        // Check if it's the default value
        if ($auth_key === 'put your unique phrase here') {
            return false;
        }
        
        // Check minimum length
        if (strlen($auth_key) < 20) {
            return false;
        }
        
        // Check if it's not empty or just whitespace
        if (empty(trim($auth_key))) {
            return false;
        }
        
        return true;
    }

    /**
     * Encrypt a string using the AUTH_KEY as a secret.
     *
     * @since 1.2.0
     * @param string $data Plain text to encrypt.
     * @return string|WP_Error Encrypted and base64-encoded string or WP_Error on failure.
     */
    public static function encrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        
        if ( ! self::is_auth_key_valid() ) {
            return new WP_Error( 
                'auth_key_invalid', 
                __( 'WordPress AUTH_KEY is not properly configured. Please define a unique AUTH_KEY in your wp-config.php file to enable encryption features.', 'aca-ai-content-agent' ) 
            );
        }
        
        // FIX: Add error handling for OpenSSL functions
        $method = 'AES-256-CBC';
        
        if (!in_array($method, openssl_get_cipher_methods())) {
            return new WP_Error( 'cipher_not_supported', __( 'Required encryption cipher is not supported on this server.', 'aca-ai-content-agent' ) );
        }
        
        $key = AUTH_KEY;
        $iv_len = openssl_cipher_iv_length( $method );
        
        if ($iv_len === false) {
            return new WP_Error( 'cipher_iv_length_failed', __( 'Failed to get cipher IV length.', 'aca-ai-content-agent' ) );
        }
        
        try {
            $iv = random_bytes( $iv_len );
        } catch (Exception $e) {
            return new WP_Error( 'random_bytes_failed', __( 'Failed to generate random bytes for encryption.', 'aca-ai-content-agent' ) );
        }
        
        $cipher = openssl_encrypt( $data, $method, substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        
        if ( false === $cipher ) {
            return new WP_Error( 'encryption_failed', __( 'Encryption operation failed.', 'aca-ai-content-agent' ) );
        }
        
        return base64_encode( $iv . $cipher );
    }

    /**
     * Decrypt a string that was encrypted with aca_ai_content_agent_encrypt().
     *
     * @since 1.2.0
     * @param string $data Encrypted string.
     * @return string|WP_Error Decrypted plain text or WP_Error on failure.
     */
    public static function decrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        
        if ( ! self::is_auth_key_valid() ) {
            return new WP_Error( 
                'auth_key_invalid', 
                __( 'WordPress AUTH_KEY is not properly configured. Please define a unique AUTH_KEY in your wp-config.php file to enable decryption.', 'aca-ai-content-agent' ) 
            );
        }
        
        // FIX: Add validation for base64 data
        $raw = base64_decode( $data, true );
        if ($raw === false) {
            return new WP_Error( 'invalid_base64', __( 'Invalid encrypted data format.', 'aca-ai-content-agent' ) );
        }
        
        $key = AUTH_KEY;
        $method = 'AES-256-CBC';
        $iv_len = openssl_cipher_iv_length( $method );
        
        if ($iv_len === false) {
            return new WP_Error( 'cipher_iv_length_failed', __( 'Failed to get cipher IV length.', 'aca-ai-content-agent' ) );
        }
        
        if (strlen($raw) < $iv_len) {
            return new WP_Error( 'invalid_encrypted_data', __( 'Encrypted data is too short.', 'aca-ai-content-agent' ) );
        }
        
        $iv = substr( $raw, 0, $iv_len );
        $cipher = substr( $raw, $iv_len );
        
        $plain = openssl_decrypt( $cipher, $method, substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        
        if ($plain === false) {
            return new WP_Error( 'decryption_failed', __( 'Decryption operation failed.', 'aca-ai-content-agent' ) );
        }
        
        return $plain;
    }

    /**
     * Attempt to decrypt a value, falling back to the raw string if decryption fails.
     * This helps maintain compatibility with previously stored plain text values.
     *
     * @since 1.2.0
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
