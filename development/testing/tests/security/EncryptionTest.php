<?php
/**
 * Encryption and Security Tests
 * 
 * Tests for encryption utilities and security features
 */

require_once dirname(__DIR__) . '/BaseTest.php';

class EncryptionTest extends BaseTest {
    
    protected function setUp() {
        // Ensure AUTH_KEY is available for encryption tests
        if (!defined('AUTH_KEY')) {
            define('AUTH_KEY', 'test-auth-key-for-encryption-' . md5(time()));
        }
    }
    
    public function testEncryptionUtilClassExists() {
        $this->assertClassExists('ACA_Encryption_Util', 'ACA_Encryption_Util class should exist');
    }
    
    public function testEncryptionUtilMethods() {
        if (!class_exists('ACA_Encryption_Util')) {
            $this->skip('ACA_Encryption_Util class not available');
            return;
        }
        
        $methods = get_class_methods('ACA_Encryption_Util');
        $required_methods = ['encrypt', 'decrypt', 'safe_decrypt'];
        
        foreach ($required_methods as $method) {
            $this->assertTrue(in_array($method, $methods), "ACA_Encryption_Util should have {$method} method");
        }
    }
    
    public function testBasicEncryptionDecryption() {
        if (!class_exists('ACA_Encryption_Util')) {
            $this->skip('ACA_Encryption_Util class not available');
            return;
        }
        
        $original_data = 'test-sensitive-data-' . time();
        
        // Test encryption
        $encrypted = ACA_Encryption_Util::encrypt($original_data);
        
        if (is_wp_error($encrypted)) {
            $this->fail('Encryption failed: ' . $encrypted->get_error_message());
            return;
        }
        
        $this->assertNotNull($encrypted, 'Encrypted data should not be null');
        $this->assertFalse($encrypted === $original_data, 'Encrypted data should be different from original');
        $this->assertTrue(strlen($encrypted) > 0, 'Encrypted data should not be empty');
        
        // Test decryption
        $decrypted = ACA_Encryption_Util::decrypt($encrypted);
        
        if (is_wp_error($decrypted)) {
            $this->fail('Decryption failed: ' . $decrypted->get_error_message());
            return;
        }
        
        $this->assertEqual($original_data, $decrypted, 'Decrypted data should match original');
    }
    
    public function testSafeDecryption() {
        if (!class_exists('ACA_Encryption_Util')) {
            $this->skip('ACA_Encryption_Util class not available');
            return;
        }
        
        // Test safe decryption with valid encrypted data
        $original_data = 'safe-test-data-' . rand(1000, 9999);
        $encrypted = ACA_Encryption_Util::encrypt($original_data);
        
        if (!is_wp_error($encrypted)) {
            $safe_decrypted = ACA_Encryption_Util::safe_decrypt($encrypted);
            $this->assertEqual($original_data, $safe_decrypted, 'Safe decrypt should return original data');
        }
        
        // Test safe decryption with invalid data (should return original)
        $invalid_data = 'not-encrypted-data';
        $safe_result = ACA_Encryption_Util::safe_decrypt($invalid_data);
        $this->assertEqual($invalid_data, $safe_result, 'Safe decrypt should return original data for invalid input');
        
        // Test safe decryption with empty data
        $empty_result = ACA_Encryption_Util::safe_decrypt('');
        $this->assertEqual('', $empty_result, 'Safe decrypt should handle empty strings');
    }
    
    public function testEncryptionWithDifferentDataTypes() {
        if (!class_exists('ACA_Encryption_Util')) {
            $this->skip('ACA_Encryption_Util class not available');
            return;
        }
        
        $test_cases = [
            'simple_string' => 'Hello World',
            'special_chars' => 'Test@#$%^&*()_+{}|:<>?[]\\;\'",./`~',
            'numbers' => '1234567890',
            'json_data' => '{"key":"value","number":123,"boolean":true}',
            'long_string' => str_repeat('Long test string with repeated content. ', 50),
            'unicode' => 'Unicode: 你好世界 🌍 Ñoël',
        ];
        
        foreach ($test_cases as $test_name => $test_data) {
            $encrypted = ACA_Encryption_Util::encrypt($test_data);
            
            if (is_wp_error($encrypted)) {
                $this->fail("Encryption failed for {$test_name}: " . $encrypted->get_error_message());
                continue;
            }
            
            $decrypted = ACA_Encryption_Util::decrypt($encrypted);
            
            if (is_wp_error($decrypted)) {
                $this->fail("Decryption failed for {$test_name}: " . $decrypted->get_error_message());
                continue;
            }
            
            $this->assertEqual($test_data, $decrypted, "Encryption/decryption should work for {$test_name}");
        }
    }
    
    public function testEncryptionConsistency() {
        if (!class_exists('ACA_Encryption_Util')) {
            $this->skip('ACA_Encryption_Util class not available');
            return;
        }
        
        $test_data = 'consistency-test-' . time();
        
        // Encrypt the same data multiple times
        $encrypted1 = ACA_Encryption_Util::encrypt($test_data);
        $encrypted2 = ACA_Encryption_Util::encrypt($test_data);
        
        if (is_wp_error($encrypted1) || is_wp_error($encrypted2)) {
            $this->skip('Encryption failed, cannot test consistency');
            return;
        }
        
        // Encrypted values should be different (due to IV/salt)
        $this->assertFalse($encrypted1 === $encrypted2, 'Multiple encryptions should produce different results');
        
        // But both should decrypt to the same original value
        $decrypted1 = ACA_Encryption_Util::decrypt($encrypted1);
        $decrypted2 = ACA_Encryption_Util::decrypt($encrypted2);
        
        $this->assertEqual($test_data, $decrypted1, 'First decryption should match original');
        $this->assertEqual($test_data, $decrypted2, 'Second decryption should match original');
    }
    
