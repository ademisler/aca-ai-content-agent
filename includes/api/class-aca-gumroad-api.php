<?php
/**
 * The Gumroad API client.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/api
 */

/**
 * The Gumroad API client.
 *
 * This class defines all code necessary for communicating with the Gumroad API.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/api
 * @author     Your Name <email@example.com>
 */
class ACA_Gumroad_Api {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 */
	public function __construct() {

	}

	public static function verify_license_key( $license_key ) {
		$response = wp_remote_post( 'https://api.gumroad.com/v2/licenses/verify', [
			'body' => [
				'product_id' => ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID,
				'license_key'       => sanitize_text_field( $license_key ),
			],
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

}
