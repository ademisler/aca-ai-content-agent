# AI Content Agent (ACA) - Bug Fix Implementation Roadmap

This document provides detailed implementation instructions for fixing all bugs listed in `report.md`. Each solution includes specific file paths, code snippets, and step-by-step instructions optimized for AI code tools.

---

## **CRITICAL PRIORITY - Immediate Action Required**

### **BUG-003: SEO Plugin Detection API Mismatch**
**Status**: Critical - Fix immediately  
**Estimated Time**: 2-4 hours  
**Files to Modify**: Frontend components calling API endpoints

#### **Analysis**:
The bug report indicates a mismatch between frontend API calls (`seo/plugins`) and backend routes (`seo-plugins`). However, after code analysis, the actual situation is:
- Backend REST API endpoint: `/wp-json/aca/v1/seo-plugins` (correctly implemented)
- Frontend calls: All using correct `seo-plugins` endpoint
- **CONCLUSION**: This bug appears to be already resolved or misreported

#### **Verification Steps**:
1. **Check all frontend API calls**:
   ```bash
   # Search for any remaining incorrect API calls
   grep -r "seo/plugins" components/ admin/js/ --include="*.tsx" --include="*.ts" --include="*.js"
   ```

2. **If any incorrect calls found, fix them**:
   ```typescript
   // WRONG (if found):
   const response = await fetch(`${window.acaData.api_url}seo/plugins`);
   
   // CORRECT:
   const response = await fetch(`${window.acaData.api_url}seo-plugins`);
   ```

3. **Verify REST API endpoint registration** in `includes/class-aca-rest-api.php` line 54:
   ```php
   register_rest_route('aca/v1', '/seo-plugins', array(
       'methods' => 'GET',
       'callback' => array($this, 'get_seo_plugins'),
       'permission_callback' => array($this, 'check_seo_permissions')
   ));
   ```

#### **Implementation Steps**:
1. Run verification grep command
2. If no mismatches found, mark bug as resolved
3. If mismatches found, update frontend calls to use `seo-plugins`
4. Add unit tests to prevent regression

---

## **HIGH PRIORITY - Address Within 1-3 Weeks**

### **BUG-005: Missing Database Migration System**
**Status**: High - Implement within 2 weeks  
**Estimated Time**: 1-2 weeks  
**Files to Create/Modify**: 
- `includes/class-aca-migration-manager.php` (new)
- `includes/class-aca-activator.php` (modify)
- `migrations/` directory (new)

#### **Current State Analysis**:
The current `class-aca-activator.php` creates tables directly without version control:
- Uses `dbDelta()` for table creation
- No version tracking
- No migration rollback capability
- Risk of data corruption during updates

#### **Implementation Steps**:

1. **Create Migration Manager Class**:
   ```php
   // File: includes/class-aca-migration-manager.php
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
           $this->target_version = ACA_VERSION;
       }
       
       /**
        * Run all pending migrations
        */
       public function run_migrations() {
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
        * Get list of pending migrations
        */
       private function get_pending_migrations() {
           $migrations = array();
           $migration_files = glob($this->migrations_path . '*.php');
           
           if (!$migration_files) {
               return $migrations;
           }
           
           foreach ($migration_files as $file) {
               $filename = basename($file, '.php');
               preg_match('/^(\d+)_(.+)$/', $filename, $matches);
               
               if (count($matches) === 3) {
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
        * Execute a single migration
        */
       private function execute_migration($migration) {
           global $wpdb;
           
           try {
               // Start transaction
               $wpdb->query('START TRANSACTION');
               
               // Include migration file
               $migration_instance = include $migration['file'];
               
               if (!is_callable(array($migration_instance, 'up'))) {
                   throw new Exception('Migration must have an "up" method');
               }
               
               // Execute migration
               $result = $migration_instance->up();
               
               if ($result === false) {
                   throw new Exception('Migration execution failed');
               }
               
               // Commit transaction
               $wpdb->query('COMMIT');
               
               // Log successful migration
               error_log("ACA Migration {$migration['version']} executed successfully");
               
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
           
           // Log migration completion
           $this->log_migration($version);
       }
       
       /**
        * Log migration for tracking
        */
       private function log_migration($version) {
           $migration_log = get_option('aca_migration_log', array());
           $migration_log[] = array(
               'version' => $version,
               'timestamp' => current_time('mysql'),
               'checksum' => md5($version . current_time('timestamp'))
           );
           update_option('aca_migration_log', $migration_log);
       }
       
       /**
        * Rollback to specific version (future enhancement)
        */
       public function rollback_to_version($target_version) {
           // Implementation for rollback functionality
           // This would require down() methods in migration files
           return new WP_Error('not_implemented', 'Rollback functionality not yet implemented');
       }
   }
   ```

2. **Create Migration Directory Structure**:
   ```bash
   mkdir -p ai-content-agent-plugin/migrations
   ```

3. **Create Initial Migration**:
   ```php
   // File: migrations/001_initial_schema.php
   <?php
   return new class {
       public function up() {
           global $wpdb;
           
           $charset_collate = $wpdb->get_charset_collate();
           
           // Ideas table
           $ideas_table_name = $wpdb->prefix . 'aca_ideas';
           $sql_ideas = "CREATE TABLE $ideas_table_name (
               id mediumint(9) NOT NULL AUTO_INCREMENT,
               title text NOT NULL,
               status varchar(20) DEFAULT 'new' NOT NULL,
               source varchar(20) DEFAULT 'ai' NOT NULL,
               created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
               PRIMARY KEY  (id)
           ) $charset_collate;";
           
           // Activity logs table
           $logs_table_name = $wpdb->prefix . 'aca_activity_logs';
           $sql_logs = "CREATE TABLE $logs_table_name (
               id mediumint(9) NOT NULL AUTO_INCREMENT,
               timestamp datetime NOT NULL,
               type varchar(50) NOT NULL,
               details text NOT NULL,
               icon varchar(50) NOT NULL,
               PRIMARY KEY  (id)
           ) $charset_collate;";
           
           // Content updates tracking table
           $content_updates_table_name = $wpdb->prefix . 'aca_content_updates';
           $sql_content_updates = "CREATE TABLE $content_updates_table_name (
               id mediumint(9) NOT NULL AUTO_INCREMENT,
               post_id bigint(20) NOT NULL,
               last_updated datetime NOT NULL,
               update_type varchar(50) NOT NULL,
               ai_suggestions longtext,
               status varchar(20) DEFAULT 'pending',
               created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
               PRIMARY KEY  (id),
               KEY post_id (post_id),
               KEY status (status)
           ) $charset_collate;";
           
           // Content freshness scores table
           $content_freshness_table_name = $wpdb->prefix . 'aca_content_freshness';
           $sql_content_freshness = "CREATE TABLE $content_freshness_table_name (
               post_id bigint(20) NOT NULL,
               freshness_score tinyint(3) DEFAULT 0,
               last_analyzed datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
               needs_update tinyint(1) DEFAULT 0,
               update_priority tinyint(1) DEFAULT 1,
               PRIMARY KEY  (post_id)
           ) $charset_collate;";
           
           require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
           dbDelta($sql_ideas);
           dbDelta($sql_logs);
           dbDelta($sql_content_updates);
           dbDelta($sql_content_freshness);
           
           return true;
       }
       
       public function down() {
           global $wpdb;
           
           $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aca_ideas");
           $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aca_activity_logs");
           $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aca_content_updates");
           $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}aca_content_freshness");
           
           return true;
       }
   };
   ```

4. **Modify Activator Class**:
   ```php
   // File: includes/class-aca-activator.php
   // Replace the activate() method:
   
   public static function activate() {
       // Run migrations instead of direct table creation
       require_once plugin_dir_path(__FILE__) . 'class-aca-migration-manager.php';
       
       $migration_manager = new ACA_Migration_Manager();
       $result = $migration_manager->run_migrations();
       
       if (is_wp_error($result)) {
           error_log('ACA Activation failed: ' . $result->get_error_message());
           wp_die('Plugin activation failed: ' . $result->get_error_message());
       }
       
       self::set_default_options();
       self::schedule_cron_jobs();
   }
   
   // Remove or comment out the create_tables() method as it's now handled by migrations
   ```

