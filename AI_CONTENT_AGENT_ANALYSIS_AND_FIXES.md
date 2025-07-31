# AI Content Agent - Comprehensive Analysis & Fixes

## Current State Analysis (v2.3.14)

### Issues Identified:

1. **Demo Mode Problem**: Plugin is running in full demo mode with `licenseStatus.is_active = true`
2. **Licensing System Bypassed**: License validation has been disabled for demo purposes
3. **API Endpoint Issues**: Content Freshness endpoints were returning 403 errors (fixed by removing pro checks entirely)
4. **GSC Integration**: Likely using mock data instead of real Google Search Console API
5. **Pro Features Accessible Without License**: All features are available without proper licensing

### Required Fixes:

## 1. Restore Proper Licensing System

### Current Implementation (v2.3.14):
```php
function is_aca_pro_active() {
    return get_option('aca_license_status') === 'active';
}
```

### Fixed Implementation:
Create `includes/class-aca-licensing.php`:

```php
<?php
/**
 * ACA Licensing System - Restored from v2.3.0 approach
 */

class ACA_Licensing {
    
    private $license_server_url = 'https://api.ai-content-agent.com/v1/';
    private $product_id = 'aca-pro';
    private $license_key_option = 'aca_license_key';
    private $license_status_option = 'aca_license_status';
    private $license_data_option = 'aca_license_data';
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_aca_activate_license', array($this, 'activate_license'));
        add_action('wp_ajax_aca_deactivate_license', array($this, 'deactivate_license'));
        add_action('wp_ajax_aca_check_license', array($this, 'check_license'));
        
        // Daily license check
        add_action('aca_daily_license_check', array($this, 'daily_license_check'));
        if (!wp_next_scheduled('aca_daily_license_check')) {
            wp_schedule_event(time(), 'daily', 'aca_daily_license_check');
        }
    }
    
    public function init() {
        $this->maybe_migrate_from_demo_mode();
    }
    
    /**
     * Migrate from demo mode to proper licensing
     */
    private function maybe_migrate_from_demo_mode() {
        $current_status = get_option($this->license_status_option);
        
        // If currently in demo mode, reset to proper licensing
        if ($current_status === 'active' && !get_option($this->license_key_option)) {
            update_option($this->license_status_option, 'inactive');
            update_option($this->license_data_option, array(
                'status' => 'inactive',
                'message' => 'Demo mode disabled. Please enter a valid license key.',
                'migrated_from_demo' => true,
                'migration_date' => current_time('mysql')
            ));
        }
    }
    
    /**
     * Check if Pro license is active - PROPER VALIDATION
     */
    public function is_pro_active() {
        $status = get_option($this->license_status_option, 'inactive');
        $license_key = get_option($this->license_key_option, '');
        
        // Must have both valid status and license key
        return ($status === 'active' && !empty($license_key));
    }
    
    /**
     * Check Pro permissions for API endpoints
     */
    public function check_pro_permissions($request) {
        if (!$this->is_pro_active()) {
            return new WP_Error('license_required', 'Pro license required for this feature', array('status' => 403));
        }
        
        if (!current_user_can('manage_options')) {
            return new WP_Error('insufficient_permissions', 'Insufficient permissions', array('status' => 403));
        }
        
        return true;
    }
    
    // ... (additional methods for license validation, activation, etc.)
}

// Initialize licensing system
new ACA_Licensing();

/**
 * Global function to check Pro status (backward compatibility)
 */
function is_aca_pro_active() {
    $licensing = new ACA_Licensing();
    return $licensing->is_pro_active();
}
```

## 2. Implement Hybrid GSC Client

### Current Issue:
GSC integration likely uses mock data or demo mode.

### Solution:
Create `includes/class-aca-google-search-console-hybrid.php`:

