# AI Content Agent (ACA) for WordPress

**Version:** 2.3.0  
**Requires:** WordPress 5.0+, PHP 7.4+  
**License:** GPL v2 or later

AI Content Agent (ACA) is a powerful WordPress plugin that leverages Google's Gemini AI to automate content creation, SEO optimization, and publishing workflows.

## 🚀 Latest Updates - v2.3.0

### 🌍 **MAJOR FEATURE: Automatic Language Detection & Multilingual Support**
- **WordPress Locale Integration**: Automatically detects website language from WordPress locale settings
- **50+ Language Support**: Comprehensive language mapping for global websites (European, Asian, Middle Eastern, Slavic, etc.)
- **Smart Fallback System**: Graceful degradation to English if language not detected
- **Language-Aware Content Generation**: AI generates blog post ideas and content in the detected website language
- **Cultural Context**: AI considers cultural nuances and language-specific writing styles
- **Zero Configuration**: Automatic detection requires no user setup

### 🏗️ **MAJOR FEATURE: Intelligent Category Hierarchy System**
- **Hierarchical Category Detection**: Automatically detects parent-child category relationships
- **AI-Powered Category Selection**: Intelligent selection between parent and subcategory based on content
- **Category Context Analysis**: AI considers category hierarchy when making selections
- **Subcategory Preference**: AI chooses specific subcategories over broad parent categories when appropriate
- **Content-Category Matching**: Sophisticated matching between content topic and category hierarchy
- **Example**: For "Asgari Ücret" content, AI can distinguish between "Fransa > Asgari Ücret" vs "Almanya > Asgari Ücret" subcategories

### 🔧 **Critical Fixes (v2.2.6)**
- **Settings Page Scroll Fix**: Complete resolution of settings page scroll jumping issue
- **Dropdown Stability**: Dropdown expansion no longer causes page to jump to top
- **Enhanced UX**: Fixed height containers with proper overflow handling and internal scrolling

## 🚀 Features

### 🤖 **AI-Powered Content Creation**
- **Gemini AI Integration**: Advanced content generation using Google's Gemini AI with retry logic
- **Smart Idea Generation**: AI suggests content ideas based on your niche and trends
- **Auto-Draft Creation**: Transform ideas into full blog posts automatically with error resilience
- **SEO Optimization**: Built-in SEO analysis and optimization suggestions
- **Robust API Handling**: Intelligent error handling and automatic retry mechanisms

### 📅 **Intelligent Content Calendar**
- **Drag & Drop Scheduling**: Intuitive drag-and-drop interface for content scheduling
- **Multi-Status Support**: Manage drafts, scheduled posts, and published content
- **Visual Timeline**: Clear visual representation of your content pipeline
- **Smart Rescheduling**: Easy rescheduling with drag-and-drop functionality
- **Multi-Post Display**: Intelligent UI for handling multiple posts on the same day

### ✨ **AI-Powered Content Freshness System (PRO)**
- **Intelligent Analysis**: AI-powered freshness scoring using Gemini AI
- **Multi-Factor Scoring**: Age score + SEO performance + AI analysis (0-100 scale)
- **Priority System**: 5-level priority system for update recommendations
- **Automated Monitoring**: Configurable periodic analysis (daily/weekly/monthly)
- **Update Suggestions**: Specific, actionable improvement recommendations
- **Performance Integration**: Google Search Console data for SEO-based scoring
- **Queue Management**: Content update queue with status tracking
- **Beautiful Interface**: Full-featured management dashboard with statistics

### 🔍 **Google Search Console Integration**
- **Real-Time Data**: Access actual GSC data for informed content decisions
- **Search Performance**: Monitor clicks, impressions, CTR, and average position
- **Keyword Insights**: Discover top-performing and underperforming keywords
- **Content Optimization**: AI suggestions based on actual search data

### ⚙️ **Automation Modes**
- **Manual Mode**: Full manual control over content creation and publishing
- **Semi-Automatic**: AI assists with content generation, manual publishing
- **Full-Automatic**: Complete automation from idea generation to publishing

