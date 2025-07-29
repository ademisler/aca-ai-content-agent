# AI Content Agent - Developer Guide

**Version:** 1.9.0  
**Last Updated:** December 19, 2024

## 📋 Table of Contents

1. [Pro License System Implementation](#pro-license-system-implementation)
2. [Architecture Overview](#architecture-overview)
3. [Development Setup](#development-setup)
4. [Build System](#build-system)
5. [API Documentation](#api-documentation)
6. [Database Schema](#database-schema)
7. [Frontend Architecture](#frontend-architecture)
8. [Testing](#testing)
9. [Deployment](#deployment)

---

## 🔑 Pro License System Implementation

### Overview

Version 1.9.0 introduces a comprehensive premium licensing system that integrates with Gumroad's License Key API to provide secure feature gating for advanced functionality.

### Architecture

#### Backend (PHP)

**License Verification Endpoint**
```php
// POST /wp-json/aca/v1/license/verify
public function verify_license_key($request) {
    // Nonce verification for security
    $nonce_check = $this->verify_nonce($request);
    if (is_wp_error($nonce_check)) {
        return $nonce_check;
    }
    
    // Sanitize input
    $params = $request->get_json_params();
    $license_key = sanitize_text_field($params['license_key'] ?? '');
    
    // Call Gumroad API
    $verification_result = $this->call_gumroad_api($product_id, $license_key);
    
    // Store license status
    if ($verification_result['success']) {
        update_option('aca_license_status', 'active');
        update_option('aca_license_data', $verification_result);
    }
    
    return rest_ensure_response($verification_result);
}
```

**Gumroad API Integration**
```php
private function call_gumroad_api($product_id, $license_key) {
    $url = 'https://api.gumroad.com/v2/licenses/verify';
    
    $response = wp_remote_post($url, array(
        'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded'
        ),
        'body' => array(
            'product_id' => $product_id,           // Use product_id for products after Jan 9, 2023
            'license_key' => $license_key,
            'increment_uses_count' => 'true'       // Track usage for analytics
        ),
        'timeout' => 30,
        'blocking' => true,
        'sslverify' => true
    ));
    
    // Validation logic
    $is_valid = ($data['success'] === true) && 
                ($data['purchase']['refunded'] === false) && 
                ($data['purchase']['chargebacked'] === false);
}
```

**Global Helper Function**
```php
/**
 * Check if ACA Pro is active
 * 
 * @return bool True if pro license is active, false otherwise
 */
function is_aca_pro_active() {
    return get_option('aca_license_status') === 'active';
}
```

#### Feature Gating Implementation

**Automation Modes (class-aca-cron.php)**
```php
// Semi-automatic mode protection
if (isset($settings['mode']) && $settings['mode'] === 'semi-automatic') {
    if (is_aca_pro_active()) {
        $this->generate_ideas_semi_auto();
    } else {
        error_log('ACA: Semi-automatic mode requires Pro license');
    }
}

// Full-automatic mode protection
if (isset($settings['mode']) && $settings['mode'] === 'full-automatic') {
    if (is_aca_pro_active()) {
        $this->run_full_automatic_cycle();
    } else {
        error_log('ACA: Full-automatic mode requires Pro license');
    }
}
```

**Google Search Console Integration**
```php
public function get_gsc_data($request) {
    // Check if pro license is active
    if (!is_aca_pro_active()) {
        return new WP_Error(
            'pro_required', 
            'Google Search Console integration requires Pro license', 
            array('status' => 403)
        );
    }
    
    // GSC data logic here...
}
```

#### Frontend (React/TypeScript)

**License API Service**
```typescript
// services/wordpressApi.ts
export const licenseApi = {
  verify: (licenseKey: string) => makeApiCall('license/verify', {
    method: 'POST',
    body: JSON.stringify({ license_key: licenseKey }),
  }),
  getStatus: () => makeApiCall('license/status'),
};
```

**License State Management**
```typescript
// License-related state
const [licenseKey, setLicenseKey] = useState('');
const [licenseStatus, setLicenseStatus] = useState<{
    status: string, 
    is_active: boolean, 
    verified_at?: string
}>({status: 'inactive', is_active: false});
const [isVerifyingLicense, setIsVerifyingLicense] = useState(false);

// Load license status on component mount
useEffect(() => {
    const loadLicenseStatus = async () => {
        try {
            const status = await licenseApi.getStatus();
            setLicenseStatus(status);
        } catch (error) {
            console.error('Failed to load license status:', error);
        }
    };
    
    loadLicenseStatus();
}, []);
```

**Conditional Feature Rendering**
```typescript
// Automation Mode with Pro gating
{currentSettings.is_pro ? (
    <div className="aca-card">
        {/* Full automation settings */}
    </div>
) : (
    <UpgradePrompt 
        title="Advanced Automation Modes"
        description="Unlock Semi-Automatic and Full-Automatic modes"
        features={[
            "Semi-Automatic: Automated idea generation",
            "Full-Automatic: Complete automation",
            "Advanced scheduling options"
        ]}
    />
)}
```

**Upgrade Prompt Component**
```typescript
export const UpgradePrompt: React.FC<UpgradePromptProps> = ({ 
    title, description, features, gumroadUrl 
}) => {
    return (
        <div className="aca-card" style={{ 
            border: '2px solid #f0ad4e',
            background: 'linear-gradient(135deg, #fff9e6 0%, #ffeaa7 100%)'
        }}>
            <div className="pro-badge">PRO</div>
            <h3>{title}</h3>
            <p>{description}</p>
            <ul>
                {features.map((feature, index) => (
                    <li key={index}>{feature}</li>
                ))}
            </ul>
            <a href={gumroadUrl} className="aca-button aca-button-primary">
                Upgrade to Pro
            </a>
        </div>
    );
};
```

### Security Considerations

1. **Input Sanitization**: All license keys are sanitized using `sanitize_text_field()`
2. **Nonce Verification**: All API endpoints require valid WordPress nonces
3. **Permission Checks**: Admin-only access to license endpoints
4. **Secure Storage**: License data stored using WordPress options API
5. **API Security**: HTTPS-only communication with Gumroad API

### Testing the License System

**Manual Testing**
1. Activate plugin without license (free mode)
2. Verify pro features show upgrade prompts
3. Enter valid license key and verify activation
4. Confirm pro features become available
5. Test invalid license key handling

**API Testing**
```bash
# Test license verification (replace with actual values)
curl -X POST http://your-site.com/wp-json/aca/v1/license/verify \
  -H "Content-Type: application/json" \
  -H "X-WP-Nonce: your-nonce" \
  -d '{"license_key": "your-license-key"}'

# Test license status
curl -X GET http://your-site.com/wp-json/aca/v1/license/status \
  -H "X-WP-Nonce: your-nonce"
```

### Configuration

**Product ID Setup**
Replace the placeholder in `class-aca-rest-api.php`:
```php
// Line ~3143
$product_id = 'YOUR_GUMROAD_PRODUCT_ID_HERE';
```

**Gumroad Store URL**
Update the Gumroad URL in upgrade prompts:
```typescript
// components/UpgradePrompt.tsx
gumroadUrl = "https://gumroad.com/l/your-actual-product-link"
```

---