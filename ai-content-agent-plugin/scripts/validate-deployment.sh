#!/bin/bash

# AI Content Agent v2.4.2 - Deployment Validation Script
# This script validates that all critical fixes are working correctly

echo "🚀 AI Content Agent v2.4.2 - Deployment Validation"
echo "=================================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Counters
TESTS_PASSED=0
TESTS_FAILED=0
TOTAL_TESTS=0

# Helper function to print test results
print_test_result() {
    local test_name="$1"
    local result="$2"
    local details="$3"
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    
    if [ "$result" = "PASS" ]; then
        echo -e "${GREEN}✅ PASS${NC} - $test_name"
        [ -n "$details" ] && echo -e "   ${BLUE}ℹ️  $details${NC}"
        TESTS_PASSED=$((TESTS_PASSED + 1))
    else
        echo -e "${RED}❌ FAIL${NC} - $test_name"
        [ -n "$details" ] && echo -e "   ${RED}⚠️  $details${NC}"
        TESTS_FAILED=$((TESTS_FAILED + 1))
    fi
    echo ""
}

# Test 1: Plugin Version Check
echo "🔍 Testing Plugin Version..."
if [ -f "ai-content-agent.php" ]; then
    VERSION=$(grep "Version:" ai-content-agent.php | grep -o "2\.4\.2")
    ACA_VERSION=$(grep "ACA_VERSION" ai-content-agent.php | grep -o "2\.4\.2")
    
    if [ "$VERSION" = "2.4.2" ] && [ "$ACA_VERSION" = "2.4.2" ]; then
        print_test_result "Plugin Version Check" "PASS" "Version 2.4.2 confirmed in plugin header and constant"
    else
        print_test_result "Plugin Version Check" "FAIL" "Version mismatch or not found"
    fi
else
    print_test_result "Plugin Version Check" "FAIL" "Plugin file not found"
fi

# Test 2: License Key Storage Implementation
echo "🔍 Testing License Key Storage Fix..."
if grep -q "update_option('aca_license_key', \$license_key)" includes/class-aca-rest-api.php; then
    print_test_result "License Key Storage Fix" "PASS" "License key storage implementation found"
else
    print_test_result "License Key Storage Fix" "FAIL" "License key storage implementation missing"
fi

# Test 3: License Cleanup Implementation
echo "🔍 Testing License Cleanup Implementation..."
CLEANUP_COUNT=$(grep -c "delete_option('aca_license_key')" includes/class-aca-rest-api.php)
if [ "$CLEANUP_COUNT" -ge 2 ]; then
    print_test_result "License Cleanup Implementation" "PASS" "License cleanup found in $CLEANUP_COUNT locations"
else
    print_test_result "License Cleanup Implementation" "FAIL" "License cleanup missing or incomplete"
fi

# Test 4: GSC Status Endpoint Registration
echo "🔍 Testing GSC Status Endpoint..."
if grep -q "register_rest_route('aca/v1', '/gsc/status'" includes/class-aca-rest-api.php; then
    print_test_result "GSC Status Endpoint Registration" "PASS" "GSC status endpoint registration found"
else
    print_test_result "GSC Status Endpoint Registration" "FAIL" "GSC status endpoint registration missing"
fi

# Test 5: GSC Status Method Implementation
echo "🔍 Testing GSC Status Method..."
if grep -q "public function get_gsc_status" includes/class-aca-rest-api.php; then
    print_test_result "GSC Status Method Implementation" "PASS" "get_gsc_status method implementation found"
else
    print_test_result "GSC Status Method Implementation" "FAIL" "get_gsc_status method implementation missing"
fi

# Test 6: Frontend AJAX Fix
echo "🔍 Testing Frontend AJAX Fix..."
if grep -q "licenseApi.getStatus()" components/SettingsAutomation.tsx; then
    if ! grep -q "admin-ajax.php" components/SettingsAutomation.tsx; then
        print_test_result "Frontend AJAX Fix" "PASS" "AJAX replaced with REST API, no admin-ajax.php references"
    else
        print_test_result "Frontend AJAX Fix" "FAIL" "Still contains admin-ajax.php references"
    fi
