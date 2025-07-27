<?php
/**
 * Performance Tests
 * 
 * Tests for plugin performance and optimization
 */

require_once dirname(__DIR__) . '/BaseTest.php';

class PerformanceTest extends BaseTest {
    
    private $start_time;
    private $start_memory;
    
    protected function setUpTest() {
        $this->start_time = microtime(true);
        $this->start_memory = memory_get_usage();
    }
    
    protected function tearDownTest() {
        // Performance metrics could be logged here
    }
    
    public function testPluginLoadTime() {
        $start = microtime(true);
        
        // Simulate plugin loading
        if (class_exists('ACA_Plugin')) {
            $plugin = ACA_Plugin::instance();
        }
        
        $load_time = microtime(true) - $start;
        
        // Plugin should load within reasonable time (< 100ms)
        $this->assertTrue($load_time < 0.1, "Plugin load time should be < 100ms, got {$load_time}s");
    }
    
    public function testMemoryUsage() {
        $initial_memory = memory_get_usage();
        
        // Perform memory-intensive operations
        $large_array = [];
        for ($i = 0; $i < 1000; $i++) {
            $large_array[] = 'Test data item ' . $i;
        }
        
        $peak_memory = memory_get_peak_usage();
        $memory_used = $peak_memory - $initial_memory;
        
        // Memory usage should be reasonable (< 10MB for this test)
        $this->assertTrue($memory_used < 10 * 1024 * 1024, 
            "Memory usage should be < 10MB, used " . round($memory_used / 1024 / 1024, 2) . "MB");
        
        // Clean up
        unset($large_array);
    }
    
    public function testDatabaseQueryPerformance() {
        global $wpdb;
        
        $start = microtime(true);
        
        // Simulate multiple database queries
        for ($i = 0; $i < 10; $i++) {
            $wpdb->get_results("SELECT * FROM {$wpdb->prefix}aca_ai_content_agent_ideas LIMIT 10");
        }
        
        $query_time = microtime(true) - $start;
        
        // Database queries should be fast (< 50ms for 10 queries in mock)
        $this->assertTrue($query_time < 0.05, 
            "Database queries should be < 50ms, took {$query_time}s");
    }
    
    public function testOptionsAPIPerformance() {
        $start = microtime(true);
        
        // Test multiple option operations
        for ($i = 0; $i < 100; $i++) {
            $option_name = "test_option_{$i}";
            update_option($option_name, "value_{$i}");
            get_option($option_name);
        }
        
        $options_time = microtime(true) - $start;
        
        // Options API should be fast (< 100ms for 100 operations in mock)
        $this->assertTrue($options_time < 0.1, 
            "Options API operations should be < 100ms, took {$options_time}s");
        
        // Clean up
        for ($i = 0; $i < 100; $i++) {
            delete_option("test_option_{$i}");
        }
    }
    
    public function testEncryptionPerformance() {
        if (!function_exists('aca_ai_content_agent_encrypt')) {
            $this->skip('Encryption functions not available');
            return;
        }
        
        $test_data = 'Performance test data ' . str_repeat('x', 1000);
        $iterations = 10;
        
        $start = microtime(true);
        
        for ($i = 0; $i < $iterations; $i++) {
            $encrypted = aca_ai_content_agent_encrypt($test_data);
            if (!is_wp_error($encrypted)) {
                aca_ai_content_agent_decrypt($encrypted);
            }
        }
        
        $encryption_time = microtime(true) - $start;
        $avg_time = $encryption_time / $iterations;
        
        // Encryption should be reasonably fast (< 10ms per operation)
        $this->assertTrue($avg_time < 0.01, 
            "Encryption should be < 10ms per operation, got {$avg_time}s");
    }
    
    public function testConcurrentOperations() {
        // Simulate concurrent operations
        $operations = [];
        $start = microtime(true);
        
        // Simulate multiple operations happening simultaneously
        for ($i = 0; $i < 5; $i++) {
            $operations[] = [
                'type' => 'database',
                'start' => microtime(true)
            ];
            
            global $wpdb;
            $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts LIMIT 5");
            
            $operations[$i]['end'] = microtime(true);
            $operations[$i]['duration'] = $operations[$i]['end'] - $operations[$i]['start'];
        }
        
        $total_time = microtime(true) - $start;
        
        // Concurrent operations should complete reasonably quickly
        $this->assertTrue($total_time < 0.1, 
            "Concurrent operations should complete < 100ms, took {$total_time}s");
        
        // Check individual operation times
        foreach ($operations as $i => $op) {
            $this->assertTrue($op['duration'] < 0.05, 
                "Operation {$i} should be < 50ms, took {$op['duration']}s");
        }
    }
    
