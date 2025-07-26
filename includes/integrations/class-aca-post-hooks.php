<?php
/**
 * The post hooks integration.
 *
 * @link       https://ademisler.com
 * @since      1.2.0
 *
 * @package    ACA_AI_Content_Agent
 * @subpackage ACA_AI_Content_Agent/includes/integrations
 */

/**
 * Post hooks integration for ACA AI Content Agent.
 *
 * This class defines all code necessary for post list and meta box hooks,
 * providing AI-powered content suggestions directly in the post editor.
 *
 * @since 1.2.0
 */
class ACA_Post_Hooks {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.2.0
	 */
	public function __construct() {

	}

	/**
	 * Initialize the post hooks functionality.
	 *
	 * Registers the meta box for AI content suggestions.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function init() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	/**
	 * Add the AI Content Agent meta box to the post editor.
	 *
	 * @since 1.2.0
	 * @return void
	 */
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

	/**
	 * Render the AI Content Agent meta box content.
	 *
	 * @since 1.2.0
	 * @param WP_Post $post The post object.
	 * @return void
	 */
	public function render_meta_box( $post ) {
		// SECURITY FIX: Add proper nonce validation
		wp_nonce_field( 'aca_ai_content_agent_meta_box', 'aca_ai_content_agent_meta_box_nonce' );

		// SECURITY FIX: Add capability check
		if (!current_user_can('edit_post', $post->ID)) {
			echo '<p>' . esc_html__('You do not have permission to use this feature.', 'aca-ai-content-agent') . '</p>';
			return;
		}

		echo '<p>' . esc_html__( 'Use the button below to have AI suggest updates for this post.', 'aca-ai-content-agent' ) . '</p>';
		echo '<button type="button" class="button" id="aca-suggest-update-button" data-post-id="' . esc_attr( $post->ID ) . '">' . esc_html__( 'Suggest Update', 'aca-ai-content-agent' ) . '</button>';

		// SECURITY FIX: Add nonce to AJAX data
		echo '<script type="text/javascript">
			jQuery(document).ready(function($) {
				$("#aca-suggest-update-button").on("click", function() {
					var postId = $(this).data("post-id");
					var nonce = $("#aca_ai_content_agent_meta_box_nonce").val();

					$.ajax({
						url: ajaxurl,
						type: "POST",
						data: {
							action: "aca_ai_content_agent_suggest_update",
							post_id: postId,
							nonce: nonce
						},
						success: function(response) {
							if (response.success) {
								alert("Update suggestion generated successfully!");
							} else {
								alert("Error: " + response.data);
							}
						},
						error: function() {
							alert("An error occurred while processing your request.");
						}
					});
				});
			});
		</script>';
	}

}
