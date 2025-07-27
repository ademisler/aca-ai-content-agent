# ACA - AI Content Agent Development Guide

**Version:** 1.3  
**Status:** Production Ready  
**Last Updated:** January 2025

## 🚀 Plugin Overview

ACA (AI Content Agent) is a WordPress plugin that uses Google Gemini AI to generate content ideas and drafts based on your existing content's style and tone.

### Key Features
- **Content Ideas**: AI-powered idea generation from existing posts
- **Draft Creation**: Style-guided content writing 
- **Style Analysis**: Automatic brand voice learning
- **Pro Features**: Advanced tools like content clustering, DALL-E images, plagiarism checks

## 🏗️ Architecture

### Core Classes

| Class | Purpose | Location |
|-------|---------|----------|
| `ACA_Plugin` | Main plugin controller | `includes/class-aca-plugin.php` |
| `ACA_Idea_Service` | Content idea generation | `includes/services/class-aca-idea-service.php` |
| `ACA_Draft_Service` | Draft creation and management | `includes/services/class-aca-draft-service.php` |
| `ACA_Style_Guide_Service` | Style analysis and learning | `includes/services/class-aca-style-guide-service.php` |
| `ACA_Gemini_Api` | Google Gemini AI integration | `includes/api/class-aca-gemini-api.php` |
| `ACA_Gumroad_Api` | Pro license management | `includes/api/class-aca-gumroad-api.php` |

### File Structure
```
aca-ai-content-agent/
├── aca-ai-content-agent.php     # Main plugin file
├── includes/
│   ├── admin/                   # Admin interface
│   ├── api/                     # External API integrations
│   ├── services/                # Core business logic
│   ├── utils/                   # Utility classes
│   ├── core/                    # Plugin lifecycle
│   └── class-aca-plugin.php     # Main controller
├── admin/                       # CSS/JS assets
├── languages/                   # Translation files
└── vendor/                      # Composer dependencies
```

## 🗄️ Database

### Tables
- `aca_ai_content_agent_ideas` - Generated content ideas
- `aca_ai_content_agent_logs` - System logs and errors
- `aca_ai_content_agent_clusters` - Content clusters (Pro)
- `aca_ai_content_agent_cluster_items` - Cluster items (Pro)

### Key Options
- `aca_ai_content_agent_options` - Main plugin settings
- `aca_ai_content_agent_gemini_api_key` - Encrypted API key
- `aca_ai_content_agent_license_key` - Pro license key
- `aca_ai_content_agent_style_guide` - Generated style guide

## 🔧 Development Setup

### Requirements
- PHP 8.0+
- WordPress 6.5+
- Composer for dependencies

### Local Development
1. Clone the repository
2. Run `composer install`
3. Set up WordPress development environment
4. Configure Google Gemini API key in settings

### Testing
Use the test script in `dev-testing/test-plugin.php`:
```bash
php dev-testing/test-plugin.php
```

## 🔌 API Integration

### Google Gemini AI
- **Endpoint**: Google AI Studio API
- **Authentication**: API key (encrypted storage)
- **Rate Limiting**: Built-in protection
- **Error Handling**: Comprehensive retry logic

### Gumroad (Pro Licensing)
- **Product ID**: `aca-ai-content-agent-pro`
- **Validation**: License key verification
- **Features**: Pro feature unlocking

## 🛡️ Security

### Best Practices
- All API keys encrypted using WordPress `AUTH_KEY`
- Nonce validation on all forms
- Capability checks for admin functions
- Input sanitization and output escaping
- SQL injection prevention with prepared statements

### Encryption
```php
// Encrypt sensitive data
$encrypted = aca_ai_content_agent_encrypt($sensitive_data);

// Decrypt with fallback
$decrypted = aca_ai_content_agent_safe_decrypt($encrypted_data);
```

## 📦 Distribution

### Building for Release
Use the distribution script:
```bash
./dev-testing/create-distribution.sh
```

This creates a clean package excluding:
- `dev-docs/` directory
- `dev-testing/` directory  
- `.git/` directory
- Development files

### Version Management
1. Update version in `aca-ai-content-agent.php`
2. Update `readme.txt` changelog
3. Update language files: `languages/aca.pot`
4. Test thoroughly before release

## 🔄 Hooks & Filters

### Key Actions
- `aca_ai_content_agent_run_main_automation` - Main automation cron
- `aca_ai_content_agent_reset_api_usage_counter` - Monthly reset
- `aca_ai_content_agent_generate_style_guide` - Style guide update

### Key Filters
- `aca_ai_content_agent_dev_mode_enabled` - Developer mode control
- `aca_ai_content_agent_api_rate_limit` - API rate limiting
- `aca_ai_content_agent_style_guide_content` - Style guide customization

## 📞 Support

- **Email**: idemasler@gmail.com
- **Website**: https://ademisler.com
- **Repository**: GitHub (private)

---

**For detailed testing procedures, see `TESTING-GUIDE.md`**  
**For Pro setup instructions, see `GUMROAD-SETUP.md`**
