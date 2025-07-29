=== AI Content Agent (ACA) ===
Contributors: ademisler
Tags: ai, content, automation, seo, calendar
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.8.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered content creation and management plugin with intelligent Content Calendar and real Google Search Console integration.

== Description ==

## 🚀 Latest Updates - v1.8.0 - COMPREHENSIVE FEATURE ENHANCEMENTS & IMPROVEMENTS 🚀

### 🔄 **GEMINI API RETRY LOGIC IMPLEMENTATION**
- **Intelligent Retry System**: Added comprehensive retry logic with exponential backoff for 503/429 errors
- **Model Fallback Strategy**: Automatic fallback from gemini-2.0-flash to gemini-1.5-pro when overloaded
- **Enhanced Error Detection**: Smart detection for overload, timeout, and API key configuration issues
- **Maximum Retry Attempts**: Up to 3 retry attempts with intelligent delay strategy (2s, 4s, 8s)
- **Production Resilience**: Enhanced API call resilience for stable production environments

### 🛡️ **ERROR HANDLING IMPROVEMENTS**
- **User-Friendly Messages**: Contextual error messages with emojis and clear action guidance
- **Specific Error Scenarios**: Tailored messages for different failure types (overload, timeout, API key)
- **Better Timeout Handling**: Increased timeout limits (120 seconds) for complex content generation
- **Enhanced Token Limits**: Increased maxOutputTokens to 4096 for longer, more detailed content
- **Graceful Degradation**: Smooth user experience even when AI services are temporarily unavailable

### ✅ **PRODUCTION READINESS**
- **Robust Overload Handling**: Automatic handling of Gemini API overload situations without user intervention
- **Automatic Model Switching**: Seamless fallback to stable models when primary model is unavailable
- **Enhanced User Experience**: Minimal disruption during AI service fluctuations
- **Comprehensive Monitoring**: Detailed error logging for system administrators and debugging

## 🚀 Previous Updates - v1.6.7 - DEEP CACHE FIX & ERROR BOUNDARY 🚀

### 🔍 **DEEP CACHE ANALYSIS**
- **Root Cause Identified**: WordPress serving old cached JS file due to alphabetical sorting
- **Cache Persistence**: WordPress using same script handle, preventing fresh loads
- **File Selection Logic Fix**: PHP logic updated to select latest JS file by modification time
- **Error Boundary Implementation**: React ErrorBoundary added for graceful error handling

= 🔍 Google Search Console Integration =

Connect your actual Google Search Console account for data-driven content creation:

* **Real-Time Data**: Access actual GSC data for informed content decisions
* **Search Performance**: Monitor clicks, impressions, CTR, and average position
* **Keyword Insights**: Discover top-performing and underperforming keywords
* **Content Optimization**: AI suggestions based on actual search data

= 🤖 AI-Powered Content Creation =

Advanced AI integration with robust error handling:

* **Gemini AI Integration**: Google's Gemini AI with intelligent retry logic
* **Smart Idea Generation**: AI suggests content ideas based on your niche and trends
* **Auto-Draft Creation**: Transform ideas into full blog posts automatically with error resilience
* **SEO Optimization**: Built-in SEO analysis and optimization suggestions
* **Robust API Handling**: Intelligent error handling and automatic retry mechanisms

= 📅 Intelligent Content Calendar =

Visual content management with drag-and-drop functionality:

* **Drag & Drop Scheduling**: Intuitive interface for content scheduling
* **Multi-Status Support**: Manage drafts, scheduled posts, and published content
* **Visual Timeline**: Clear representation of your content pipeline
* **Smart Rescheduling**: Easy rescheduling with drag-and-drop functionality

= ⚙️ Automation Modes =

Choose your preferred level of automation:

* **Manual Mode**: Full manual control over content creation and publishing
* **Semi-Automatic**: AI assists with content generation, manual publishing with retry logic
* **Full-Automatic**: Complete automation from idea generation to publishing with error resilience

= 🎨 Style Guide Management =

Maintain consistent brand voice:

