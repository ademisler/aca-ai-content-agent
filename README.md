# ACA - AI Content Agent WordPress Plugin

A powerful WordPress plugin that leverages artificial intelligence to understand your brand voice and generate content that matches your style perfectly using Google Gemini AI.

## 📁 Project Structure

This repository is now properly organized with clear separation between plugin files and development resources:

### Plugin Files (Production Ready)
```
├── aca-ai-content-agent.php    # Main plugin file
├── readme.txt                  # WordPress plugin readme
├── index.php                   # Security file
├── uninstall.php              # Plugin uninstall script
├── composer.json              # PHP dependencies
├── composer.lock              # Locked dependency versions
├── admin/                     # Admin interface files
├── assets/                    # CSS, JS, and media files
├── includes/                  # Core plugin classes
├── templates/                 # Template files
├── languages/                 # Translation files
└── vendor/                    # Composer dependencies
```

### Development Files (Not for Production)
```
├── dev-docs/                  # Developer documentation
│   ├── AGENTS.md             # AI agents documentation
│   ├── BUGFIXES-SUMMARY.md   # Bug fixes summary
│   ├── GUMROAD_SETUP.md      # Gumroad integration setup
│   ├── README-TESTING.md     # Testing procedures
│   ├── UX-IMPROVEMENTS*.md   # UX improvement notes
│   └── README.md             # Dev docs overview
└── dev-testing/              # Testing files
    ├── test-plugin.php       # Plugin testing script
    ├── wordpress-test/       # Testing environment
    └── README.md             # Testing overview
```

## 🚀 Installation

1. Upload the plugin files (excluding `dev-docs/` and `dev-testing/`) to your WordPress `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure your Google Gemini AI API key in the plugin settings

## 🛠️ Development

For developers working on this plugin:

- **Documentation**: See `dev-docs/` directory for all development documentation
- **Testing**: Use files in `dev-testing/` directory for testing procedures
- **Contributing**: Follow the guidelines in `dev-docs/README-TESTING.md`

## 📦 Building for Distribution

When creating a distribution package:

1. Exclude the following directories:
   - `dev-docs/`
   - `dev-testing/`
   - `.git/`
   - `node_modules/` (if present)

2. Include only the production-ready plugin files listed above

## 📝 License

GPLv2 or later - see the plugin header for full license information.

## 👤 Author

**Adem Isler**
- Website: https://ademisler.com
- Email: idemasler@gmail.com