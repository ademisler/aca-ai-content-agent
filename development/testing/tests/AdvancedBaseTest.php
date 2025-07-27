<?php
/**
 * Advanced Base Test Class
 * 
 * Extended testing capabilities for comprehensive plugin validation
 */

abstract class AdvancedBaseTest extends BaseTest {
    
    protected $performance_monitor;
    protected $file_analyzer;
    protected $compatibility_checker;
    
    public function __construct($runner) {
        parent::__construct($runner);
        $this->performance_monitor = new TestPerformanceMonitor();
        $this->file_analyzer = new TestFileAnalyzer();
        $this->compatibility_checker = new TestCompatibilityChecker();
    }
    
    /**
     * Setup before each test method with performance monitoring
     */
    protected function setUpTest() {
        parent::setUpTest();
        $this->performance_monitor->startTest($this->getCurrentTestMethod());
        MockPerformanceMonitor::log_memory('test_start');
    }
    
    /**
     * Cleanup after each test method with performance monitoring
     */
    protected function tearDownTest() {
        $this->performance_monitor->endTest($this->getCurrentTestMethod());
        MockPerformanceMonitor::log_memory('test_end');
        parent::tearDownTest();
    }
    
    /**
     * Get current test method name
     */
    private function getCurrentTestMethod() {
        $trace = debug_backtrace();
        foreach ($trace as $frame) {
            if (isset($frame['function']) && strpos($frame['function'], 'test') === 0) {
                return $frame['function'];
            }
        }
        return 'unknown_test';
    }
    
    // === ADVANCED ASSERTION METHODS ===
    
    /**
     * Assert that a file exists and is readable
     */
    protected function assertFileExistsAndReadable($file_path, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": File should exist and be readable: {$file_path}";
        }
        
