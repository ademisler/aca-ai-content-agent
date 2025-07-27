<?php
/**
 * ACA AI Content Agent - Comprehensive Test Runner
 * 
 * Modern, organized test framework for the plugin
 * 
 * @package ACA_AI_Content_Agent
 * @version 1.3
 */

class ACA_TestRunner {
    
    private $tests_passed = 0;
    private $tests_failed = 0;
    private $test_results = [];
    private $verbose = false;
    private $category_filter = null;
    private $start_time;
    
    public function __construct($args = []) {
        $this->verbose = in_array('--verbose', $args) || in_array('-v', $args);
        
        // Check for category filter
        foreach ($args as $arg) {
            if (strpos($arg, '--category=') === 0) {
                $this->category_filter = substr($arg, 11);
            }
        }
        
        $this->start_time = microtime(true);
        $this->setupEnvironment();
    }
    
    /**
     * Setup test environment with WordPress mocks
     */
    private function setupEnvironment() {
        // Load WordPress mocks
        require_once __DIR__ . '/mocks/WordPressMocks.php';
        
        // Load base test class
        require_once __DIR__ . '/tests/BaseTest.php';
        
        // Load plugin files for testing
        $this->loadPluginFiles();
        
        // Setup test database
        $this->setupTestDatabase();
    }
    
    /**
     * Load plugin files safely for testing
     */
    private function loadPluginFiles() {
        $plugin_root = dirname(dirname(__DIR__));
        
        // Define constants if not defined
        if (!defined('ACA_AI_CONTENT_AGENT_VERSION')) {
            define('ACA_AI_CONTENT_AGENT_VERSION', '1.3');
        }
        if (!defined('ACA_AI_CONTENT_AGENT_PLUGIN_DIR')) {
            define('ACA_AI_CONTENT_AGENT_PLUGIN_DIR', $plugin_root . '/');
        }
        if (!defined('ACA_AI_CONTENT_AGENT_PLUGIN_FILE')) {
            define('ACA_AI_CONTENT_AGENT_PLUGIN_FILE', $plugin_root . '/aca-ai-content-agent.php');
        }
        if (!defined('ACA_AI_CONTENT_AGENT_DEV_MODE')) {
            define('ACA_AI_CONTENT_AGENT_DEV_MODE', true);
        }
        
        // Load core plugin classes
        $core_files = [
            '/includes/utils/class-aca-encryption-util.php',
            '/includes/utils/class-aca-helper.php',
            '/includes/api/class-aca-gemini-api.php',
            '/includes/api/class-aca-gumroad-api.php',
            '/includes/services/class-aca-idea-service.php',
            '/includes/services/class-aca-draft-service.php',
            '/includes/services/class-aca-style-guide-service.php',
            '/includes/class-aca-plugin.php'
        ];
        
        foreach ($core_files as $file) {
            $file_path = $plugin_root . $file;
            if (file_exists($file_path)) {
                require_once $file_path;
            }
        }
    }
    
    /**
     * Setup mock database for testing
     */
    private function setupTestDatabase() {
        // Mock database operations
        global $wpdb;
        $wpdb = new MockWPDB();
    }
    
    /**
     * Run all tests
     */
    public function runAll() {
        $this->printHeader();
        
        $test_categories = [
            'unit' => 'Unit Tests',
            'integration' => 'Integration Tests', 
            'api' => 'API Tests',
            'security' => 'Security Tests',
            'performance' => 'Performance Tests'
        ];
        
        foreach ($test_categories as $category => $title) {
            if ($this->category_filter && $this->category_filter !== $category) {
                continue;
            }
            
            $this->runTestCategory($category, $title);
        }
        
        $this->printSummary();
        
        return $this->tests_failed === 0;
    }
    
