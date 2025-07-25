<?php
/**
 * The post hooks integration.
 *
 * @link       https://yourwebsite.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/integrations
 */

/**
 * The post hooks integration.
 *
 * This class defines all code necessary for post list and meta box hooks.
 *
 * @since      1.2.0
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/integrations
 * @author     Your Name <email@example.com>
 */
class ACA_Post_Hooks {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.2.0
	 */
	public function __construct() {

	}

	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	public function add_meta_box() {
		add_meta_box(
			'aca_ai_content_agent_meta_box',
			esc_html__( 'AI Content Agent', 'aca-ai-content-agent' ),
			array( $this, 'render_meta_box' ),
			'post',
			'side',
			'high'
		);
	}

	public function render_meta_box( $post ) {
		wp_nonce_field( 'aca_ai_content_agent_meta_box', 'aca_ai_content_agent_meta_box_nonce' );
		echo '<p>' . esc_html__( 'Use the button below to have AI suggest updates for this post.', 'aca-ai-content-agent' ) . '</p>';
		echo '<button type="button" class="button" id="aca-suggest-update-button" data-post-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Suggest Update', 'aca-ai-content-agent' ) . '</button>';
	}

}