5. **Add Migration Hook to Main Plugin File**:
   ```php
   // File: ai-content-agent.php
   // Add this after plugin initialization:
   
   // Check for database updates on admin_init
   add_action('admin_init', 'aca_check_database_updates');
   
   function aca_check_database_updates() {
       if (is_admin() && current_user_can('activate_plugins')) {
           require_once plugin_dir_path(__FILE__) . 'includes/class-aca-migration-manager.php';
           
           $migration_manager = new ACA_Migration_Manager();
           $result = $migration_manager->run_migrations();
           
           if (is_wp_error($result)) {
               add_action('admin_notices', function() use ($result) {
                   echo '<div class="notice notice-error"><p>ACA Database Update Failed: ' . 
                        esc_html($result->get_error_message()) . '</p></div>';
               });
           }
       }
   }
   ```

### **BUG-100: Version Compatibility Issues**
**Status**: High - Implement within 1 week  
**Estimated Time**: 3-5 days  
**Files to Create/Modify**:
- `includes/class-aca-compatibility-checker.php` (new)
- `includes/class-aca-activator.php` (modify)

#### **Implementation Steps**:

1. **Create Compatibility Checker Class**:
   ```php
   // File: includes/class-aca-compatibility-checker.php
   <?php
   if (!defined('ABSPATH')) {
       exit;
   }

   class ACA_Compatibility_Checker {
       
       private $supported_plugins = array(
           'wordpress-seo/wp-seo.php' => array(
               'name' => 'Yoast SEO',
               'min_version' => '19.0',
               'max_version' => '22.0',
               'tested_version' => '21.8'
           ),
           'seo-by-rank-math/rank-math.php' => array(
               'name' => 'Rank Math',
               'min_version' => '1.0.95',
               'max_version' => '1.1.0',
               'tested_version' => '1.0.112'
           ),
           'all-in-one-seo-pack/all_in_one_seo_pack.php' => array(
               'name' => 'All in One SEO',
               'min_version' => '4.3.0',
               'max_version' => '4.5.0',
               'tested_version' => '4.4.1'
           )
       );
       
       /**
        * Check compatibility of all active plugins
        */
       public function check_all_compatibility() {
           $results = array();
           $active_plugins = get_option('active_plugins', array());
           
           foreach ($this->supported_plugins as $plugin_file => $plugin_info) {
               if (in_array($plugin_file, $active_plugins)) {
                   $results[$plugin_file] = $this->check_plugin_compatibility($plugin_file);
               }
           }
           
           return $results;
       }
       
       /**
        * Check compatibility of specific plugin
        */
       public function check_plugin_compatibility($plugin_file) {
           if (!isset($this->supported_plugins[$plugin_file])) {
               return array(
                   'status' => 'unknown',
                   'message' => 'Plugin not in compatibility matrix'
               );
           }
           
           $plugin_info = $this->supported_plugins[$plugin_file];
           $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_file);
           $current_version = $plugin_data['Version'];
           
           // Check minimum version
           if (version_compare($current_version, $plugin_info['min_version'], '<')) {
               return array(
                   'status' => 'incompatible',
                   'message' => sprintf(
                       '%s version %s is below minimum required version %s',
                       $plugin_info['name'],
                       $current_version,
                       $plugin_info['min_version']
                   ),
                   'action' => 'update_required'
               );
           }
           
           // Check maximum version
           if (version_compare($current_version, $plugin_info['max_version'], '>=')) {
               return array(
                   'status' => 'warning',
                   'message' => sprintf(
                       '%s version %s has not been tested with ACA. Last tested version: %s',
                       $plugin_info['name'],
                       $current_version,
                       $plugin_info['tested_version']
                   ),
                   'action' => 'proceed_with_caution'
               );
           }
           
           return array(
               'status' => 'compatible',
               'message' => sprintf(
                   '%s version %s is compatible',
                   $plugin_info['name'],
                   $current_version
               ),
               'action' => 'none'
           );
       }
       
       /**
        * Get compatibility report for admin display
        */
       public function get_compatibility_report() {
           $compatibility_results = $this->check_all_compatibility();
           $report = array(
               'compatible' => array(),
               'warnings' => array(),
               'incompatible' => array(),
               'unknown' => array()
           );
           
           foreach ($compatibility_results as $plugin_file => $result) {
               $plugin_info = $this->supported_plugins[$plugin_file];
               $report_item = array(
                   'name' => $plugin_info['name'],
                   'file' => $plugin_file,
                   'message' => $result['message'],
                   'action' => $result['action']
               );
               
               switch ($result['status']) {
                   case 'compatible':
                       $report['compatible'][] = $report_item;
                       break;
                   case 'warning':
                       $report['warnings'][] = $report_item;
                       break;
                   case 'incompatible':
                       $report['incompatible'][] = $report_item;
                       break;
                   default:
                       $report['unknown'][] = $report_item;
               }
           }
           
           return $report;
       }
       
       /**
        * Display admin notices for compatibility issues
        */
       public function display_compatibility_notices() {
           $compatibility_results = $this->check_all_compatibility();
           
           foreach ($compatibility_results as $plugin_file => $result) {
               if ($result['status'] === 'incompatible') {
                   add_action('admin_notices', function() use ($result) {
                       echo '<div class="notice notice-error"><p><strong>ACA Compatibility Issue:</strong> ' . 
                            esc_html($result['message']) . '</p></div>';
                   });
               } elseif ($result['status'] === 'warning') {
                   add_action('admin_notices', function() use ($result) {
                       echo '<div class="notice notice-warning"><p><strong>ACA Compatibility Warning:</strong> ' . 
                            esc_html($result['message']) . '</p></div>';
                   });
               }
           }
       }
   }
   ```

2. **Add Compatibility Checks to Plugin Activation**:
   ```php
   // File: includes/class-aca-activator.php
   // Add to activate() method:
   
   public static function activate() {
       // Check compatibility before activation
       require_once plugin_dir_path(__FILE__) . 'class-aca-compatibility-checker.php';
       
       $compatibility_checker = new ACA_Compatibility_Checker();
       $compatibility_results = $compatibility_checker->check_all_compatibility();
       
       // Check for critical incompatibilities
       foreach ($compatibility_results as $plugin_file => $result) {
           if ($result['status'] === 'incompatible') {
               wp_die(
                   'ACA Plugin Activation Failed: ' . $result['message'],
                   'Compatibility Error',
                   array('back_link' => true)
               );
           }
       }
       
       // Continue with normal activation...
       // ... existing activation code
   }
   ```

3. **Add Runtime Compatibility Monitoring**:
   ```php
   // File: ai-content-agent.php
   // Add after plugin initialization:
   
   // Monitor plugin updates
   add_action('upgrader_process_complete', 'aca_check_plugin_updates', 10, 2);
   add_action('activated_plugin', 'aca_validate_plugin_compatibility');
   
   function aca_check_plugin_updates($upgrader, $hook_extra) {
       if (isset($hook_extra['plugins'])) {
           require_once plugin_dir_path(__FILE__) . 'includes/class-aca-compatibility-checker.php';
           
           $compatibility_checker = new ACA_Compatibility_Checker();
           $compatibility_checker->display_compatibility_notices();
       }
   }
   
   function aca_validate_plugin_compatibility($plugin) {
       require_once plugin_dir_path(__FILE__) . 'includes/class-aca-compatibility-checker.php';
       
       $compatibility_checker = new ACA_Compatibility_Checker();
       $result = $compatibility_checker->check_plugin_compatibility($plugin);
       
       if ($result['status'] === 'incompatible') {
           add_action('admin_notices', function() use ($result) {
               echo '<div class="notice notice-error"><p><strong>ACA Compatibility Issue:</strong> ' . 
                    esc_html($result['message']) . '</p></div>';
           });
       }
   }
   ```