```php
<?php
/**
 * Hybrid Google Search Console Client
 * Uses real API when available, falls back to demo data
 */

class ACA_Google_Search_Console_Hybrid {
    
    private $client_id = 'YOUR_GOOGLE_CLIENT_ID';
    private $client_secret = 'YOUR_GOOGLE_CLIENT_SECRET';
    private $redirect_uri = '';
    
    public function __construct() {
        $this->redirect_uri = admin_url('admin.php?page=ai-content-agent&gsc_auth=callback');
    }
    
    /**
     * Get GSC data - hybrid approach
     */
    public function get_search_analytics($site_url, $start_date, $end_date) {
        // Try real API first
        if ($this->has_valid_credentials()) {
            try {
                return $this->get_real_search_analytics($site_url, $start_date, $end_date);
            } catch (Exception $e) {
                error_log('GSC Real API failed: ' . $e->getMessage());
                // Fall back to demo data
                return $this->get_demo_search_analytics($site_url, $start_date, $end_date);
            }
        }
        
        // Use demo data if no credentials
        return $this->get_demo_search_analytics($site_url, $start_date, $end_date);
    }
    
    /**
     * Check if we have valid GSC credentials
     */
    private function has_valid_credentials() {
        $access_token = get_option('aca_gsc_access_token');
        $refresh_token = get_option('aca_gsc_refresh_token');
        
        return !empty($access_token) && !empty($refresh_token);
    }
    
    /**
     * Get real GSC data using Google API
     */
    private function get_real_search_analytics($site_url, $start_date, $end_date) {
        $access_token = $this->get_valid_access_token();
        
        if (!$access_token) {
            throw new Exception('No valid access token');
        }
        
        $api_url = 'https://searchconsole.googleapis.com/webmasters/v3/sites/' . 
                   urlencode($site_url) . '/searchAnalytics/query';
        
        $request_body = array(
            'startDate' => $start_date,
            'endDate' => $end_date,
            'dimensions' => array('query', 'page'),
            'rowLimit' => 1000
        );
        
        $response = wp_remote_post($api_url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($request_body),
            'timeout' => 30
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('API request failed: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (empty($data['rows'])) {
            throw new Exception('No data returned from GSC API');
        }
        
        return $this->format_gsc_data($data['rows']);
    }
    
    /**
     * Get demo GSC data for fallback
     */
    private function get_demo_search_analytics($site_url, $start_date, $end_date) {
        // Generate realistic demo data
        $demo_data = array();
        
        $sample_queries = array(
            'wordpress tutorial', 'seo tips', 'content marketing',
            'blog writing', 'digital marketing', 'web development'
        );
        
        foreach ($sample_queries as $query) {
            $demo_data[] = array(
                'query' => $query,
                'page' => $site_url . '/sample-post-' . sanitize_title($query),
                'clicks' => rand(10, 100),
                'impressions' => rand(100, 1000),
                'ctr' => rand(5, 15) / 100,
                'position' => rand(1, 20)
            );
        }
        
        return array(
            'data' => $demo_data,
            'is_demo' => true,
            'message' => 'Demo data - Connect GSC for real analytics'
        );
    }
    
    /**
     * Get valid access token (refresh if needed)
     */
    private function get_valid_access_token() {
        $access_token = get_option('aca_gsc_access_token');
        $token_expires = get_option('aca_gsc_token_expires');
        
        // Check if token is still valid
        if ($access_token && $token_expires && time() < $token_expires) {
            return $access_token;
        }
        
        // Try to refresh token
        $refresh_token = get_option('aca_gsc_refresh_token');
        if (!$refresh_token) {
            return false;
        }
        
        return $this->refresh_access_token($refresh_token);
    }
    
    /**
     * Refresh access token using refresh token
     */
    private function refresh_access_token($refresh_token) {
        $response = wp_remote_post('https://oauth2.googleapis.com/token', array(
            'body' => array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'refresh_token' => $refresh_token,
                'grant_type' => 'refresh_token'
            )
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (empty($data['access_token'])) {
            return false;
        }
        
        // Save new token
        update_option('aca_gsc_access_token', $data['access_token']);
        update_option('aca_gsc_token_expires', time() + $data['expires_in']);
        
        return $data['access_token'];
    }
    
    /**
     * Format GSC data for consistency
     */
    private function format_gsc_data($rows) {
        $formatted = array();
        
        foreach ($rows as $row) {
            $formatted[] = array(
                'query' => $row['keys'][0] ?? '',
                'page' => $row['keys'][1] ?? '',
                'clicks' => $row['clicks'] ?? 0,
                'impressions' => $row['impressions'] ?? 0,
                'ctr' => $row['ctr'] ?? 0,
                'position' => $row['position'] ?? 0
            );
        }
        
        return array(
            'data' => $formatted,
            'is_demo' => false,
            'message' => 'Real GSC data'
        );
    }
}
```

## 3. Restore Pro Endpoints with Proper Licensing

### Fix REST API Endpoints:

Update `includes/class-aca-rest-api.php` to restore proper Pro endpoint protection:

```php
// Replace current permission callbacks
'permission_callback' => array($this, 'check_admin_permissions') // WRONG - allows all admin users

// With proper Pro permission checks
'permission_callback' => array($this, 'check_pro_permissions') // CORRECT - requires Pro license
```

### Specific Endpoints to Fix:

1. **Content Freshness Endpoints**:
   - `/aca/v1/content-freshness/analyze`
   - `/aca/v1/content-freshness/update`
   - `/aca/v1/content-freshness/bulk-update`

2. **Automation Endpoints**:
   - `/aca/v1/automation/schedule`
   - `/aca/v1/automation/settings`
   - `/aca/v1/automation/status`