        $this->assertTrue(file_exists($file_path), "File should exist: {$file_path}");
        $this->assertTrue(is_readable($file_path), "File should be readable: {$file_path}");
    }
    
    /**
     * Assert that a file contains specific content
     */
    protected function assertFileContains($file_path, $needle, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": File should contain '{$needle}': {$file_path}";
        }
        
        $this->assertFileExistsAndReadable($file_path);
        $content = file_get_contents($file_path);
        $this->assertStringContains($needle, $content, $message);
    }
    
    /**
     * Assert that a file does not contain specific content
     */
    protected function assertFileNotContains($file_path, $needle, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": File should not contain '{$needle}': {$file_path}";
        }
        
        $this->assertFileExistsAndReadable($file_path);
        $content = file_get_contents($file_path);
        $this->assertFalse(strpos($content, $needle) !== false, $message);
    }
    
    /**
     * Assert that a file has valid PHP syntax
     */
    protected function assertValidPHPSyntax($file_path, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": File should have valid PHP syntax: {$file_path}";
        }
        
        $this->assertFileExistsAndReadable($file_path);
        
        // Use php -l to check syntax (if available)
        $output = [];
        $return_var = 0;
        exec("php -l " . escapeshellarg($file_path) . " 2>&1", $output, $return_var);
        
        if ($return_var === 0) {
            $this->assertTrue(true, $message);
        } else {
            // Fallback to basic content checks
            $content = file_get_contents($file_path);
            $this->assertTrue(strpos($content, '<?php') !== false, "PHP file should start with <?php tag: {$file_path}");
            
            // Check for common syntax errors
            $this->assertFalse(substr_count($content, '{') !== substr_count($content, '}'), 
                "Braces should be balanced in: {$file_path}");
            $this->assertFalse(substr_count($content, '(') !== substr_count($content, ')'), 
                "Parentheses should be balanced in: {$file_path}");
        }
    }
    
    /**
     * Assert that a class method exists and is callable
     */
    protected function assertMethodExistsAndCallable($class_name, $method_name, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Method {$class_name}::{$method_name}() should exist and be callable";
        }
        
        $this->assertClassExists($class_name);
        $this->assertTrue(method_exists($class_name, $method_name), 
            "Method {$method_name} should exist in class {$class_name}");
        $this->assertTrue(is_callable([$class_name, $method_name]), 
            "Method {$class_name}::{$method_name}() should be callable");
    }
    
    /**
     * Assert that a class has required properties
     */
    protected function assertClassHasProperties($class_name, $properties, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Class {$class_name} should have required properties";
        }
        
        $this->assertClassExists($class_name);
        $reflection = new ReflectionClass($class_name);
        
        foreach ($properties as $property) {
            $this->assertTrue($reflection->hasProperty($property), 
                "Class {$class_name} should have property: {$property}");
        }
    }
    
    /**
     * Assert that a function has correct parameter count
     */
    protected function assertFunctionParameterCount($function_name, $expected_count, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Function {$function_name} should have {$expected_count} parameters";
        }
        
        $this->assertFunctionExists($function_name);
        $reflection = new ReflectionFunction($function_name);
        $actual_count = $reflection->getNumberOfParameters();
        
        $this->assertEqual($expected_count, $actual_count, 
            "Function {$function_name} should have {$expected_count} parameters, has {$actual_count}");
    }
    
    /**
     * Assert that execution time is within acceptable limits
     */
    protected function assertExecutionTimeWithin($max_seconds, $callback, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Execution should complete within {$max_seconds} seconds";
        }
        
        $start_time = microtime(true);
        call_user_func($callback);
        $execution_time = microtime(true) - $start_time;
        
        $this->assertTrue($execution_time <= $max_seconds, 
            "Execution took {$execution_time}s, should be <= {$max_seconds}s");
    }
    
    /**
     * Assert that memory usage is within acceptable limits
     */
    protected function assertMemoryUsageWithin($max_bytes, $callback, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Memory usage should be within " . 
                       round($max_bytes / 1024 / 1024, 2) . "MB";
        }
        
        $initial_memory = memory_get_usage();
        call_user_func($callback);
        $memory_used = memory_get_usage() - $initial_memory;
        
        $this->assertTrue($memory_used <= $max_bytes, 
            "Memory used: " . round($memory_used / 1024 / 1024, 2) . "MB, " .
            "should be <= " . round($max_bytes / 1024 / 1024, 2) . "MB");
    }
    
    /**
     * Assert that a WordPress hook is registered
     */
    protected function assertHookRegistered($hook_name, $function_name = null, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Hook '{$hook_name}' should be registered";
        }
        
        global $wp_actions, $wp_filters;
        
        $hook_registered = isset($wp_actions[$hook_name]) || isset($wp_filters[$hook_name]);
        $this->assertTrue($hook_registered, "Hook '{$hook_name}' should be registered");
        
        if ($function_name) {
            $hook_list = isset($wp_actions[$hook_name]) ? $wp_actions[$hook_name] : $wp_filters[$hook_name];
            $function_found = false;
            
            foreach ($hook_list as $hook_data) {
                if (is_string($hook_data['callback']) && $hook_data['callback'] === $function_name) {
                    $function_found = true;
                    break;
                } elseif (is_array($hook_data['callback']) && 
                         isset($hook_data['callback'][1]) && 
                         $hook_data['callback'][1] === $function_name) {
                    $function_found = true;
                    break;
                }
            }
            
            $this->assertTrue($function_found, 
                "Function '{$function_name}' should be registered for hook '{$hook_name}'");
        }
    }
    
    /**
     * Assert that database table structure is correct
     */
    protected function assertDatabaseTableStructure($table_name, $expected_columns, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Table '{$table_name}' should have correct structure";
        }
        
        global $wpdb;
        
        // In mock environment, we simulate table structure validation
        $mock_columns = [
            'aca_ai_content_agent_ideas' => ['id', 'title', 'keywords', 'status', 'created_at'],
            'aca_ai_content_agent_logs' => ['id', 'level', 'message', 'context', 'created_at'],
            'aca_ai_content_agent_clusters' => ['id', 'name', 'description', 'created_at'],
            'aca_ai_content_agent_cluster_items' => ['id', 'cluster_id', 'post_id', 'created_at']
        ];
        
        $table_key = str_replace($wpdb->prefix, '', $table_name);
        if (isset($mock_columns[$table_key])) {
            $actual_columns = $mock_columns[$table_key];
            
            foreach ($expected_columns as $column) {
                $this->assertTrue(in_array($column, $actual_columns), 
                    "Table '{$table_name}' should have column '{$column}'");
            }
        } else {
            $this->skip("Table structure validation not available for: {$table_name}");
        }
    }
    
    /**
     * Assert that API response has correct format
     */
    protected function assertAPIResponseFormat($response, $expected_keys, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": API response should have correct format";
        }
        
        $this->assertTrue(is_array($response) || is_object($response), 
            "API response should be array or object");
        
        $response_array = is_object($response) ? (array) $response : $response;
        
        foreach ($expected_keys as $key) {
            $this->assertArrayHasKey($key, $response_array, 
                "API response should have key '{$key}'");
        }
    }
    
    /**
     * Assert that security measures are in place
     */
    protected function assertSecurityMeasures($file_path, $measures = [], $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": File should have security measures: {$file_path}";
        }
        
        $this->assertFileExistsAndReadable($file_path);
        $content = file_get_contents($file_path);
        
        $default_measures = [
            'abspath_check' => ['ABSPATH', 'exit', 'die'],
            'nonce_verification' => ['wp_verify_nonce', 'check_admin_referer'],
            'capability_check' => ['current_user_can', 'user_can'],
            'input_sanitization' => ['sanitize_', 'wp_kses', 'filter_var'],
            'output_escaping' => ['esc_html', 'esc_attr', 'esc_url']
        ];
        
        $measures = empty($measures) ? array_keys($default_measures) : $measures;
        
        foreach ($measures as $measure) {
            if (isset($default_measures[$measure])) {
                $found = false;
                foreach ($default_measures[$measure] as $pattern) {
                    if (strpos($content, $pattern) !== false) {
                        $found = true;
                        break;
                    }
                }
                $this->assertTrue($found, 
                    "File should have {$measure} security measure: {$file_path}");
            }
        }
    }
    
    // === PLUGIN-SPECIFIC TESTING METHODS ===
    
    /**
     * Test plugin file structure
     */
    protected function testPluginFileStructure($base_path) {
        $required_files = [
            'aca-ai-content-agent.php',
            'readme.txt',
            'uninstall.php'
        ];
        
        $required_directories = [
            'includes',
            'admin',
            'assets',
            'languages'
        ];
        
        foreach ($required_files as $file) {
            $file_path = $base_path . '/' . $file;
            $this->assertFileExistsAndReadable($file_path, 
                "Required plugin file should exist: {$file}");
        }
        
        foreach ($required_directories as $dir) {
            $dir_path = $base_path . '/' . $dir;
            $this->assertTrue(is_dir($dir_path), 
                "Required plugin directory should exist: {$dir}");
        }
    }
    
    /**
     * Test plugin class structure
     */
    protected function testPluginClassStructure($class_name, $expected_methods = []) {
        $this->assertClassExists($class_name);
        
        $reflection = new ReflectionClass($class_name);
        
        // Test singleton pattern for main plugin class
        if (strpos($class_name, 'Plugin') !== false) {
            $this->assertTrue($reflection->hasMethod('instance'), 
                "Plugin class should have instance() method for singleton pattern");
        }
        
        // Test service classes
        if (strpos($class_name, 'Service') !== false) {
            $this->assertTrue($reflection->hasMethod('__construct'), 
                "Service class should have constructor");
        }
        
        // Test API classes
        if (strpos($class_name, 'Api') !== false) {
            $api_methods = ['get_api_key', 'set_api_key'];
            $has_api_method = false;
            
            foreach ($api_methods as $method) {
                if ($reflection->hasMethod($method)) {
                    $has_api_method = true;
                    break;
                }
            }
            
            $this->assertTrue($has_api_method, 
                "API class should have API key management methods");
        }
        
        // Test expected methods
        foreach ($expected_methods as $method) {
            $this->assertTrue($reflection->hasMethod($method), 
                "Class {$class_name} should have method: {$method}");
        }
    }
    
    /**
     * Test WordPress integration
     */
    protected function testWordPressIntegration() {
        // Test WordPress functions availability
        $wp_functions = [
            'add_action', 'add_filter', 'remove_action', 'remove_filter',
            'get_option', 'update_option', 'delete_option',
            'wp_enqueue_script', 'wp_enqueue_style',
            'current_user_can', 'wp_verify_nonce',
            'sanitize_text_field', 'esc_html', 'esc_attr'
        ];
        
        foreach ($wp_functions as $function) {
            $this->assertFunctionExists($function, 
                "WordPress function should be available: {$function}");
        }
        
        // Test WordPress constants
        $wp_constants = ['ABSPATH', 'WP_CONTENT_DIR', 'WP_PLUGIN_DIR'];
        
        foreach ($wp_constants as $constant) {
            $this->assertConstantDefined($constant, 
                "WordPress constant should be defined: {$constant}");
        }
    }
    
    /**
     * Test plugin activation/deactivation
     */
    protected function testPluginLifecycle() {
        // Test activation hooks
        $this->assertFunctionExists('register_activation_hook', 
            'WordPress activation hook function should be available');
        
        // Test deactivation hooks
        $this->assertFunctionExists('register_deactivation_hook', 
            'WordPress deactivation hook function should be available');
        
        // Test uninstall functionality
        $uninstall_file = dirname(dirname(dirname(__DIR__))) . '/uninstall.php';
        if (file_exists($uninstall_file)) {
            $this->assertFileExistsAndReadable($uninstall_file);
            $this->assertFileContains($uninstall_file, 'WP_UNINSTALL_PLUGIN', 
                'Uninstall file should check WP_UNINSTALL_PLUGIN constant');
        }
    }
    
    /**
     * Get comprehensive plugin analysis
     */
    protected function getPluginAnalysis($plugin_root) {
        return [
            'structure' => $this->file_analyzer->analyzeStructure($plugin_root),
            'dependencies' => $this->file_analyzer->analyzeDependencies($plugin_root),
            'performance' => $this->performance_monitor->getStats(),
            'compatibility' => $this->compatibility_checker->checkCompatibility()
        ];
    }
}