else
    print_test_result "Frontend AJAX Fix" "FAIL" "licenseApi.getStatus() not found"
fi

# Test 7: Frontend Assets Build
echo "🔍 Testing Frontend Assets..."
if [ -f "admin/assets/index-C6y68bIb.js" ] && [ -f "admin/js/index.js" ]; then
    ASSET_SIZE=$(stat -c%s "admin/assets/index-C6y68bIb.js" 2>/dev/null || echo "0")
    FALLBACK_SIZE=$(stat -c%s "admin/js/index.js" 2>/dev/null || echo "0")
    
    if [ "$ASSET_SIZE" -gt 600000 ] && [ "$FALLBACK_SIZE" -gt 600000 ]; then
        print_test_result "Frontend Assets Build" "PASS" "Both asset files present and correct size (~${ASSET_SIZE} bytes)"
    else
        print_test_result "Frontend Assets Build" "FAIL" "Asset files present but incorrect size"
    fi
else
    print_test_result "Frontend Assets Build" "FAIL" "Required asset files missing"
fi

# Test 8: Package.json Version
echo "🔍 Testing Package.json Version..."
if grep -q '"version": "2.4.2"' package.json; then
    print_test_result "Package.json Version" "PASS" "Package.json version updated to 2.4.2"
else
    print_test_result "Package.json Version" "FAIL" "Package.json version not updated"
fi

# Test 9: Documentation Updates
echo "🔍 Testing Documentation Updates..."
DOC_UPDATES=0
if grep -q "v2.4.2" README.md; then DOC_UPDATES=$((DOC_UPDATES + 1)); fi
if grep -q "2.4.2-critical-fixes" CHANGELOG.md; then DOC_UPDATES=$((DOC_UPDATES + 1)); fi
if [ -f "docs/DEPLOYMENT-GUIDE-v2.4.2.md" ]; then DOC_UPDATES=$((DOC_UPDATES + 1)); fi

if [ "$DOC_UPDATES" -eq 3 ]; then
    print_test_result "Documentation Updates" "PASS" "All documentation files updated"
else
    print_test_result "Documentation Updates" "FAIL" "Documentation updates incomplete ($DOC_UPDATES/3)"
fi

# Test 10: Release Package
echo "🔍 Testing Release Package..."
if [ -f "../releases/ai-content-agent-v2.4.2-critical-fixes.zip" ]; then
    PACKAGE_SIZE=$(stat -c%s "../releases/ai-content-agent-v2.4.2-critical-fixes.zip" 2>/dev/null || echo "0")
    if [ "$PACKAGE_SIZE" -gt 500000 ] && [ "$PACKAGE_SIZE" -lt 700000 ]; then
        print_test_result "Release Package" "PASS" "Release package exists and correct size (${PACKAGE_SIZE} bytes)"
    else
        print_test_result "Release Package" "FAIL" "Release package size incorrect"
    fi
else
    print_test_result "Release Package" "FAIL" "Release package not found"
fi

# Summary
echo "=================================================="
echo "🎯 VALIDATION SUMMARY"
echo "=================================================="
echo -e "Total Tests: ${BLUE}$TOTAL_TESTS${NC}"
echo -e "Passed: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Failed: ${RED}$TESTS_FAILED${NC}"

if [ $TESTS_FAILED -eq 0 ]; then
    echo ""
    echo -e "${GREEN}🎉 ALL TESTS PASSED - DEPLOYMENT READY!${NC}"
    echo -e "${GREEN}✅ v2.4.2 is ready for production deployment${NC}"
    echo ""
    exit 0
else
    echo ""
    echo -e "${RED}⚠️  $TESTS_FAILED TESTS FAILED - DEPLOYMENT NOT READY${NC}"
    echo -e "${RED}❌ Please fix issues before deploying${NC}"
    echo ""
    exit 1
fi