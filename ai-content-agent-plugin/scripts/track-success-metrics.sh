#!/bin/bash

# AI Content Agent v2.4.2 - Success Metrics Tracking Script
# This script automatically collects and reports on deployment success metrics

echo "📊 AI Content Agent v2.4.2 - Success Metrics Collection"
echo "======================================================="
echo ""

# Configuration
DEPLOYMENT_DATE="2025-08-01"
WORDPRESS_DB_PREFIX="wp_"
LOG_FILE="success-metrics-$(date +%Y%m%d).log"
REPORT_FILE="success-report-$(date +%Y%m%d).md"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper function to log with timestamp
log_metric() {
    local message="$1"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $message" | tee -a "$LOG_FILE"
}

# Helper function to execute SQL and return result
execute_sql() {
    local query="$1"
    local result
    
    # Try different MySQL connection methods
    if command -v mysql >/dev/null 2>&1; then
        result=$(mysql -se "$query" 2>/dev/null || echo "0")
    elif command -v wp >/dev/null 2>&1; then
        result=$(wp db query "$query" --skip-column-names --silent 2>/dev/null || echo "0")
    else
        echo "0"
    fi
    
    echo "$result"
}

# Initialize report
cat > "$REPORT_FILE" << EOF
# AI Content Agent v2.4.2 - Success Metrics Report

**Generated**: $(date '+%Y-%m-%d %H:%M:%S')  
**Deployment Date**: $DEPLOYMENT_DATE  
**Days Since Deployment**: $(( ($(date +%s) - $(date -d "$DEPLOYMENT_DATE" +%s)) / 86400 ))  

---

## 📊 KEY METRICS SUMMARY

EOF

echo -e "${BLUE}🔍 Collecting License Validation Metrics...${NC}"

# Metric 1: License Status Distribution
log_metric "Collecting license status distribution..."
ACTIVE_LICENSES=$(execute_sql "SELECT COUNT(*) FROM ${WORDPRESS_DB_PREFIX}options WHERE option_name = 'aca_license_status' AND option_value = 'active';")
TOTAL_LICENSES=$(execute_sql "SELECT COUNT(*) FROM ${WORDPRESS_DB_PREFIX}options WHERE option_name = 'aca_license_status';")

if [ "$TOTAL_LICENSES" -gt 0 ]; then
    ACTIVE_PERCENTAGE=$(( ACTIVE_LICENSES * 100 / TOTAL_LICENSES ))
else
    ACTIVE_PERCENTAGE=0
fi

log_metric "Active licenses: $ACTIVE_LICENSES / $TOTAL_LICENSES ($ACTIVE_PERCENTAGE%)"

cat >> "$REPORT_FILE" << EOF
### 🎯 License Validation Success Rate

- **Active Licenses**: $ACTIVE_LICENSES
- **Total Licenses**: $TOTAL_LICENSES  
- **Success Rate**: $ACTIVE_PERCENTAGE%
- **Target**: >95%
- **Status**: $([ $ACTIVE_PERCENTAGE -ge 95 ] && echo "✅ TARGET MET" || echo "⚠️ BELOW TARGET")

EOF

# Metric 2: License Key Storage Verification
echo -e "${BLUE}🔍 Verifying License Key Storage...${NC}"
log_metric "Checking license key storage implementation..."

LICENSES_WITH_KEYS=$(execute_sql "SELECT COUNT(*) FROM ${WORDPRESS_DB_PREFIX}options WHERE option_name = 'aca_license_key' AND option_value IS NOT NULL AND option_value != '';")
KEY_STORAGE_PERCENTAGE=$([ "$ACTIVE_LICENSES" -gt 0 ] && echo $(( LICENSES_WITH_KEYS * 100 / ACTIVE_LICENSES )) || echo "0")

log_metric "Licenses with stored keys: $LICENSES_WITH_KEYS / $ACTIVE_LICENSES ($KEY_STORAGE_PERCENTAGE%)"

cat >> "$REPORT_FILE" << EOF
### 🔑 License Key Storage Fix

