# AI Content Agent (ACA) - Bug Fix Roadmap

## Solutions Implementation Plan
**Date**: January 1, 2025  
**Version**: v2.4.1  
**Priority**: Critical - Pro Features Non-Functional

---

## 1. Fix License Status Loading Issue in Automation Section

### **Solution Overview**
Add the missing AJAX handler for license status checking and ensure proper nonce verification.

### **Implementation Steps**

#### **Step 1.1: Add AJAX Handler Registration**
**File**: `ai-content-agent.php`  
**Location**: After line 217 (in the admin notices section)

```php
// Add AJAX handlers for license status
add_action('wp_ajax_aca_get_license_status', 'aca_handle_get_license_status');

/**
 * AJAX handler for getting license status
 */
function aca_handle_get_license_status() {
    // Verify nonce for security
    if (!check_ajax_referer('aca_ajax_nonce', 'nonce', false)) {
        wp_die(json_encode(array(
            'success' => false,
            'message' => 'Security check failed'
        )));
    }
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_die(json_encode(array(
            'success' => false,
            'message' => 'Insufficient permissions'
        )));
    }
    
    // Get license status using existing function
    $license_status = get_option('aca_license_status', 'inactive');
    $license_data = get_option('aca_license_data', array());
    $is_active = is_aca_pro_active();
    
    wp_die(json_encode(array(
        'success' => true,
        'data' => array(
            'status' => $license_status,
            'is_active' => $is_active,
            'verified_at' => isset($license_data['verified_at']) ? $license_data['verified_at'] : null
        )
    )));
}
```

#### **Step 1.2: Add Nonce for AJAX Security**
**File**: `ai-content-agent.php`  
**Location**: In the `enqueue_admin_scripts` method around line 175

```php
// Add after line 180 (after wp_localize_script)
wp_localize_script($script_handle, 'acaAjax', array(
    'nonce' => wp_create_nonce('aca_ajax_nonce'),
    'ajax_url' => admin_url('admin-ajax.php')
));
```

#### **Step 1.3: Update Frontend to Use Consistent API**
**File**: `components/SettingsAutomation.tsx`  
**Location**: Lines 82-104

**Replace the current AJAX call with REST API call:**
```typescript
const loadLicenseStatus = async () => {
    try {
        const response = await fetch(`${window.acaData.api_url}license/status`, {
            method: 'GET',
            headers: {
                'X-WP-Nonce': window.acaData.nonce,
                'Content-Type': 'application/json',
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            setLicenseStatus({
                status: data.status || 'inactive',
                is_active: data.is_active || false,
                verified_at: data.verified_at
            });
        }
    } catch (error) {
        console.error('Failed to load license status:', error);
    } finally {
        setIsLoadingLicenseStatus(false);
    }
};
```

### **Testing Requirements**
- [ ] Automation section loads without "Loading license status..." hanging
- [ ] Pro features are accessible with active license
- [ ] Non-Pro users see upgrade prompts correctly
- [ ] AJAX security nonces work properly

---

## 2. Fix Content Freshness Manager Functionality

### **Solution Overview**
Add missing GSC status endpoint and fix license status synchronization for Content Freshness features.

### **Implementation Steps**

#### **Step 2.1: Add Missing GSC Status Endpoint**
**File**: `includes/class-aca-rest-api.php`  
**Location**: After line 246 (after the `/gsc/data` endpoint)

```php
// Add GSC status endpoint
register_rest_route('aca/v1', '/gsc/status', array(
    'methods' => 'GET',
    'callback' => array($this, 'get_gsc_status'),
    'permission_callback' => array($this, 'check_admin_permissions')
));
```

#### **Step 2.2: Implement GSC Status Method**
**File**: `includes/class-aca-rest-api.php`  
**Location**: After the `get_gsc_data` method (around line 2750)

