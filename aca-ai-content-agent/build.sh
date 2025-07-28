#!/bin/bash

# ACA AI Content Agent Build Script
# This script builds the React frontend for the WordPress plugin

echo "Building ACA AI Content Agent frontend..."

# Navigate to the source directory
cd src

# Install dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
    echo "Installing dependencies..."
    npm install
fi

# Build the production version
echo "Building production assets..."
npm run build

echo "Build completed! Files are in the /build directory."
echo "The plugin is ready to be activated in WordPress."