### **BUG-102: No Multisite Support**
**Status**: High - Implement within 2 weeks  
**Estimated Time**: 1-2 weeks  
**Files to Create/Modify**:
- `includes/class-aca-multisite-manager.php` (new)
- `ai-content-agent.php` (modify)
- `includes/class-aca-activator.php` (modify)

#### **Implementation Steps**:

1. **Create Multisite Manager Class**:
   ```php
   // File: includes/class-aca-multisite-manager.php
   <?php
   if (!defined('ABSPATH')) {
       exit;
   }

   class ACA_Multisite_Manager {
       
       /**
        * Initialize multisite functionality
        */
       public function init() {
           if (!is_multisite()) {
               return;
           }
           
           // Network admin hooks
           add_action('network_admin_menu', array($this, 'add_network_admin_menu'));
           add_action('wp_network_dashboard_setup', array($this, 'add_network_dashboard_widgets'));
           
           // Site-specific hooks
           add_action('wp_initialize_site', array($this, 'initialize_new_site'));
           add_action('wp_delete_site', array($this, 'cleanup_deleted_site'));
           
           // Settings management
           add_filter('aca_get_setting', array($this, 'filter_site_settings'), 10, 3);
           add_action('aca_update_setting', array($this, 'update_site_setting'), 10, 3);
       }
       
       /**
        * Add network admin menu
        */
       public function add_network_admin_menu() {
           add_menu_page(
               'AI Content Agent Network',
               'ACA Network',
               'manage_network_options',
               'aca-network',
               array($this, 'network_admin_page'),
               'dashicons-robot',
               30
           );
           
           add_submenu_page(
               'aca-network',
               'Network Settings',
               'Network Settings',
               'manage_network_options',
               'aca-network-settings',
               array($this, 'network_settings_page')
           );
           
           add_submenu_page(
               'aca-network',
               'Site Management',
               'Site Management',
               'manage_network_options',
               'aca-site-management',
               array($this, 'site_management_page')
           );
       }
       
       /**
        * Network admin dashboard page
        */
       public function network_admin_page() {
           $sites = get_sites(array('number' => 100));
           $active_sites = 0;
           $total_content = 0;
           
           foreach ($sites as $site) {
               switch_to_blog($site->blog_id);
               
               if (get_option('aca_settings')) {
                   $active_sites++;
               }
               
               $total_content += wp_count_posts()->publish;
               restore_current_blog();
           }
           
           ?>
           <div class="wrap">
               <h1>AI Content Agent - Network Dashboard</h1>
               
               <div class="aca-network-stats">
                   <div class="aca-stat-box">
                       <h3>Active Sites</h3>
                       <p class="aca-stat-number"><?php echo $active_sites; ?></p>
                   </div>
                   <div class="aca-stat-box">
                       <h3>Total Sites</h3>
                       <p class="aca-stat-number"><?php echo count($sites); ?></p>
                   </div>
                   <div class="aca-stat-box">
                       <h3>Total Content</h3>
                       <p class="aca-stat-number"><?php echo $total_content; ?></p>
                   </div>
               </div>
               
               <h2>Recent Activity Across Network</h2>
               <?php $this->display_network_activity(); ?>
           </div>
           
           <style>
           .aca-network-stats {
               display: flex;
               gap: 20px;
               margin: 20px 0;
           }
           .aca-stat-box {
               background: #fff;
               border: 1px solid #ccd0d4;
               padding: 20px;
               text-align: center;
               flex: 1;
           }
           .aca-stat-number {
               font-size: 2em;
               font-weight: bold;
               color: #0073aa;
               margin: 0;
           }
           </style>
           <?php
       }
       
       /**
        * Network settings page
        */
       public function network_settings_page() {
           if (isset($_POST['submit'])) {
               $this->save_network_settings();
           }
           
           $network_settings = get_site_option('aca_network_settings', array());
           
           ?>
           <div class="wrap">
               <h1>ACA Network Settings</h1>
               
               <form method="post" action="">
                   <?php wp_nonce_field('aca_network_settings'); ?>
                   
                   <table class="form-table">
                       <tr>
                           <th scope="row">Default API Keys</th>
                           <td>
                               <fieldset>
                                   <label>
                                       <input type="checkbox" name="inherit_api_keys" value="1" 
                                              <?php checked(isset($network_settings['inherit_api_keys'])); ?>>
                                       Allow sites to inherit network API keys
                                   </label>
                               </fieldset>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Gemini API Key</th>
                           <td>
                               <input type="password" name="gemini_api_key" 
                                      value="<?php echo esc_attr($network_settings['gemini_api_key'] ?? ''); ?>" 
                                      class="regular-text">
                               <p class="description">Network-wide Gemini API key (optional)</p>
                           </td>
                       </tr>
                       <tr>
                           <th scope="row">Auto-activate for New Sites</th>
                           <td>
                               <fieldset>
                                   <label>
                                       <input type="checkbox" name="auto_activate" value="1" 
                                              <?php checked(isset($network_settings['auto_activate'])); ?>>
                                       Automatically activate ACA for new sites
                                   </label>
                               </fieldset>
                           </td>
                       </tr>
                   </table>
                   
                   <?php submit_button('Save Network Settings'); ?>
               </form>
           </div>
           <?php
       }
       
       /**
        * Site management page
        */
       public function site_management_page() {
           $sites = get_sites(array('number' => 100));
           
           ?>
           <div class="wrap">
               <h1>ACA Site Management</h1>
               
               <table class="wp-list-table widefat fixed striped">
                   <thead>
                       <tr>
                           <th>Site</th>
                           <th>Status</th>
                           <th>Last Activity</th>
                           <th>Content Count</th>
                           <th>Actions</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($sites as $site): ?>
                           <?php
                           switch_to_blog($site->blog_id);
                           $aca_settings = get_option('aca_settings');
                           $is_active = !empty($aca_settings);
                           $content_count = wp_count_posts()->publish;
                           restore_current_blog();
                           ?>
                           <tr>
                               <td>
                                   <strong><?php echo esc_html($site->domain . $site->path); ?></strong>
                                   <br>
                                   <small>ID: <?php echo $site->blog_id; ?></small>
                               </td>
                               <td>
                                   <span class="aca-status <?php echo $is_active ? 'active' : 'inactive'; ?>">
                                       <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                                   </span>
                               </td>
                               <td>
                                   <?php echo $this->get_site_last_activity($site->blog_id); ?>
                               </td>
                               <td><?php echo $content_count; ?></td>
                               <td>
                                   <a href="<?php echo network_admin_url('admin.php?page=aca-site-settings&site_id=' . $site->blog_id); ?>" 
                                      class="button">Configure</a>
                               </td>
                           </tr>
                       <?php endforeach; ?>
                   </tbody>
               </table>
           </div>
           
           <style>
           .aca-status.active { color: #00a32a; font-weight: bold; }
           .aca-status.inactive { color: #d63638; }
           </style>
           <?php
       }
       
       /**
        * Initialize new site with ACA
        */
       public function initialize_new_site($site) {
           $network_settings = get_site_option('aca_network_settings', array());
           
           if (isset($network_settings['auto_activate']) && $network_settings['auto_activate']) {
               switch_to_blog($site->blog_id);
               
               // Create default settings with network inheritance
               $default_settings = array(
                   'mode' => 'manual',
                   'autoPublish' => false,
                   'searchConsoleUser' => null,
                   'seoPlugin' => 'none',
               );
               
               // Inherit network API keys if enabled
               if (isset($network_settings['inherit_api_keys']) && $network_settings['inherit_api_keys']) {
                   if (!empty($network_settings['gemini_api_key'])) {
                       $default_settings['geminiApiKey'] = $network_settings['gemini_api_key'];
                   }
               }
               
               add_option('aca_settings', $default_settings);
               
               // Run site-specific activation
               require_once plugin_dir_path(__FILE__) . 'class-aca-activator.php';
               ACA_Activator::activate();
               
               restore_current_blog();
           }
       }
       
       /**
        * Clean up deleted site data
        */
       public function cleanup_deleted_site($site) {
           // Clean up any network-wide references to this site
           $network_activity = get_site_option('aca_network_activity', array());
           $network_activity = array_filter($network_activity, function($activity) use ($site) {
               return $activity['blog_id'] !== $site->blog_id;
           });
           update_site_option('aca_network_activity', $network_activity);
       }
       
       /**
        * Filter settings to handle network inheritance
        */
       public function filter_site_settings($value, $key, $default) {
           if (!is_multisite()) {
               return $value;
           }
           
           // If site setting is empty, check for network default
           if (empty($value)) {
               $network_settings = get_site_option('aca_network_settings', array());
               
               if (isset($network_settings['inherit_api_keys']) && $network_settings['inherit_api_keys']) {
                   switch ($key) {
                       case 'geminiApiKey':
                           return $network_settings['gemini_api_key'] ?? $default;
                   }
               }
           }
           
           return $value;
       }
       
       /**
        * Save network settings
        */
       private function save_network_settings() {
           if (!wp_verify_nonce($_POST['_wpnonce'], 'aca_network_settings')) {
               wp_die('Security check failed');
           }
           
           $network_settings = array(
               'inherit_api_keys' => isset($_POST['inherit_api_keys']),
               'gemini_api_key' => sanitize_text_field($_POST['gemini_api_key']),
               'auto_activate' => isset($_POST['auto_activate'])
           );
           
           update_site_option('aca_network_settings', $network_settings);
           
           add_action('admin_notices', function() {
               echo '<div class="notice notice-success"><p>Network settings saved!</p></div>';
           });
       }
       
       /**
        * Get last activity for a site
        */
       private function get_site_last_activity($blog_id) {
           switch_to_blog($blog_id);
           
           global $wpdb;
           $last_activity = $wpdb->get_var(
               "SELECT timestamp FROM {$wpdb->prefix}aca_activity_logs 
                ORDER BY timestamp DESC LIMIT 1"
           );
           
           restore_current_blog();
           
           return $last_activity ? human_time_diff(strtotime($last_activity)) . ' ago' : 'No activity';
       }
       
       /**
        * Display network-wide activity
        */
       private function display_network_activity() {
           $sites = get_sites(array('number' => 20));
           $network_activity = array();
           
           foreach ($sites as $site) {
               switch_to_blog($site->blog_id);
               
               global $wpdb;
               $activities = $wpdb->get_results(
                   "SELECT * FROM {$wpdb->prefix}aca_activity_logs 
                    ORDER BY timestamp DESC LIMIT 5"
               );
               
               foreach ($activities as $activity) {
                   $activity->site_name = $site->domain . $site->path;
                   $activity->blog_id = $site->blog_id;
                   $network_activity[] = $activity;
               }
               
               restore_current_blog();
           }
           
           // Sort by timestamp
           usort($network_activity, function($a, $b) {
               return strtotime($b->timestamp) - strtotime($a->timestamp);
           });
           
           // Display top 10
           $network_activity = array_slice($network_activity, 0, 10);
           
           if (empty($network_activity)) {
               echo '<p>No recent activity across the network.</p>';
               return;
           }
           
           echo '<table class="wp-list-table widefat fixed striped">';
           echo '<thead><tr><th>Site</th><th>Activity</th><th>Time</th></tr></thead>';
           echo '<tbody>';
           
           foreach ($network_activity as $activity) {
               echo '<tr>';
               echo '<td>' . esc_html($activity->site_name) . '</td>';
               echo '<td>' . esc_html($activity->details) . '</td>';
               echo '<td>' . human_time_diff(strtotime($activity->timestamp)) . ' ago</td>';
               echo '</tr>';
           }
           
           echo '</tbody></table>';
       }
   }
   ```