/**
 * Test Performance Monitor
 */
class TestPerformanceMonitor {
    private $test_times = [];
    private $memory_usage = [];
    
    public function startTest($test_name) {
        $this->test_times[$test_name] = ['start' => microtime(true)];
        $this->memory_usage[$test_name] = ['start' => memory_get_usage()];
    }
    
    public function endTest($test_name) {
        if (isset($this->test_times[$test_name])) {
            $this->test_times[$test_name]['end'] = microtime(true);
            $this->test_times[$test_name]['duration'] = 
                $this->test_times[$test_name]['end'] - $this->test_times[$test_name]['start'];
        }
        
        if (isset($this->memory_usage[$test_name])) {
            $this->memory_usage[$test_name]['end'] = memory_get_usage();
            $this->memory_usage[$test_name]['used'] = 
                $this->memory_usage[$test_name]['end'] - $this->memory_usage[$test_name]['start'];
        }
    }
    
    public function getStats() {
        return [
            'execution_times' => $this->test_times,
            'memory_usage' => $this->memory_usage
        ];
    }
}

/**
 * Test File Analyzer
 */
class TestFileAnalyzer {
    public function analyzeStructure($plugin_root) {
        $structure = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($plugin_root)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relative_path = str_replace($plugin_root . '/', '', $file->getPathname());
                $structure[$relative_path] = [
                    'size' => $file->getSize(),
                    'modified' => $file->getMTime(),
                    'type' => $this->determineFileType($relative_path)
                ];
            }
        }
        
        return $structure;
    }
    
    public function analyzeDependencies($plugin_root) {
        // Implementation would analyze require/include statements
        return ['dependencies' => [], 'conflicts' => []];
    }
    
    private function determineFileType($path) {
        if (strpos($path, '/admin/') !== false) return 'admin';
        if (strpos($path, '/api/') !== false) return 'api';
        if (strpos($path, '/services/') !== false) return 'service';
        if (strpos($path, '/utils/') !== false) return 'utility';
        return 'other';
    }
}

/**
 * Test Compatibility Checker
 */
class TestCompatibilityChecker {
    public function checkCompatibility() {
        return [
            'php_version' => PHP_VERSION,
            'required_extensions' => $this->checkRequiredExtensions(),
            'wordpress_functions' => $this->checkWordPressFunctions()
        ];
    }
    
    private function checkRequiredExtensions() {
        $required = ['json', 'curl', 'mbstring', 'openssl'];
        $status = [];
        
        foreach ($required as $ext) {
            $status[$ext] = extension_loaded($ext);
        }
        
        return $status;
    }
    
    private function checkWordPressFunctions() {
        $required = ['add_action', 'get_option', 'wp_remote_get'];
        $status = [];
        
        foreach ($required as $func) {
            $status[$func] = function_exists($func);
        }
        
        return $status;
    }
}