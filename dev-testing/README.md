# Development Testing Files

This directory contains testing tools and scripts for the **ACA - AI Content Agent** plugin.

## 📁 Files

| File | Purpose |
|------|---------|
| **test-plugin.php** | Comprehensive test suite for plugin validation |
| **create-distribution.sh** | Script to create production-ready packages |
| **wordpress-test/** | Docker environment for WordPress testing |

## 🧪 Quick Testing

```bash
# Run all tests
php test-plugin.php

# Create distribution package
./create-distribution.sh
```

## 🚫 Distribution Note

**Exclude this entire directory** from production releases.