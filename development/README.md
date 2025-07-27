# ACA - AI Content Agent Development

This directory contains all development-related files for the **ACA - AI Content Agent** WordPress plugin.

## 📁 Directory Structure

```
development/
├── docs/                    # Documentation
│   ├── DEVELOPMENT-GUIDE.md # Plugin architecture & development guide
│   ├── TESTING-GUIDE.md     # Testing procedures & QA
│   ├── GUMROAD-SETUP.md     # Pro license setup via Gumroad
│   └── README.md            # Documentation index
└── testing/                 # Testing tools & scripts
    ├── test-plugin.php      # Comprehensive test suite
    ├── create-distribution.sh # Distribution package creator
    ├── wordpress-test/      # Docker test environment
    └── README.md            # Testing guide
```

## 🚀 Quick Start

### For Developers
1. **Architecture**: See `docs/DEVELOPMENT-GUIDE.md`
2. **Testing**: Use `testing/test-plugin.php`
3. **Distribution**: Run `testing/create-distribution.sh`

### For Pro Setup
1. **License System**: Follow `docs/GUMROAD-SETUP.md`

## 🎯 Purpose

This unified development directory contains:
- **Complete documentation** for plugin development
- **Testing tools** for quality assurance
- **Distribution scripts** for release preparation
- **Setup guides** for Pro license integration

## ⚠️ Important

**This entire `development/` directory should be excluded from production releases.**

Add to your build scripts:
```bash
--exclude='development/'
```