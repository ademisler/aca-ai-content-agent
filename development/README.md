# ACA - AI Content Agent Development

This directory contains all development-related files for the **ACA - AI Content Agent** WordPress plugin.

## 📁 Directory Structure

```
development/
├── README.md                # This file - development overview
├── docs/                    # Complete documentation
│   ├── DEVELOPMENT-GUIDE.md # Plugin architecture & development guide (~150 lines)
│   ├── TESTING-GUIDE.md     # Testing procedures & QA (~170 lines)
│   ├── GUMROAD-SETUP.md     # Pro license setup via Gumroad (~115 lines)
│   └── README.md            # Documentation index & navigation
└── testing/                 # Testing tools & scripts
    ├── test-plugin.php      # Comprehensive test suite (~280 lines)
    ├── create-distribution.sh # Distribution package creator (~40 lines)
    ├── wordpress-test/      # Docker test environment
    │   └── docker-compose.yml # WordPress + MySQL setup
    └── README.md            # Testing tools guide
```

## 🚀 Quick Start

### For  Developers
1. **Read First**: `docs/DEVELOPMENT-GUIDE.md` - Complete plugin architecture
2. **Setup Environment**: Follow development setup instructions
3. **Run Tests**: `php testing/test-plugin.php` - Validate your setup
4. **Start Coding**: Use the architecture guide as reference

### For Experienced Developers
1. **Architecture**: `docs/DEVELOPMENT-GUIDE.md` - Core classes and structure
2. **Testing**: `testing/test-plugin.php` - Comprehensive test suite
3. **Distribution**: `testing/create-distribution.sh` - Build production package

### For Pro License Setup
1. **Gumroad Integration**: Follow `docs/GUMROAD-SETUP.md`
2. **License Validation**: Test with the provided tools

## 🎯 What's Inside

### Documentation (`docs/`)
- **Complete development guide** with plugin architecture
- **Testing procedures** and quality assurance guidelines
- **Pro license setup** instructions for Gumroad integration
- **Navigation and quick reference** for all documentation

### Testing Tools (`testing/`)
- **Automated test suite** covering all plugin functionality
- **Distribution builder** for creating production packages
- **Docker environment** for WordPress testing
- **Testing guides** and procedures

## 🔧 Common Tasks

### Run Tests
```bash
# From project root
php development/testing/test-plugin.php

# With verbose output
php development/testing/test-plugin.php --verbose
```

### Create Distribution Package
```bash
# From project root
./development/testing/create-distribution.sh
```

### Start Test Environment
```bash
cd development/testing/wordpress-test
docker-compose up -d
```

## 📊 Statistics

- **Total Documentation**: ~435 lines across 4 files
- **Test Coverage**: Comprehensive (all core functionality)
- **Development Tools**: 3 active scripts
- **Last Updated**: January 2025

## ⚠️ Important Notes

### Production Exclusion
**This entire `development/` directory must be excluded from production releases.**

**Build Scripts:**
```bash
--exclude='development/'
```

**Distribution:**
The included `create-distribution.sh` script automatically excludes this directory.

### Version Compatibility
- **Plugin Version**: 1.3
- **WordPress**: 6.5+
- **PHP**: 8.0+
- **Documentation**: Always matches plugin version

---

**Need help?** Start with `docs/DEVELOPMENT-GUIDE.md` for complete guidance.