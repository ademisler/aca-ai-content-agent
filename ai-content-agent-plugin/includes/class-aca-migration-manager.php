<?php
if (!defined('ABSPATH')) {
    exit;
}

class ACA_Migration_Manager {
    
    private $current_version;
    private $target_version;
    private $migrations_path;
    
    public function __construct() {
        $this->migrations_path = plugin_dir_path(__FILE__) . '../migrations/';
        $this->current_version = get_option('aca_db_version', '0.0.0');
        $this->target_version = defined('ACA_VERSION') ? ACA_VERSION : '2.4.0';
    }
    
    /**
     * Run migrations safely - only for future updates
     */
    public function run_migrations() {
        // First time installation - let existing activator handle it
        if ($this->current_version === '0.0.0') {
            $this->mark_initial_version();
            return true;
        }
        
        $pending_migrations = $this->get_pending_migrations();
        
        if (empty($pending_migrations)) {
            return true;
        }
        
        foreach ($pending_migrations as $migration) {
            $result = $this->execute_migration($migration);
            if (is_wp_error($result)) {
                error_log('ACA Migration failed: ' . $result->get_error_message());
                return $result;
            }
            
            $this->update_migration_version($migration['version']);
        }
        
        return true;
    }
    
    /**
     * Mark initial version for existing installations
     */
    private function mark_initial_version() {
        global $wpdb;
        
        // Check if tables exist (existing installation)
        $tables_exist = $wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}aca_ideas'");
        
        if ($tables_exist) {
            // Existing installation - mark current version and log existing options
            $this->log_existing_options();
            update_option('aca_db_version', $this->target_version);
            $this->log_migration($this->target_version, 'Initial version marked for existing installation');
        } else {
            // New installation - will be handled by activator, then marked
            update_option('aca_db_version', $this->target_version);
        }
    }
    
    /**
     * Log existing options for migration tracking
     */
    private function log_existing_options() {
        $existing_options = array(
            'aca_settings',
            'aca_style_guide', 
            'aca_freshness_settings',
            'aca_gsc_tokens',
            'aca_license_status',
            'aca_license_data',
            'aca_license_site_hash',
            'aca_google_cloud_project_id',
            'aca_google_cloud_location',
            'aca_last_cron_run',
            'aca_last_freshness_analysis'
        );
        
        $found_options = array();
        foreach ($existing_options as $option) {
            if (get_option($option) !== false) {
                $found_options[] = $option;
            }
        }
        
        if (!empty($found_options)) {
            $this->log_migration('existing_options', 'Found existing options: ' . implode(', ', $found_options));
        }
    }
    
    /**
     * Get list of pending migrations
     */
    private function get_pending_migrations() {
        $migrations = array();
        
        if (!is_dir($this->migrations_path)) {
            return $migrations;
        }
        
        $migration_files = glob($this->migrations_path . '*.php');
        
        if (!$migration_files) {
            return $migrations;
        }
        
        foreach ($migration_files as $file) {
            $filename = basename($file, '.php');
            // Format: YYYY_MM_DD_HHMMSS_description
            if (preg_match('/^(\d{4}_\d{2}_\d{2}_\d{6})_(.+)$/', $filename, $matches)) {
                $version = $matches[1];
                $name = $matches[2];
                
                if (version_compare($version, $this->current_version, '>')) {
                    $migrations[] = array(
                        'version' => $version,
                        'name' => $name,
                        'file' => $file
                    );
                }
            }
        }
        
        // Sort by version
        usort($migrations, function($a, $b) {
            return version_compare($a['version'], $b['version']);
        });
        
        return $migrations;
    }
    
    /**
     * Execute a single migration with proper error handling
     */
    private function execute_migration($migration) {
        global $wpdb;
        
        try {
            // Start transaction (if supported)
            $wpdb->query('START TRANSACTION');
            
            // Include migration file
            if (!file_exists($migration['file'])) {
                throw new Exception("Migration file not found: {$migration['file']}");
            }
            
            $migration_instance = include $migration['file'];
            
            if (!is_object($migration_instance) || !method_exists($migration_instance, 'up')) {
                throw new Exception('Migration must return an object with an "up" method');
            }
            
            // Execute migration
            $result = $migration_instance->up();
            
            if ($result === false) {
                throw new Exception('Migration execution returned false');
            }
            
            // Commit transaction
            $wpdb->query('COMMIT');
            
            // Log successful migration
            $this->log_migration($migration['version'], "Migration {$migration['name']} executed successfully");
            
            return true;
            
        } catch (Exception $e) {
            // Rollback transaction
            $wpdb->query('ROLLBACK');
            return new WP_Error('migration_failed', $e->getMessage());
        }
    }
    
    /**
     * Update migration version
     */
    private function update_migration_version($version) {
        update_option('aca_db_version', $version);
    }
    
    /**
     * Log migration for tracking
     */
    private function log_migration($version, $message) {
        $migration_log = get_option('aca_migration_log', array());
        $migration_log[] = array(
            'version' => $version,
            'message' => $message,
            'timestamp' => current_time('mysql'),
            'checksum' => md5($version . current_time('timestamp'))
        );
        
        // Keep only last 50 migration logs
        if (count($migration_log) > 50) {
            $migration_log = array_slice($migration_log, -50);
        }
        
        update_option('aca_migration_log', $migration_log);
        error_log("ACA Migration: $message");
    }
}