2. **Modify Main Plugin File for Multisite**:
   ```php
   // File: ai-content-agent.php
   // Add after plugin initialization:
   
   // Initialize multisite support
   if (is_multisite()) {
       require_once plugin_dir_path(__FILE__) . 'includes/class-aca-multisite-manager.php';
       $aca_multisite_manager = new ACA_Multisite_Manager();
       $aca_multisite_manager->init();
   }
   ```

3. **Create Network Database Schema**:
   ```php
   // File: migrations/002_multisite_support.php
   <?php
   return new class {
       public function up() {
           if (!is_multisite()) {
               return true;
           }
           
           global $wpdb;
           
           $charset_collate = $wpdb->get_charset_collate();
           
           // Network settings table
           $network_settings_table = $wpdb->base_prefix . 'aca_network_settings';
           $sql = "CREATE TABLE $network_settings_table (
               id mediumint(9) NOT NULL AUTO_INCREMENT,
               blog_id bigint(20) NOT NULL DEFAULT 0,
               setting_key varchar(255) NOT NULL,
               setting_value longtext,
               is_network_wide tinyint(1) DEFAULT 0,
               created_at datetime DEFAULT CURRENT_TIMESTAMP,
               updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
               PRIMARY KEY (id),
               KEY blog_id (blog_id),
               KEY setting_key (setting_key),
               KEY is_network_wide (is_network_wide),
               UNIQUE KEY unique_blog_setting (blog_id, setting_key)
           ) $charset_collate;";
           
           require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
           dbDelta($sql);
           
           return true;
       }
       
       public function down() {
           global $wpdb;
           
           $wpdb->query("DROP TABLE IF EXISTS {$wpdb->base_prefix}aca_network_settings");
           
           return true;
       }
   };
   ```

### **BUG-108: Token Refresh Failures**
**Status**: High - Fix within 3-5 days  
**Estimated Time**: 2-3 days  
**Files to Modify**: `includes/class-aca-google-search-console.php`

#### **Current State Analysis**:
The existing token refresh mechanism in `class-aca-google-search-console.php` (lines 132-167) has basic functionality but lacks:
- Proactive refresh before expiration
- Retry mechanism with exponential backoff
- User re-authentication flow
- Proper error handling and recovery

#### **Implementation Steps**:

