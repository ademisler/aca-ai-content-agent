=== ACA - AI Content Agent ===
Contributors: ademisler
Tags: ai, content, generation, automation, seo, writing, blog, posts, ideas, drafts, gemini, google-ai
Requires at least: 6.5
Tested up to: 6.5
Requires PHP: 8.0
Stable tag: 1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author: Adem Isler
Author URI: https://ademisler.com
Author Email: idemasler@gmail.com

ACA is an intelligent WordPress plugin that learns your existing content's tone and style to autonomously generate high-quality, SEO-friendly new posts using Google Gemini AI.

== Description ==

ACA (AI Content Agent) is a powerful WordPress plugin that revolutionizes content creation by leveraging artificial intelligence to understand your brand voice and generate content that matches your style perfectly.

### 🎯 **Key Features**

**Content Idea Generation**
* AI-powered content ideas based on your existing posts
* Google Search Console integration for SEO opportunities
* Smart filtering to avoid duplicate ideas
* Content clustering for strategic content planning (Pro)

**Intelligent Draft Creation**
* Style-guided writing that matches your brand voice
* SEO optimization built into every draft
* Automatic content enrichment with internal links
* Featured image generation and data sections

**Style Guide Learning**
* Automatic analysis of your existing content
* Brand voice profile creation and management
* Custom prompt templates for tailored AI behavior
* Periodic style guide updates

**Automation Modes**
* Manual mode for full control
* Semi-automated for idea generation
* Fully automated content creation (Pro)

### 🚀 **Pro Features**

**Advanced Content Tools**
* Content Cluster Planner for strategic content planning
* DALL-E 3 integration for unique featured images
* Copyscape plagiarism checking for content originality
* Content update assistant for existing posts
* Data-driven sections with relevant statistics

**Enhanced Analytics**
* Unlimited content generation
* Advanced usage analytics and insights
* Performance tracking and engagement metrics

### 🔧 **Technical Highlights**

* **Google Gemini AI Integration**: Powered by the latest AI technology
* **Secure API Management**: Encrypted API keys and secure communication
* **WordPress Standards**: Built following WordPress coding standards
* **Performance Optimized**: Efficient caching and database queries
* **GDPR Compliant**: Full privacy and data protection compliance

### 📊 **System Requirements**

* WordPress 6.5 or higher
* PHP 8.0 or higher
* Google Gemini API key (free tier available)
* MySQL 5.7 or higher

### 🛡️ **Security Features**

* API keys encrypted using WordPress AUTH_KEY
* Nonce validation for all forms
* Capability-based access control
* Rate limiting to prevent abuse
* Input sanitization and validation

### 📈 **Performance Benefits**