```php
/**
 * Get Google Search Console connection status
 */
public function get_gsc_status($request) {
    try {
        // Check if GSC is configured
        $gsc_client_id = get_option('aca_gsc_client_id', '');
        $gsc_client_secret = get_option('aca_gsc_client_secret', '');
        $gsc_access_token = get_option('aca_gsc_access_token', '');
        $gsc_refresh_token = get_option('aca_gsc_refresh_token', '');
        
        $is_configured = !empty($gsc_client_id) && !empty($gsc_client_secret);
        $is_connected = !empty($gsc_access_token) && !empty($gsc_refresh_token);
        
        // Get selected site if connected
        $selected_site = '';
        if ($is_connected) {
            $selected_site = get_option('aca_gsc_selected_site', '');
        }
        
        return rest_ensure_response(array(
            'configured' => $is_configured,
            'connected' => $is_connected,
            'selected_site' => $selected_site,
            'last_sync' => get_option('aca_gsc_last_sync', null)
        ));
        
    } catch (Exception $e) {
        error_log('ACA GSC Status Error: ' . $e->getMessage());
        return new WP_Error('gsc_status_error', 'Failed to get GSC status', array('status' => 500));
    }
}
```

#### **Step 2.3: Fix License Status Synchronization**
**File**: `includes/class-aca-rest-api.php`  
**Location**: Update the `check_pro_permissions` method (lines 332-344)

```php
public function check_pro_permissions() {
    // Check admin permissions first
    if (!current_user_can('manage_options')) {
        return false;
    }
    
    // Enhanced license checking with multiple validation points
    $license_checks = array(
        get_option('aca_license_status') === 'active',
        get_option('aca_license_verified') === wp_hash('verified'),
        (time() - get_option('aca_license_timestamp', 0)) < 86400, // 24-hour verification
        !empty(get_option('aca_license_key'))
    );
    
    $is_pro_active = count(array_filter($license_checks)) === 4;
    
    if (!$is_pro_active) {
        error_log('ACA: Pro permission denied - License checks failed: ' . json_encode($license_checks));
        return new WP_Error('pro_required', 'This feature requires an active Pro license', array('status' => 403));
    }
    
    return true;
}
```

#### **Step 2.4: Add License Status to Settings API**
**File**: `includes/class-aca-rest-api.php`  
**Location**: Update the `get_settings` method to include license status

```php
// In the get_settings method, add license status to the response
$settings['is_pro'] = is_aca_pro_active();
$settings['license_status'] = get_option('aca_license_status', 'inactive');
```

### **Testing Requirements**
- [ ] `/aca/v1/gsc/status` endpoint returns 200 OK
- [ ] Content Freshness endpoints return data instead of 403 errors
- [ ] License status is consistent across frontend and backend
- [ ] Pro features work with active license

---

## 3. Fix Console Errors from Test Site Deployment

### **Solution Overview**
Address remaining AJAX and API issues to eliminate console errors and improve user experience.

### **Implementation Steps**

#### **Step 3.1: Add Missing AJAX Handlers Registration**
**File**: `ai-content-agent.php`  
**Location**: After the license status handler (from Step 1.1)

```php
// Register all AJAX handlers in one place
add_action('wp_ajax_aca_get_seo_plugins', 'aca_handle_get_seo_plugins');

/**
 * AJAX handler for SEO plugins detection
 */
function aca_handle_get_seo_plugins() {
    if (!check_ajax_referer('aca_ajax_nonce', 'nonce', false)) {
        wp_die(json_encode(array('success' => false, 'message' => 'Security check failed')));
    }
    
    if (!current_user_can('edit_posts')) {
        wp_die(json_encode(array('success' => false, 'message' => 'Insufficient permissions')));
    }
    
    // Use existing REST API method
    $rest_api = new ACA_Rest_Api();
    $result = $rest_api->get_seo_plugins(new WP_REST_Request());
    
    wp_die(json_encode($result->get_data()));
}
```

#### **Step 3.2: Improve Error Handling in Frontend**
**File**: `components/ContentFreshnessManager.tsx`  
**Location**: Update API calls to handle errors gracefully

