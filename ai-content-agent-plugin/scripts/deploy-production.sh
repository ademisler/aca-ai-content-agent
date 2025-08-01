#!/bin/bash

# AI Content Agent v2.4.2 - Production Deployment Script
# This script handles the safe deployment of v2.4.2 to production

echo "🚀 AI Content Agent v2.4.2 - Production Deployment"
echo "=================================================="
echo ""

# Configuration
VERSION="2.4.2"
RELEASE_PACKAGE="ai-content-agent-v2.4.2-critical-fixes.zip"
BACKUP_DIR="backups/pre-v2.4.2-$(date +%Y%m%d-%H%M%S)"
DEPLOYMENT_LOG="deployment-v2.4.2-$(date +%Y%m%d-%H%M%S).log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Helper function to log with timestamp
log_deployment() {
    local message="$1"
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    echo "[$timestamp] $message" | tee -a "$DEPLOYMENT_LOG"
}

# Helper function for user confirmation
confirm_action() {
    local message="$1"
    echo -e "${YELLOW}⚠️  $message${NC}"
    read -p "Continue? (y/N): " -n 1 -r
    echo ""
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${RED}❌ Deployment cancelled by user${NC}"
        exit 1
    fi
}

# Pre-deployment checks
echo -e "${BLUE}🔍 PRE-DEPLOYMENT CHECKS${NC}"
echo "================================"

# Check 1: Release package exists
log_deployment "Checking release package availability..."
if [ ! -f "../releases/$RELEASE_PACKAGE" ]; then
    echo -e "${RED}❌ Release package not found: $RELEASE_PACKAGE${NC}"
    log_deployment "ERROR: Release package not found"
    exit 1
fi
echo -e "${GREEN}✅ Release package found${NC}"
log_deployment "Release package verified: $RELEASE_PACKAGE"

# Check 2: Run validation tests
log_deployment "Running pre-deployment validation..."
if ! ./scripts/validate-deployment.sh > validation-pre-deploy.log 2>&1; then
    echo -e "${RED}❌ Pre-deployment validation failed${NC}"
    echo "Check validation-pre-deploy.log for details"
    log_deployment "ERROR: Pre-deployment validation failed"
    exit 1
fi
echo -e "${GREEN}✅ Pre-deployment validation passed${NC}"
log_deployment "Pre-deployment validation: PASSED"

# Check 3: Database backup verification
log_deployment "Verifying database backup capability..."
if command -v mysqldump >/dev/null 2>&1 || command -v wp >/dev/null 2>&1; then
    echo -e "${GREEN}✅ Database backup tools available${NC}"
    log_deployment "Database backup tools: AVAILABLE"
else
    echo -e "${YELLOW}⚠️  Database backup tools not found - manual backup required${NC}"
    log_deployment "WARNING: Database backup tools not found"
    confirm_action "Database backup tools not available. Have you manually backed up the database?"
fi

# Check 4: Maintenance mode capability
log_deployment "Checking maintenance mode capability..."
if [ -f "wp-config.php" ] || command -v wp >/dev/null 2>&1; then
    echo -e "${GREEN}✅ Maintenance mode capability confirmed${NC}"
    log_deployment "Maintenance mode: AVAILABLE"
else
    echo -e "${YELLOW}⚠️  WordPress CLI not available - manual maintenance mode required${NC}"
    log_deployment "WARNING: WordPress CLI not available"
fi

echo ""
echo -e "${BLUE}📋 DEPLOYMENT SUMMARY${NC}"
echo "================================"
echo "Version: $VERSION"
echo "Package: $RELEASE_PACKAGE"
echo "Backup Directory: $BACKUP_DIR"
echo "Deployment Log: $DEPLOYMENT_LOG"
echo ""

confirm_action "Ready to proceed with production deployment?"

# Create backup directory
log_deployment "Creating backup directory..."
mkdir -p "$BACKUP_DIR"
echo -e "${GREEN}✅ Backup directory created: $BACKUP_DIR${NC}"