* Reduces content creation time by 80%
* Improves SEO performance with optimized content
* Maintains consistent brand voice across all content
* Scales content production without quality loss

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/aca-ai-content-agent` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->ACA AI Content Agent screen to configure the plugin.
4. Enter your Google Gemini API key to start generating content.

### 🔑 **Getting Your Google Gemini API Key**

1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Click "Create API Key"
4. Copy the generated key
5. Paste it into the ACA plugin settings

### ⚙️ **Initial Setup**

1. **API Configuration**: Enter your Google Gemini API key
2. **Content Analysis**: Select post types and categories to analyze
3. **Working Mode**: Choose manual, semi-automated, or fully automated
4. **Style Guide**: Generate your first style guide from existing content
5. **Start Creating**: Generate your first content ideas

== Frequently Asked Questions ==

= Is the plugin free? =
Yes, the core features of ACA (manual idea and draft generation, style analysis, GSC integration, etc.) are completely free. For advanced features like the Content Cluster Planner, DALL-E 3 image generation, plagiarism checks, and full automation, you will need to upgrade to ACA Pro.

= How do I get ACA Pro? =
ACA Pro is available through Gumroad. You can purchase a license at our Gumroad store, and you'll receive a license key via email. Enter this key in the License tab of the plugin settings to unlock all Pro features.

= What happens if my license expires? =
ACA Pro licenses are perpetual and don't expire. However, if you need a refund or your purchase is refunded, the Pro features will be deactivated. You can always purchase a new license to reactivate Pro features.

= Can I use the same license on multiple sites? =
Each license is valid for one WordPress installation. If you need to use ACA Pro on multiple sites, you'll need to purchase separate licenses for each site.

= Is the generated content original and will it pass AI detectors? =
Yes, ACA generates original content based on your style guide and existing content. The AI creates unique content that matches your writing style, making it difficult for AI detectors to identify as machine-generated.

= How does the style guide work? =
ACA analyzes your existing published posts to understand your writing style, tone, and preferences. It then uses this information to generate new content that matches your brand voice perfectly.

= Can I customize the AI prompts? =
Yes, you can customize the prompts used for idea generation, draft creation, and style guide generation in the Prompts settings tab.

= What if I don't have a Google Gemini API key? =
You can get a free Google Gemini API key from [Google AI Studio](https://makersuite.google.com/app/apikey). The free tier includes generous usage limits for most users.

= How often should I regenerate the style guide? =
We recommend regenerating your style guide weekly or whenever you publish significant new content. This ensures the AI stays current with your evolving writing style.

= Can I use ACA with other content plugins? =
Yes, ACA is designed to work alongside other WordPress plugins. It creates standard WordPress posts that can be edited, scheduled, and published like any other content.

= Is my content safe and private? =
Yes, ACA respects your privacy. Your content is only sent to Google Gemini for processing and is not stored or used for any other purpose. All API communications are encrypted.

= What if I'm not satisfied with the generated content? =
All generated content is saved as drafts, so you can review and edit before publishing. You can also provide feedback on ideas to improve future generations.

== Screenshots ==

1. Dashboard showing generated ideas and content statistics
2. Settings page with API configuration and automation options
3. Style guide generation and management interface
4. Content idea generation with AI-powered suggestions
5. Draft creation with automatic content enrichment
6. License management for Pro features

== Changelog ==

= 1.3 =
* 🌐 **Primary Language Conversion**: Converted entire plugin interface from Turkish to English
* 🔧 **Critical Bug Fixes**: Resolved admin assets initialization and class loading issues
* ⚡ **WordPress Modernization**: Updated to use wp_doing_ajax() instead of deprecated DOING_AJAX
* 🔒 **Enhanced Security**: Improved developer mode handling with production safety filters
* 🎨 **UI/UX Improvements**: Complete English localization with proper internationalization
* 📝 **Code Optimization**: Removed redundant require_once statements and improved loading
* 🏗️ **Architecture Improvements**: Better class initialization order and dependency management
* 🔍 **Error Resolution**: Fixed all JavaScript language strings and AJAX error handling
* 📊 **Translation Updates**: Updated POT file with new English translation strings
* 🚀 **Production Ready**: Comprehensive testing and validation of all 162 files

= 1.2 =
* Enhanced security with API key encryption and proper sanitization
* Added comprehensive rate limiting system
* Improved error handling and recovery mechanisms
* Added caching system for better performance
* Enhanced logging with structured data
* Added database optimization with proper indexes
* Improved input validation and sanitization
* Added proper nonce validation for all forms
* Enhanced capability checks for better security
* Added fallback functionality for graceful degradation
* Improved admin notices and user feedback
* Added comprehensive uninstaller for clean removal
* License check optimization
* Admin notice optimization
* Gumroad Pro license integration
* Content cluster planning features
* DALL-E 3 image generation
* Copyscape plagiarism checking
* Advanced automation modes
* Unlimited content generation for Pro users

= 1.0 =
* Initial release with basic AI content generation
* Google Gemini API integration
* Style guide generation
* Manual and semi-automated modes
* Basic content enrichment features

== Upgrade Notice ==

= 1.3 =
MAJOR UPDATE: Complete English language conversion, critical bug fixes, and WordPress modernization. All Turkish strings converted to English with proper internationalization. Enhanced security, improved admin interface, and production-ready stability.

= 1.2 =
This major update introduces Pro licensing, enhanced security, improved performance, and advanced content creation features. Upgrade to Pro for unlimited generation, content clustering, DALL-E 3 images, and plagiarism checking.

== Support ==

For support, please visit our [support page](https://ademisler.com/support) or email us at idemasler@gmail.com.

### 📞 **Support Channels**

* **Email Support**: idemasler@gmail.com
* **Website**: https://ademisler.com
* **Documentation**: https://ademisler.com/docs
* **Community Forum**: WordPress.org support forum

### 🕒 **Support Hours**

* Monday - Friday: 9:00 AM - 6:00 PM (GMT+3)
* Weekend: Limited support available
* Emergency support for Pro users

== Credits ==

ACA is built with the following technologies and services:

* **Google Gemini AI**: Advanced AI content generation
* **WordPress**: Content management platform
* **Action Scheduler**: Reliable task scheduling
* **Gumroad**: Secure Pro license management

### 👨‍💻 **Development Team**

* **Lead Developer**: Adem Isler
* **AI Integration**: Google Gemini API
* **Security**: WordPress security standards
* **Testing**: WordPress community

---

**Version**: 1.2  
**Last Updated**: January 2025  
**Compatibility**: WordPress 6.5+, PHP 8.0+  
**License**: GPL v2 or later  
**Author**: Adem Isler  
**Support**: idemasler@gmail.com

