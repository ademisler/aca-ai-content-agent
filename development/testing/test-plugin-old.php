<?php
/**
 * ACA - AI Content Agent Test File
 * 
 * This file helps test the plugin functionality and identify issues.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress, simulate basic WordPress functions
    if (!function_exists('wp_die')) {
        function wp_die($message) {
            echo "ERROR: " . $message;
            exit;
        }
    }
    
    if (!function_exists('current_user_can')) {
        function current_user_can($capability) {
            return true; // Assume admin for testing
        }
    }
    
    if (!function_exists('esc_html__')) {
        function esc_html__($text, $domain) {
            return htmlspecialchars($text);
        }
    }
    
    if (!function_exists('esc_html_e')) {
        function esc_html_e($text, $domain) {
            echo htmlspecialchars($text);
        }
    }
    
    if (!function_exists('esc_attr')) {
        function esc_attr($text) {
            return htmlspecialchars($text);
        }
    }
    
    if (!function_exists('wp_get_current_user')) {
        function wp_get_current_user() {
            return (object) [
                'exists' => function() { return true; },
                'has_cap' => function($cap) { return true; },
                'add_cap' => function($cap) { return true; },
                'display_name' => 'Test User'
            ];
        }
    }
    
    if (!function_exists('get_option')) {
        function get_option($key, $default = false) {
            return $default;
        }
    }
    
    if (!function_exists('get_transient')) {
        function get_transient($key) {
            return false;
        }
    }
    
    if (!function_exists('set_transient')) {
        function set_transient($key, $value, $expiration) {
            return true;
        }
    }
    
    if (!function_exists('plugin_dir_path')) {
        function plugin_dir_path($file) {
            return dirname($file) . '/';
        }
    }
    
    if (!function_exists('plugin_dir_url')) {
        function plugin_dir_url($file) {
            return 'https://example.com/wp-content/plugins/aca-ai-content-agent/';
        }
    }
    
    if (!function_exists('admin_url')) {
        function admin_url($path = '') {
            return 'https://example.com/wp-admin/' . $path;
        }
    }
    
    if (!function_exists('wp_create_nonce')) {
        function wp_create_nonce($action) {
            return 'test_nonce_' . $action;
        }
    }
    
    if (!function_exists('sanitize_text_field')) {
        function sanitize_text_field($text) {
            return trim(strip_tags($text));
        }
    }
    
    if (!function_exists('wp_unslash')) {
        function wp_unslash($text) {
            return stripslashes($text);
        }
    }
    
    if (!function_exists('sanitize_key')) {
        function sanitize_key($key) {
            return preg_replace('/[^a-z0-9_-]/', '', strtolower($key));
        }
    }
}

// Test the plugin functionality
echo "<h1>ACA - AI Content Agent Test</h1>";

// Test 1: Check if main plugin file exists
echo "<h2>Test 1: Plugin File Check</h2>";
$plugin_file = 'aca-ai-content-agent.php';
if (file_exists($plugin_file)) {
    echo "✅ Plugin file exists: $plugin_file<br>";
} else {
    echo "❌ Plugin file missing: $plugin_file<br>";
}

// Test 2: Check if required directories exist
echo "<h2>Test 2: Directory Structure Check</h2>";
$required_dirs = [
    'admin',
    'admin/css',
    'admin/js',
    'includes',
    'includes/admin',
    'includes/core',
    'includes/utils',
    'includes/api',
    'includes/services',
    'includes/integrations'
];

foreach ($required_dirs as $dir) {
    if (is_dir($dir)) {
        echo "✅ Directory exists: $dir<br>";
    } else {
        echo "❌ Directory missing: $dir<br>";
    }
}

// Test 3: Check if required files exist
echo "<h2>Test 3: Required Files Check</h2>";
$required_files = [
    'admin/css/aca-admin.css',
    'admin/js/aca-admin.js',
    'includes/class-aca-plugin.php',
    'includes/admin/class-aca-admin.php',
    'includes/admin/class-aca-admin-menu.php',
    'includes/admin/class-aca-admin-assets.php',
    'includes/admin/class-aca-dashboard.php',
    'includes/core/class-aca-activator.php'
];

foreach ($required_files as $file) {
    if (file_exists($file)) {
        echo "✅ File exists: $file<br>";
    } else {
        echo "❌ File missing: $file<br>";
    }
}

// Test 4: Check CSS content
echo "<h2>Test 4: CSS Content Check</h2>";
$css_file = 'admin/css/aca-admin.css';
if (file_exists($css_file)) {
    $css_content = file_get_contents($css_file);
    if (strpos($css_content, '--aca-primary-start') !== false) {
        echo "✅ CSS variables found<br>";
    } else {
        echo "❌ CSS variables missing<br>";
    }
    
    if (strpos($css_content, '.bi') !== false) {
        echo "✅ Bootstrap Icons references found<br>";
    } else {
        echo "❌ Bootstrap Icons references missing<br>";
    }
} else {
    echo "❌ CSS file not found<br>";
}

// Test 5: Check JavaScript content
echo "<h2>Test 5: JavaScript Content Check</h2>";
$js_file = 'admin/js/aca-admin.js';
if (file_exists($js_file)) {
    $js_content = file_get_contents($js_file);
    if (strpos($js_content, 'ACA_Admin') !== false) {
        echo "✅ ACA_Admin references found<br>";
    } else {
        echo "❌ ACA_Admin references missing<br>";
    }
    
    if (strpos($js_content, 'showNotification') !== false) {
        echo "✅ Notification system found<br>";
    } else {
        echo "❌ Notification system missing<br>";
    }
} else {
    echo "❌ JavaScript file not found<br>";
}

// Test 6: Check Dashboard class
echo "<h2>Test 6: Dashboard Class Check</h2>";
$dashboard_file = 'includes/admin/class-aca-dashboard.php';
if (file_exists($dashboard_file)) {
    $dashboard_content = file_get_contents($dashboard_file);
    if (strpos($dashboard_content, 'ACA_Dashboard') !== false) {
        echo "✅ Dashboard class found<br>";
    } else {
        echo "❌ Dashboard class missing<br>";
    }
    
    if (strpos($dashboard_content, 'render_page_header') !== false) {
        echo "✅ Page header method found<br>";
    } else {
        echo "❌ Page header method missing<br>";
    }
} else {
    echo "❌ Dashboard file not found<br>";
}

// Test 7: Check Admin Assets class
echo "<h2>Test 7: Admin Assets Class Check</h2>";
$assets_file = 'includes/admin/class-aca-admin-assets.php';
if (file_exists($assets_file)) {
    $assets_content = file_get_contents($assets_file);
    if (strpos($assets_content, 'ACA_Admin_Assets') !== false) {
        echo "✅ Admin Assets class found<br>";
    } else {
        echo "❌ Admin Assets class missing<br>";
    }
    
    if (strpos($assets_content, 'bootstrap-icons') !== false) {
        echo "✅ Bootstrap Icons enqueue found<br>";
    } else {
        echo "❌ Bootstrap Icons enqueue missing<br>";
    }
} else {
    echo "❌ Admin Assets file not found<br>";
}

// Test 8: Check Admin Menu class
echo "<h2>Test 8: Admin Menu Class Check</h2>";
$menu_file = 'includes/admin/class-aca-admin-menu.php';
if (file_exists($menu_file)) {
    $menu_content = file_get_contents($menu_file);
    if (strpos($menu_content, 'manage_options') !== false) {
        echo "✅ Standard WordPress capability found<br>";
    } else {
        echo "❌ Standard WordPress capability missing<br>";
    }
    
    if (strpos($menu_content, 'ACA_Dashboard::render') !== false) {
        echo "✅ Dashboard render call found<br>";
    } else {
        echo "❌ Dashboard render call missing<br>";
    }
} else {
    echo "❌ Admin Menu file not found<br>";
}

echo "<h2>Test Complete!</h2>";
echo "<p>If you see any ❌ errors above, those need to be fixed for the plugin to work properly.</p>";
echo "<p>To test the plugin in WordPress:</p>";
echo "<ol>";
echo "<li>Upload the plugin to your WordPress site</li>";
echo "<li>Activate the plugin</li>";
echo "<li>Go to WordPress Admin → ACA Agent</li>";
echo "<li>Check if the modern dashboard loads properly</li>";
echo "</ol>"; 