    public function testLargeDataHandling() {
        // Test handling of large data sets
        $large_data = str_repeat('Large content data. ', 10000); // ~200KB
        
        $start = microtime(true);
        $start_memory = memory_get_usage();
        
        // Process large data
        $processed = sanitize_text_field($large_data);
        $escaped = esc_html($processed);
        
        $processing_time = microtime(true) - $start;
        $memory_used = memory_get_usage() - $start_memory;
        
        // Large data processing should be reasonable
        $this->assertTrue($processing_time < 0.5, 
            "Large data processing should be < 500ms, took {$processing_time}s");
        
        $this->assertTrue($memory_used < 5 * 1024 * 1024, 
            "Memory usage should be < 5MB, used " . round($memory_used / 1024 / 1024, 2) . "MB");
        
        // Verify data integrity
        $this->assertTrue(strlen($escaped) > 0, 'Processed data should not be empty');
    }
    
    public function testCachePerformance() {
        $cache_keys = [];
        $cache_data = [];
        
        // Generate test data
        for ($i = 0; $i < 50; $i++) {
            $cache_keys[] = "test_cache_key_{$i}";
            $cache_data[] = "Cached data item {$i} with some content";
        }
        
        // Test cache write performance
        $start = microtime(true);
        
        foreach ($cache_keys as $i => $key) {
            set_transient($key, $cache_data[$i], 3600);
        }
        
        $write_time = microtime(true) - $start;
        
        // Test cache read performance
        $start = microtime(true);
        
        foreach ($cache_keys as $key) {
            get_transient($key);
        }
        
        $read_time = microtime(true) - $start;
        
        // Cache operations should be fast
        $this->assertTrue($write_time < 0.1, 
            "Cache write should be < 100ms, took {$write_time}s");
        $this->assertTrue($read_time < 0.05, 
            "Cache read should be < 50ms, took {$read_time}s");
        
        // Clean up
        foreach ($cache_keys as $key) {
            delete_transient($key);
        }
    }
    
    public function testHookPerformance() {
        $hook_name = 'test_performance_hook';
        $iterations = 100;
        
        // Add multiple callbacks to the same hook
        for ($i = 0; $i < 10; $i++) {
            add_action($hook_name, function() use ($i) {
                // Simulate some work
                $result = $i * 2;
                return $result;
            });
        }
        
        $start = microtime(true);
        
        // Execute the hook multiple times
        for ($i = 0; $i < $iterations; $i++) {
            do_action($hook_name);
        }
        
        $hook_time = microtime(true) - $start;
        $avg_time = $hook_time / $iterations;
        
        // Hook execution should be fast
        $this->assertTrue($avg_time < 0.001, 
            "Hook execution should be < 1ms per call, got {$avg_time}s");
    }
    
    public function testScalabilitySimulation() {
        // Simulate plugin behavior under load
        $users = 10;
        $operations_per_user = 5;
        
        $start = microtime(true);
        $start_memory = memory_get_usage();
        
        for ($user = 1; $user <= $users; $user++) {
            for ($op = 1; $op <= $operations_per_user; $op++) {
                // Simulate user operations
                $option_name = "user_{$user}_operation_{$op}";
                update_option($option_name, "User {$user} data {$op}");
                get_option($option_name);
                
                // Simulate some processing
                $data = str_repeat("Data for user {$user} op {$op}. ", 10);
                $processed = sanitize_text_field($data);
            }
        }
        
        $total_time = microtime(true) - $start;
        $memory_used = memory_get_usage() - $start_memory;
        
        // Scalability test should complete reasonably
        $this->assertTrue($total_time < 1.0, 
            "Scalability test should complete < 1s, took {$total_time}s");
        
        $this->assertTrue($memory_used < 10 * 1024 * 1024, 
            "Memory usage should be < 10MB, used " . round($memory_used / 1024 / 1024, 2) . "MB");
        
        // Clean up
        for ($user = 1; $user <= $users; $user++) {
            for ($op = 1; $op <= $operations_per_user; $op++) {
                delete_option("user_{$user}_operation_{$op}");
            }
        }
    }
    
    public function testResourceCleanup() {
        $initial_memory = memory_get_usage();
        
        // Create resources that need cleanup
        $resources = [];
        for ($i = 0; $i < 100; $i++) {
            $resources[] = [
                'id' => $i,
                'data' => str_repeat("Resource data {$i}. ", 100),
                'created' => time()
            ];
        }
        
        $peak_memory = memory_get_peak_usage();
        
        // Clean up resources
        unset($resources);
        
        // Force garbage collection if available
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
        
        $final_memory = memory_get_usage();
        $memory_freed = $peak_memory - $final_memory;
        
        // Memory should be freed after cleanup
        $this->assertTrue($memory_freed > 0, 'Memory should be freed after resource cleanup');
        
        // Final memory should be close to initial
        $memory_diff = $final_memory - $initial_memory;
        $this->assertTrue($memory_diff < 1024 * 1024, 
            'Memory difference should be < 1MB after cleanup, got ' . 
            round($memory_diff / 1024, 2) . 'KB');
    }
}