1. **Enhance Token Refresh Mechanism**:
   ```php
   // File: includes/class-aca-google-search-console.php
   // Replace the existing refresh_token() method with this enhanced version:
   
   /**
    * Enhanced token refresh with retry mechanism and proactive refresh
    */
   private function refresh_token($force_refresh = false) {
       $current_tokens = get_option('aca_gsc_tokens');
       
       if (!$current_tokens) {
           error_log('ACA GSC: No tokens available for refresh');
           return false;
       }
       
       // Check if token needs refresh (proactive refresh 5 minutes before expiry)
       if (!$force_refresh && isset($current_tokens['expires_in'], $current_tokens['created'])) {
           $expires_at = $current_tokens['created'] + $current_tokens['expires_in'] - 300; // 5 min buffer
           if (time() < $expires_at) {
               return true; // Token still valid
           }
       }
       
       $refresh_token = $this->client->getRefreshToken();
       
       if (!$refresh_token && isset($current_tokens['refresh_token'])) {
           $refresh_token = $current_tokens['refresh_token'];
           $this->client->setAccessToken($current_tokens);
       }
       
       if (!$refresh_token) {
           error_log('ACA GSC: No refresh token available');
           $this->handle_refresh_failure('no_refresh_token');
           return false;
       }
       
       // Retry mechanism with exponential backoff
       $max_retries = 3;
       $retry_delay = 1; // Start with 1 second
       
       for ($attempt = 1; $attempt <= $max_retries; $attempt++) {
           try {
               error_log("ACA GSC: Token refresh attempt $attempt of $max_retries");
               
               $new_tokens = $this->client->fetchAccessTokenWithRefreshToken($refresh_token);
               
               if (isset($new_tokens['error'])) {
                   throw new Exception($new_tokens['error_description'] ?? $new_tokens['error']);
               }
               
               // Success - preserve refresh token and add metadata
               if (!isset($new_tokens['refresh_token'])) {
                   $new_tokens['refresh_token'] = $refresh_token;
               }
               
               $new_tokens['created'] = time();
               $new_tokens['last_refresh'] = time();
               $new_tokens['refresh_attempts'] = $attempt;
               
               update_option('aca_gsc_tokens', $new_tokens);
               
               // Reset failure count on success
               delete_option('aca_gsc_refresh_failures');
               
               error_log('ACA GSC: Successfully refreshed access token on attempt ' . $attempt);
               return true;
               
           } catch (Exception $e) {
               error_log("ACA GSC: Token refresh attempt $attempt failed: " . $e->getMessage());
               
               // If this is the last attempt, handle the failure
               if ($attempt === $max_retries) {
                   $this->handle_refresh_failure('max_retries_exceeded', $e->getMessage());
                   return false;
               }
               
               // Wait before retrying (exponential backoff)
               sleep($retry_delay);
               $retry_delay *= 2; // Double the delay for next attempt
           }
       }
       
       return false;
   }
   
   /**
    * Handle token refresh failures with recovery strategies
    */
   private function handle_refresh_failure($failure_type, $error_message = '') {
       $failure_count = get_option('aca_gsc_refresh_failures', 0) + 1;
       update_option('aca_gsc_refresh_failures', $failure_count);
       
       // Log the failure
       error_log("ACA GSC: Token refresh failure #$failure_count - Type: $failure_type - Message: $error_message");
       
       // After 3 consecutive failures, trigger re-authentication flow
       if ($failure_count >= 3) {
           $this->trigger_reauth_flow($failure_type, $error_message);
       } else {
           // Show admin notice for temporary failures
           add_action('admin_notices', function() use ($failure_count, $error_message) {
               echo '<div class="notice notice-warning is-dismissible">';
               echo '<p><strong>ACA Google Search Console:</strong> Token refresh failed (attempt ' . $failure_count . '/3). ';
               if ($error_message) {
                   echo 'Error: ' . esc_html($error_message) . ' ';
               }
               echo 'The plugin will retry automatically.</p>';
               echo '</div>';
           });
       }
   }
   
   /**
    * Trigger re-authentication flow
    */
   private function trigger_reauth_flow($failure_type, $error_message) {
       // Clear existing tokens
       delete_option('aca_gsc_tokens');
       delete_option('aca_gsc_refresh_failures');
       
       // Update settings to reflect disconnection
       $settings = get_option('aca_settings', array());
       $settings['searchConsoleUser'] = null;
       update_option('aca_settings', $settings);
       
       // Set flag to show re-authentication notice
       set_transient('aca_gsc_reauth_required', array(
           'failure_type' => $failure_type,
           'error_message' => $error_message,
           'timestamp' => time()
       ), DAY_IN_SECONDS);
       
       // Log the re-auth trigger
       error_log("ACA GSC: Re-authentication required due to: $failure_type");
       
       // Show persistent admin notice
       add_action('admin_notices', array($this, 'show_reauth_notice'));
   }
   
   /**
    * Show re-authentication notice
    */
   public function show_reauth_notice() {
       $reauth_data = get_transient('aca_gsc_reauth_required');
       
       if (!$reauth_data) {
           return;
       }
       
       $auth_url = $this->get_auth_url();
       
       echo '<div class="notice notice-error">';
       echo '<p><strong>ACA Google Search Console - Re-authentication Required</strong></p>';
       echo '<p>Your Google Search Console connection has been lost due to token refresh failures. ';
       echo 'Please re-authenticate to continue using GSC features.</p>';
       
       if ($reauth_data['error_message']) {
           echo '<p><small>Technical details: ' . esc_html($reauth_data['error_message']) . '</small></p>';
       }
       
       echo '<p>';
       echo '<a href="' . esc_url($auth_url) . '" class="button button-primary">Re-authenticate with Google</a> ';
       echo '<a href="' . esc_url(admin_url('admin.php?page=aca-settings&dismiss_gsc_reauth=1')) . '" class="button">Dismiss</a>';
       echo '</p>';
       echo '</div>';
   }
   
   /**
    * Proactive token refresh - call this before making API requests
    */
   public function ensure_valid_token() {
       $current_tokens = get_option('aca_gsc_tokens');
       
       if (!$current_tokens) {
           return false;
       }
       
       // Check if token expires within next 10 minutes
       if (isset($current_tokens['expires_in'], $current_tokens['created'])) {
           $expires_at = $current_tokens['created'] + $current_tokens['expires_in'] - 600; // 10 min buffer
           
           if (time() >= $expires_at) {
               error_log('ACA GSC: Proactively refreshing token (expires soon)');
               return $this->refresh_token(true);
           }
       }
       
       return true;
   }
   ```

2. **Add Proactive Token Refresh to API Methods**:
   ```php
   // File: includes/class-aca-google-search-console.php
   // Modify existing API methods to ensure valid tokens:
   
   /**
    * Get search analytics data with proactive token refresh
    */
   public function get_search_analytics($site_url, $start_date = null, $end_date = null, $dimensions = array('query'), $row_limit = 25) {
       // Ensure we have a valid token before making the request
       if (!$this->ensure_valid_token()) {
           return new WP_Error('token_refresh_failed', 'Failed to refresh access token');
       }
       
       if (!$this->service) {
           return new WP_Error('not_authenticated', 'Not authenticated with Google Search Console');
       }
       
       // ... rest of existing method code
   }
   
   /**
    * Get sites list with proactive token refresh
    */
   public function get_sites() {
       // Ensure we have a valid token before making the request
       if (!$this->ensure_valid_token()) {
           return new WP_Error('token_refresh_failed', 'Failed to refresh access token');
       }
       
       if (!$this->service) {
           return new WP_Error('not_authenticated', 'Not authenticated with Google Search Console');
       }
       
       // ... rest of existing method code
   }
   ```

3. **Add Cron Job for Automatic Token Refresh**:
   ```php
   // File: includes/class-aca-cron.php
   // Add this method to the existing cron class:
   
   /**
    * Proactive token refresh cron job
    */
   public function refresh_gsc_tokens() {
       if (!class_exists('ACA_Google_Search_Console')) {
           return;
       }
       
       $gsc = new ACA_Google_Search_Console();
       $auth_status = $gsc->get_auth_status();
       
       if ($auth_status['is_authenticated']) {
           $result = $gsc->ensure_valid_token();
           
           if (!$result) {
               error_log('ACA Cron: GSC token refresh failed');
           } else {
               error_log('ACA Cron: GSC token refresh successful');
           }
       }
   }
   ```

4. **Schedule Token Refresh Cron Job**:
   ```php
   // File: includes/class-aca-activator.php
   // Add to schedule_cron_jobs() method:
   
   private static function schedule_cron_jobs() {
       // ... existing cron jobs
       
       // Schedule token refresh every 6 hours
       if (!wp_next_scheduled('aca_refresh_gsc_tokens')) {
           wp_schedule_event(time(), 'aca_six_hours', 'aca_refresh_gsc_tokens');
       }
   }
   
   // Also add the custom cron interval to the main plugin file:
   // File: ai-content-agent.php
   add_filter('cron_schedules', 'aca_add_cron_intervals');
   
   function aca_add_cron_intervals($schedules) {
       $schedules['aca_six_hours'] = array(
           'interval' => 6 * HOUR_IN_SECONDS,
           'display' => __('Every 6 Hours')
       );
       return $schedules;
   }
   
   // Hook the cron job
   add_action('aca_refresh_gsc_tokens', array('ACA_Cron', 'refresh_gsc_tokens'));
   ```

5. **Add Dismissal Handler for Re-auth Notice**:
   ```php
   // File: includes/class-aca-rest-api.php or appropriate admin handler
   // Add this to handle dismissal:
   
   add_action('admin_init', 'aca_handle_gsc_reauth_dismissal');
   
   function aca_handle_gsc_reauth_dismissal() {
       if (isset($_GET['dismiss_gsc_reauth']) && $_GET['dismiss_gsc_reauth'] == '1') {
           delete_transient('aca_gsc_reauth_required');
           wp_redirect(remove_query_arg('dismiss_gsc_reauth'));
           exit;
       }
   }
   ```