3. **Advanced Analytics Endpoints**:
   - `/aca/v1/analytics/performance`
   - `/aca/v1/analytics/trends`
   - `/aca/v1/analytics/reports`

## 4. Update Main Plugin File

### Fix `ai-content-agent.php`:

```php
// Add licensing system to required files
$required_files = [
    'includes/class-aca-licensing.php', // ADD THIS
    'includes/class-aca-activator.php',
    'includes/class-aca-deactivator.php',
    'includes/class-aca-rest-api.php',
    'includes/class-aca-cron.php',
    'includes/class-aca-content-freshness.php',
    'includes/class-aca-rate-limiter.php',
    'includes/class-aca-performance-monitor.php',
    'includes/class-aca-google-search-console-hybrid.php', // ADD THIS
    'includes/gsc-data-fix.php'
];

// Update is_aca_pro_active function
function is_aca_pro_active() {
    // Remove demo mode - use proper licensing
    if (class_exists('ACA_Licensing')) {
        $licensing = new ACA_Licensing();
        return $licensing->is_pro_active();
    }
    
    // Fallback for backward compatibility
    $status = get_option('aca_license_status', 'inactive');
    $license_key = get_option('aca_license_key', '');
    return ($status === 'active' && !empty($license_key));
}
```

## 5. Frontend Changes Required

### Update React Components:

1. **License Status Display**: Show proper license status instead of demo mode
2. **Feature Availability**: Disable Pro features when license is inactive
3. **License Management UI**: Add license key input and activation buttons

### Example React Component Update:

```typescript
// Update license status check
const licenseStatus = {
    is_active: false, // Remove demo mode override
    status: 'inactive',
    features: {
        automation: false,
        content_freshness: false,
        // ... other features
    }
};

// Add license management UI
const LicenseManagement = () => {
    return (
        <div className="license-section">
            <h3>License Management</h3>
            <input 
                type="text" 
                placeholder="Enter license key"
                value={licenseKey}
                onChange={(e) => setLicenseKey(e.target.value)}
            />
            <button onClick={activateLicense}>Activate License</button>
        </div>
    );
};
```

## 6. Database Migration

### Migration Script:

```php
/**
 * Migrate from demo mode to proper licensing
 */
function aca_migrate_from_demo_mode() {
    $current_status = get_option('aca_license_status');
    
    // If in demo mode, reset
    if ($current_status === 'active' && !get_option('aca_license_key')) {
        update_option('aca_license_status', 'inactive');
        
        // Add migration notice
        add_option('aca_demo_migration_notice', array(
            'message' => 'Plugin migrated from demo mode. Please enter a valid license key to access Pro features.',
            'timestamp' => current_time('mysql'),
            'dismissed' => false
        ));
    }
}

// Run migration on plugin activation
register_activation_hook(__FILE__, 'aca_migrate_from_demo_mode');
```

## 7. Testing Checklist

### Before Deployment:

- [ ] License validation works correctly
- [ ] Pro features are disabled without license
- [ ] GSC hybrid client falls back properly
- [ ] API endpoints return proper 403 errors
- [ ] Frontend shows correct license status
- [ ] Migration from demo mode works
- [ ] License activation/deactivation works
- [ ] Daily license checks function properly

### Manual Testing:

1. **Without License**:
   - Verify Pro features are disabled
   - Check 403 errors on Pro endpoints
   - Confirm GSC uses demo data

2. **With License**:
   - Verify all Pro features work
   - Check real GSC data (if configured)
   - Confirm proper license status display

3. **License Management**:
   - Test license activation
   - Test license deactivation
   - Test invalid license handling

## 8. Deployment Steps

1. **Backup Current Version**: Create backup of v2.3.14
2. **Apply Licensing System**: Add new licensing class
3. **Update REST API**: Restore Pro endpoint protection
4. **Add GSC Hybrid Client**: Implement real API fallback
5. **Update Frontend**: Modify React components
6. **Run Migration**: Execute demo mode migration
7. **Test Thoroughly**: Complete testing checklist
8. **Deploy Gradually**: Start with staging environment

## Summary

This comprehensive fix addresses all the major issues in v2.3.14:

1. ✅ **Restores proper licensing system** from v2.3.0 approach
2. ✅ **Implements hybrid GSC client** with real API and demo fallback
3. ✅ **Restores Pro endpoint protection** with proper license checks
4. ✅ **Fixes demo mode issues** by migrating to proper licensing
5. ✅ **Maintains backward compatibility** while improving security
6. ✅ **Provides clear upgrade path** from current demo mode

The result will be a stable, properly licensed plugin that maintains the functionality of v2.3.14 while restoring the professional licensing approach from v2.3.0.