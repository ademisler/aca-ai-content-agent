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
     * @return string Encrypted and base64-encoded string.
     */
    public static function encrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        $key    = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'aca_ai_content_agent_default_key';
        $method = 'AES-256-CBC';
        $iv_len = openssl_cipher_iv_length( $method );
        $iv     = random_bytes( $iv_len );
        $cipher = openssl_encrypt( $data, $method, substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        if ( false === $cipher ) {
            return '';
        }
        return base64_encode( $iv . $cipher );
    }

    /**
     * Decrypt a string that was encrypted with aca_ai_content_agent_encrypt().
     *
     * @param string $data Encrypted string.
     * @return string Decrypted plain text.
     */
    public static function decrypt( $data ) {
        if ( empty( $data ) ) {
            return '';
        }
        $key    = defined( 'AUTH_KEY' ) ? AUTH_KEY : 'aca_ai_content_agent_default_key';
        $method = 'AES-256-CBC';
        $raw    = base64_decode( $data );
        $iv_len = openssl_cipher_iv_length( $method );
        $iv     = substr( $raw, 0, $iv_len );
        $cipher = substr( $raw, $iv_len );
        $plain  = openssl_decrypt( $cipher, $method, substr( hash( 'sha256', $key ), 0, 32 ), 0, $iv );
        return $plain ?: '';
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
        return '' === $decrypted ? $data : $decrypted;
    }
}
