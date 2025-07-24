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
            esc_html__('Welcome to ACA', 'aca-ai-content-agent'),
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
                $api_key = sanitize_text_field(wp_unslash($_POST['aca_gemini_api_key']));
                update_option( 'aca_gemini_api_key', aca_encrypt( $api_key ) );
                $this->step = 2;
            } elseif ($step === 2 && !empty($_POST['aca_options']['analysis_post_types'])) {
                $options = get_option('aca_options', []);
                $options['analysis_post_types'] = array_map('sanitize_text_field', wp_unslash($_POST['aca_options']['analysis_post_types']));
                update_option('aca_options', $options);
                $this->step = 3;
            } elseif ($step === 3 && !empty($_POST['aca_options']['working_mode'])) {
                $options = get_option('aca_options', []);
                $options['working_mode'] = sanitize_key(wp_unslash($_POST['aca_options']['working_mode']));
                update_option('aca_options', $options);
                
                // Complete onboarding
                update_option('aca_onboarding_complete', true);
                ACA_Engine::add_log('Onboarding completed successfully.', 'success');
                wp_redirect(admin_url('admin.php?page=aca'));
                exit;
            }
        }
    }

    public function render_onboarding_page() {
        ?>
        <div class="wrap aca-onboarding">
            <h1><?php esc_html_e('Welcome to ACA - AI Content Agent', 'aca-ai-content-agent'); ?></h1>
            <p><?php esc_html_e('Let\'s get you set up in just a few steps.', 'aca-ai-content-agent'); ?></p>

            <form method="post">
                <?php wp_nonce_field('aca_onboarding_nonce'); ?>
                <input type="hidden" name="aca_onboarding_step" value="<?php echo esc_attr( $this->step ); ?>">

                <?php if ($this->step === 1) : ?>
                    <h2><?php esc_html_e('Step 1: Connect to Google Gemini', 'aca-ai-content-agent'); ?></h2>
                    <p><?php esc_html_e('Enter your Google Gemini API key. You can get one from Google AI Studio.', 'aca-ai-content-agent'); ?></p>
                    <input type="password" name="aca_gemini_api_key" class="regular-text" placeholder="<?php esc_attr_e('Enter your API Key', 'aca-ai-content-agent'); ?>" required>
                    <?php submit_button(esc_html__('Next Step', 'aca-ai-content-agent'), 'primary'); ?>
                <?php elseif ($this->step === 2) : ?>
                    <h2><?php esc_html_e('Step 2: Choose Content for Analysis', 'aca-ai-content-agent'); ?></h2>
                    <p><?php esc_html_e('Select the content types ACA should analyze to learn your writing style.', 'aca-ai-content-agent'); ?></p>
                    <?php
                    $post_types = get_post_types(['public' => true], 'objects');
                    foreach ($post_types as $post_type) {
                        if (in_array($post_type->name, ['attachment', 'page'])) continue;
                        echo '<label><input type="checkbox" name="aca_options[analysis_post_types][]" value="' . esc_attr($post_type->name) . '" checked> ' . esc_html($post_type->label) . '</label><br>';
                    }
                    ?>
                    <?php submit_button(esc_html__('Next Step', 'aca-ai-content-agent'), 'primary'); ?>
                <?php elseif ($this->step === 3) : ?>
                    <h2><?php esc_html_e('Step 3: Choose Your Working Mode', 'aca-ai-content-agent'); ?></h2>
                    <select name="aca_options[working_mode]">
                        <option value="manual"><?php esc_html_e('Manual Mode', 'aca-ai-content-agent'); ?></option>
                        <option value="semi-auto"><?php esc_html_e('Semi-Automatic (Ideas & Approval)', 'aca-ai-content-agent'); ?></option>
                        <option value="full-auto"><?php esc_html_e('Fully Automatic (Draft Creation)', 'aca-ai-content-agent'); ?></option>
                    </select>
                    <?php submit_button(esc_html__('Complete Setup', 'aca-ai-content-agent'), 'primary'); ?>
                <?php endif; ?>
            </form>
        </div>
        <?php
    }
}

new ACA_Onboarding();