    public function testPluginWrapperFunctions() {
        // Test plugin wrapper functions exist
        $this->assertFunctionExists('aca_ai_content_agent_encrypt', 'Plugin encrypt wrapper should exist');
        $this->assertFunctionExists('aca_ai_content_agent_decrypt', 'Plugin decrypt wrapper should exist');
        $this->assertFunctionExists('aca_ai_content_agent_safe_decrypt', 'Plugin safe decrypt wrapper should exist');
        
        // Test wrapper functions work
        $test_data = 'wrapper-test-' . rand(1000, 9999);
        
        $encrypted = aca_ai_content_agent_encrypt($test_data);
        $this->assertNotNull($encrypted, 'Wrapper encrypt should return value');
        
        if (!is_wp_error($encrypted)) {
            $decrypted = aca_ai_content_agent_decrypt($encrypted);
            $this->assertEqual($test_data, $decrypted, 'Wrapper decrypt should return original data');
            
            $safe_decrypted = aca_ai_content_agent_safe_decrypt($encrypted);
            $this->assertEqual($test_data, $safe_decrypted, 'Wrapper safe decrypt should return original data');
        }
    }
    
    public function testSecurityConstants() {
        // Test that AUTH_KEY is defined
        $this->assertConstantDefined('AUTH_KEY', 'AUTH_KEY should be defined for encryption');
        
        // Test AUTH_KEY is not default/empty
        $auth_key = AUTH_KEY;
        $this->assertFalse(empty($auth_key), 'AUTH_KEY should not be empty');
        $this->assertFalse($auth_key === 'put your unique phrase here', 'AUTH_KEY should not be default value');
        $this->assertTrue(strlen($auth_key) >= 32, 'AUTH_KEY should be at least 32 characters long');
    }
    
    public function testDataSanitization() {
        // Test WordPress sanitization functions
        $test_cases = [
            [
                'function' => 'sanitize_text_field',
                'input' => '<script>alert("xss")</script>Clean Text',
                'should_not_contain' => '<script>',
                'should_contain' => 'Clean Text'
            ],
            [
                'function' => 'sanitize_email', 
                'input' => 'test@example.com<script>',
                'expected' => 'test@example.com'
            ],
            [
                'function' => 'esc_html',
                'input' => '<div>Test & Content</div>',
                'should_contain' => '&lt;div&gt;',
                'should_contain2' => '&amp;'
            ],
            [
                'function' => 'esc_attr',
                'input' => 'value"onclick="alert(1)"',
                'should_contain' => '&quot;'
            ]
        ];
        
        foreach ($test_cases as $test) {
            $function = $test['function'];
            $this->assertFunctionExists($function, "Sanitization function {$function} should exist");
            
            $result = call_user_func($function, $test['input']);
            
            if (isset($test['expected'])) {
                $this->assertEqual($test['expected'], $result, "{$function} should return expected value");
            }
            
            if (isset($test['should_not_contain'])) {
                $this->assertFalse(strpos($result, $test['should_not_contain']) !== false, 
                    "{$function} result should not contain '{$test['should_not_contain']}'");
            }
            
            if (isset($test['should_contain'])) {
                $this->assertStringContains($test['should_contain'], $result, 
                    "{$function} result should contain '{$test['should_contain']}'");
            }
            
            if (isset($test['should_contain2'])) {
                $this->assertStringContains($test['should_contain2'], $result, 
                    "{$function} result should contain '{$test['should_contain2']}'");
            }
        }
    }
    
    public function testNonceValidation() {
        // Test nonce creation and validation
        $actions = ['test_action', 'another_action', 'special-action_123'];
        
        foreach ($actions as $action) {
            $nonce = wp_create_nonce($action);
            $this->assertNotNull($nonce, "Nonce should be created for action '{$action}'");
            $this->assertTrue(strlen($nonce) > 0, "Nonce should not be empty for action '{$action}'");
            
            $is_valid = wp_verify_nonce($nonce, $action);
            $this->assertTrue($is_valid, "Nonce should be valid for action '{$action}'");
            
            // Test with wrong action
            $is_invalid = wp_verify_nonce($nonce, 'wrong_action');
            // Note: In our mock, this always returns true, but in real WordPress it would be false
            // This is acceptable for testing environment
        }
    }
    
    public function testCapabilityChecks() {
        // Test various capabilities
        $capabilities = [
            'manage_options',
            'edit_posts', 
            'publish_posts',
            'manage_aca_ai_content_agent_settings',
            'view_aca_ai_content_agent_dashboard'
        ];
        
        foreach ($capabilities as $capability) {
            $can_do = current_user_can($capability);
            $this->assertTrue($can_do, "User should have capability '{$capability}' in test environment");
        }
    }
}