* **Brand Consistency**: Maintain consistent brand voice and style
* **AI Analysis**: Automatic style guide analysis and suggestions
* **Custom Guidelines**: Define your unique writing style and tone

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ai-content-agent-plugin` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->AI Content Agent screen to configure the plugin.
4. Add your Gemini AI API key in the settings.
5. Optionally connect your Google Search Console account for enhanced functionality.

== Frequently Asked Questions ==

= What happens if the Gemini API is overloaded? =

The plugin automatically handles API overload situations with intelligent retry logic. It will:
1. Try the fallback model (gemini-1.5-pro) automatically
2. Retry up to 3 times with exponential backoff delays
3. Show user-friendly error messages with clear guidance
4. Allow you to try again when the service is available

= Do I need a Google Search Console account? =

No, Google Search Console integration is optional but recommended. The plugin works perfectly without it, but connecting GSC provides:
* Real search performance data for better content ideas
* Keyword insights from actual search queries
* Data-driven content optimization suggestions

= What AI models are supported? =

The plugin uses Google's Gemini AI with intelligent model fallback:
* **Primary**: gemini-2.0-flash (latest and fastest)
* **Fallback**: gemini-1.5-pro (stable and reliable)
* **Automatic Switching**: Plugin automatically switches models when needed

= Is the plugin compatible with SEO plugins? =

Yes! The plugin integrates seamlessly with:
* Yoast SEO (Free and Premium versions)
* RankMath (Free and Pro versions)
* Automatic meta title and description generation
* Focus keyword optimization
* Schema markup support

== Screenshots ==

1. Main dashboard with content calendar and AI-powered tools
2. Idea generation interface with Google Search Console integration
3. Draft creation with AI assistance and retry logic
4. Settings page with API configuration and automation options
5. Content calendar with drag-and-drop scheduling
6. Style guide management interface

== Changelog ==

= 1.6.8 - 2025-01-29 =
* **MAJOR**: Added comprehensive Gemini API retry logic with exponential backoff
* **MAJOR**: Implemented automatic model fallback (gemini-2.0-flash → gemini-1.5-pro)
* **ENHANCEMENT**: User-friendly error messages with emojis and clear guidance
* **ENHANCEMENT**: Increased timeout limits (120s) and token limits (4096) for better performance
* **ENHANCEMENT**: Enhanced error detection for overload, timeout, and API key issues
* **FIX**: Production-grade error handling for AI service disruptions
* **FIX**: Comprehensive error logging for system administrators

= 1.6.7 - 2025-01-29 =
* **MAJOR**: Deep cache analysis and fix for WordPress JavaScript file loading
* **MAJOR**: Implemented React ErrorBoundary for graceful error handling
* **FIX**: WordPress cache bypass with unique script handles using MD5 hash
* **FIX**: File selection logic updated to use modification time instead of alphabetical order
* **FIX**: Removed old problematic JavaScript files causing initialization errors

= 1.6.6 - 2025-01-29 =
* **MAJOR**: Fixed WordPress cache issues preventing JavaScript file updates
* **MAJOR**: Resolved Google API dependency warnings with placeholder files
* **FIX**: Corrected JavaScript file loading path from admin/js to admin/assets
* **FIX**: Implemented proper file versioning with timestamp for cache bypass
* **ENHANCEMENT**: Created vendor/autoload.php placeholder for dependency checks

= 1.6.5 - 2025-01-29 =
* **MAJOR**: Fixed temporal dead zone errors by disabling minification
* **MAJOR**: Enhanced Vite build configuration for proper function execution order
* **FIX**: Changed Rollup output format to IIFE for better variable isolation
* **FIX**: Removed source maps to reduce bundle size and improve loading
* **ENHANCEMENT**: Complete resolution of "Cannot access 'Te' before initialization" errors

= 1.6.4 - 2025-01-29 =
* **CRITICAL**: Fixed "Cannot access 'Te' before initialization" JavaScript error
* **FIX**: Resolved function hoisting issues in App.tsx with proper declaration order
* **FIX**: Added window.acaData existence checks to all API calls in Settings.tsx
* **ENHANCEMENT**: Enhanced Terser configuration to prevent variable hoisting
* **ENHANCEMENT**: Improved WordPress data availability checks throughout application

= 1.6.3 - 2025-01-29 =
* **MAJOR**: Comprehensive documentation update across all files
* **ENHANCEMENT**: Updated all version references to maintain consistency
* **ENHANCEMENT**: Improved setup guides for all integrations
* **ENHANCEMENT**: Enhanced build processes and deployment procedures
* **ENHANCEMENT**: Better file organization and release management

= 1.6.2 - 2025-01-28 =
* **CRITICAL**: Fixed JavaScript initialization error completely
* **FIX**: Switched from ESBuild to Terser minifier for safer variable scoping
* **FIX**: Updated build target from ES2015 to ES2020 for better compatibility
* **ENHANCEMENT**: Improved WordPress plugin asset management
* **ENHANCEMENT**: Enhanced build system with optimized settings

= 1.6.1 - 2025-01-28 =
* **FIX**: Fixed window object consistency issues
* **ENHANCEMENT**: Improved browser compatibility
* **FIX**: Resolved various initialization edge cases

= 1.6.0 - 2025-01-28 =
* **MAJOR**: Fixed build system and React initialization
* **ENHANCEMENT**: Improved overall plugin stability
* **FIX**: Resolved multiple compatibility issues

== Upgrade Notice ==

= 1.6.8 =
Major update with intelligent Gemini API retry logic and enhanced error handling. Automatic model fallback ensures content creation continues even when AI services are overloaded. Highly recommended update for production sites.

= 1.6.7 =
Critical update fixing WordPress cache issues and JavaScript initialization errors. Includes React ErrorBoundary for graceful error handling. Essential update for all users.

= 1.6.6 =
Important update resolving cache and dependency issues. Fixes JavaScript file loading and eliminates dependency warnings. Recommended for all users experiencing loading issues.

= 1.6.5 =
Critical update completely fixing temporal dead zone JavaScript errors. All users experiencing "Cannot access before initialization" errors should update immediately.

= 1.6.4 =
Critical JavaScript initialization error fix. Essential update for users experiencing plugin loading issues or console errors.

== Additional Info ==

**Minimum Requirements:**
* WordPress 5.0 or higher
* PHP 7.4 or higher
* Gemini AI API key (required for content generation)

**Recommended:**
* WordPress 6.8 or higher
* PHP 8.0 or higher
* Google Search Console account (optional)
* Modern web browser with JavaScript enabled

**Support:**
* Comprehensive documentation included
* GitHub repository for issues and feature requests
* Regular updates and improvements
* Community support through WordPress forums

**Privacy:**
* Plugin only sends content to Google's Gemini AI for generation
* No personal data is stored or transmitted without explicit user action
* Google Search Console data is only accessed with explicit user authorization
* All API communications are secured with HTTPS