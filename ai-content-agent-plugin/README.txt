=== AI Content Agent (ACA) ===
Contributors: aca-team
Tags: ai, content, automation, blog, writing, seo, gemini, markdown, categories
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 8.0
Stable tag: 1.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered content creation and management plugin that generates blog posts, ideas, and manages your content workflow automatically using Google Gemini 2.0 Flash.

== Description ==

AI Content Agent (ACA) is a powerful WordPress plugin that revolutionizes your content creation process using Google Gemini 2.0 Flash artificial intelligence. Create high-quality blog posts, generate content ideas, and manage your entire content workflow with minimal effort.

= Key Features =

* **AI-Powered Content Generation**: Create complete blog posts with title, content, meta descriptions, and focus keywords using Google Gemini 2.0 Flash
* **Smart Content Formatting**: Automatic Markdown to HTML conversion for perfect WordPress display
* **Intelligent Category Management**: AI selects from existing categories or creates new ones intelligently
* **Smart Idea Generation**: Generate unlimited content ideas based on your writing style
* **Style Guide Analysis**: AI analyzes your existing content to maintain consistent tone and style
* **Advanced Error Recovery**: Comprehensive error handling with fallback mechanisms
* **SEO Optimization**: Built-in SEO features with meta titles, descriptions, and focus keywords
* **Internal Linking**: Contextual internal links to your existing content
* **Activity Logging**: Track all content creation activities with detailed logging
* **Draft Management**: Create, edit, and publish drafts seamlessly

= Content Creation Workflow =

1. **Style Analysis**: AI analyzes your last 20 posts to understand your writing style
2. **Idea Generation**: Generate content ideas based on your style guide
3. **Draft Creation**: AI creates complete blog posts (800-1500 words) with proper HTML formatting
4. **WordPress Integration**: Creates real WordPress draft posts with categories, tags, and SEO metadata

= AI Integration =

The plugin uses Google's Gemini 2.0 Flash for:
- Content style analysis from your existing posts
- Blog post generation with proper HTML formatting
- Idea creation based on your writing style
- Smart category selection from existing WordPress categories
- SEO optimization and internal linking

= Recent Improvements (v1.3.x) =

* **v1.3.2**: Fixed content formatting issues - automatic Markdown to HTML conversion
* **v1.3.1**: Enhanced AI response parsing with smart JSON cleaning
* **v1.3.0**: Fixed category management with intelligent existing category selection
* **Advanced Error Handling**: Comprehensive debug logging and error recovery

= WordPress Integration =

* **Native Posts**: Creates actual WordPress draft posts
* **Smart Categories**: Uses existing categories intelligently
* **Automatic Tags**: Generates relevant tags based on content
* **Custom Meta Fields**: Stores SEO data and AI generation metadata
* **Perfect Formatting**: Clean HTML output for WordPress

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/ai-content-agent` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to AI Content Agent in your WordPress admin menu
4. Add your Google Gemini API key in the Settings tab
5. Generate your first Style Guide by clicking "Analyze Content"
6. Start creating content!

= Getting Gemini API Key =

1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key
3. Copy the key to plugin settings

== Frequently Asked Questions ==

= Do I need a Google Gemini API key? =

Yes, you need a Google Gemini API key to use the content generation features. You can get one free from Google AI Studio.

= What AI model does the plugin use? =

The plugin uses Google Gemini 2.0 Flash, the latest and most advanced model from Google for superior content quality.

= Will the generated content be properly formatted? =

Yes! The plugin automatically converts any Markdown formatting to clean HTML, ensuring perfect display in WordPress.

= How does category management work? =

The AI intelligently selects from your existing WordPress categories. If no suitable category exists, it can create new ones as needed.

= Can I edit the generated content? =

Absolutely! All generated content can be fully edited in WordPress before publishing.

= Is the plugin compatible with SEO plugins? =

Yes, the plugin is compatible with popular SEO plugins like RankMath and Yoast SEO.

= What if I encounter errors? =

The plugin has comprehensive error handling and detailed logging. Check the WordPress debug logs for troubleshooting information.

== Screenshots ==

1. Dashboard overview showing content statistics and recent activity
2. Style Guide management interface with AI analysis
3. Content ideas board with generation options
4. Draft editor with full content management
5. Settings panel with Gemini API configuration
6. Activity logs with detailed operation tracking

== Changelog ==

= 1.3.2 =
* FIXED: Markdown formatting issues in generated content
* ADDED: Automatic Markdown to HTML conversion for perfect WordPress display
* ENHANCED: Content display and formatting in WordPress
* IMPROVED: AI prompt instructions for clean HTML output
* ADDED: Fallback Markdown parser for legacy content

= 1.3.1 =
* FIXED: Invalid JSON response errors from AI service
* ADDED: Smart JSON cleaning and parsing with fallback mechanisms
* ENHANCED: Error handling for AI responses
* IMPROVED: Debug logging for troubleshooting

= 1.3.0 =
* FIXED: Fatal error with wp_create_category() function
* ADDED: Smart category selection from existing WordPress categories
* ENHANCED: Category management workflow
* IMPROVED: AI category selection process

= 1.2.2 =
* ADDED: Extensive debug logging system
* ENHANCED: Error tracking and reporting
* IMPROVED: Fatal error handling with detailed logs

= 1.2.1 =
* FIXED: Activity logs API parameter mismatch
* ENHANCED: Error handling for draft creation
* IMPROVED: Response handling with fallback mechanisms

= 1.2.0 =
* ADDED: Complete WordPress integration for drafts
* ADDED: Categories and tags support
* ADDED: Internal linking implementation
* ADDED: SEO metadata integration

= 1.1.0 =
* UPDATED: Gemini API model to 2.0 Flash
* ENHANCED: WordPress content analysis
* IMPROVED: Style guide generation

= 1.0.0 =
* Initial release
* AI-powered content generation using Google Gemini
* Style guide analysis
* WordPress admin integration
* Activity logging

== Upgrade Notice ==

= 1.3.2 =
Important update: Fixes content formatting issues. All generated content now displays perfectly in WordPress with automatic Markdown to HTML conversion.

= 1.3.1 =
Critical update: Fixes AI response parsing errors. Enhanced error handling and recovery mechanisms.

= 1.3.0 =
Critical update: Fixes fatal category creation errors. Now uses existing WordPress categories intelligently.

== Technical Requirements ==

* WordPress 6.0 or higher
* PHP 8.0 or higher
* Google Gemini API key (free from Google AI Studio)
* Modern web browser for admin interface

== Support ==

For support and documentation:
* Check the plugin's comprehensive documentation
* Review debug logs in WordPress
* Visit our GitHub repository for technical details

== Privacy ==

This plugin sends content to Google Gemini AI for analysis and generation. Please review Google's privacy policy for AI services. No user data is stored by the plugin beyond what's necessary for WordPress functionality.