- **Active Licenses**: $ACTIVE_LICENSES
- **Licenses with Keys**: $LICENSES_WITH_KEYS
- **Storage Success Rate**: $KEY_STORAGE_PERCENTAGE%
- **Target**: 100%
- **Status**: $([ $KEY_STORAGE_PERCENTAGE -ge 95 ] && echo "✅ EXCELLENT" || echo "⚠️ NEEDS ATTENTION")

EOF

# Metric 3: License Validation Chain Completeness
echo -e "${BLUE}🔍 Checking License Validation Chain...${NC}"
log_metric "Verifying complete license validation chain..."

VERIFIED_LICENSES=$(execute_sql "SELECT COUNT(*) FROM ${WORDPRESS_DB_PREFIX}options WHERE option_name = 'aca_license_verified' AND option_value IS NOT NULL;")
TIMESTAMPED_LICENSES=$(execute_sql "SELECT COUNT(*) FROM ${WORDPRESS_DB_PREFIX}options WHERE option_name = 'aca_license_timestamp' AND CAST(option_value AS UNSIGNED) > (UNIX_TIMESTAMP() - 86400);")

CHAIN_COMPLETENESS=$([ "$ACTIVE_LICENSES" -gt 0 ] && echo $(( (VERIFIED_LICENSES + TIMESTAMPED_LICENSES + LICENSES_WITH_KEYS) * 100 / (ACTIVE_LICENSES * 3) )) || echo "0")

log_metric "Validation chain completeness: $CHAIN_COMPLETENESS%"

cat >> "$REPORT_FILE" << EOF
### 🔗 License Validation Chain

- **Status Check**: $ACTIVE_LICENSES licenses active
- **Verification Check**: $VERIFIED_LICENSES licenses verified  
- **Timestamp Check**: $TIMESTAMPED_LICENSES licenses with recent timestamps
- **Key Storage Check**: $LICENSES_WITH_KEYS licenses with stored keys
- **Chain Completeness**: $CHAIN_COMPLETENESS%
- **Status**: $([ $CHAIN_COMPLETENESS -ge 90 ] && echo "✅ STRONG CHAIN" || echo "⚠️ WEAK CHAIN")

EOF

# Metric 4: Plugin Version Verification
echo -e "${BLUE}🔍 Verifying Plugin Version...${NC}"
log_metric "Checking plugin version deployment..."

if [ -f "ai-content-agent.php" ]; then
    PLUGIN_VERSION=$(grep "Version:" ai-content-agent.php | grep -o "2\.4\.2" || echo "NOT_FOUND")
    CONSTANT_VERSION=$(grep "ACA_VERSION" ai-content-agent.php | grep -o "2\.4\.2" || echo "NOT_FOUND")
    
    if [ "$PLUGIN_VERSION" = "2.4.2" ] && [ "$CONSTANT_VERSION" = "2.4.2" ]; then
        VERSION_STATUS="✅ CORRECT"
        log_metric "Plugin version: CORRECT (2.4.2)"
    else
        VERSION_STATUS="❌ INCORRECT"
        log_metric "Plugin version: INCORRECT ($PLUGIN_VERSION / $CONSTANT_VERSION)"
    fi
else
    VERSION_STATUS="❌ FILE NOT FOUND"
    log_metric "Plugin version: FILE NOT FOUND"
fi

cat >> "$REPORT_FILE" << EOF
### 📦 Plugin Version Status

- **Header Version**: $PLUGIN_VERSION
- **Constant Version**: $CONSTANT_VERSION
- **Expected**: 2.4.2
- **Status**: $VERSION_STATUS

EOF

# Metric 5: Frontend Assets Verification
echo -e "${BLUE}🔍 Checking Frontend Assets...${NC}"
log_metric "Verifying frontend assets build..."

if [ -f "admin/assets/index-C6y68bIb.js" ] && [ -f "admin/js/index.js" ]; then
    ASSET_SIZE=$(stat -c%s "admin/assets/index-C6y68bIb.js" 2>/dev/null || echo "0")
    FALLBACK_SIZE=$(stat -c%s "admin/js/index.js" 2>/dev/null || echo "0")
    
    if [ "$ASSET_SIZE" -gt 600000 ] && [ "$FALLBACK_SIZE" -gt 600000 ]; then
        ASSETS_STATUS="✅ CORRECT"
        log_metric "Frontend assets: CORRECT (${ASSET_SIZE} bytes)"
    else
        ASSETS_STATUS="⚠️ SIZE ISSUE"
        log_metric "Frontend assets: SIZE ISSUE (${ASSET_SIZE} bytes)"
    fi
