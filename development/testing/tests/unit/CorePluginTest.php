<?php
/**
 * Core Plugin Unit Tests
 * 
 * Tests for basic plugin functionality and architecture
 */

require_once dirname(__DIR__) . '/BaseTest.php';

class CorePluginTest extends BaseTest {
    
    protected function setUp() {
        // Setup before all tests
    }
    
    public function testPluginConstantsAreDefined() {
        $this->assertConstantDefined('ACA_AI_CONTENT_AGENT_VERSION', 'Plugin version constant should be defined');
        $this->assertConstantDefined('ACA_AI_CONTENT_AGENT_PLUGIN_DIR', 'Plugin directory constant should be defined');
        $this->assertConstantDefined('ACA_AI_CONTENT_AGENT_PLUGIN_FILE', 'Plugin file constant should be defined');
        $this->assertConstantDefined('ACA_AI_CONTENT_AGENT_DEV_MODE', 'Dev mode constant should be defined');
    }
    
    public function testPluginConstantValues() {
        $this->assertEqual('1.3', ACA_AI_CONTENT_AGENT_VERSION, 'Plugin version should be 1.3');
        $this->assertTrue(ACA_AI_CONTENT_AGENT_DEV_MODE, 'Dev mode should be enabled in tests');
        $this->assertStringContains('aca-ai-content-agent', ACA_AI_CONTENT_AGENT_PLUGIN_DIR, 'Plugin directory should contain plugin name');
    }
    
    public function testCoreClassesExist() {
        $core_classes = [
            'ACA_Plugin',
            'ACA_Encryption_Util',
            'ACA_Helper',
            'ACA_Gemini_Api',
            'ACA_Gumroad_Api',
            'ACA_Idea_Service',
            'ACA_Draft_Service',
            'ACA_Style_Guide_Service'
        ];
        
        foreach ($core_classes as $class) {
            $this->assertClassExists($class, "Core class {$class} should exist");
        }
    }
    
    public function testPluginMainClassCanBeInstantiated() {
        if (class_exists('ACA_Plugin')) {
            $plugin = ACA_Plugin::instance();
            $this->assertNotNull($plugin, 'Plugin instance should not be null');
            $this->assertTrue($plugin instanceof ACA_Plugin, 'Plugin should be instance of ACA_Plugin');
            
            // Test singleton pattern
            $plugin2 = ACA_Plugin::instance();
            $this->assertTrue($plugin === $plugin2, 'Plugin should follow singleton pattern');
        } else {
            $this->skip('ACA_Plugin class not loaded');
        }
    }
    
    public function testWordPressFunctionsAreMocked() {
        $wp_functions = [
            'add_action',
            'add_filter',
            'get_option',
            'update_option',
            'current_user_can',
            'wp_verify_nonce',
            'esc_html',
            '__'
        ];
        
        foreach ($wp_functions as $function) {
            $this->assertFunctionExists($function, "WordPress function {$function} should be mocked");
        }
    }
    
    public function testDatabaseMockIsWorking() {
        global $wpdb;
        
        $this->assertNotNull($wpdb, 'Global $wpdb should be available');
        $this->assertTrue($wpdb instanceof MockWPDB, '$wpdb should be MockWPDB instance');
        $this->assertEqual('wp_', $wpdb->prefix, 'Database prefix should be wp_');
        
        // Test mock database operations
        $result = $wpdb->query("SELECT * FROM test");
        $this->assertTrue($result, 'Mock database query should return true');
        
        $insert_result = $wpdb->insert('test_table', ['column' => 'value']);
        $this->assertTrue($insert_result, 'Mock database insert should return true');
    }
    
    public function testOptionsAPIWorking() {
        // Test WordPress options API
        $test_option = 'test_option_' . time();
        $test_value = 'test_value_' . rand(1000, 9999);
        
        // Test update_option
        $update_result = update_option($test_option, $test_value);
        $this->assertTrue($update_result, 'update_option should return true');
        
        // Test get_option
        $retrieved_value = get_option($test_option);
        $this->assertEqual($test_value, $retrieved_value, 'Retrieved option value should match set value');
        
        // Test get_option with default
        $default_value = 'default_test';
        $non_existent_value = get_option('non_existent_option', $default_value);
        $this->assertEqual($default_value, $non_existent_value, 'Non-existent option should return default value');
        
        // Test delete_option
        $delete_result = delete_option($test_option);
        $this->assertTrue($delete_result, 'delete_option should return true');
        
        $deleted_value = get_option($test_option, 'not_found');
        $this->assertEqual('not_found', $deleted_value, 'Deleted option should return default value');
    }
    
