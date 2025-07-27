#!/bin/bash

# ACA AI Content Agent - Distribution Package Creator
# This script creates a clean distribution package excluding development files

PLUGIN_NAME="aca-ai-content-agent"
VERSION="1.3"
DIST_DIR="dist"
PACKAGE_NAME="${PLUGIN_NAME}-v${VERSION}"

echo "Creating distribution package for ${PLUGIN_NAME} v${VERSION}..."

# Create distribution directory
mkdir -p ${DIST_DIR}
rm -rf ${DIST_DIR}/${PACKAGE_NAME}
mkdir -p ${DIST_DIR}/${PACKAGE_NAME}

# Copy plugin files (excluding development directories)
echo "Copying plugin files..."
rsync -av --progress \
  --exclude='development/' \
  --exclude='.git/' \
  --exclude='node_modules/' \
  --exclude='*.zip' \
  --exclude='dist/' \
  . ${DIST_DIR}/${PACKAGE_NAME}/

# Create zip package
echo "Creating zip package..."
cd ${DIST_DIR}
zip -r ${PACKAGE_NAME}.zip ${PACKAGE_NAME}/
cd ..

echo "Distribution package created: ${DIST_DIR}/${PACKAGE_NAME}.zip"
echo "Package contents:"
unzip -l ${DIST_DIR}/${PACKAGE_NAME}.zip | head -20

echo ""
echo "✅ Distribution package ready for deployment!"
echo "📦 Location: ${DIST_DIR}/${PACKAGE_NAME}.zip"