else
    ASSETS_STATUS="❌ MISSING"
    log_metric "Frontend assets: MISSING"
fi

cat >> "$REPORT_FILE" << EOF
### 🎨 Frontend Assets Status

- **Primary Asset**: $([ -f "admin/assets/index-C6y68bIb.js" ] && echo "✅ Present ($ASSET_SIZE bytes)" || echo "❌ Missing")
- **Fallback Asset**: $([ -f "admin/js/index.js" ] && echo "✅ Present ($FALLBACK_SIZE bytes)" || echo "❌ Missing")
- **Status**: $ASSETS_STATUS

EOF

# Metric 6: API Endpoints Status (if possible to test)
echo -e "${BLUE}🔍 Checking API Endpoints...${NC}"
log_metric "Verifying API endpoint registrations..."

GSC_STATUS_ENDPOINT=$(grep -c "register_rest_route.*gsc/status" includes/class-aca-rest-api.php 2>/dev/null || echo "0")
GSC_STATUS_METHOD=$(grep -c "function get_gsc_status" includes/class-aca-rest-api.php 2>/dev/null || echo "0")

if [ "$GSC_STATUS_ENDPOINT" -gt 0 ] && [ "$GSC_STATUS_METHOD" -gt 0 ]; then
    API_STATUS="✅ IMPLEMENTED"
    log_metric "GSC Status API: IMPLEMENTED"
else
    API_STATUS="❌ MISSING"
    log_metric "GSC Status API: MISSING"
fi

cat >> "$REPORT_FILE" << EOF
### 🔌 API Endpoints Status

- **GSC Status Endpoint**: $([ $GSC_STATUS_ENDPOINT -gt 0 ] && echo "✅ Registered" || echo "❌ Missing")
- **GSC Status Method**: $([ $GSC_STATUS_METHOD -gt 0 ] && echo "✅ Implemented" || echo "❌ Missing")
- **Overall Status**: $API_STATUS

EOF

# Calculate Overall Success Score
echo -e "${BLUE}🔍 Calculating Overall Success Score...${NC}"

SCORE=0
TOTAL_CHECKS=6

# License validation success (25 points)
[ $ACTIVE_PERCENTAGE -ge 95 ] && SCORE=$((SCORE + 25))

# License key storage (25 points)  
[ $KEY_STORAGE_PERCENTAGE -ge 95 ] && SCORE=$((SCORE + 25))

# Validation chain (15 points)
[ $CHAIN_COMPLETENESS -ge 90 ] && SCORE=$((SCORE + 15))

# Plugin version (15 points)
[ "$VERSION_STATUS" = "✅ CORRECT" ] && SCORE=$((SCORE + 15))

# Frontend assets (10 points)
[ "$ASSETS_STATUS" = "✅ CORRECT" ] && SCORE=$((SCORE + 10))

# API endpoints (10 points)
[ "$API_STATUS" = "✅ IMPLEMENTED" ] && SCORE=$((SCORE + 10))

log_metric "Overall success score: $SCORE/100"

cat >> "$REPORT_FILE" << EOF

---

## 🎯 OVERALL SUCCESS ASSESSMENT

### Success Score: $SCORE/100

$(if [ $SCORE -ge 90 ]; then
    echo "### 🎉 DEPLOYMENT SUCCESS!"
    echo ""
    echo "**Status**: ✅ **HIGHLY SUCCESSFUL**"
    echo ""
    echo "The v2.4.2 deployment has been highly successful with $SCORE/100 points."
    echo "All critical metrics are meeting or exceeding targets."
elif [ $SCORE -ge 75 ]; then
    echo "### ✅ DEPLOYMENT MOSTLY SUCCESSFUL"
    echo ""
    echo "**Status**: ⚠️ **MOSTLY SUCCESSFUL**"
    echo ""
    echo "The v2.4.2 deployment is mostly successful with $SCORE/100 points."
    echo "Some areas may need attention but overall impact is positive."
else
    echo "### ⚠️ DEPLOYMENT NEEDS ATTENTION"
    echo ""
    echo "**Status**: ❌ **NEEDS IMMEDIATE ATTENTION**"
    echo ""
    echo "The v2.4.2 deployment scored $SCORE/100 points."
    echo "Critical issues need immediate resolution."
fi)

