<?php
/**
 * ACA - AI Content Agent
 *
 * Onboarding Wizard
 *
 * @package ACA
 * @version 1.0
 * @since   1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Onboarding {

    private $step = 1;

    public function __construct() {
        add_action('admin_menu', [$this, 'register_onboarding_page']);
        add_action('admin_init', [$this, 'handle_onboarding_steps']);
    }

    public function register_onboarding_page() {
        add_dashboard_page(
            __('Welcome to ACA', 'aca'),
            null, // Hide from menu
            'manage_options',
            'aca-onboarding',
            [$this, 'render_onboarding_page']
        );
    }

    public function handle_onboarding_steps() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'aca-onboarding') {
            return;
        }

        if (isset($_POST['aca_onboarding_step'])) {
            check_admin_referer('aca_onboarding_nonce');

            $step = absint($_POST['aca_onboarding_step']);

            if ($step === 1 && !empty($_POST['aca_gemini_api_key'])) {
                $api_key = sanitize_text_field($_POST['aca_gemini_api_key']);
                update_option('aca_gemini_api_key', base64_encode($api_key));
                $this->step = 2;
            } elseif ($step === 2 && !empty($_POST['aca_options']['analysis_post_types'])) {
                $options = get_option('aca_options', []);
                $options['analysis_post_types'] = array_map('sanitize_text_field', $_POST['aca_options']['analysis_post_types']);
                update_option('aca_options', $options);
                $this->step = 3;
            } elseif ($step === 3 && !empty($_POST['aca_options']['working_mode'])) {
                $options = get_option('aca_options', []);
                $options['working_mode'] = sanitize_key($_POST['aca_options']['working_mode']);
                update_option('aca_options', $options);
                
                // Complete onboarding
                update_option('aca_onboarding_complete', true);
                ACA_Core::add_log('Onboarding completed successfully.', 'success');
                wp_redirect(admin_url('admin.php?page=aca'));
                exit;
            }
        }
    }

    public function render_onboarding_page() {
        ?>
        <div class="wrap aca-onboarding">
            <h1><?php _e('Welcome to ACA - AI Content Agent', 'aca'); ?></h1>
            <p><?php _e('Let\'s get you set up in just a few steps.', 'aca'); ?></p>

            <form method="post">
                <?php wp_nonce_field('aca_onboarding_nonce'); ?>
                <input type="hidden" name="aca_onboarding_step" value="<?php echo $this->step; ?>">

                <?php if ($this->step === 1) : ?>
                    <h2><?php _e('Step 1: Connect to Google Gemini', 'aca'); ?></h2>
                    <p><?php _e('Enter your Google Gemini API key. You can get one from Google AI Studio.', 'aca'); ?></p>
                    <input type="password" name="aca_gemini_api_key" class="regular-text" placeholder="<?php _e('Enter your API Key', 'aca'); ?>" required>
                    <?php submit_button(__('Next Step', 'aca'), 'primary'); ?>
                <?php elseif ($this->step === 2) : ?>
                    <h2><?php _e('Step 2: Choose Content for Analysis', 'aca'); ?></h2>
                    <p><?php _e('Select the content types ACA should analyze to learn your writing style.', 'aca'); ?></p>
                    <?php
                    $post_types = get_post_types(['public' => true], 'objects');
                    foreach ($post_types as $post_type) {
                        if (in_array($post_type->name, ['attachment', 'page'])) continue;
                        echo '<label><input type="checkbox" name="aca_options[analysis_post_types][]" value="' . esc_attr($post_type->name) . '" checked> ' . esc_html($post_type->label) . '</label><br>';
                    }
                    ?>
                    <?php submit_button(__('Next Step', 'aca'), 'primary'); ?>
                <?php elseif ($this->step === 3) : ?>
                    <h2><?php _e('Step 3: Choose Your Working Mode', 'aca'); ?></h2>
                    <select name="aca_options[working_mode]">
                        <option value="manual"><?php _e('Manual Mode', 'aca'); ?></option>
                        <option value="semi-auto"><?php _e('Semi-Automatic (Ideas & Approval)', 'aca'); ?></option>
                        <option value="full-auto"><?php _e('Fully Automatic (Draft Creation)', 'aca'); ?></option>
                    </select>
                    <?php submit_button(__('Complete Setup', 'aca'), 'primary'); ?>
                <?php endif; ?>
            </form>
        </div>
        <?php
    }
}