---

## **MEDIUM PRIORITY - Planned Implementation**

### **BUG-010: Missing Runtime Validation**
**Estimated Time**: 3-5 days  
**Files to Create**: `includes/class-aca-validator.php`

#### **Implementation Steps**:

1. **Create Validation Library**:
   ```php
   // File: includes/class-aca-validator.php
   <?php
   if (!defined('ABSPATH')) {
       exit;
   }

   class ACA_Validator {
       
       private $schemas = array();
       
       public function __construct() {
           $this->load_schemas();
       }
       
       /**
        * Load validation schemas
        */
       private function load_schemas() {
           $this->schemas = array(
               'api_response' => array(
                   'required' => array('status', 'data'),
                   'types' => array(
                       'status' => 'string',
                       'data' => 'array',
                       'message' => 'string'
                   )
               ),
               'seo_plugin_data' => array(
                   'required' => array('name', 'version', 'active'),
                   'types' => array(
                       'name' => 'string',
                       'version' => 'string',
                       'active' => 'boolean',
                       'settings' => 'array'
                   )
               ),
               'gsc_analytics' => array(
                   'required' => array('rows'),
                   'types' => array(
                       'rows' => 'array',
                       'responseAggregationType' => 'string'
                   )
               ),
               'content_data' => array(
                   'required' => array('title', 'content'),
                   'types' => array(
                       'title' => 'string',
                       'content' => 'string',
                       'meta_description' => 'string',
                       'keywords' => 'array'
                   ),
                   'sanitize' => array(
                       'title' => 'sanitize_text_field',
                       'content' => 'wp_kses_post',
                       'meta_description' => 'sanitize_textarea_field'
                   )
               )
           );
       }
       
       /**
        * Validate data against schema
        */
       public function validate($data, $schema_name) {
           if (!isset($this->schemas[$schema_name])) {
               return new WP_Error('invalid_schema', "Schema '$schema_name' not found");
           }
           
           $schema = $this->schemas[$schema_name];
           $validated_data = array();
           $errors = array();
           
           // Check required fields
           if (isset($schema['required'])) {
               foreach ($schema['required'] as $field) {
                   if (!isset($data[$field])) {
                       $errors[] = "Required field '$field' is missing";
                   }
               }
           }
           
           // Validate types and sanitize
           foreach ($data as $field => $value) {
               // Type validation
               if (isset($schema['types'][$field])) {
                   $expected_type = $schema['types'][$field];
                   
                   if (!$this->validate_type($value, $expected_type)) {
                       $errors[] = "Field '$field' must be of type '$expected_type'";
                       continue;
                   }
               }
               
               // Sanitization
               if (isset($schema['sanitize'][$field])) {
                   $sanitize_function = $schema['sanitize'][$field];
                   
                   if (function_exists($sanitize_function)) {
                       $validated_data[$field] = $sanitize_function($value);
                   } else {
                       $validated_data[$field] = $value;
                   }
               } else {
                   $validated_data[$field] = $this->default_sanitize($value, $schema['types'][$field] ?? 'string');
               }
           }
           
           if (!empty($errors)) {
               return new WP_Error('validation_failed', implode(', ', $errors));
           }
           
           return $validated_data;
       }
       
       /**
        * Validate field type
        */
       private function validate_type($value, $expected_type) {
           switch ($expected_type) {
               case 'string':
                   return is_string($value);
               case 'integer':
               case 'int':
                   return is_int($value) || (is_string($value) && ctype_digit($value));
               case 'float':
               case 'double':
                   return is_float($value) || is_numeric($value);
               case 'boolean':
               case 'bool':
                   return is_bool($value) || in_array($value, array('true', 'false', '1', '0', 1, 0), true);
               case 'array':
                   return is_array($value);
               case 'object':
                   return is_object($value) || is_array($value);
               default:
                   return true;
           }
       }
       
       /**
        * Default sanitization based on type
        */
       private function default_sanitize($value, $type) {
           switch ($type) {
               case 'string':
                   return sanitize_text_field($value);
               case 'integer':
               case 'int':
                   return intval($value);
               case 'float':
               case 'double':
                   return floatval($value);
               case 'boolean':
               case 'bool':
                   return (bool) $value;
               case 'array':
                   return is_array($value) ? array_map('sanitize_text_field', $value) : array();
               default:
                   return $value;
           }
       }
       
       /**
        * Validate API response
        */
       public function validate_api_response($response) {
           return $this->validate($response, 'api_response');
       }
       
       /**
        * Validate SEO plugin data
        */
       public function validate_seo_plugin_data($data) {
           return $this->validate($data, 'seo_plugin_data');
       }
       
       /**
        * Validate GSC analytics data
        */
       public function validate_gsc_analytics($data) {
           return $this->validate($data, 'gsc_analytics');
       }
       
       /**
        * Validate content data
        */
       public function validate_content_data($data) {
           return $this->validate($data, 'content_data');
       }
   }
   ```

2. **Integrate Validation into REST API**:
   ```php
   // File: includes/class-aca-rest-api.php
   // Add validation to existing endpoints:
   
   public function get_seo_plugins($request) {
       // ... existing code to get plugin data
       
       // Validate the response data
       $validator = new ACA_Validator();
       
       foreach ($plugin_data as &$plugin) {
           $validated_plugin = $validator->validate_seo_plugin_data($plugin);
           
           if (is_wp_error($validated_plugin)) {
               error_log('ACA: SEO plugin data validation failed: ' . $validated_plugin->get_error_message());
               // Use default safe values
               $plugin = array(
                   'name' => sanitize_text_field($plugin['name'] ?? 'Unknown'),
                   'version' => sanitize_text_field($plugin['version'] ?? '0.0.0'),
                   'active' => (bool) ($plugin['active'] ?? false),
                   'settings' => array()
               );
           } else {
               $plugin = $validated_plugin;
           }
       }
       
       return rest_ensure_response($plugin_data);
   }
   ```

### **BUG-042: Responsive Breakpoint Conflicts**
**Estimated Time**: 1-2 days  
**Files to Modify**: `index.css`

#### **Current Issue Analysis**:
Line 782 in `index.css` shows font-size adjustments, but the real issue is WordPress's 782px breakpoint conflicting with plugin responsive design.

#### **Implementation Steps**:

1. **Fix CSS Conflicts**:
   ```css
   /* File: index.css
    * Replace existing responsive styles with namespaced versions */
   
   /* ACA Plugin Responsive Wrapper */
   .aca-plugin-wrapper {
     /* Isolate plugin styles from WordPress theme conflicts */
     all: initial;
     font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
   }
   
   /* Custom breakpoints to avoid WordPress conflicts */
   @media screen and (max-width: 1024px) {
     .aca-plugin-wrapper .aca-main-content {
       padding: 15px;
     }
     
     .aca-plugin-wrapper .aca-sidebar {
       width: 280px;
     }
   }
   
   @media screen and (max-width: 768px) {
     .aca-plugin-wrapper .aca-sidebar {
       width: 100%;
       transform: translateX(-100%);
       position: fixed;
       top: 0;
       left: 0;
       height: 100vh;
       z-index: 99999;
       transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
     }
     
     .aca-plugin-wrapper .aca-sidebar.is-open {
       transform: translateX(0);
     }
     
     .aca-plugin-wrapper .aca-main-content {
       margin-left: 0;
       padding: 10px;
     }
     
     .aca-plugin-wrapper .aca-welcome-section h1 {
       font-size: 24px !important;
     }
     
     .aca-plugin-wrapper .aca-welcome-section p {
       font-size: 16px !important;
     }
   }
   
   @media screen and (max-width: 480px) {
     .aca-plugin-wrapper .aca-main-content {
       padding: 8px;
     }
     
     .aca-plugin-wrapper .aca-welcome-section h1 {
       font-size: 20px !important;
     }
     
     .aca-plugin-wrapper .aca-welcome-section p {
       font-size: 14px !important;
     }
   }
   
   /* WordPress admin bar compatibility */
   @media screen and (max-width: 782px) {
     .aca-plugin-wrapper .aca-sidebar {
       top: 46px; /* WordPress mobile admin bar height */
       height: calc(100vh - 46px);
     }
   }
   
   @media screen and (min-width: 783px) {
     .aca-plugin-wrapper .aca-sidebar {
       top: 32px; /* WordPress desktop admin bar height */
       height: calc(100vh - 32px);
     }
   }
   ```

