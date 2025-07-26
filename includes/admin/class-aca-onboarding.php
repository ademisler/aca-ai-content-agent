<?php
/**
 * ACA - AI Content Agent
 *
 * Onboarding Wizard
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
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
     * Total steps in the onboarding process.
     *
     * @since 1.2.0
     * @var int
     */
    private $total_steps = 4;

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
        add_action('admin_enqueue_scripts', [$this, 'enqueue_onboarding_assets']);
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
     * Enqueue onboarding specific assets.
     *
     * @since 1.2.0
     * @return void
     */
    public function enqueue_onboarding_assets($hook) {
        if (strpos($hook, 'aca-ai-content-agent-onboarding') === false) {
            return;
        }

        wp_enqueue_style(
            'aca-onboarding-css',
            plugin_dir_url(dirname(__FILE__)) . 'admin/css/aca-admin.css',
            [],
            ACA_AI_CONTENT_AGENT_VERSION
        );

        wp_enqueue_script(
            'aca-onboarding-js',
            plugin_dir_url(dirname(__FILE__)) . 'admin/js/aca-admin.js',
            ['jquery'],
            ACA_AI_CONTENT_AGENT_VERSION,
            true
        );

        wp_localize_script('aca-onboarding-js', 'aca_onboarding_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('aca_onboarding_nonce'),
            'strings' => [
                'next_step' => __('Next Step', 'aca-ai-content-agent'),
                'previous_step' => __('Previous Step', 'aca-ai-content-agent'),
                'finish_setup' => __('Finish Setup', 'aca-ai-content-agent'),
                'testing_connection' => __('Testing connection...', 'aca-ai-content-agent'),
                'connection_success' => __('Connection successful!', 'aca-ai-content-agent'),
                'connection_failed' => __('Connection failed. Please check your API key.', 'aca-ai-content-agent'),
            ],
        ]);
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

            switch ($step) {
                case 1:
                    $this->handle_api_key_step();
                    break;
                case 2:
                    $this->handle_content_settings_step();
                    break;
                case 3:
                    $this->handle_working_mode_step();
                    break;
                case 4:
                    $this->handle_final_step();
                    break;
            }
        }
    }

    /**
     * Handle API key configuration step.
     *
     * @since 1.2.0
     * @return void
     */
    private function handle_api_key_step() {
        if (!empty($_POST['aca_ai_content_agent_gemini_api_key'])) {
            $api_key = sanitize_text_field(wp_unslash($_POST['aca_ai_content_agent_gemini_api_key']));
            
            // Validate API key format
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
                'timeout' => 15,
            ]);

            if (is_wp_error($test_response) || wp_remote_retrieve_response_code($test_response) !== 200) {
                wp_die(__('API key test failed. Please check your key and try again.', 'aca-ai-content-agent'));
            }

            update_option('aca_ai_content_agent_gemini_api_key', $api_key);
            $this->step = 2;
        }
    }

    /**
     * Handle content settings step.
     *
     * @since 1.2.0
     * @return void
     */
    private function handle_content_settings_step() {
        $content_type = sanitize_text_field(wp_unslash($_POST['aca_ai_content_agent_content_type'] ?? 'blog'));
        $target_audience = sanitize_text_field(wp_unslash($_POST['aca_ai_content_agent_target_audience'] ?? 'general'));
        $content_tone = sanitize_text_field(wp_unslash($_POST['aca_ai_content_agent_content_tone'] ?? 'professional'));

        update_option('aca_ai_content_agent_content_type', $content_type);
        update_option('aca_ai_content_agent_target_audience', $target_audience);
        update_option('aca_ai_content_agent_content_tone', $content_tone);

        $this->step = 3;
    }

    /**
     * Handle working mode step.
     *
     * @since 1.2.0
     * @return void
     */
    private function handle_working_mode_step() {
        $working_mode = sanitize_text_field(wp_unslash($_POST['aca_ai_content_agent_working_mode'] ?? 'manual'));
        $auto_generate = isset($_POST['aca_ai_content_agent_auto_generate']) ? 1 : 0;
        $daily_limit = absint($_POST['aca_ai_content_agent_daily_limit'] ?? 10);

        update_option('aca_ai_content_agent_working_mode', $working_mode);
        update_option('aca_ai_content_agent_auto_generate', $auto_generate);
        update_option('aca_ai_content_agent_daily_limit', $daily_limit);

        $this->step = 4;
    }

    /**
     * Handle final step.
     *
     * @since 1.2.0
     * @return void
     */
    private function handle_final_step() {
        // Mark onboarding as completed
        update_option('aca_ai_content_agent_onboarding_completed', true);
        update_option('aca_ai_content_agent_onboarding_date', current_time('mysql'));

        // Redirect to dashboard
        wp_redirect(admin_url('admin.php?page=aca-ai-content-agent&onboarding=completed'));
        exit;
    }

    /**
     * Render the onboarding page.
     *
     * @since 1.2.0
     * @return void
     */
    public function render_onboarding_page() {
        ?>
        <div class="wrap aca-onboarding">
            <div class="aca-onboarding-header">
                <h1>
                    <span class="dashicons dashicons-robot" style="margin-right: 10px; color: #667eea;"></span>
                    <?php esc_html_e('Welcome to ACA AI Content Agent', 'aca-ai-content-agent'); ?>
                </h1>
                <p class="aca-onboarding-subtitle">
                    <?php esc_html_e('Let\'s get you started with AI-powered content creation in just a few steps!', 'aca-ai-content-agent'); ?>
                </p>
            </div>

            <div class="aca-onboarding-progress">
                <?php for ($i = 1; $i <= $this->total_steps; $i++): ?>
                    <div class="aca-progress-step <?php echo $i < $this->step ? 'completed' : ($i === $this->step ? 'active' : ''); ?>">
                        <?php echo $i; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <div class="aca-onboarding-step">
                <form method="post" action="" id="aca-onboarding-form">
                    <?php wp_nonce_field('aca_ai_content_agent_onboarding_nonce'); ?>
                    <input type="hidden" name="aca_ai_content_agent_onboarding_step" value="<?php echo esc_attr($this->step); ?>">

                    <?php switch ($this->step): 
                        case 1: ?>
                            <div class="aca-step-content">
                                <h2><?php esc_html_e('Step 1: Connect Your AI', 'aca-ai-content-agent'); ?></h2>
                                <p><?php esc_html_e('First, we need to connect to Google Gemini AI. Please enter your API key below.', 'aca-ai-content-agent'); ?></p>
                                
                                <div class="aca-form-row">
                                    <label for="aca_ai_content_agent_gemini_api_key">
                                        <?php esc_html_e('Google Gemini API Key', 'aca-ai-content-agent'); ?>
                                    </label>
                                    <input type="password" 
                                           id="aca_ai_content_agent_gemini_api_key" 
                                           name="aca_ai_content_agent_gemini_api_key" 
                                           class="aca-form-input" 
                                           required 
                                           placeholder="<?php esc_attr_e('Enter your API key here...', 'aca-ai-content-agent'); ?>">
                                    <p class="aca-form-help">
                                        <?php esc_html_e('Don\'t have an API key?', 'aca-ai-content-agent'); ?> 
                                        <a href="https://makersuite.google.com/app/apikey" target="_blank">
                                            <?php esc_html_e('Get one from Google AI Studio', 'aca-ai-content-agent'); ?>
                                        </a>
                                    </p>
                                </div>

                                <div class="aca-step-actions">
                                    <button type="button" class="aca-action-button secondary" id="test-api-connection">
                                        <span class="dashicons dashicons-admin-network"></span>
                                        <?php esc_html_e('Test Connection', 'aca-ai-content-agent'); ?>
                                    </button>
                                    <button type="submit" class="aca-action-button">
                                        <span class="dashicons dashicons-arrow-right-alt"></span>
                                        <?php esc_html_e('Next Step', 'aca-ai-content-agent'); ?>
                                    </button>
                                </div>
                            </div>
                            <?php break; ?>

                        <?php case 2: ?>
                            <div class="aca-step-content">
                                <h2><?php esc_html_e('Step 2: Content Preferences', 'aca-ai-content-agent'); ?></h2>
                                <p><?php esc_html_e('Tell us about your content preferences to help us generate better ideas.', 'aca-ai-content-agent'); ?></p>
                                
                                <div class="aca-form-row">
                                    <label for="aca_ai_content_agent_content_type">
                                        <?php esc_html_e('Content Type', 'aca-ai-content-agent'); ?>
                                    </label>
                                    <select id="aca_ai_content_agent_content_type" name="aca_ai_content_agent_content_type" class="aca-form-select">
                                        <option value="blog"><?php esc_html_e('Blog Posts', 'aca-ai-content-agent'); ?></option>
                                        <option value="article"><?php esc_html_e('Articles', 'aca-ai-content-agent'); ?></option>
                                        <option value="newsletter"><?php esc_html_e('Newsletters', 'aca-ai-content-agent'); ?></option>
                                        <option value="social"><?php esc_html_e('Social Media', 'aca-ai-content-agent'); ?></option>
                                        <option value="product"><?php esc_html_e('Product Descriptions', 'aca-ai-content-agent'); ?></option>
                                    </select>
                                </div>

                                <div class="aca-form-row">
                                    <label for="aca_ai_content_agent_target_audience">
                                        <?php esc_html_e('Target Audience', 'aca-ai-content-agent'); ?>
                                    </label>
                                    <select id="aca_ai_content_agent_target_audience" name="aca_ai_content_agent_target_audience" class="aca-form-select">
                                        <option value="general"><?php esc_html_e('General Audience', 'aca-ai-content-agent'); ?></option>
                                        <option value="professionals"><?php esc_html_e('Professionals', 'aca-ai-content-agent'); ?></option>
                                        <option value="beginners"><?php esc_html_e('Beginners', 'aca-ai-content-agent'); ?></option>
                                        <option value="experts"><?php esc_html_e('Experts', 'aca-ai-content-agent'); ?></option>
                                        <option value="business"><?php esc_html_e('Business', 'aca-ai-content-agent'); ?></option>
                                    </select>
                                </div>

                                <div class="aca-form-row">
                                    <label for="aca_ai_content_agent_content_tone">
                                        <?php esc_html_e('Content Tone', 'aca-ai-content-agent'); ?>
                                    </label>
                                    <select id="aca_ai_content_agent_content_tone" name="aca_ai_content_agent_content_tone" class="aca-form-select">
                                        <option value="professional"><?php esc_html_e('Professional', 'aca-ai-content-agent'); ?></option>
                                        <option value="casual"><?php esc_html_e('Casual', 'aca-ai-content-agent'); ?></option>
                                        <option value="friendly"><?php esc_html_e('Friendly', 'aca-ai-content-agent'); ?></option>
                                        <option value="authoritative"><?php esc_html_e('Authoritative', 'aca-ai-content-agent'); ?></option>
                                        <option value="conversational"><?php esc_html_e('Conversational', 'aca-ai-content-agent'); ?></option>
                                    </select>
                                </div>

                                <div class="aca-step-actions">
                                    <button type="button" class="aca-action-button secondary" onclick="history.back()">
                                        <span class="dashicons dashicons-arrow-left-alt"></span>
                                        <?php esc_html_e('Previous Step', 'aca-ai-content-agent'); ?>
                                    </button>
                                    <button type="submit" class="aca-action-button">
                                        <span class="dashicons dashicons-arrow-right-alt"></span>
                                        <?php esc_html_e('Next Step', 'aca-ai-content-agent'); ?>
                                    </button>
                                </div>
                            </div>
                            <?php break; ?>

                        <?php case 3: ?>
                            <div class="aca-step-content">
                                <h2><?php esc_html_e('Step 3: Working Mode', 'aca-ai-content-agent'); ?></h2>
                                <p><?php esc_html_e('Choose how you want the AI to work with you.', 'aca-ai-content-agent'); ?></p>
                                
                                <div class="aca-form-row">
                                    <label for="aca_ai_content_agent_working_mode">
                                        <?php esc_html_e('Working Mode', 'aca-ai-content-agent'); ?>
                                    </label>
                                    <select id="aca_ai_content_agent_working_mode" name="aca_ai_content_agent_working_mode" class="aca-form-select">
                                        <option value="manual"><?php esc_html_e('Manual - I\'ll generate ideas when needed', 'aca-ai-content-agent'); ?></option>
                                        <option value="semi-auto"><?php esc_html_e('Semi-Automatic - Generate ideas daily', 'aca-ai-content-agent'); ?></option>
                                        <option value="auto"><?php esc_html_e('Automatic - Full automation', 'aca-ai-content-agent'); ?></option>
                                    </select>
                                </div>

                                <div class="aca-form-row">
                                    <label class="aca-checkbox-label">
                                        <input type="checkbox" name="aca_ai_content_agent_auto_generate" value="1">
                                        <span class="aca-checkbox-text">
                                            <?php esc_html_e('Enable automatic content generation', 'aca-ai-content-agent'); ?>
                                        </span>
                                    </label>
                                </div>

                                <div class="aca-form-row">
                                    <label for="aca_ai_content_agent_daily_limit">
                                        <?php esc_html_e('Daily Generation Limit', 'aca-ai-content-agent'); ?>
                                    </label>
                                    <input type="number" 
                                           id="aca_ai_content_agent_daily_limit" 
                                           name="aca_ai_content_agent_daily_limit" 
                                           class="aca-form-input" 
                                           value="10" 
                                           min="1" 
                                           max="50">
                                    <p class="aca-form-help">
                                        <?php esc_html_e('Maximum number of ideas to generate per day', 'aca-ai-content-agent'); ?>
                                    </p>
                                </div>

                                <div class="aca-step-actions">
                                    <button type="button" class="aca-action-button secondary" onclick="history.back()">
                                        <span class="dashicons dashicons-arrow-left-alt"></span>
                                        <?php esc_html_e('Previous Step', 'aca-ai-content-agent'); ?>
                                    </button>
                                    <button type="submit" class="aca-action-button">
                                        <span class="dashicons dashicons-arrow-right-alt"></span>
                                        <?php esc_html_e('Next Step', 'aca-ai-content-agent'); ?>
                                    </button>
                                </div>
                            </div>
                            <?php break; ?>

                        <?php case 4: ?>
                            <div class="aca-step-content">
                                <h2><?php esc_html_e('Step 4: You\'re All Set!', 'aca-ai-content-agent'); ?></h2>
                                <p><?php esc_html_e('Congratulations! Your ACA AI Content Agent is ready to help you create amazing content.', 'aca-ai-content-agent'); ?></p>
                                
                                <div class="aca-success-summary">
                                    <div class="aca-summary-item">
                                        <span class="dashicons dashicons-yes-alt" style="color: #22c55e;"></span>
                                        <span><?php esc_html_e('API Connection Established', 'aca-ai-content-agent'); ?></span>
                                    </div>
                                    <div class="aca-summary-item">
                                        <span class="dashicons dashicons-yes-alt" style="color: #22c55e;"></span>
                                        <span><?php esc_html_e('Content Preferences Saved', 'aca-ai-content-agent'); ?></span>
                                    </div>
                                    <div class="aca-summary-item">
                                        <span class="dashicons dashicons-yes-alt" style="color: #22c55e;"></span>
                                        <span><?php esc_html_e('Working Mode Configured', 'aca-ai-content-agent'); ?></span>
                                    </div>
                                </div>

                                <div class="aca-getting-started">
                                    <h3><?php esc_html_e('What\'s Next?', 'aca-ai-content-agent'); ?></h3>
                                    <ul>
                                        <li><?php esc_html_e('Generate your first content ideas', 'aca-ai-content-agent'); ?></li>
                                        <li><?php esc_html_e('Explore the dashboard features', 'aca-ai-content-agent'); ?></li>
                                        <li><?php esc_html_e('Customize your prompts and settings', 'aca-ai-content-agent'); ?></li>
                                        <li><?php esc_html_e('Set up content clusters for SEO', 'aca-ai-content-agent'); ?></li>
                                    </ul>
                                </div>

                                <div class="aca-step-actions">
                                    <button type="button" class="aca-action-button secondary" onclick="history.back()">
                                        <span class="dashicons dashicons-arrow-left-alt"></span>
                                        <?php esc_html_e('Previous Step', 'aca-ai-content-agent'); ?>
                                    </button>
                                    <button type="submit" class="aca-action-button">
                                        <span class="dashicons dashicons-admin-home"></span>
                                        <?php esc_html_e('Go to Dashboard', 'aca-ai-content-agent'); ?>
                                    </button>
                                </div>
                            </div>
                            <?php break; ?>

                    <?php endswitch; ?>
                </form>
            </div>
        </div>
        <?php
    }
}