    /**
     * Run tests in a specific category
     */
    private function runTestCategory($category, $title) {
        $this->printCategoryHeader($title);
        
        $test_dir = __DIR__ . "/tests/{$category}";
        if (!is_dir($test_dir)) {
            $this->output("⚠️  No tests found in {$category} category");
            return;
        }
        
        $test_files = glob($test_dir . '/*Test.php');
        
        foreach ($test_files as $test_file) {
            require_once $test_file;
            
            $class_name = $this->getTestClassName($test_file);
            if (class_exists($class_name)) {
                $test_instance = new $class_name($this);
                $test_instance->runTests();
            }
        }
    }
    
    /**
     * Get test class name from file path
     */
    private function getTestClassName($file_path) {
        $filename = basename($file_path, '.php');
        return $filename;
    }
    
    /**
     * Assert that condition is true
     */
    public function assertTrue($condition, $message = '') {
        if ($condition) {
            $this->tests_passed++;
            $this->test_results[] = ['status' => 'PASS', 'message' => $message];
            if ($this->verbose) {
                $this->output("✓ PASS: {$message}");
            }
        } else {
            $this->tests_failed++;
            $this->test_results[] = ['status' => 'FAIL', 'message' => $message];
            $this->output("✗ FAIL: {$message}");
        }
    }
    
    /**
     * Assert that two values are equal
     */
    public function assertEqual($expected, $actual, $message = '') {
        $condition = $expected === $actual;
        if (!$condition && empty($message)) {
            $message = "Expected '" . var_export($expected, true) . "', got '" . var_export($actual, true) . "'";
        }
        $this->assertTrue($condition, $message);
    }
    
    /**
     * Assert that condition is false
     */
    public function assertFalse($condition, $message = '') {
        $this->assertTrue(!$condition, $message);
    }
    
    /**
     * Assert that value is not null
     */
    public function assertNotNull($value, $message = '') {
        $this->assertTrue($value !== null, $message);
    }
    
    /**
     * Assert that array contains key
     */
    public function assertArrayHasKey($key, $array, $message = '') {
        $this->assertTrue(array_key_exists($key, $array), $message);
    }
    
    /**
     * Print test header
     */
    private function printHeader() {
        $this->output("=== ACA AI Content Agent - Comprehensive Test Suite ===");
        $this->output("Version: " . ACA_AI_CONTENT_AGENT_VERSION);
        $this->output("Environment: Isolated Testing");
        $this->output("Verbose: " . ($this->verbose ? 'ON' : 'OFF'));
        if ($this->category_filter) {
            $this->output("Category Filter: {$this->category_filter}");
        }
        $this->output("");
    }
    
    /**
     * Print category header
     */
    private function printCategoryHeader($title) {
        $this->output("--- {$title} ---");
    }
    
    /**
     * Print test summary
     */
    private function printSummary() {
        $total = $this->tests_passed + $this->tests_failed;
        $duration = round(microtime(true) - $this->start_time, 2);
        
        $this->output("");
        $this->output("=== Test Results ===");
        $this->output("Total Tests: {$total}");
        $this->output("Passed: {$this->tests_passed}");
        $this->output("Failed: {$this->tests_failed}");
        $this->output("Success Rate: " . ($total > 0 ? round(($this->tests_passed / $total) * 100, 1) : 0) . "%");
        $this->output("Duration: {$duration} seconds");
        
        if ($this->tests_failed === 0) {
            $this->output("✅ ALL TESTS PASSED!");
        } else {
            $this->output("❌ SOME TESTS FAILED!");
        }
    }
    
    /**
     * Output message
     */
    public function output($message) {
        echo $message . "\n";
    }
    
    /**
     * Get mock data for testing
     */
    public function getMockData($type) {
        $fixtures_file = __DIR__ . "/fixtures/{$type}.php";
        if (file_exists($fixtures_file)) {
            return require $fixtures_file;
        }
        return [];
    }
}

// Auto-run if called directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $runner = new ACA_TestRunner($argv ?? []);
    $success = $runner->runAll();
    exit($success ? 0 : 1);
}