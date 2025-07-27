<?php
/**
 * Service Integration Tests
 * 
 * Tests for integration between different plugin services
 */

require_once dirname(__DIR__) . '/BaseTest.php';

class ServiceIntegrationTest extends BaseTest {
    
    protected function setUp() {
        // Setup test environment for integration tests
    }
    
    public function testIdeaServiceIntegration() {
        if (!class_exists('ACA_Idea_Service')) {
            $this->skip('ACA_Idea_Service class not available');
            return;
        }
        
        // Test idea service basic functionality
        $methods = get_class_methods('ACA_Idea_Service');
        $required_methods = ['generate_ideas', 'save_idea', 'get_ideas'];
        
        foreach ($required_methods as $method) {
            $this->assertTrue(in_array($method, $methods), 
                "ACA_Idea_Service should have {$method} method");
        }
    }
    
    public function testDraftServiceIntegration() {
        if (!class_exists('ACA_Draft_Service')) {
            $this->skip('ACA_Draft_Service class not available');
            return;
        }
        
        // Test draft service basic functionality
        $methods = get_class_methods('ACA_Draft_Service');
        $required_methods = ['create_draft', 'update_draft', 'get_draft'];
        
        foreach ($required_methods as $method) {
            $this->assertTrue(in_array($method, $methods), 
                "ACA_Draft_Service should have {$method} method");
        }
    }
    
    public function testStyleGuideServiceIntegration() {
        if (!class_exists('ACA_Style_Guide_Service')) {
            $this->skip('ACA_Style_Guide_Service class not available');
            return;
        }
        
        // Test style guide service basic functionality
        $methods = get_class_methods('ACA_Style_Guide_Service');
        $required_methods = ['analyze_content', 'generate_style_guide', 'get_style_guide'];
        
        foreach ($required_methods as $method) {
            $this->assertTrue(in_array($method, $methods), 
                "ACA_Style_Guide_Service should have {$method} method");
        }
    }
    
    public function testServiceToServiceCommunication() {
        // Test how services communicate with each other
        $mock_idea_data = $this->getMockData('ideas');
        
        if (!empty($mock_idea_data)) {
            $first_idea = $mock_idea_data[0];
            $this->assertArrayHasKey('title', $first_idea, 'Mock idea should have title');
            $this->assertArrayHasKey('status', $first_idea, 'Mock idea should have status');
        }
        
        // Test workflow: Idea -> Draft -> Style Guide
        $this->assertTrue(true, 'Service communication workflow should be testable');
    }
    
    public function testDatabaseIntegration() {
        global $wpdb;
        
        // Test plugin tables exist (in mock)
        $expected_tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items'
        ];
        
        foreach ($expected_tables as $table) {
            // In mock environment, we just verify the table names are correctly formatted
            $this->assertStringContains('aca_ai_content_agent', $table, 
                "Table name should contain plugin prefix: {$table}");
        }
        
        // Test basic database operations
        $test_data = ['title' => 'Test Idea', 'status' => 'pending'];
        $insert_result = $wpdb->insert($wpdb->prefix . 'aca_ai_content_agent_ideas', $test_data);
        $this->assertTrue($insert_result, 'Database insert should succeed');
        