### **BUG-043: Mobile Navigation Problems**
**Estimated Time**: 2-3 days  
**Files to Modify**: `index.css`, React components

#### **Implementation Steps**:

1. **Enhanced Mobile Sidebar CSS**:
   ```css
   /* File: index.css - Add these mobile-specific improvements */
   
   /* Mobile sidebar overlay */
   .aca-mobile-overlay {
     display: none;
     position: fixed;
     top: 0;
     left: 0;
     width: 100%;
     height: 100%;
     background: rgba(0, 0, 0, 0.5);
     z-index: 99998;
     opacity: 0;
     transition: opacity 0.3s ease;
   }
   
   .aca-mobile-overlay.is-visible {
     display: block;
     opacity: 1;
   }
   
   /* Mobile hamburger menu */
   .aca-mobile-menu-toggle {
     display: none;
     position: fixed;
     top: 50px;
     left: 20px;
     z-index: 100000;
     background: #0073aa;
     color: white;
     border: none;
     border-radius: 4px;
     padding: 8px 12px;
     cursor: pointer;
     font-size: 16px;
   }
   
   @media screen and (max-width: 768px) {
     .aca-mobile-menu-toggle {
       display: block;
     }
     
     /* Improve touch targets */
     .aca-sidebar .aca-nav-item {
       min-height: 44px; /* iOS recommended touch target */
       display: flex;
       align-items: center;
     }
     
     .aca-sidebar .aca-nav-item a {
       padding: 12px 20px;
       width: 100%;
       display: block;
     }
   }
   
   /* Swipe gesture support */
   .aca-sidebar.swiping {
     transition: none;
   }
   ```

2. **Add Touch Gesture Support**:
   ```typescript
   // File: components/Sidebar.tsx or similar component
   import React, { useState, useEffect, useRef } from 'react';
   
   interface TouchState {
     startX: number;
     currentX: number;
     startY: number;
     currentY: number;
     isDragging: boolean;
   }
   
   const MobileSidebar: React.FC = () => {
     const [isOpen, setIsOpen] = useState(false);
     const [touchState, setTouchState] = useState<TouchState>({
       startX: 0,
       currentX: 0,
       startY: 0,
       currentY: 0,
       isDragging: false
     });
     const sidebarRef = useRef<HTMLDivElement>(null);
     
     // Touch event handlers
     const handleTouchStart = (e: TouchEvent) => {
       const touch = e.touches[0];
       setTouchState({
         startX: touch.clientX,
         currentX: touch.clientX,
         startY: touch.clientY,
         currentY: touch.clientY,
         isDragging: true
       });
     };
     
     const handleTouchMove = (e: TouchEvent) => {
       if (!touchState.isDragging) return;
       
       const touch = e.touches[0];
       const deltaX = touch.clientX - touchState.startX;
       const deltaY = touch.clientY - touchState.startY;
       
       // Only handle horizontal swipes
       if (Math.abs(deltaX) > Math.abs(deltaY)) {
         e.preventDefault();
         
         setTouchState(prev => ({
           ...prev,
           currentX: touch.clientX,
           currentY: touch.clientY
         }));
         
         // Update sidebar position during drag
         if (sidebarRef.current) {
           const sidebar = sidebarRef.current;
           const progress = Math.max(0, Math.min(1, (deltaX + (isOpen ? 280 : 0)) / 280));
           sidebar.style.transform = `translateX(${-280 + (progress * 280)}px)`;
           sidebar.classList.add('swiping');
         }
       }
     };
     
     const handleTouchEnd = (e: TouchEvent) => {
       if (!touchState.isDragging) return;
       
       const deltaX = touchState.currentX - touchState.startX;
       const threshold = 50; // Minimum swipe distance
       
       // Remove swiping class to restore transitions
       if (sidebarRef.current) {
         sidebarRef.current.classList.remove('swiping');
         sidebarRef.current.style.transform = '';
       }
       
       // Determine if sidebar should open or close
       if (Math.abs(deltaX) > threshold) {
         if (deltaX > 0 && !isOpen) {
           setIsOpen(true);
         } else if (deltaX < 0 && isOpen) {
           setIsOpen(false);
         }
       }
       
       setTouchState({
         startX: 0,
         currentX: 0,
         startY: 0,
         currentY: 0,
         isDragging: false
       });
     };
     
     // Add touch event listeners
     useEffect(() => {
       const sidebar = sidebarRef.current;
       if (!sidebar) return;
       
       sidebar.addEventListener('touchstart', handleTouchStart, { passive: false });
       sidebar.addEventListener('touchmove', handleTouchMove, { passive: false });
       sidebar.addEventListener('touchend', handleTouchEnd);
       
       return () => {
         sidebar.removeEventListener('touchstart', handleTouchStart);
         sidebar.removeEventListener('touchmove', handleTouchMove);
         sidebar.removeEventListener('touchend', handleTouchEnd);
       };
     }, [touchState.isDragging, isOpen]);
     
     return (
       <>
         <button 
           className="aca-mobile-menu-toggle"
           onClick={() => setIsOpen(!isOpen)}
           aria-label="Toggle navigation menu"
         >
           ☰
         </button>
         
         <div 
           className={`aca-mobile-overlay ${isOpen ? 'is-visible' : ''}`}
           onClick={() => setIsOpen(false)}
         />
         
         <div 
           ref={sidebarRef}
           className={`aca-sidebar ${isOpen ? 'is-open' : ''}`}
         >
           {/* Sidebar content */}
         </div>
       </>
     );
   };
   ```

### **BUG-047: Color Contrast Issues**
**Estimated Time**: 1-2 days  
**Files to Modify**: `index.css`

#### **Implementation Steps**:

1. **WCAG Compliant Color Scheme**:
   ```css
   /* File: index.css - Replace existing color variables */
   
   :root {
     /* WCAG AA compliant colors (4.5:1 contrast ratio minimum) */
     --aca-text-primary: #212529;        /* 16.75:1 on white */
     --aca-text-secondary: #495057;      /* 8.59:1 on white */
     --aca-text-muted: #6c757d;          /* 4.54:1 on white */
     --aca-text-light: #ffffff;          /* High contrast on dark backgrounds */
     
     --aca-bg-primary: #ffffff;
     --aca-bg-secondary: #f8f9fa;
     --aca-bg-dark: #343a40;
     --aca-bg-darker: #212529;
     
     /* Interactive elements */
     --aca-link-color: #0056b3;          /* 7.27:1 on white */
     --aca-link-hover: #004085;          /* 9.67:1 on white */
     
     /* Status colors */
     --aca-success: #155724;             /* 7.44:1 on white */
     --aca-success-bg: #d4edda;
     --aca-warning: #856404;             /* 5.94:1 on white */
     --aca-warning-bg: #fff3cd;
     --aca-error: #721c24;               /* 8.80:1 on white */
     --aca-error-bg: #f8d7da;
     --aca-info: #0c5460;                /* 8.17:1 on white */
     --aca-info-bg: #d1ecf1;
     
     /* Button colors */
     --aca-btn-primary: #0056b3;
     --aca-btn-primary-hover: #004085;
     --aca-btn-secondary: #495057;
     --aca-btn-secondary-hover: #343a40;
   }
   
   /* High contrast mode support */
   @media (prefers-contrast: high) {
     :root {
       --aca-text-primary: #000000;
       --aca-text-secondary: #000000;
       --aca-bg-primary: #ffffff;
       --aca-link-color: #0000ee;
       --aca-link-hover: #0000aa;
     }
   }
   
   /* Dark mode support */
   @media (prefers-color-scheme: dark) {
     :root {
       --aca-text-primary: #f8f9fa;      /* High contrast on dark */
       --aca-text-secondary: #dee2e6;    /* Good contrast on dark */
       --aca-text-muted: #adb5bd;        /* Adequate contrast on dark */
       
       --aca-bg-primary: #212529;
       --aca-bg-secondary: #343a40;
       --aca-bg-dark: #495057;
       
       --aca-link-color: #66b3ff;        /* Good contrast on dark */
       --aca-link-hover: #99ccff;        /* Higher contrast on dark */
     }
   }
   
   /* Apply consistent colors throughout */
   .aca-plugin-wrapper {
     color: var(--aca-text-primary);
     background-color: var(--aca-bg-primary);
   }
   
   .aca-plugin-wrapper a {
     color: var(--aca-link-color);
   }
   
   .aca-plugin-wrapper a:hover {
     color: var(--aca-link-hover);
   }
   
   .aca-plugin-wrapper .aca-button-primary {
     background-color: var(--aca-btn-primary);
     color: var(--aca-text-light);
     border: 1px solid var(--aca-btn-primary);
   }
   
   .aca-plugin-wrapper .aca-button-primary:hover {
     background-color: var(--aca-btn-primary-hover);
     border-color: var(--aca-btn-primary-hover);
   }
   
   /* Status messages with proper contrast */
   .aca-notice-success {
     color: var(--aca-success);
     background-color: var(--aca-success-bg);
     border-left: 4px solid var(--aca-success);
   }
   
   .aca-notice-warning {
     color: var(--aca-warning);
     background-color: var(--aca-warning-bg);
     border-left: 4px solid var(--aca-warning);
   }
   
   .aca-notice-error {
     color: var(--aca-error);
     background-color: var(--aca-error-bg);
     border-left: 4px solid var(--aca-error);
   }
   ```