### 🎨 **Style Guide Management**
- **Brand Consistency**: Maintain consistent brand voice and style
- **AI Analysis**: Automatic style guide analysis and suggestions
- **Custom Guidelines**: Define your unique writing style and tone

### 📊 **Analytics & Reporting**
- **Activity Logging**: Comprehensive activity tracking and reporting
- **Performance Metrics**: Track content performance and engagement
- **SEO Insights**: Monitor SEO performance and optimization opportunities

## 🛠️ Installation

### Automatic Installation (Recommended)
1. Download the latest release from the releases directory
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Choose the downloaded zip file and click "Install Now"
4. Activate the plugin
5. Configure your settings in AI Content Agent (ACA) → Settings

### Manual Installation
1. Download and extract the plugin files
2. Upload the `ai-content-agent-plugin` folder to `/wp-content/plugins/`
3. Activate the plugin through the WordPress Plugins screen
4. Configure your settings

## 📁 Release Management

### Current Release
- **Latest Stable**: v2.2.6 (Located in `/releases/`)
- **File**: `ai-content-agent-v2.2.6-settings-scroll-jumping-fix.zip`
- **Status**: Stable, ready for production with critical settings page fixes
- **Development Version**: v2.3.0 with multilingual support and intelligent categorization (in development)
- **Next Release**: v2.3.0 will be available when development is complete

### Archive
- **Previous Versions**: All older versions are stored in `/releases/archive/`
- **Total Archived**: 27 previous versions
- **Purpose**: Development history and rollback capability

### For Developers
```bash
# Current stable release
releases/ai-content-agent-v2.2.6-settings-scroll-jumping-fix.zip

# Development build (after making changes)
npm run build:wp  # Builds and copies to both admin/assets/ and admin/js/

# Archived versions
releases/archive/ai-content-agent-v1.3.x-*.zip
releases/archive/ai-content-agent-v1.4.x-*.zip
releases/archive/ai-content-agent-v1.5.x-*.zip
releases/archive/ai-content-agent-v1.6.x-*.zip
releases/archive/ai-content-agent-v1.7.x-*.zip
releases/archive/ai-content-agent-v2.0.x-*.zip
releases/archive/ai-content-agent-v2.1.x-*.zip
```

**⚠️ Important for Developers**: When making style changes or any frontend modifications, always use `npm run build:wp` to ensure both asset locations are updated. See `DEVELOPER_GUIDE.md` for detailed build instructions.

## ⚡ Quick Start

1. **Install & Activate** the plugin
2. **Configure API Keys** in Settings:
   - Add your Gemini AI API key
   - Configure image provider APIs (optional)
3. **Set Up Google Search Console** (optional but recommended):
   - Follow the setup guide in `GOOGLE_SEARCH_CONSOLE_SETUP.md`
   - Connect your GSC account for real search data
4. **Choose Your Mode**:
   - Manual: Full control
   - Semi-Auto: AI assistance with retry logic
   - Full-Auto: Complete automation with error resilience
5. **Start Creating**: Use the Idea Board to generate and manage content ideas

## 🔧 Configuration

### Required
- **Gemini AI API Key**: For content generation with automatic retry support
- **WordPress 5.0+**: Minimum WordPress version
- **PHP 7.4+**: Minimum PHP version

### Optional
- **Google Search Console**: For real search data integration
- **Image APIs**: Google Imagen 3.0 for AI-generated images, plus Pexels, Unsplash, and Pixabay for stock photos
- **SEO Plugins**: Yoast SEO or RankMath integration

## 📚 Documentation

Comprehensive documentation is now organized in the [`docs/`](docs/) directory:

### 🎯 User Guides
- **[Content Creation Guide](docs/user-guides/content-creation.md)** - Complete content creation workflow
- **[Multilingual Features](docs/user-guides/multilingual.md)** - Language detection and multilingual content