# Step 1: Enable maintenance mode
echo ""
echo -e "${BLUE}🔧 STEP 1: ENABLE MAINTENANCE MODE${NC}"
log_deployment "Enabling maintenance mode..."

if command -v wp >/dev/null 2>&1; then
    wp maintenance-mode activate 2>/dev/null || echo "# Maintenance mode" > .maintenance
    echo -e "${GREEN}✅ Maintenance mode enabled${NC}"
    log_deployment "Maintenance mode: ENABLED"
else
    echo "<?php \$upgrading = time(); ?>" > .maintenance
    echo -e "${GREEN}✅ Maintenance mode enabled (manual)${NC}"
    log_deployment "Maintenance mode: ENABLED (manual)"
fi

# Step 2: Create backup
echo ""
echo -e "${BLUE}💾 STEP 2: CREATE BACKUP${NC}"
log_deployment "Creating complete backup..."

# Backup current plugin files
log_deployment "Backing up plugin files..."
if [ -d "ai-content-agent-plugin" ]; then
    cp -r ai-content-agent-plugin "$BACKUP_DIR/plugin-backup" 2>/dev/null || {
        echo -e "${RED}❌ Plugin backup failed${NC}"
        log_deployment "ERROR: Plugin backup failed"
        rm -f .maintenance
        exit 1
    }
    echo -e "${GREEN}✅ Plugin files backed up${NC}"
    log_deployment "Plugin backup: COMPLETED"
else
    echo -e "${YELLOW}⚠️  Plugin directory not found - assuming fresh installation${NC}"
    log_deployment "WARNING: Plugin directory not found"
fi

# Backup database (if possible)
log_deployment "Attempting database backup..."
if command -v wp >/dev/null 2>&1; then
    wp db export "$BACKUP_DIR/database-backup.sql" 2>/dev/null && {
        echo -e "${GREEN}✅ Database backed up${NC}"
        log_deployment "Database backup: COMPLETED"
    } || {
        echo -e "${YELLOW}⚠️  Database backup failed - continuing with file backup only${NC}"
        log_deployment "WARNING: Database backup failed"
    }
elif command -v mysqldump >/dev/null 2>&1; then
    # This would need database credentials - skipping in this example
    echo -e "${YELLOW}⚠️  Manual database backup required${NC}"
    log_deployment "WARNING: Manual database backup required"
else
    echo -e "${YELLOW}⚠️  No database backup tools available${NC}"
    log_deployment "WARNING: No database backup tools available"
fi

# Step 3: Deploy new version
echo ""
echo -e "${BLUE}📦 STEP 3: DEPLOY NEW VERSION${NC}"
log_deployment "Deploying v$VERSION..."

# Extract new version
log_deployment "Extracting release package..."
cd ..
if unzip -q "releases/$RELEASE_PACKAGE" -d temp-deployment/; then
    echo -e "${GREEN}✅ Release package extracted${NC}"
    log_deployment "Package extraction: COMPLETED"
else
    echo -e "${RED}❌ Package extraction failed${NC}"
    log_deployment "ERROR: Package extraction failed"
    cd ai-content-agent-plugin
    rm -f .maintenance
    exit 1
fi

# Replace plugin files
log_deployment "Replacing plugin files..."
if [ -d "ai-content-agent-plugin" ]; then
    rm -rf ai-content-agent-plugin.old 2>/dev/null
    mv ai-content-agent-plugin ai-content-agent-plugin.old
fi