```typescript
// Add proper error handling for all API calls
const handleApiError = (error: any, context: string) => {
    console.error(`ACA ${context} error:`, error);
    
    if (error.message?.includes('Pro license')) {
        showToast('This feature requires an active Pro license', 'warning');
    } else if (error.message?.includes('404')) {
        showToast(`${context} service temporarily unavailable`, 'info');
    } else {
        showToast(`${context} failed. Please try again.`, 'error');
    }
};
```

#### **Step 3.3: Add Graceful Degradation for Missing Features**
**File**: `components/ContentFreshnessManager.tsx`  
**Location**: Add feature detection before making API calls

```typescript
// Check if content freshness is available before loading
useEffect(() => {
    const checkFeatureAvailability = async () => {
        try {
            const response = await fetch(`${window.acaData.api_url}license/status`, {
                headers: { 'X-WP-Nonce': window.acaData.nonce }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.is_active) {
                    loadContentFreshnessData();
                } else {
                    setShowUpgradePrompt(true);
                }
            }
        } catch (error) {
            handleApiError(error, 'Feature availability check');
        }
    };
    
    checkFeatureAvailability();
}, []);
```

### **Testing Requirements**
- [ ] No 400 Bad Request errors in console
- [ ] All AJAX calls have proper nonce verification
- [ ] Graceful error messages instead of console errors
- [ ] Feature detection works correctly

---

## 4. Implementation Priority and Timeline

### **Phase 1: Critical Fixes (Day 1)**
1. **License Status AJAX Handler** (Step 1.1, 1.2) - 2 hours
2. **GSC Status Endpoint** (Step 2.1, 2.2) - 1 hour
3. **License Permission Fix** (Step 2.3) - 1 hour

**Total Phase 1**: 4 hours

### **Phase 2: Frontend Updates (Day 2)**
1. **SettingsAutomation API Fix** (Step 1.3) - 1 hour
2. **Content Freshness Error Handling** (Step 3.2, 3.3) - 2 hours
3. **Settings API License Status** (Step 2.4) - 30 minutes

**Total Phase 2**: 3.5 hours

### **Phase 3: Testing and Validation (Day 3)**
1. **Unit Testing** - 2 hours
2. **Integration Testing** - 2 hours
3. **User Acceptance Testing** - 1 hour

**Total Phase 3**: 5 hours

### **Total Implementation Time**: 12.5 hours (1.5 days)

---

## 5. Validation Checklist

### **Pre-Deployment Testing**
- [ ] License verification works end-to-end
- [ ] Automation section loads correctly with active license
- [ ] Content Freshness Manager functions properly
- [ ] All REST API endpoints return expected responses
- [ ] No console errors on fresh WordPress installation
- [ ] AJAX security nonces prevent unauthorized access
- [ ] Pro features are properly gated behind license check
- [ ] Graceful degradation for users without Pro license

### **Post-Deployment Monitoring**
- [ ] Monitor error logs for new issues
- [ ] Track user reports of license-related problems
- [ ] Verify Pro feature usage analytics
- [ ] Check performance impact of additional AJAX handlers

---

## 6. Risk Assessment and Mitigation

### **Low Risk Changes**
- Adding AJAX handlers (Step 1.1, 3.1)
- Adding GSC status endpoint (Step 2.1, 2.2)
- Frontend error handling improvements (Step 3.2, 3.3)

### **Medium Risk Changes**
- License permission logic updates (Step 2.3)
- Settings API modifications (Step 2.4)

### **Mitigation Strategies**
1. **Backup Strategy**: Archive current working version before changes
2. **Rollback Plan**: Keep previous build artifacts for quick rollback
3. **Staged Deployment**: Test on development site before production
4. **Monitoring**: Implement error logging for new code paths

---

**Implementation Ready**: All solutions are technically feasible and can be implemented immediately with the existing codebase structure.