### 🛠️ Setup & Configuration
- **[Google Search Console Setup](docs/setup/google-search-console.md)** - Complete GSC integration guide
- **[AI Image Generation Setup](docs/setup/ai-image-generation.md)** - Google Imagen 3.0 integration
- **[SEO Integration Setup](docs/setup/seo-integration.md)** - SEO plugin integration guide

### 🔧 Developer Documentation
- **[Development Guide](docs/developer/development-guide.md)** - Comprehensive development guide
- **[Build Process](docs/developer/build-process.md)** - Build and deployment instructions

### 📖 Reference
- **[Changelog](docs/reference/CHANGELOG.md)** - Detailed version history
- **[Release Notes](docs/reference/RELEASES.md)** - Release management and notes
- **[Plugin Improvement Ideas](docs/reference/improvement-ideas.md)** - Future development roadmap

**📋 [Complete Documentation Index](docs/README.md)** - Browse all available documentation

## 🐛 Troubleshooting

### Common Issues

1. **Gemini API Overload (503 Error)**
   - **Solution**: Plugin automatically retries with fallback model (v1.6.8 feature)
   - **Expected**: User sees friendly message: "🤖 AI service is temporarily overloaded. Please wait a moment and try again."
   - **Automatic**: Plugin tries gemini-1.5-pro as fallback, then retries with exponential backoff

2. **Plugin Activation Error**
   - **Solution**: Use latest v1.6.8 which includes all previous fixes
   - **Cause**: Previous versions had various compatibility issues

3. **JavaScript Initialization Errors**
   - **Solution**: All JavaScript errors completely fixed in v1.6.7+
   - **Root Cause**: Cache and minification issues resolved with error boundary implementation
   - **Expected**: No JavaScript errors should appear in browser console

4. **GSC Integration Issues**
   - **Solution**: Follow the detailed setup guide in `GOOGLE_SEARCH_CONSOLE_SETUP.md`
   - **Requirements**: Google API client library (auto-installed)

5. **Missing Dependencies**
   - **Solution**: Plugin automatically detects and offers to install
   - **Manual**: Run `composer install` in plugin directory

## 🤝 Support

- **GitHub Issues**: Report bugs and request features
- **Documentation**: Comprehensive guides included
- **Version History**: Full changelog available
- **Error Handling**: Enhanced error messages guide users to solutions

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 🔄 Version History

- **v2.3.0**: Multilingual support & intelligent categorization (Development)
- **v2.2.6**: Settings scroll jumping fix (Current Release)
- **v2.1.0**: AI-powered content freshness system (PRO)
- **v2.0.5**: Essential Gemini API warning system
- **v2.0.4**: Complete license management & UX overhaul
- **v2.0.3**: Asset deployment & cache invalidation
- **v1.8.0**: Comprehensive feature enhancements & improvements
- **v1.7.0**: Comprehensive feature enhancements & improvements
- **v1.6.9**: UI/UX improvements & critical bug fixes
- **v1.6.8**: Gemini API retry logic & improved error handling
- **v1.6.7**: Deep cache fix & error boundary implementation
- **v1.6.6**: Cache fix & dependencies resolved
- **v1.6.5**: Temporal dead zone fix
- **v1.6.4**: Critical JavaScript initialization error fix
- **v1.6.3**: Documentation update and build optimization
- **v1.6.2**: Fixed JavaScript initialization error
- **v1.6.1**: Fixed window object consistency
- **v1.6.0**: Fixed build system and React initialization
- **v1.4.9**: Fixed critical activation error
- **v1.4.8**: Enhanced GSC error handling
- **v1.4.7**: Resolved console errors
- **v1.4.0**: Google Search Console integration
- **v1.3.8**: Content Calendar improvements
- **v1.3.5**: Scheduling system overhaul

For complete version history, see `CHANGELOG.md`.

---

**Ready to revolutionize your content workflow with AI Content Agent (ACA)?** Download v2.2.6 for the most stable experience with critical settings page fixes, or stay tuned for v2.3.0 with revolutionary multilingual support and intelligent categorization! 🚀
