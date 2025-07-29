<?php
/**
 * Google API Dependencies Installer
 * 
 * This script helps install the required Google API client library
 * for the Google Search Console integration.
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Dependencies_Installer {
    
    public function __construct() {
        add_action('wp_ajax_aca_install_dependencies', array($this, 'install_dependencies'));
    }
    
    /**
     * Check if dependencies are installed
     */
    public static function are_dependencies_installed() {
        return file_exists(ACA_PLUGIN_PATH . 'vendor/autoload.php') && 
               file_exists(ACA_PLUGIN_PATH . 'vendor/google/apiclient');
    }
    
    /**
     * Get dependency status for display
     */
    public static function get_dependency_status() {
        $vendor_exists = file_exists(ACA_PLUGIN_PATH . 'vendor/autoload.php');
        $google_client_exists = file_exists(ACA_PLUGIN_PATH . 'vendor/google/apiclient');
        $composer_json_exists = file_exists(ACA_PLUGIN_PATH . 'composer.json');
        
        return array(
            'vendor_exists' => $vendor_exists,
            'google_client_exists' => $google_client_exists,
            'composer_json_exists' => $composer_json_exists,
            'all_installed' => $vendor_exists && $google_client_exists,
            'can_auto_install' => function_exists('exec') && $composer_json_exists,
            'composer_available' => $this->is_composer_available()
        );
    }
    
    /**
     * Check if Composer is available on the system
     */
    private static function is_composer_available() {
        if (!function_exists('exec')) {
            return false;
        }
        
        $output = array();
        $return_code = 0;
        
        // Try different composer commands
        $commands = array('composer', 'composer.phar', '/usr/local/bin/composer');
        
        foreach ($commands as $command) {
            exec($command . ' --version 2>/dev/null', $output, $return_code);
            if ($return_code === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * AJAX handler to install dependencies
     */
    public function install_dependencies() {
        // Check permissions
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'aca_install_dependencies')) {
            wp_die('Security check failed');
        }
        
        $result = $this->run_composer_install();
        
        wp_send_json($result);
    }
    
    /**
     * Run composer install
     */
    private function run_composer_install() {
        if (!function_exists('exec')) {
            return array(
                'success' => false,
                'message' => 'exec() function is not available. Please install dependencies manually.',
                'manual_instructions' => $this->get_manual_instructions()
            );
        }
        
        $plugin_dir = ACA_PLUGIN_PATH;
        $output = array();
        $return_code = 0;
        
        // Change to plugin directory and run composer install
        $command = "cd " . escapeshellarg($plugin_dir) . " && composer install --no-dev --optimize-autoloader 2>&1";
        
        exec($command, $output, $return_code);
        
        if ($return_code === 0) {
            return array(
                'success' => true,
                'message' => 'Dependencies installed successfully!',
                'output' => implode("\n", $output)
            );
        } else {
            return array(
                'success' => false,
                'message' => 'Failed to install dependencies. Please install manually.',
                'output' => implode("\n", $output),
                'manual_instructions' => $this->get_manual_instructions()
            );
        }
    }
    
    /**
     * Get manual installation instructions
     */
    private function get_manual_instructions() {
        return array(
            'title' => 'Manual Installation Instructions',
            'steps' => array(
                '1. Connect to your server via SSH or FTP',
                '2. Navigate to: ' . ACA_PLUGIN_PATH,
                '3. Run: composer install --no-dev --optimize-autoloader',
                '4. Alternatively, download dependencies manually from GitHub',
                '5. Refresh this page to check installation status'
            ),
            'requirements' => array(
                'Composer must be installed on your server',
                'PHP exec() function must be enabled',
                'Write permissions to the plugin directory'
            )
        );
    }
    
    /**
     * Display dependency status in admin
     */
    public static function display_dependency_status() {
        $status = self::get_dependency_status();
        
        if ($status['all_installed']) {
            echo '<div class="notice notice-success"><p><strong>✅ Google API Dependencies:</strong> All required libraries are installed and ready.</p></div>';
            return;
        }
        
        echo '<div class="notice notice-warning"><p><strong>⚠️ Google API Dependencies:</strong> Required libraries are missing.</p>';
        
        if ($status['can_auto_install']) {
            echo '<p><button id="aca-install-deps" class="button button-primary">Install Dependencies Automatically</button></p>';
        } else {
            echo '<p>Automatic installation is not available. Please install manually:</p>';
            echo '<ol>';
            echo '<li>Connect to your server via SSH</li>';
            echo '<li>Navigate to: <code>' . ACA_PLUGIN_PATH . '</code></li>';
            echo '<li>Run: <code>composer install --no-dev --optimize-autoloader</code></li>';
            echo '</ol>';
        }
        
        echo '</div>';
        
        // Add JavaScript for auto-installation
        if ($status['can_auto_install']) {
            ?>
            <script>
            jQuery(document).ready(function($) {
                $('#aca-install-deps').click(function() {
                    var button = $(this);
                    button.prop('disabled', true).text('Installing...');
                    
                    $.post(ajaxurl, {
                        action: 'aca_install_dependencies',
                        nonce: '<?php echo wp_create_nonce('aca_install_dependencies'); ?>'
                    }, function(response) {
                        if (response.success) {
                            button.text('✅ Installed Successfully');
                            location.reload();
                        } else {
                            button.prop('disabled', false).text('Install Dependencies');
                            alert('Installation failed: ' + response.message);
                            if (response.manual_instructions) {
                                console.log('Manual instructions:', response.manual_instructions);
                            }
                        }
                    }).fail(function() {
                        button.prop('disabled', false).text('Install Dependencies');
                        alert('Installation request failed. Please try manual installation.');
                    });
                });
            });
            </script>
            <?php
        }
    }
}

// Initialize the installer
new ACA_Dependencies_Installer();