    public function testTransientsAPIWorking() {
        $transient_name = 'test_transient_' . time();
        $transient_value = 'transient_value_' . rand(1000, 9999);
        
        // Test set_transient
        $set_result = set_transient($transient_name, $transient_value, 3600);
        $this->assertTrue($set_result, 'set_transient should return true');
        
        // Test get_transient
        $retrieved_value = get_transient($transient_name);
        $this->assertEqual($transient_value, $retrieved_value, 'Retrieved transient value should match set value');
        
        // Test delete_transient
        $delete_result = delete_transient($transient_name);
        $this->assertTrue($delete_result, 'delete_transient should return true');
        
        $deleted_value = get_transient($transient_name);
        $this->assertFalse($deleted_value, 'Deleted transient should return false');
    }
    
    public function testSecurityFunctionsWorking() {
        // Test nonce functions
        $action = 'test_action';
        $nonce = wp_create_nonce($action);
        $this->assertNotNull($nonce, 'wp_create_nonce should return a nonce');
        $this->assertStringContains('mock_nonce_', $nonce, 'Mock nonce should contain expected prefix');
        
        $verify_result = wp_verify_nonce($nonce, $action);
        $this->assertTrue($verify_result, 'wp_verify_nonce should return true for valid nonce');
        
        // Test capability functions
        $can_manage = current_user_can('manage_options');
        $this->assertTrue($can_manage, 'current_user_can should return true in test environment');
        
        // Test admin functions
        $is_admin = is_admin();
        $this->assertTrue($is_admin, 'is_admin should return true in test environment');
    }
    
    public function testSanitizationFunctionsWorking() {
        // Test sanitize_text_field
        $dirty_text = '<script>alert("xss")</script>Hello World';
        $clean_text = sanitize_text_field($dirty_text);
        $this->assertFalse(strpos($clean_text, '<script>') !== false, 'Script tags should be removed');
        $this->assertStringContains('Hello World', $clean_text, 'Clean text should remain');
        
        // Test sanitize_email
        $dirty_email = 'test@example.com<script>';
        $clean_email = sanitize_email($dirty_email);
        $this->assertEqual('test@example.com', $clean_email, 'Email should be sanitized');
        
        // Test escaping functions
        $html_text = '<div>Test & Content</div>';
        $escaped_html = esc_html($html_text);
        $this->assertStringContains('&lt;div&gt;', $escaped_html, 'HTML should be escaped');
        $this->assertStringContains('&amp;', $escaped_html, 'Ampersand should be escaped');
    }
    
    public function testInternationalizationFunctionsWorking() {
        $text_domain = 'aca-ai-content-agent';
        
        // Test __() function
        $translated = __('Hello World', $text_domain);
        $this->assertEqual('Hello World', $translated, '__() should return original text in test environment');
        
        // Test esc_html__() function
        $escaped_translated = esc_html__('<b>Hello World</b>', $text_domain);
        $this->assertStringContains('&lt;b&gt;', $escaped_translated, 'esc_html__() should escape HTML');
    }
    
    public function testHTTPAPIWorking() {
        $test_url = 'https://api.example.com/test';
        
        // Test wp_remote_get
        $response = wp_remote_get($test_url);
        $this->assertNotNull($response, 'wp_remote_get should return response');
        $this->assertArrayHasKey('response', $response, 'Response should have response key');
        $this->assertArrayHasKey('body', $response, 'Response should have body key');
        
        $response_code = wp_remote_retrieve_response_code($response);
        $this->assertEqual(200, $response_code, 'Mock response should return 200');
        
        $response_body = wp_remote_retrieve_body($response);
        $this->assertStringContains('mock', $response_body, 'Mock response body should contain mock data');
        
        // Test wp_remote_post
        $post_response = wp_remote_post($test_url, ['body' => 'test data']);
        $this->assertNotNull($post_response, 'wp_remote_post should return response');
    }
}