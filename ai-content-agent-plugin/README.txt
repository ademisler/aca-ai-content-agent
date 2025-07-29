=== AI Content Agent (ACA) ===
Contributors: aicontentagent
Tags: ai, content, automation, calendar, scheduling, gemini, chatgpt, seo, blog
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.3.8
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AI-powered content creation and management with intelligent Content Calendar. Generate, schedule, and manage your WordPress content automatically.

== Description ==

**AI Content Agent (ACA)** transforms your WordPress content workflow with intelligent automation and a revolutionary Content Calendar system. Create, schedule, and manage all your content from one beautiful interface.

= 🚀 Latest Updates - v1.3.8 =

**CRITICAL FIXES:**
* **Published Posts Now Visible**: Fixed backend status mapping - all existing WordPress posts appear in calendar
* **Complete WordPress Integration**: Full compatibility with existing content
* **Status Filtering Fixed**: Resolved frontend filtering preventing published content display

**SMART MULTI-POST UI:**
* **Expandable Calendar Cells**: Revolutionary design handles unlimited posts per day
* **Intelligent Space Management**: Auto-expands with smooth animations
* **Post Count Indicators**: Smart badges show total posts with visual hierarchy
* **Enhanced UX**: Direct WordPress editor navigation, full drag & drop rescheduling

= 🎯 Key Features =

**📅 Intelligent Content Calendar**
* Smart multi-post management with expandable cells
* Visual post types: Scheduled (🟡), Published (🟢), Today (🔵)
* Drag & drop scheduling and rescheduling
* Direct WordPress editor integration
* Real-time updates with notifications

**🤖 AI-Powered Content Creation**
* Multi-AI support: Gemini, ChatGPT, Claude
* Smart content generation with SEO optimization
* Style guide management for consistent brand voice
* Automated workflow from idea to publication

**📊 Advanced Management**
* Comprehensive dashboard with real-time statistics
* Activity logging and detailed tracking
* Draft lifecycle management
* Published content overview with analytics

**⚙️ Professional Features**
* Stock photo integration and optimization
* Built-in SEO analysis and recommendations
* Bulk operations and mass content creation
* Advanced WordPress cron-based scheduling

= 📖 How to Use =

**Content Calendar Workflow:**
1. View all content in the visual calendar
2. Schedule content by dragging drafts to dates
3. Reschedule by dragging existing posts
4. Click any post to edit in WordPress
5. Track progress with visual indicators

**AI Content Creation:**
1. Generate ideas with AI assistance
2. Convert ideas to full blog posts
3. Apply SEO recommendations and style guide
4. Schedule strategically using the calendar

= 🎨 Smart Calendar Design =

* **Expandable Cells**: Days with multiple posts show compact view initially
* **Click to Expand**: Reveal all posts with smooth animation
* **Visual Hierarchy**: Different post types have distinct styling
* **Responsive Layout**: Optimized for all screen sizes
* **Direct Navigation**: Click posts to edit in WordPress

= 🛠️ Technical Requirements =

* WordPress 5.0 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher
* 128MB memory minimum (256MB recommended)
* Modern browser (Chrome, Firefox, Safari, Edge)

= 🔧 Configuration =

**Required Settings:**
* AI Service selection (Gemini recommended)
* API key configuration
* Content settings (categories, tags, formats)

**Optional Enhancements:**
* Stock photo integration
* SEO settings and meta optimization
* Automated scheduling preferences

== Installation ==

= Method 1: WordPress Admin (Recommended) =

1. Download `ai-content-agent-v1.3.8-optimized.zip` (161KB)
2. Go to WordPress Admin → Plugins → Add New → Upload Plugin
3. Choose the downloaded zip file
4. Click Install Now → Activate Plugin
5. Configure settings in AI Content Agent menu

= Method 2: Manual Installation =

1. Extract zip file to `/wp-content/plugins/` directory
2. Activate plugin through WordPress admin
3. Configure your AI API keys in settings

= Method 3: WordPress Repository =

1. Go to Plugins → Add New
2. Search for "AI Content Agent"
3. Install and activate
4. Configure settings

== Frequently Asked Questions ==

= What AI services are supported? =

ACA supports multiple AI providers including Google Gemini, ChatGPT, and Claude. Gemini is recommended for optimal performance and cost-effectiveness.

= Do I need coding knowledge? =

No! ACA is designed for non-technical users. The intuitive interface handles all technical aspects automatically.

= Will it work with my existing content? =

Yes! ACA integrates seamlessly with your existing WordPress posts. All published content appears in the Content Calendar automatically.

= Can I schedule content for future dates? =

Absolutely! Use the drag-and-drop calendar to schedule drafts for any future date. The plugin handles WordPress scheduling automatically.

= Is my content data secure? =

Yes. Content is processed through secure AI APIs and stored in your WordPress database. No content is stored on external servers.

= Can I customize the AI writing style? =

Yes! ACA includes a Style Guide feature that analyzes your existing content to maintain consistent brand voice across all AI-generated posts.

== Screenshots ==

