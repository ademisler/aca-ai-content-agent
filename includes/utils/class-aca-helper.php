<?php
/**
 * The helper utility.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/utils
 */

/**
 * Helper utility class for ACA AI Content Agent.
 *
 * Provides static helper methods for license checks and other plugin-wide utilities.
 *
 * @since 1.2.0
 */
class ACA_Helper {

    /**
     * Check if ACA Pro is active.
     *
     * @since 1.2.0
     * @return boolean True if Pro is active, false otherwise.
     */
    public static function is_pro() {
        self::maybe_check_license();

        $is_active    = false;
        $active_flag  = get_option( 'aca_ai_content_agent_is_pro_active' ) === 'true';
        $valid_until  = intval( get_option( 'aca_ai_content_agent_license_valid_until', 0 ) );
        $status       = get_transient( 'aca_ai_content_agent_license_status' );

        if ( $active_flag && $valid_until > time() && $status === 'valid' ) {
            $is_active = true;
        }

        return apply_filters( 'aca_ai_content_agent_is_pro', $is_active );
    }

    /**
     * Check and update license status if needed.
     *
     * @since 1.2.0
     * @param bool $force_check Whether to force a license check.
     * @return void
     */
    public static function maybe_check_license($force_check = false) {
        // PERFORMANCE FIX: Optimize license checking with better caching
        $last_check = get_transient('aca_ai_content_agent_last_license_check');
        $check_interval = $force_check ? 0 : (6 * HOUR_IN_SECONDS); // Check every 6 hours instead of daily
        
        if ($force_check || false === $last_check || (time() - $last_check) > $check_interval) {
            $license_key_encrypted = get_option('aca_ai_content_agent_license_key');
            if (!empty($license_key_encrypted)) {
                $license_key = ACA_Encryption_Util::decrypt($license_key_encrypted);
                
                // PERFORMANCE FIX: Add timeout to prevent hanging requests
                $api_response = ACA_Gumroad_Api::verify_license_key($license_key);
                
                if (ACA_Gumroad_Api::is_license_valid($api_response)) {
                    update_option('aca_ai_content_agent_is_pro_active', 'true');
                    set_transient('aca_ai_content_agent_license_status', 'valid', WEEK_IN_SECONDS);
                    
                    // Store license details
                    $license_details = ACA_Gumroad_Api::get_license_details($api_response);
                    update_option('aca_ai_content_agent_license_data', $license_details);
                    
                    ACA_Log_Service::info('License validation successful', $license_details);
                } else {
                    update_option('aca_ai_content_agent_is_pro_active', 'false');
                    set_transient('aca_ai_content_agent_license_status', 'invalid', WEEK_IN_SECONDS);
                    
                    if (is_wp_error($api_response)) {
                        ACA_Log_Service::error('License validation failed: ' . $api_response->get_error_message());
                    } else {
                        ACA_Log_Service::error('License validation failed: Invalid license key');
                    }
                }
            } else {
                // No license key, mark as inactive
                update_option('aca_ai_content_agent_is_pro_active', 'false');
                set_transient('aca_ai_content_agent_license_status', 'invalid', WEEK_IN_SECONDS);
            }
            
            // PERFORMANCE FIX: Cache check timestamp for 6 hours
            set_transient('aca_ai_content_agent_last_license_check', time(), 6 * HOUR_IN_SECONDS);
        }
    }

    /**
     * Get license details.
     *
     * @since 1.2.0
     * @return array License details or empty array.
     */
    public static function get_license_details() {
        return get_option('aca_ai_content_agent_license_data', []);
    }

    /**
     * Check if license is expired.
     *
     * @since 1.2.0
     * @return bool True if license is expired, false otherwise.
     */
    public static function is_license_expired() {
        $license_details = self::get_license_details();
        
        if (empty($license_details) || !isset($license_details['created_at'])) {
            return false; // No license data, assume not expired
        }

        // Gumroad licenses don't typically expire, but we can check for refunds
        if (isset($license_details['refunded']) && $license_details['refunded']) {
            return true;
        }

        return false;
    }

    /**
     * Get license status message.
     *
     * @since 1.2.0
     * @return string Human-readable license status.
     */
    public static function get_license_status_message() {
        if (!self::is_pro()) {
            return __('Free version active', 'aca-ai-content-agent');
        }

        $license_details = self::get_license_details();
        
        if (empty($license_details)) {
            return __('Pro license active', 'aca-ai-content-agent');
        }

        if (self::is_license_expired()) {
            return __('License expired or refunded', 'aca-ai-content-agent');
        }

        $email = isset($license_details['email']) ? $license_details['email'] : '';
        $purchase_id = isset($license_details['purchase_id']) ? $license_details['purchase_id'] : '';
        
        return sprintf(
            __('Pro license active (Email: %s, Purchase ID: %s)', 'aca-ai-content-agent'),
            $email,
            $purchase_id
        );
    }
}
