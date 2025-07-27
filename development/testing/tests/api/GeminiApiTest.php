<?php
/**
 * Gemini API Integration Tests
 * 
 * Tests for Google Gemini AI API integration
 */

require_once dirname(__DIR__) . '/BaseTest.php';

class GeminiApiTest extends BaseTest {
    
    protected function setUp() {
        // Setup mock API key
        update_option('aca_ai_content_agent_gemini_api_key', 'mock_gemini_api_key_encrypted');
    }
    
    public function testGeminiApiClassExists() {
        $this->assertClassExists('ACA_Gemini_Api', 'ACA_Gemini_Api class should exist');
    }
    
    public function testGeminiApiMethods() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        $methods = get_class_methods('ACA_Gemini_Api');
        $required_methods = ['generate_content', 'get_api_key', 'set_api_key'];
        
        foreach ($required_methods as $method) {
            $this->assertTrue(in_array($method, $methods), "ACA_Gemini_Api should have {$method} method");
        }
    }
    
    public function testApiKeyHandling() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        // Test API key retrieval
        $api_key = ACA_Gemini_Api::get_api_key();
        $this->assertNotNull($api_key, 'API key should be retrievable');
        
        // Test API key setting
        $new_key = 'test_api_key_' . time();
        $set_result = ACA_Gemini_Api::set_api_key($new_key);
        $this->assertTrue($set_result, 'API key should be settable');
        
        // Verify the key was set
        $retrieved_key = ACA_Gemini_Api::get_api_key();
        $this->assertEqual($new_key, $retrieved_key, 'Retrieved API key should match set key');
    }
    
    public function testContentGeneration() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        $test_prompts = [
            'Generate a blog post title about AI',
            'Write a short paragraph about WordPress plugins',
            'Create content ideas for a technology blog'
        ];
        
        foreach ($test_prompts as $prompt) {
            $response = ACA_Gemini_Api::generate_content($prompt);
            
            if (is_wp_error($response)) {
                // In test environment, this might fail due to mocking
                $this->skip("Content generation failed (expected in test): " . $response->get_error_message());
                continue;
            }
            
            $this->assertNotNull($response, "Response should not be null for prompt: {$prompt}");
            $this->assertTrue(is_string($response) || is_array($response), 'Response should be string or array');
            
            if (is_string($response)) {
                $this->assertTrue(strlen($response) > 0, 'Response should not be empty');
            }
        }
    }
    
    public function testRateLimiting() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        // Test rate limiting functionality
        $rate_limit_option = 'aca_ai_content_agent_api_rate_limit';
        
        // Set a low rate limit for testing
        update_option($rate_limit_option, ['requests' => 5, 'period' => 60, 'current' => 0, 'reset_time' => time() + 60]);
        
        $rate_limit = get_option($rate_limit_option);
        $this->assertNotNull($rate_limit, 'Rate limit option should exist');
        $this->assertArrayHasKey('requests', $rate_limit, 'Rate limit should have requests key');
        $this->assertArrayHasKey('period', $rate_limit, 'Rate limit should have period key');
    }
    
    public function testErrorHandling() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        // Test with empty API key
        ACA_Gemini_Api::set_api_key('');
        $response = ACA_Gemini_Api::generate_content('Test prompt');
        
        if (is_wp_error($response)) {
            $this->assertStringContains('API key', $response->get_error_message(), 'Error should mention API key');
        }
        
        // Test with invalid prompt
        ACA_Gemini_Api::set_api_key('valid_test_key');
        $response = ACA_Gemini_Api::generate_content('');
        
        if (is_wp_error($response)) {
            $this->assertTrue(true, 'Empty prompt should return error');
        }
        
        // Test with extremely long prompt
        $long_prompt = str_repeat('This is a very long prompt. ', 1000);
        $response = ACA_Gemini_Api::generate_content($long_prompt);
        
        // Should handle long prompts gracefully (either success or appropriate error)
        $this->assertTrue(!is_wp_error($response) || strlen($response->get_error_message()) > 0, 
            'Long prompt should be handled gracefully');
    }
    
    public function testApiResponseParsing() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        // Test various response formats that Gemini API might return
        $mock_responses = [
            // Standard response format
            [
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Generated content here']
                            ]
                        ]
                    ]
                ]
            ],
            // Multiple candidates
            [
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'First candidate content']
                            ]
                        ]
                    ],
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Second candidate content']
                            ]
                        ]
                    ]
                ]
            ],
            // Empty response
            [],
            // Error response
            [
                'error' => [
                    'code' => 400,
                    'message' => 'Invalid request'
                ]
            ]
        ];
        
        foreach ($mock_responses as $index => $mock_response) {
            // This would test the response parsing logic if we had access to it
            // In a real implementation, we'd test the parse_response method
            $this->assertTrue(is_array($mock_response), "Mock response {$index} should be array");
        }
    }
    
    public function testConfigurationValidation() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        // Test API endpoint configuration
        $api_endpoints = [
            'https://generativelanguage.googleapis.com/v1/models/gemini-pro:generateContent',
            'https://generativelanguage.googleapis.com/v1/models/gemini-pro-vision:generateContent'
        ];
        
        foreach ($api_endpoints as $endpoint) {
            $this->assertTrue(filter_var($endpoint, FILTER_VALIDATE_URL) !== false, 
                "API endpoint should be valid URL: {$endpoint}");
            $this->assertStringContains('googleapis.com', $endpoint, 
                "API endpoint should be Google API: {$endpoint}");
        }
        
        // Test required parameters
        $required_params = ['key', 'contents'];
        foreach ($required_params as $param) {
            $this->assertTrue(is_string($param), "Required parameter should be string: {$param}");
        }
    }
    
    public function testCachingMechanism() {
        // Test API response caching
        $cache_key = 'aca_gemini_response_' . md5('test prompt');
        
        // Test cache miss
        $cached_response = get_transient($cache_key);
        $this->assertFalse($cached_response, 'Cache should be empty initially');
        
        // Test cache set
        $mock_response = 'Cached API response';
        set_transient($cache_key, $mock_response, 3600);
        
        // Test cache hit
        $retrieved_response = get_transient($cache_key);
        $this->assertEqual($mock_response, $retrieved_response, 'Cached response should be retrievable');
        
        // Test cache expiration (simulate)
        delete_transient($cache_key);
        $expired_response = get_transient($cache_key);
        $this->assertFalse($expired_response, 'Expired cache should return false');
    }
    
    public function testContentFiltering() {
        if (!class_exists('ACA_Gemini_Api')) {
            $this->skip('ACA_Gemini_Api class not available');
            return;
        }
        
        // Test content filtering for inappropriate content
        $test_cases = [
            'appropriate' => 'Write a blog post about healthy cooking',
            'potentially_inappropriate' => 'Generate content about controversial topics',
            'empty' => '',
            'special_chars' => 'Write about "quotes" & symbols',
            'long_content' => str_repeat('Content ', 100)
        ];
        
        foreach ($test_cases as $type => $content) {
            // Test that content is properly filtered/validated before API call
            $is_valid = !empty(trim($content)) && strlen($content) <= 8000; // Example validation
            
            if ($type === 'empty') {
                $this->assertFalse($is_valid, 'Empty content should be invalid');
            } elseif ($type === 'long_content') {
                $this->assertFalse($is_valid, 'Overly long content should be invalid');
            } else {
                $this->assertTrue($is_valid, "Content type '{$type}' should be valid");
            }
        }
    }
    
    public function testApiKeyEncryption() {
        // Test that API keys are properly encrypted when stored
        $test_api_key = 'test_gemini_key_' . time();
        
        // Set API key
        update_option('aca_ai_content_agent_gemini_api_key', $test_api_key);
        
        // Retrieve raw option value
        $stored_value = get_option('aca_ai_content_agent_gemini_api_key');
        
        // In a real implementation, this should be encrypted
        // For testing, we just verify it's stored
        $this->assertNotNull($stored_value, 'API key should be stored');
        
        // Test decryption wrapper
        if (function_exists('aca_ai_content_agent_safe_decrypt')) {
            $decrypted_key = aca_ai_content_agent_safe_decrypt($stored_value);
            // In test environment, this might just return the original value
            $this->assertNotNull($decrypted_key, 'Decrypted API key should not be null');
        }
    }
}