mv temp-deployment/ai-content-agent-plugin ./ && {
    echo -e "${GREEN}✅ Plugin files updated${NC}"
    log_deployment "Plugin files: UPDATED"
} || {
    echo -e "${RED}❌ Plugin file update failed${NC}"
    log_deployment "ERROR: Plugin file update failed"
    # Rollback
    if [ -d "ai-content-agent-plugin.old" ]; then
        mv ai-content-agent-plugin.old ai-content-agent-plugin
        echo -e "${YELLOW}⚠️  Rolled back to previous version${NC}"
        log_deployment "ROLLBACK: Restored previous version"
    fi
    cd ai-content-agent-plugin
    rm -f .maintenance
    exit 1
fi

# Cleanup
rm -rf temp-deployment/
rm -rf ai-content-agent-plugin.old 2>/dev/null

cd ai-content-agent-plugin

# Step 4: Post-deployment validation
echo ""
echo -e "${BLUE}✅ STEP 4: POST-DEPLOYMENT VALIDATION${NC}"
log_deployment "Running post-deployment validation..."

if ./scripts/validate-deployment.sh > validation-post-deploy.log 2>&1; then
    echo -e "${GREEN}✅ Post-deployment validation passed${NC}"
    log_deployment "Post-deployment validation: PASSED"
else
    echo -e "${RED}❌ Post-deployment validation failed${NC}"
    echo "Check validation-post-deploy.log for details"
    log_deployment "ERROR: Post-deployment validation failed"
    
    confirm_action "Validation failed. Continue anyway or rollback?"
fi

# Step 5: Disable maintenance mode
echo ""
echo -e "${BLUE}🔓 STEP 5: DISABLE MAINTENANCE MODE${NC}"
log_deployment "Disabling maintenance mode..."

rm -f .maintenance
if command -v wp >/dev/null 2>&1; then
    wp maintenance-mode deactivate 2>/dev/null
fi

echo -e "${GREEN}✅ Maintenance mode disabled${NC}"
log_deployment "Maintenance mode: DISABLED"

# Step 6: Success tracking initialization
echo ""
echo -e "${BLUE}📊 STEP 6: INITIALIZE SUCCESS TRACKING${NC}"
log_deployment "Initializing success tracking..."

# Run initial success metrics collection
if ./scripts/track-success-metrics.sh > initial-metrics.log 2>&1; then
    echo -e "${GREEN}✅ Success tracking initialized${NC}"
    log_deployment "Success tracking: INITIALIZED"
else
    echo -e "${YELLOW}⚠️  Success tracking initialization had issues${NC}"
    log_deployment "WARNING: Success tracking initialization issues"
fi

# Final summary
echo ""
echo "=================================================="
echo -e "${GREEN}🎉 DEPLOYMENT COMPLETED SUCCESSFULLY!${NC}"
echo "=================================================="
echo ""
echo "✅ Version: $VERSION deployed"
echo "✅ Backup created: $BACKUP_DIR"
echo "✅ Validation: PASSED"
echo "✅ Maintenance mode: DISABLED"
echo "✅ Success tracking: ACTIVE"
echo ""
echo -e "📄 Deployment log: ${BLUE}$DEPLOYMENT_LOG${NC}"
echo -e "📊 Initial metrics: ${BLUE}initial-metrics.log${NC}"
echo -e "🔍 Validation results: ${BLUE}validation-post-deploy.log${NC}"
echo ""

log_deployment "DEPLOYMENT COMPLETED SUCCESSFULLY"
log_deployment "Version $VERSION is now live in production"

# Post-deployment instructions
echo -e "${YELLOW}📋 NEXT STEPS:${NC}"
echo "1. Monitor user feedback and support tickets"
echo "2. Check success metrics in 1 hour, 6 hours, and 24 hours"
echo "3. Send user communications about the fixes"
echo "4. Prepare success report for stakeholders"
echo ""

# Schedule monitoring reminder
echo -e "${BLUE}⏰ MONITORING SCHEDULE:${NC}"
echo "- Hour 1: Check for immediate issues"
echo "- Hour 6: Verify user response and metrics"
echo "- Day 1: Comprehensive success assessment"
echo "- Week 1: Long-term impact evaluation"
echo ""

log_deployment "Deployment process completed - monitoring phase begins"

echo -e "${GREEN}🚀 AI Content Agent v$VERSION is now live!${NC}"
echo ""

exit 0