### **BUG-051: Admin Bar Conflicts**
**Estimated Time**: 1 day  
**Files to Modify**: `index.css`

#### **Current Issue Analysis**:
Lines 40-42 in `index.css` show fixed positioning that conflicts with WordPress admin bar.

#### **Implementation Steps**:

1. **Dynamic Admin Bar Handling**:
   ```css
   /* File: index.css - Replace existing fixed positioning */
   
   /* Dynamic admin bar height calculation */
   .aca-fixed-header {
     position: fixed;
     top: var(--wp-admin-bar-height, 32px);
     left: 160px;
     right: 0;
     z-index: 9998;
     background: var(--aca-bg-primary);
     border-bottom: 1px solid var(--aca-border-color);
     transition: top 0.3s ease;
   }
   
   .aca-sidebar {
     position: fixed;
     top: var(--wp-admin-bar-height, 32px);
     left: 160px;
     width: 240px;
     height: calc(100vh - var(--wp-admin-bar-height, 32px));
     background: #23282d;
     border-right: 1px solid #ccd0d4;
     z-index: 9999;
     overflow-y: auto;
     transform: translateX(-100%);
     transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
   }
   
   /* WordPress admin bar responsive breakpoints */
   @media screen and (max-width: 782px) {
     :root {
       --wp-admin-bar-height: 46px;
     }
   }
   
   @media screen and (min-width: 783px) {
     :root {
       --wp-admin-bar-height: 32px;
     }
   }
   
   /* Handle no admin bar scenario */
   body.no-admin-bar .aca-fixed-header,
   body.no-admin-bar .aca-sidebar {
     top: 0;
   }
   
   body.no-admin-bar {
     --wp-admin-bar-height: 0px;
   }
   ```

2. **JavaScript Admin Bar Detection**:
   ```javascript
   // File: admin/js/admin-bar-handler.js (new file)
   document.addEventListener('DOMContentLoaded', function() {
       function updateAdminBarHeight() {
           const adminBar = document.getElementById('wpadminbar');
           const height = adminBar ? adminBar.offsetHeight : 0;
           
           document.documentElement.style.setProperty('--wp-admin-bar-height', height + 'px');
           
           // Also handle admin bar visibility changes
           if (adminBar) {
               const observer = new MutationObserver(function(mutations) {
                   mutations.forEach(function(mutation) {
                       if (mutation.type === 'attributes' && 
                           (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
                           const newHeight = adminBar.offsetHeight;
                           document.documentElement.style.setProperty('--wp-admin-bar-height', newHeight + 'px');
                       }
                   });
               });
               
               observer.observe(adminBar, {
                   attributes: true,
                   attributeFilter: ['style', 'class']
               });
           }
       }
       
       // Initial setup
       updateAdminBarHeight();
       
       // Handle window resize
       window.addEventListener('resize', updateAdminBarHeight);
       
       // Handle admin bar show/hide
       window.addEventListener('scroll', function() {
           // Some themes hide admin bar on scroll
           setTimeout(updateAdminBarHeight, 100);
       });
   });
   ```

3. **Enqueue the Script**:
   ```php
   // File: ai-content-agent.php or admin handler
   add_action('admin_enqueue_scripts', 'aca_enqueue_admin_bar_handler');
   
   function aca_enqueue_admin_bar_handler($hook) {
       // Only load on ACA pages
       if (strpos($hook, 'aca') !== false || strpos($hook, 'ai-content-agent') !== false) {
           wp_enqueue_script(
               'aca-admin-bar-handler',
               plugin_dir_url(__FILE__) . 'admin/js/admin-bar-handler.js',
               array(),
               ACA_VERSION,
               true
           );
       }
   }
   ```

---

## **IMPLEMENTATION PHASES**

### **Phase 1: Critical Issues (Week 1)**
1. **BUG-003**: Verify and fix API endpoint consistency
2. Test all API endpoints thoroughly

### **Phase 2: High Priority Database & Auth (Weeks 2-4)**
1. **BUG-005**: Implement migration system
2. **BUG-108**: Enhanced token refresh mechanism  
3. **BUG-100**: Version compatibility system
4. **BUG-102**: Multisite support

### **Phase 3: Medium Priority UI/UX (Weeks 5-7)**
1. **BUG-042**: Responsive breakpoint fixes
2. **BUG-043**: Mobile navigation improvements
3. **BUG-047**: Color contrast compliance
4. **BUG-051**: Admin bar conflict resolution
5. **BUG-010**: Runtime validation system

### **Phase 4: Remaining Medium Priority (Weeks 8-9)**
1. **BUG-076**: State management race conditions
2. **BUG-101**: Meta data conflict resolution
3. **BUG-110**: OAuth permission scope fixes
4. **BUG-112**: Cron context improvements
5. **BUG-123**: Special character handling

---

## **TESTING STRATEGY**

### **Automated Testing**
1. **Unit Tests**: Each bug fix requires corresponding unit tests
2. **Integration Tests**: Cross-plugin compatibility testing
3. **Regression Tests**: Ensure existing functionality remains intact
4. **Performance Tests**: Monitor impact on site performance

### **Manual Testing Checklist**
1. **WordPress Compatibility**: Test with WordPress 5.8+ through latest
2. **PHP Compatibility**: Test with PHP 7.4, 8.0, 8.1, 8.2
3. **SEO Plugin Compatibility**: Test with Yoast, RankMath, AIOSEO
4. **Theme Compatibility**: Test with popular themes
5. **Multisite Testing**: Test network activation and site-specific features
6. **Mobile Testing**: Test responsive design and touch interactions
7. **Accessibility Testing**: Screen reader and keyboard navigation testing

### **Quality Assurance**
1. **Code Review**: All changes require peer review
2. **Security Audit**: Security implications of each change
3. **Performance Monitoring**: Before/after performance metrics
4. **User Acceptance Testing**: Real-world usage scenarios

### **Deployment Strategy**
1. **Staging Environment**: Full testing in production-like environment
2. **Beta Testing**: Limited release to beta users
3. **Gradual Rollout**: Phased deployment to production
4. **Monitoring**: Real-time error monitoring and user feedback collection

This comprehensive roadmap provides detailed, actionable instructions for an AI code tool to implement all the bug fixes systematically while maintaining code quality and user experience.
