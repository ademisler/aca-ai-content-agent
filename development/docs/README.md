# Developer Documentation

This directory contains comprehensive development documentation for the **ACA - AI Content Agent** WordPress plugin.

## 📁 Files Overview

| File | Purpose | Status | Lines |
|------|---------|--------|-------|
| **DEVELOPMENT-GUIDE.md** | Plugin architecture & development guide | ✅ Current | ~150 |
| **TESTING-GUIDE.md** | Testing procedures and quality assurance | ✅ Current | ~170 |
| **GUMROAD-SETUP.md** | Pro license setup via Gumroad | ✅ Current | ~115 |

## 🎯 Quick Navigation

### For New Developers
1. **Start Here**: `DEVELOPMENT-GUIDE.md` - Plugin architecture and core concepts
2. **Testing**: `TESTING-GUIDE.md` - How to test and validate your changes
3. **Pro Features**: `GUMROAD-SETUP.md` - License system integration

### Key Topics Covered

**DEVELOPMENT-GUIDE.md:**
- Plugin architecture and core classes
- Database structure and API integrations
- Security best practices
- Development setup and workflows

**TESTING-GUIDE.md:**
- Automated test suite usage
- Manual testing procedures
- Quality assurance checklist
- Debugging and troubleshooting

**GUMROAD-SETUP.md:**
- Pro license system setup
- Gumroad integration steps
- Sales and customer support

## 📝 Documentation Standards

- **Language**: All documentation in English
- **Version**: Matches plugin version (currently **1.3**)
- **Updates**: Maintained with each release
- **Focus**: Current, actionable information only

## 🔗 Related Resources

- **Testing Tools**: `../testing/` directory
- **Distribution**: Use `../testing/create-distribution.sh`
- **WordPress Test Environment**: `../testing/wordpress-test/`

## ⚠️ Distribution Note

**These documentation files are for development only** and should be excluded from production releases using `--exclude='development/'`.