<?php
/**
 * The helper utility.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/utils
 */

/**
 * The helper utility.
 *
 * This class defines all code necessary for helper functions.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/utils
 * @author     Your Name <email@example.com>
 */
class ACA_Helper {

	/**
	 * Check if ACA Pro is active.
	 *
	 * @since    1.2.0
	 * @return boolean
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

	public static function maybe_check_license($force_check = false) {
        $last_check = get_transient('aca_ai_content_agent_last_license_check');
        if ($force_check || false === $last_check) {
            $license_key_encrypted = get_option('aca_ai_content_agent_license_key');
            if (!empty($license_key_encrypted)) {
                $license_key = ACA_Encryption_Util::decrypt($license_key_encrypted);
                $body = ACA_Gumroad_Api::verify_license_key($license_key);
                if (isset($body['success']) && $body['success'] === true) {
                    update_option('aca_ai_content_agent_is_pro_active', 'true');
                    set_transient('aca_ai_content_agent_license_status', 'valid', WEEK_IN_SECONDS);
                } else {
                    update_option('aca_ai_content_agent_is_pro_active', 'false');
                    set_transient('aca_ai_content_agent_license_status', 'invalid', WEEK_IN_SECONDS);
                }
            }
            set_transient('aca_ai_content_agent_last_license_check', time(), DAY_IN_SECONDS);
        }
    }

}