### 📈 Trend Analysis

- **Days Since Deployment**: $(( ($(date +%s) - $(date -d "$DEPLOYMENT_DATE" +%s)) / 86400 )) days
- **License Success Rate**: $ACTIVE_PERCENTAGE% (Target: >95%)
- **Key Storage Rate**: $KEY_STORAGE_PERCENTAGE% (Target: 100%)
- **Validation Chain**: $CHAIN_COMPLETENESS% (Target: >90%)

### 🎯 Recommendations

$(if [ $ACTIVE_PERCENTAGE -lt 95 ]; then
    echo "- ⚠️ License validation success rate below target - investigate database issues"
fi)
$(if [ $KEY_STORAGE_PERCENTAGE -lt 95 ]; then
    echo "- ⚠️ License key storage incomplete - some users may need to re-verify licenses"
fi)
$(if [ $CHAIN_COMPLETENESS -lt 90 ]; then
    echo "- ⚠️ Validation chain incomplete - check all license-related options"
fi)
$(if [ "$VERSION_STATUS" != "✅ CORRECT" ]; then
    echo "- ❌ Plugin version incorrect - verify deployment completed successfully"
fi)
$(if [ "$ASSETS_STATUS" != "✅ CORRECT" ]; then
    echo "- ⚠️ Frontend assets issue - rebuild and redeploy assets"
fi)
$(if [ "$API_STATUS" != "✅ IMPLEMENTED" ]; then
    echo "- ❌ API endpoints missing - verify code deployment"
fi)
$(if [ $SCORE -ge 90 ]; then
    echo "- ✅ All systems operating optimally - continue monitoring"
    echo "- 🎉 Consider announcing success to stakeholders"
    echo "- 📊 Prepare success case study for future deployments"
fi)

---

**Report Generated**: $(date '+%Y-%m-%d %H:%M:%S')  
**Next Check Recommended**: $(date -d '+1 day' '+%Y-%m-%d %H:%M:%S')

EOF

# Summary output
echo ""
echo "======================================================="
echo -e "${BLUE}📊 SUCCESS METRICS SUMMARY${NC}"
echo "======================================================="
echo -e "Overall Success Score: ${GREEN}$SCORE/100${NC}"
echo -e "License Success Rate: ${GREEN}$ACTIVE_PERCENTAGE%${NC}"
echo -e "Key Storage Rate: ${GREEN}$KEY_STORAGE_PERCENTAGE%${NC}"
echo -e "Validation Chain: ${GREEN}$CHAIN_COMPLETENESS%${NC}"
echo ""

if [ $SCORE -ge 90 ]; then
    echo -e "${GREEN}🎉 DEPLOYMENT HIGHLY SUCCESSFUL!${NC}"
    echo -e "${GREEN}All critical metrics are meeting targets.${NC}"
elif [ $SCORE -ge 75 ]; then
    echo -e "${YELLOW}✅ DEPLOYMENT MOSTLY SUCCESSFUL${NC}"
    echo -e "${YELLOW}Some areas may need attention.${NC}"
else
    echo -e "${RED}⚠️ DEPLOYMENT NEEDS IMMEDIATE ATTENTION${NC}"
    echo -e "${RED}Critical issues require resolution.${NC}"
fi

echo ""
echo -e "📄 Detailed report saved to: ${BLUE}$REPORT_FILE${NC}"
echo -e "📝 Metrics log saved to: ${BLUE}$LOG_FILE${NC}"
echo ""

# Exit with appropriate code
if [ $SCORE -ge 75 ]; then
    exit 0
else
    exit 1
fi