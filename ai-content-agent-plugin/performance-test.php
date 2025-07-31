<?php
/**
 * Performance Benchmark Test for AI Content Agent
 * 
 * Run this file to test plugin performance
 */

// Simulate WordPress environment
if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/');
}

// Load plugin
require_once 'ai-content-agent.php';

echo "=== AI Content Agent Performance Benchmark ===\n";
echo "Plugin Version: " . (defined('ACA_VERSION') ? ACA_VERSION : 'Unknown') . "\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Memory Limit: " . ini_get('memory_limit') . "\n";
echo "Start Memory: " . round(memory_get_usage(true) / 1024 / 1024, 2) . " MB\n\n";

$start_time = microtime(true);

// Test 1: Class Loading Performance
echo "1. Class Loading Test...\n";
$class_start = microtime(true);
$classes_loaded = 0;

$core_classes = [
    'AI_Content_Agent',
    'ACA_Performance_Monitor', 
    'ACA_File_Manager',
    'ACA_Hook_Manager',
    'ACA_Service_Container'
];

foreach ($core_classes as $class) {
    if (class_exists($class)) {
        $classes_loaded++;
    }
}

$class_time = microtime(true) - $class_start;
echo "   - Classes loaded: $classes_loaded/" . count($core_classes) . "\n";
echo "   - Time: " . round($class_time * 1000, 2) . " ms\n\n";

// Test 2: Memory Usage Test
echo "2. Memory Usage Test...\n";
$current_memory = memory_get_usage(true);
$peak_memory = memory_get_peak_usage(true);

echo "   - Current: " . round($current_memory / 1024 / 1024, 2) . " MB\n";
echo "   - Peak: " . round($peak_memory / 1024 / 1024, 2) . " MB\n\n";

// Test 3: Interface Implementation Test
echo "3. Interface Implementation Test...\n";
$interface_count = 0;
$interfaces = ['ACA_Cache_Interface', 'ACA_Performance_Interface', 'ACA_Cleanup_Interface', 'ACA_Container_Interface'];

foreach ($interfaces as $interface) {
    if (interface_exists($interface)) {
        $interface_count++;
    }
}

echo "   - Interfaces available: $interface_count/" . count($interfaces) . "\n\n";

$total_time = microtime(true) - $start_time;
$final_memory = memory_get_usage(true);

echo "=== BENCHMARK RESULTS ===\n";
echo "Total Execution Time: " . round($total_time * 1000, 2) . " ms\n";
echo "Final Memory Usage: " . round($final_memory / 1024 / 1024, 2) . " MB\n";
echo "Memory Efficiency: " . ($total_time < 0.1 && $final_memory < 10485760 ? "GOOD" : "NEEDS OPTIMIZATION") . "\n";
echo "Overall Status: " . ($classes_loaded >= 4 && $interface_count >= 3 ? "PASSED" : "FAILED") . "\n";

// Cleanup test
if (class_exists('AI_Content_Agent')) {
    AI_Content_Agent::destroy_instance();
    echo "Cleanup: COMPLETED\n";
}

echo "\n=== END BENCHMARK ===\n";
