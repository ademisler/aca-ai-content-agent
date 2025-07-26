<?php
/**
 * ACA - AI Content Agent
 *
 * Onboarding Wizard
 *
 * @package ACA_AI_Content_Agent
 * @version 1.2
 * @since   1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Onboarding wizard class.
 *
 * Handles the initial setup process for new users, guiding them through
 * API key configuration, content analysis settings, and working mode selection.
 *
 * @since 1.2.0
 */
class ACA_Onboarding {

    /**
     * Current step in the onboarding process.
     *
     * @since 1.2.0
     * @var int
     */
    private $step = 1;

    /**
     * Constructor.
     *
     * Registers the onboarding page and handles form submissions.
     *
     * @since 1.2.0
     */
    public function __construct() {
        add_action('admin_menu', [$this, 'register_onboarding_page']);
        add_action('admin_init', [$this, 'handle_onboarding_steps']);
    }

    /**
     * Register the onboarding page in the admin menu.
     *
     * @since 1.2.0
     * @return void
     */
    public function register_onboarding_page() {
        add_dashboard_page(
            esc_html__('Welcome to ACA', 'aca-ai-content-agent'),
            null, // Hide from menu
            'manage_aca_ai_content_agent_settings',
            'aca-ai-content-agent-onboarding',
            [$this, 'render_onboarding_page']
        );
    }

    /**
     * Handle onboarding form submissions and step progression.
     *
     * @since 1.2.0
     * @return void
     */
    public function handle_onboarding_steps() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'aca-ai-content-agent-onboarding') {
            return;
        }

        if (isset($_POST['aca_ai_content_agent_onboarding_step'])) {
            check_admin_referer('aca_ai_content_agent_onboarding_nonce');

            $step = absint($_POST['aca_ai_content_agent_onboarding_step']);

            if ($step === 1 && !empty($_POST['aca_ai_content_agent_gemini_api_key'])) {
                // SECURITY FIX: Enhanced API key validation and sanitization
                $api_key = sanitize_text_field(wp_unslash($_POST['aca_ai_content_agent_gemini_api_key']));
                
                // Validate API key format (basic validation)
                if (!preg_match('/^[A-Za-z0-9_-]{20,}$/', $api_key)) {
                    wp_die(__('Invalid API key format. Please check your Google Gemini API key.', 'aca-ai-content-agent'));
                }
                
                // Test API key before saving
                $test_response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $api_key,
                    ],
                    'body' => wp_json_encode([
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [['text' => 'Test connection']]
                            ]
                        ]
                    ]),
                    'timeout' => 10,
                ]);

                if (is_wp_error($test_response) || wp_remote_retrieve_response_code($test_response) !== 200) {
                    wp_die(__('API key validation failed. Please check your Google Gemini API key.', 'aca-ai-content-agent'));
                }
                
                update_option( 'aca_ai_content_agent_gemini_api_key', ACA_Encryption_Util::encrypt( $api_key ) );
                $this->step = 2;
            } elseif ($step === 2 && !empty($_POST['aca_ai_content_agent_options']['analysis_post_types'])) {
                $options = get_option('aca_ai_content_agent_options', []);
                // SECURITY FIX: Enhanced sanitization for post types
                $post_types = array_map('sanitize_key', wp_unslash($_POST['aca_ai_content_agent_options']['analysis_post_types']));
                
                // Validate post types exist
                $valid_post_types = array_keys(get_post_types(['public' => true]));
                $post_types = array_intersect($post_types, $valid_post_types);
                
                if (empty($post_types)) {
                    wp_die(__('No valid post types selected.', 'aca-ai-content-agent'));
                }
                
                $options['analysis_post_types'] = $post_types;
                update_option('aca_ai_content_agent_options', $options);
                $this->step = 3;
            } elseif ($step === 3 && !empty($_POST['aca_ai_content_agent_options']['working_mode'])) {
                $options = get_option('aca_ai_content_agent_options', []);
                // SECURITY FIX: Validate working mode
                $working_mode = sanitize_key(wp_unslash($_POST['aca_ai_content_agent_options']['working_mode']));
                $valid_modes = ['manual', 'semi-auto', 'full-auto'];
                
                if (!in_array($working_mode, $valid_modes, true)) {
                    wp_die(__('Invalid working mode selected.', 'aca-ai-content-agent'));
                }
                
                $options['working_mode'] = $working_mode;
                update_option('aca_ai_content_agent_options', $options);
                
                // Complete onboarding
                update_option('aca_ai_content_agent_onboarding_complete', true);
                wp_redirect(admin_url('admin.php?page=aca-ai-content-agent'));
                exit;
            }
        }
    }

    /**
     * Render the onboarding page with step-specific content.
     *
     * @since 1.2.0
     * @return void
     */
    public function render_onboarding_page() {
        ?>
        <div class="wrap aca-ai-content-agent-onboarding">
            <h1><?php esc_html_e('Welcome to ACA - AI Content Agent', 'aca-ai-content-agent'); ?></h1>
            <p><?php esc_html_e('Let\'s get you set up in just a few steps.', 'aca-ai-content-agent'); ?></p>

            <form method="post">
                <?php wp_nonce_field('aca_ai_content_agent_onboarding_nonce'); ?>
                <input type="hidden" name="aca_ai_content_agent_onboarding_step" value="<?php echo esc_attr( $this->step ); ?>">

                <?php if ($this->step === 1) : ?>
                    <h2><?php esc_html_e('Step 1: Connect to Google Gemini', 'aca-ai-content-agent'); ?></h2>
                    <p><?php esc_html_e('Enter your Google Gemini API key. You can get one from Google AI Studio.', 'aca-ai-content-agent'); ?></p>
                    <input type="password" name="aca_ai_content_agent_gemini_api_key" class="regular-text" placeholder="<?php esc_attr_e('Enter your API Key', 'aca-ai-content-agent'); ?>" required>
                    <?php submit_button(esc_html__('Next Step', 'aca-ai-content-agent'), 'primary'); ?>
                <?php elseif ($this->step === 2) : ?>
                    <h2><?php esc_html_e('Step 2: Choose Content for Analysis', 'aca-ai-content-agent'); ?></h2>
                    <p><?php esc_html_e('Select the content types ACA should analyze to learn your writing style.', 'aca-ai-content-agent'); ?></p>
                    <?php
                    $post_types = get_post_types(['public' => true], 'objects');
                    foreach ($post_types as $post_type) {
                        if (in_array($post_type->name, ['attachment', 'page'])) continue;
                        echo '<label><input type="checkbox" name="aca_ai_content_agent_options[analysis_post_types][]" value="' . esc_attr($post_type->name) . '" checked> ' . esc_html($post_type->label) . '</label><br>';
                    }
                    ?>
                    <?php submit_button(esc_html__('Next Step', 'aca-ai-content-agent'), 'primary'); ?>
                <?php elseif ($this->step === 3) : ?>
                    <h2><?php esc_html_e('Step 3: Choose Your Working Mode', 'aca-ai-content-agent'); ?></h2>
                    <select name="aca_ai_content_agent_options[working_mode]">
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