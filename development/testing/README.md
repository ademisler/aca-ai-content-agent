# Development Testing Tools

This directory contains testing tools and scripts for the **ACA - AI Content Agent** plugin.

## 📁 Files

| File | Purpose | Status |
|------|---------|--------|
| **test-plugin.php** | Comprehensive test suite for plugin validation | ✅ Active |
| **create-distribution.sh** | Script to create production-ready packages | ✅ Active |
| **wordpress-test/** | Docker environment for WordPress testing | ✅ Active |

## 🧪 Quick Testing

```bash
# From project root, run all tests
php development/testing/test-plugin.php

# Create distribution package
./development/testing/create-distribution.sh

# Start WordPress test environment
cd development/testing/wordpress-test && docker-compose up -d
```

## 📋 Test Categories

- **Core Plugin Tests**: Architecture, class loading, WordPress integration
- **Security Tests**: Encryption, validation, permission checks
- **API Tests**: Gemini AI, Gumroad license validation
- **Database Tests**: Schema, CRUD operations, data integrity

## 🔧 Distribution

The `create-distribution.sh` script creates a clean production package by:
- Excluding the entire `development/` directory
- Removing development artifacts
- Creating a ZIP file ready for distribution

## ⚠️ Important

**This testing directory is for development only** and should be excluded from production releases.