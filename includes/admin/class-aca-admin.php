<?php
/**
 * ACA - AI Content Agent
 *
 * Main Admin Class
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Admin {

    public function __construct() {
        try {
            $this->init_hooks();
            add_action('admin_notices', array($this, 'capability_notice'));
        } catch (Exception $e) {
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::error('Admin class initialization failed: ' . $e->getMessage());
            }
        }
    }

    private function init_hooks() {
        try {
            add_action( 'admin_init', [ $this, 'register_core_settings' ] );
            
            // FIX: Initialize admin components in correct order
            // First initialize admin menu (this creates the menu structure)
            new ACA_Admin_Menu();
            
            // Then initialize assets handler (this enqueues CSS/JS for menu pages)
            ACA_Admin_Assets::init();
            
            // Initialize other admin components
            new ACA_Admin_Notices();
            new ACA_Ajax_Handler();
            
            // Initialize settings classes
            new ACA_Settings_Api();
            new ACA_Settings_Automation();
            new ACA_Settings_Analysis();
            new ACA_Settings_Enrichment();
            new ACA_Settings_Management();
            new ACA_Settings_Prompts();
            new ACA_Settings_License();
            
            // Initialize onboarding
            new ACA_Onboarding();
            
            // Initialize post hooks
            if (class_exists('ACA_Post_Hooks')) {
                $post_hooks = new ACA_Post_Hooks();
                $post_hooks->init();
            }
        } catch (Exception $e) {
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::error('Admin hooks initialization failed: ' . $e->getMessage());
            }
        }
    }

    /**
     * Register the core settings required by the plugin settings page.
     * This tells WordPress which options are allowed to be saved for our settings group.
     */
    public function register_core_settings() {
        register_setting(
            'aca_ai_content_agent_settings_group',
            'aca_ai_content_agent_gemini_api_key',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_api_key'),
                'default' => ''
            )
        );

        register_setting(
            'aca_ai_content_agent_settings_group',
            'aca_ai_content_agent_options',
            array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_options'),
                'default' => array()
            )
        );

        register_setting(
            'aca_ai_content_agent_settings_group',
            'aca_ai_content_agent_license_key',
            array(
                'type' => 'string',
                'sanitize_callback' => array($this, 'sanitize_license_key'),
                'default' => ''
            )
        );

        register_setting(
            'aca_ai_content_agent_settings_group',
            'aca_ai_content_agent_is_pro_active',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'false'
            )
        );
    }

    /**
     * Sanitize API key input.
     */
    public function sanitize_api_key($input) {
        try {
            $input = sanitize_text_field($input);
            
            // If the input is not empty, encrypt it
            if (!empty($input)) {
                $input = ACA_Encryption_Util::encrypt($input);
            }
            
            return $input;
        } catch (Exception $e) {
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::error('API key sanitization failed: ' . $e->getMessage());
            }
            return '';
        }
    }

    /**
     * Sanitize options array.
     */
    public function sanitize_options($input) {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        $existing_options = get_option('aca_ai_content_agent_options', array());
        
        // Sanitize each option
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'idea_generation_limit':
                case 'content_generation_limit':
                case 'api_monthly_limit':
                    $sanitized[$key] = intval($value);
                    break;
                case 'working_mode':
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
                case 'auto_generate':
                case 'enable_analytics':
                    $sanitized[$key] = (bool) $value;
                    break;
                case 'copyscape_api_key':
                case 'gsc_api_key':
                case 'pexels_api_key':
                case 'openai_api_key':
                    // Handle API keys - only encrypt if not empty
                    if (!empty(trim($value))) {
                        $sanitized[$key] = ACA_Encryption_Util::encrypt(sanitize_text_field($value));
                    } else {
                        // Keep existing encrypted key if input is empty
                        $sanitized[$key] = $existing_options[$key] ?? '';
                    }
                    break;
                default:
                    $sanitized[$key] = sanitize_text_field($value);
                    break;
            }
        }
        
        // Merge with existing options to preserve unchanged values
        return array_merge($existing_options, $sanitized);
    }

    /**
     * Sanitize license key input.
     */
    public function sanitize_license_key($input) {
        try {
            $input = sanitize_text_field($input);
            
            // If the input is not empty, encrypt it
            if (!empty($input)) {
                $input = ACA_Encryption_Util::encrypt($input);
            }
            
            return $input;
        } catch (Exception $e) {
            if (class_exists('ACA_Log_Service')) {
                ACA_Log_Service::error('License key sanitization failed: ' . $e->getMessage());
            }
            return '';
        }
    }

    /**
     * Display capability notice.
     */
    public function capability_notice() {
        // Only show notice on ACA plugin pages
        $screen = get_current_screen();
        if (!$screen || strpos($screen->id, 'aca-ai-content-agent') === false) {
            return;
        }
        
        // Check if user has proper capabilities for the current page
        $required_capability = 'edit_posts';
        if (strpos($screen->id, 'settings') !== false || strpos($screen->id, 'license') !== false || strpos($screen->id, 'diagnostics') !== false) {
            $required_capability = 'manage_options';
        }
        
        if (!current_user_can($required_capability)) {
            echo '<div class="notice notice-error is-dismissible"><p>' . 
                 sprintf(
                     esc_html__('ACA: You need %s permission to access this page. Please contact your administrator.', 'aca-ai-content-agent'),
                     '<strong>' . $required_capability . '</strong>'
                 ) . 
                 '</p></div>';
        }
    }
}