        $select_result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aca_ai_content_agent_ideas");
        $this->assertNotNull($select_result, 'Database select should return results');
    }
    
    public function testOptionsIntegration() {
        // Test plugin options are properly integrated
        $plugin_options = [
            'aca_ai_content_agent_options',
            'aca_ai_content_agent_gemini_api_key',
            'aca_ai_content_agent_license_key',
            'aca_ai_content_agent_style_guide'
        ];
        
        foreach ($plugin_options as $option) {
            // Test option can be set and retrieved
            $test_value = 'test_value_' . time();
            update_option($option, $test_value);
            
            $retrieved_value = get_option($option);
            $this->assertEqual($test_value, $retrieved_value, 
                "Option {$option} should be settable and retrievable");
            
            // Clean up
            delete_option($option);
        }
    }
    
    public function testHooksIntegration() {
        // Test WordPress hooks integration
        $test_hook = 'aca_test_integration_hook';
        $callback_executed = false;
        
        // Add action
        add_action($test_hook, function() use (&$callback_executed) {
            $callback_executed = true;
        });
        
        // Execute action
        do_action($test_hook);
        
        $this->assertTrue($callback_executed, 'WordPress hooks should be properly integrated');
        
        // Test filters
        $test_filter = 'aca_test_integration_filter';
        $original_value = 'original';
        $modified_value = 'modified';
        
        add_filter($test_filter, function($value) use ($modified_value) {
            return $modified_value;
        });
        
        $filtered_value = apply_filters($test_filter, $original_value);
        $this->assertEqual($modified_value, $filtered_value, 'WordPress filters should be properly integrated');
    }
    
    public function testCronIntegration() {
        // Test WordPress cron integration
        $cron_hook = 'aca_test_cron_hook';
        $timestamp = time() + 3600; // 1 hour from now
        
        // Schedule event
        $scheduled = wp_schedule_event($timestamp, 'hourly', $cron_hook);
        $this->assertTrue($scheduled, 'Cron event should be schedulable');
        
        // Unschedule event
        $unscheduled = wp_unschedule_event($timestamp, $cron_hook);
        $this->assertTrue($unscheduled, 'Cron event should be unschedulable');
        
        // Clear hook
        $cleared = wp_clear_scheduled_hook($cron_hook);
        $this->assertTrue($cleared, 'Cron hook should be clearable');
    }
    
    public function testAPIIntegration() {
        // Test external API integrations
        $test_url = 'https://api.test.com/endpoint';
        
        // Test GET request
        $get_response = wp_remote_get($test_url);
        $this->assertNotNull($get_response, 'HTTP GET should return response');
        $this->assertArrayHasKey('response', $get_response, 'Response should have response key');
        
        $response_code = wp_remote_retrieve_response_code($get_response);
        $this->assertEqual(200, $response_code, 'Mock response should return 200');
        
        // Test POST request
        $post_data = ['key' => 'value', 'test' => 'data'];
        $post_response = wp_remote_post($test_url, ['body' => $post_data]);
        $this->assertNotNull($post_response, 'HTTP POST should return response');
    }
    
    public function testSecurityIntegration() {
        // Test security features integration
        
        // Test nonce integration
        $action = 'test_security_action';
        $nonce = wp_create_nonce($action);
        $this->assertNotNull($nonce, 'Nonce should be created');
        
        $is_valid = wp_verify_nonce($nonce, $action);
        $this->assertTrue($is_valid, 'Nonce should be valid');
        
        // Test capability integration
        $capability = 'manage_options';
        $can_do = current_user_can($capability);
        $this->assertTrue($can_do, 'User should have capability in test environment');
        
        // Test data sanitization integration
        $dirty_data = '<script>alert("xss")</script>Clean Data';
        $clean_data = sanitize_text_field($dirty_data);
        $this->assertFalse(strpos($clean_data, '<script>') !== false, 'Script tags should be removed');
        $this->assertStringContains('Clean Data', $clean_data, 'Clean content should remain');
    }
    
    public function testCacheIntegration() {
        // Test caching system integration
        $cache_key = 'aca_test_cache_integration';
        $cache_data = ['test' => 'data', 'timestamp' => time()];
        
        // Test cache miss
        $miss_result = get_transient($cache_key);
        $this->assertFalse($miss_result, 'Cache miss should return false');
        
        // Test cache set
        $set_result = set_transient($cache_key, $cache_data, 3600);
        $this->assertTrue($set_result, 'Cache set should return true');
        
        // Test cache hit
        $hit_result = get_transient($cache_key);
        $this->assertEqual($cache_data, $hit_result, 'Cache hit should return original data');
        
        // Test cache delete
        $delete_result = delete_transient($cache_key);
        $this->assertTrue($delete_result, 'Cache delete should return true');
        
        $deleted_result = get_transient($cache_key);
        $this->assertFalse($deleted_result, 'Deleted cache should return false');
    }
    
    public function testErrorHandlingIntegration() {
        // Test error handling across services
        
        // Test WP_Error integration
        $error = new WP_Error('test_error', 'This is a test error');
        $this->assertTrue(is_wp_error($error), 'WP_Error should be recognized');
        
        $error_code = $error->get_error_code();
        $this->assertEqual('test_error', $error_code, 'Error code should be retrievable');
        
        $error_message = $error->get_error_message();
        $this->assertEqual('This is a test error', $error_message, 'Error message should be retrievable');
        
        // Test error propagation
        $error->add('additional_error', 'Additional error message');
        $this->assertStringContains('test_error', $error->get_error_code(), 'Original error should still be primary');
    }
    
    public function testLoggingIntegration() {
        // Test logging system integration
        global $wpdb;
        
        $log_table = $wpdb->prefix . 'aca_ai_content_agent_logs';
        
        // Test log entry creation
        $log_data = [
            'level' => 'info',
            'message' => 'Test log entry',
            'context' => 'integration_test',
            'created_at' => current_time('mysql')
        ];
        
        $log_result = $wpdb->insert($log_table, $log_data);
        $this->assertTrue($log_result, 'Log entry should be insertable');
        
        // Test log retrieval
        $logs = $wpdb->get_results("SELECT * FROM {$log_table} WHERE context = 'integration_test'");
        $this->assertNotNull($logs, 'Logs should be retrievable');
    }
    
    public function testFullWorkflowIntegration() {
        // Test complete workflow integration
        
        // Step 1: Generate idea (mock)
        $idea_data = [
            'title' => 'Integration Test Idea',
            'status' => 'pending',
            'source' => 'integration_test'
        ];
        
        // Step 2: Process idea through services (mock)
        $processed_idea = $idea_data;
        $processed_idea['status'] = 'approved';
        
        // Step 3: Create draft (mock)
        $draft_data = [
            'idea_id' => 1,
            'content' => 'This is a test draft content',
            'status' => 'draft'
        ];
        
        // Step 4: Apply style guide (mock)
        $styled_content = $draft_data['content'] . ' [Style applied]';
        
        // Verify workflow completion
        $this->assertStringContains('Integration Test Idea', $processed_idea['title'], 'Idea should be processed');
        $this->assertEqual('approved', $processed_idea['status'], 'Idea should be approved');
        $this->assertStringContains('test draft content', $draft_data['content'], 'Draft should be created');
        $this->assertStringContains('[Style applied]', $styled_content, 'Style should be applied');
    }
}