<?php
/**
 * Base Test Class
 * 
 * Common functionality for all test classes
 */

abstract class BaseTest {
    
    protected $runner;
    protected $test_name;
    
    public function __construct($runner) {
        $this->runner = $runner;
        $this->test_name = get_class($this);
    }
    
    /**
     * Run all tests in this class
     */
    public function runTests() {
        $this->setUp();
        
        $methods = get_class_methods($this);
        $test_methods = array_filter($methods, function($method) {
            return strpos($method, 'test') === 0;
        });
        
        foreach ($test_methods as $method) {
            $this->runSingleTest($method);
        }
        
        $this->tearDown();
    }
    
    /**
     * Run a single test method
     */
    private function runSingleTest($method) {
        try {
            $this->setUpTest();
            $this->$method();
            $this->tearDownTest();
        } catch (Exception $e) {
            $this->fail($method . ': ' . $e->getMessage());
        }
    }
    
    /**
     * Setup before all tests in this class
     */
    protected function setUp() {
        // Override in child classes
    }
    
    /**
     * Cleanup after all tests in this class
     */
    protected function tearDown() {
        // Override in child classes
    }
    
    /**
     * Setup before each test method
     */
    protected function setUpTest() {
        // Override in child classes
    }
    
    /**
     * Cleanup after each test method
     */
    protected function tearDownTest() {
        // Override in child classes
    }
    
    /**
     * Assert that condition is true
     */
    protected function assertTrue($condition, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ': Assertion failed';
        }
        $this->runner->assertTrue($condition, $message);
    }
    
    /**
     * Assert that condition is false
     */
    protected function assertFalse($condition, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ': Assertion failed';
        }
        $this->runner->assertFalse($condition, $message);
    }
    
    /**
     * Assert that two values are equal
     */
    protected function assertEqual($expected, $actual, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ': Values not equal';
        }
        $this->runner->assertEqual($expected, $actual, $message);
    }
    
    /**
     * Assert that value is not null
     */
    protected function assertNotNull($value, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ': Value is null';
        }
        $this->runner->assertNotNull($value, $message);
    }
    
    /**
     * Assert that array has key
     */
    protected function assertArrayHasKey($key, $array, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Array does not have key '{$key}'";
        }
        $this->runner->assertArrayHasKey($key, $array, $message);
    }
    
    /**
     * Assert that string contains substring
     */
    protected function assertStringContains($needle, $haystack, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": String does not contain '{$needle}'";
        }
        $this->assertTrue(strpos($haystack, $needle) !== false, $message);
    }
    
    /**
     * Assert that class exists
     */
    protected function assertClassExists($class_name, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Class '{$class_name}' does not exist";
        }
        $this->assertTrue(class_exists($class_name), $message);
    }
    
    /**
     * Assert that function exists
     */
    protected function assertFunctionExists($function_name, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Function '{$function_name}' does not exist";
        }
        $this->assertTrue(function_exists($function_name), $message);
    }
    
    /**
     * Assert that constant is defined
     */
    protected function assertConstantDefined($constant_name, $message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ": Constant '{$constant_name}' is not defined";
        }
        $this->assertTrue(defined($constant_name), $message);
    }
    
    /**
     * Fail a test with message
     */
    protected function fail($message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ': Test failed';
        }
        $this->runner->assertTrue(false, $message);
    }
    
    /**
     * Skip a test with message
     */
    protected function skip($message = '') {
        if (empty($message)) {
            $message = $this->getCallerInfo() . ': Test skipped';
        }
        $this->runner->output("⚠️  SKIP: {$message}");
    }
    
    /**
     * Get caller information for better error messages
     */
    private function getCallerInfo() {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $caller = $trace[2] ?? $trace[1] ?? [];
        
        $class = $caller['class'] ?? 'Unknown';
        $function = $caller['function'] ?? 'unknown';
        $line = $caller['line'] ?? 0;
        
        return "{$class}::{$function}() line {$line}";
    }
    
    /**
     * Get mock data for testing
     */
    protected function getMockData($type) {
        return $this->runner->getMockData($type);
    }
    
    /**
     * Create a mock object
     */
    protected function createMock($class_name) {
        return new MockObject($class_name);
    }
}

/**
 * Simple mock object for testing
 */
class MockObject {
    private $class_name;
    private $methods = [];
    
    public function __construct($class_name) {
        $this->class_name = $class_name;
    }
    
    public function method($name, $return_value = null) {
        $this->methods[$name] = $return_value;
        return $this;
    }
    
    public function __call($name, $arguments) {
        return $this->methods[$name] ?? null;
    }
}