1. **Smart Content Calendar** - Revolutionary expandable design handles multiple posts per day
2. **AI Content Generation** - Create full blog posts with SEO optimization
3. **Dashboard Overview** - Real-time statistics and content management
4. **Drag & Drop Scheduling** - Intuitive content scheduling interface
5. **Style Guide Management** - Consistent brand voice across all content
6. **Settings Configuration** - Easy setup and customization options

== Changelog ==

= 1.3.8 - 2025-01-28 - CRITICAL STATUS FIX + SMART MULTI-POST UI =

**CRITICAL FIXES:**
* PUBLISHED POSTS NOW VISIBLE: Fixed backend status mapping from 'publish' to 'published'
* Status Filtering Fixed: Frontend properly recognizes published posts
* All WordPress Content: Existing published posts appear in calendar

**SMART MULTI-POST UI REDESIGN:**
* Expandable Calendar Cells: Revolutionary expandable/collapsible design
* Intelligent Space Management: Auto-expands based on content density
* Visual Hierarchy: Clear indicators for different post types
* Post Count Badges: Smart badges show total posts per day
* Hover Effects: Enhanced UX with smooth transitions
* Compact Mode: Efficient space usage with smart truncation
* Direct Navigation: Click posts to edit in WordPress
* Drag & Drop: Full rescheduling support for all post types

**UI/UX ENHANCEMENTS:**
* Smooth Animations: 0.2s transitions for expand/collapse
* Typography Improvements: Better readability and hierarchy
* Color Coding: Intuitive visual indicators for post status
* Responsive Design: Perfect on all screen sizes
* Performance Optimized: Faster loading and smoother interactions

= 1.3.7 - 2025-01-28 - Published Posts Fix + Multi-Post UI =

**CRITICAL FIXES:**
* Published Posts Now Visible: Fixed get_published_posts API
* Meta Key Dependency Removed: No longer requires _aca_meta_title
* Existing Content Integration: All WordPress posts appear in calendar

**MULTI-POST UI ENHANCEMENTS:**
* Scrollable Calendar Cells: Days with many posts have scrollable areas
* Post Count Indicator: Orange badge shows total when >3 posts per day
* Compact Design: Smaller items to fit more content
* Visual Hierarchy: Posts beyond 3rd have reduced opacity
* Improved Spacing: Tighter gaps and optimized padding

= 1.3.6 - 2025-01-28 - Enhanced Calendar UX =

**UX IMPROVEMENTS:**
* Direct WordPress Editor: Click posts to edit directly in WordPress
* Re-draggable Drafts: Scheduled drafts can be rescheduled by dragging
* Published Posts Display: All published posts visible on calendar
* Visual Enhancements: Better icons, colors, and layout
* Icon System: Consistent custom icons throughout interface

**TECHNICAL IMPROVEMENTS:**
* WordPress Admin URL: Proper admin URL handling for editor links
* Drag & Drop: Enhanced drag and drop for all post types
* State Management: Improved React state handling
* Build Process: Optimized compilation and asset management

= 1.3.5 - 2025-01-28 - Comprehensive Scheduling Fix =

**CRITICAL SCHEDULING FIX:**
* WordPress Date/Time Compatibility: Complete rewrite of scheduling system
* Timezone Handling: Proper WordPress timezone management
* GMT Conversion: Correct local to GMT time conversion
* Future Post Status: Proper 'future' status for scheduled posts
* Edit Date Parameter: Added edit_date => true for draft modifications

**TECHNICAL IMPROVEMENTS:**
* DateTime Objects: Robust date parsing and manipulation
* WordPress Functions: Using current_time() and get_gmt_from_date()
* Debug Logging: Comprehensive scheduling debug information
* Error Handling: Better error recovery and user feedback

= 1.3.4 - 2025-01-28 - Critical Scheduling Fix =

**SCHEDULING IMPROVEMENTS:**
* Calendar Time Fix: Set default 9:00 AM time for calendar drops
* Future Time Logic: Ensure scheduled posts are always in future
* Debug Logging: Added detailed scheduling debug information
* WordPress Integration: Better WordPress scheduling compatibility

= 1.3.3 - 2025-01-28 - Content Calendar Fix =

**CALENDAR FIXES:**
* Drag-and-Drop: Fixed drafts disappearing after scheduling
* WordPress Scheduling: Proper post_status 'future' implementation
* Visual Indicators: Yellow background for scheduled drafts
* API Parameters: Enhanced parameter handling and state management
* Clickable Items: Scheduled drafts now clickable for editing

== Upgrade Notice ==

= 1.3.8 =
CRITICAL UPDATE: Fixes published posts visibility and introduces revolutionary smart multi-post calendar UI. All existing WordPress content now appears in calendar. Highly recommended upgrade.

= 1.3.7 =
Important fix for published posts visibility. Adds multi-post UI improvements for better content management.

= 1.3.6 =
Major UX improvements with direct WordPress editor integration and re-draggable scheduled content.

= 1.3.5 =
Critical scheduling fix with complete WordPress date/time compatibility rewrite. Essential for proper content scheduling.

== Support ==

For support, documentation, and feature requests, please visit our plugin settings page which includes comprehensive help sections and troubleshooting guides.

**Transform your WordPress content workflow with intelligent automation!**