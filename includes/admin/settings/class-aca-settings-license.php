<?php
/**
 * ACA - AI Content Agent
 *
 * License Settings
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handles the registration and rendering of license settings fields and page.
 *
 * @since 1.2.0
 */
class ACA_Settings_License {

    /**
     * Constructor.
     *
     * Registers the license settings group and fields.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Register license settings group and fields.
     *
     * @since 1.2.0
     */
    public function register_settings() {
        register_setting( 'aca_ai_content_agent_license_group', 'aca_ai_content_agent_license_key', [ 'sanitize_callback' => [ $this, 'sanitize_license_key' ] ] );
    }

    /**
     * Render the license page form.
     *
     * @since 1.2.0
     */
    public function render_license_page() {
        $license_key = get_option('aca_ai_content_agent_license_key');
        $is_pro = ACA_Helper::is_pro();
        $license_details = ACA_Helper::get_license_details();
        $status_message = ACA_Helper::get_license_status_message();
        
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('ACA Pro License Management', 'aca-ai-content-agent'); ?></h2>
            
            <div class="aca-license-status">
                <h3><?php esc_html_e('Current Status', 'aca-ai-content-agent'); ?></h3>
                <p>
                    <strong><?php esc_html_e('Status:', 'aca-ai-content-agent'); ?></strong>
                    <?php if ($is_pro): ?>
                        <span style="color: green;">✓ <?php esc_html_e('Pro License Active', 'aca-ai-content-agent'); ?></span>
                    <?php else: ?>
                        <span style="color: orange;">⚠ <?php esc_html_e('Free Version', 'aca-ai-content-agent'); ?></span>
                    <?php endif; ?>
                </p>
                <p><strong><?php esc_html_e('Details:', 'aca-ai-content-agent'); ?></strong> <?php echo esc_html($status_message); ?></p>
                
                <?php if (!empty($license_details) && isset($license_details['email'])): ?>
                <p><strong><?php esc_html_e('License Email:', 'aca-ai-content-agent'); ?></strong> <?php echo esc_html($license_details['email']); ?></p>
                <?php endif; ?>
            </div>

            <div class="aca-license-form">
                <h3><?php esc_html_e('License Key', 'aca-ai-content-agent'); ?></h3>
                <p><?php esc_html_e('Enter your ACA Pro license key to unlock all premium features.', 'aca-ai-content-agent'); ?></p>
                
                <form action="options.php" method="post">
                    <?php settings_fields('aca_ai_content_agent_license_group'); ?>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="aca_ai_content_agent_license_key"><?php esc_html_e('License Key', 'aca-ai-content-agent'); ?></label>
                            </th>
                            <td>
                                <input type="text" 
                                       id="aca_ai_content_agent_license_key" 
                                       name="aca_ai_content_agent_license_key" 
                                       value="" 
                                       placeholder="<?php echo !empty($license_key) ? esc_attr__('License key is saved', 'aca-ai-content-agent') : esc_attr__('Enter your license key here', 'aca-ai-content-agent'); ?>" 
                                       class="regular-text"
                                       <?php echo !empty($license_key) ? 'readonly' : ''; ?>>
                                
                                <?php if (!empty($license_key)): ?>
                                    <button type="button" class="button" id="aca-license-change-key">
                                        <?php esc_html_e('Change Key', 'aca-ai-content-agent'); ?>
                                    </button>
                                <?php endif; ?>
                                
                                <p class="description">
                                    <?php esc_html_e('Your license key can be found in your purchase confirmation email from Gumroad.', 'aca-ai-content-agent'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="submit">
                        <button type="button" class="button button-primary" id="aca-validate-license">
                            <?php esc_html_e('Validate License', 'aca-ai-content-agent'); ?>
                        </button>
                        <?php if (empty($license_key)): ?>
                            <button type="submit" class="button button-secondary">
                                <?php esc_html_e('Save License Key', 'aca-ai-content-agent'); ?>
                            </button>
                        <?php endif; ?>
                        <span id="aca-license-status" style="margin-left: 10px;"></span>
                    </p>
                </form>
            </div>

            <div class="aca-pro-features">
                <h3><?php esc_html_e('Pro Features', 'aca-ai-content-agent'); ?></h3>
                <ul>
                    <li><?php esc_html_e('Content Cluster Planner - Build strategic content clusters', 'aca-ai-content-agent'); ?></li>
                    <li><?php esc_html_e('DALL-E 3 Image Generation - Create unique featured images', 'aca-ai-content-agent'); ?></li>
                    <li><?php esc_html_e('Copyscape Plagiarism Check - Ensure content originality', 'aca-ai-content-agent'); ?></li>
                    <li><?php esc_html_e('Content Update Assistant - Improve existing posts', 'aca-ai-content-agent'); ?></li>
                    <li><?php esc_html_e('Data-Driven Sections - Add relevant statistics', 'aca-ai-content-agent'); ?></li>
                    <li><?php esc_html_e('Full Automation Modes - Hands-off content generation', 'aca-ai-content-agent'); ?></li>
                    <li><?php esc_html_e('Unlimited Generation - No monthly limits', 'aca-ai-content-agent'); ?></li>
                </ul>
                
                <?php if (!$is_pro): ?>
                <p>
                    <a href="<?php echo esc_url(apply_filters('aca_pro_purchase_url', 'https://gumroad.com/l/aca-ai-content-agent-pro')); ?>" target="_blank" class="button button-primary">
                        <?php esc_html_e('Get ACA Pro License', 'aca-ai-content-agent'); ?>
                    </a>
                </p>
                <?php endif; ?>
            </div>

            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#aca-validate-license').on('click', function() {
                    var button = $(this);
                    var statusSpan = $('#aca-license-status');
                    var licenseKey = $('#aca_ai_content_agent_license_key').val();
                    
                    if (!licenseKey) {
                        statusSpan.html('<span style="color: red;"><?php esc_html_e('Please enter a license key', 'aca-ai-content-agent'); ?></span>');
                        return;
                    }
                    
                    button.prop('disabled', true).text('<?php esc_html_e('Validating...', 'aca-ai-content-agent'); ?>');
                    statusSpan.html('<span style="color: blue;"><?php esc_html_e('Checking license...', 'aca-ai-content-agent'); ?></span>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'aca_validate_license',
                            license_key: licenseKey,
                            nonce: aca_ai_content_agent_admin_ajax.nonce
                        },
                        success: function(response) {
                            if (response.success) {
                                statusSpan.html('<span style="color: green;">' + response.data + '</span>');
                                setTimeout(function() {
                                    location.reload();
                                }, 2000);
                            } else {
                                statusSpan.html('<span style="color: red;">' + response.data + '</span>');
                            }
                        },
                        error: function() {
                            statusSpan.html('<span style="color: red;"><?php esc_html_e('Validation failed. Please try again.', 'aca-ai-content-agent'); ?></span>');
                        },
                        complete: function() {
                            button.prop('disabled', false).text('<?php esc_html_e('Validate License', 'aca-ai-content-agent'); ?>');
                        }
                    });
                });
                
                $('#aca-license-change-key').on('click', function() {
                    $('#aca_ai_content_agent_license_key').prop('readonly', false).val('').focus();
                    $(this).hide();
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Sanitize the license key before saving.
     *
     * @since 1.2.0
     * @param string $input The input license key.
     * @return string The sanitized and encrypted license key.
     */
    public function sanitize_license_key( $input ) {
        $existing = get_option( 'aca_ai_content_agent_license_key' );

        if ( ! isset( $input ) || '' === trim( $input ) ) {
            return $existing;
        }

        $sanitized_key = sanitize_text_field( $input );

        return aca_ai_content_agent_encrypt( $sanitized_key );
    }
}
