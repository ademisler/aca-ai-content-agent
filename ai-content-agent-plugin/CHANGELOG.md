# Changelog

All notable changes to the AI Content Agent WordPress plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.9] - 2025-01-28 - AUTOMATION MODE FIXES + DEBUG SYSTEM

### 🎯 CRITICAL AUTOMATION FIXES
- **Semi-Automatic Mode Fixed**: Fixed WP_REST_Request parameter issue in cron calls
- **Full-Automatic Mode Fixed**: Corrected analyze_style_guide method parameter passing
- **Cron System Verified**: All 3 automation modes now work correctly
- **Error Handling Added**: Comprehensive error logging for all automation operations

### 🔧 AUTOMATION MODE IMPROVEMENTS
- **Manual Mode**: No automatic operations, user has full control ✅
- **Semi-Automatic Mode**: 15-minute cron generates 5 ideas automatically ✅
- **Full-Automatic Mode**: 30-minute cron does full cycle (idea → draft → optional publish) ✅
- **Auto-Publish Option**: Works correctly in full-automatic mode ✅

### 🛠️ DEBUG & MONITORING SYSTEM
- **Debug API Endpoints**: `/debug/automation`, `/debug/cron/semi-auto`, `/debug/cron/full-auto`
- **Automation Status Panel**: Real-time cron schedule and last run information
- **Manual Cron Triggers**: Test automation modes manually from Settings
- **Comprehensive Logging**: Track all automation operations and failures
- **Last Run Tracking**: Monitor when cron jobs last executed

### 🔄 CRON SYSTEM ENHANCEMENTS
- **Proper Request Objects**: Fixed API method calls from cron tasks
- **Schedule Verification**: Cron jobs properly scheduled on plugin activation
- **Time Tracking**: Last run timestamps for monitoring
- **Error Recovery**: Better handling of automation failures

### 📊 MONITORING FEATURES
- **Debug Panel**: Settings page includes automation testing tools
- **Status Information**: Current mode, API key status, style guide existence
- **Cron Schedules**: Next scheduled run times for both cron jobs
- **WordPress Cron Status**: Verify if WP-Cron is enabled
- **Server Time Display**: Current server and GMT times

### 🎨 UI/UX IMPROVEMENTS
- **Debug Interface**: Easy-to-use buttons for testing automation
- **Real-time Feedback**: Instant alerts and console logging
- **Status Indicators**: Clear display of automation system health
- **Error Messages**: User-friendly error reporting

### 🔧 TECHNICAL IMPROVEMENTS
- **Method Visibility**: Proper public/private method declarations
- **Parameter Validation**: Correct parameter passing between classes
- **Exception Handling**: Try-catch blocks for all automation operations
- **Database Options**: Proper storage of automation status and timestamps

## [1.3.8] - 2025-01-28 - CRITICAL STATUS FIX + SMART MULTI-POST UI

### 🎯 CRITICAL FIXES
- **PUBLISHED POSTS NOW VISIBLE**: Fixed backend status mapping from 'publish' to 'published' for frontend consistency
- **Status Filtering Fixed**: Frontend now properly recognizes published posts with correct status
- **All WordPress Content**: Existing published posts now appear in calendar as intended

### 🧠 SMART MULTI-POST UI REDESIGN
- **Expandable Calendar Cells**: Revolutionary expand/collapse system for days with many posts
- **Smart Display Logic**: Shows first 3 posts, then "+X More" button to reveal remaining content
- **Adaptive Layout**: Posts automatically resize based on content density (compact vs normal)
- **No More Lost Content**: Names never disappear, just become expandable

### ✨ ENHANCED USER EXPERIENCE
- **Expand/Collapse Buttons**: Click "+X More" to see all posts, "Show Less" to collapse
- **Visual Hierarchy**: Clear distinction between scheduled (yellow) and published (green) posts
- **Smart Post Count**: Color-coded badges (green ≤3 posts, orange >3 posts)
- **Responsive Sizing**: Items automatically adjust size based on content density

### 🎨 VISUAL IMPROVEMENTS
- **Compact Mode**: Smaller items (18px height, 9px font) when many posts exist
- **Normal Mode**: Larger items (20px height, 10px font) for better readability
- **Smooth Transitions**: All interactions have smooth 0.2s transitions
- **Better Icons**: Adaptive icon sizing (6-9px) based on item size

### 🔧 TECHNICAL ENHANCEMENTS
- **State Management**: Expandable dates tracked with Set-based state
- **Performance**: Efficient rendering with smart visibility calculations
- **Memory Efficient**: Only expanded dates stored in state
- **Event Handling**: Proper event propagation for expand/collapse buttons

### 📱 ACCESSIBILITY IMPROVEMENTS
- **Descriptive Tooltips**: Clear action descriptions for all interactive elements
- **Keyboard Navigation**: Proper focus management for expand/collapse
- **Screen Reader Friendly**: Semantic HTML structure with proper ARIA labels
- **Color Contrast**: High contrast colors for better visibility

Now the calendar handles ANY number of posts per day with an elegant, scalable interface!

## [1.3.7] - 2025-01-28 - Published Posts Fix + Multi-Post UI

### 🎯 CRITICAL FIXES
- **Published Posts Now Visible**: Fixed `get_published_posts` API to include ALL WordPress posts, not just ACA-created ones
- **Meta Key Dependency Removed**: Published posts no longer require `_aca_meta_title` meta key to appear in calendar
- **Existing Content Integration**: All your existing WordPress posts now appear in the calendar

### 🎨 MULTI-POST UI ENHANCEMENTS
- **Scrollable Calendar Cells**: Days with many posts now have scrollable content areas
- **Post Count Indicator**: Orange badge shows total post count when more than 3 posts per day
- **Compact Design**: Smaller post items (22px height) to fit more content
- **Visual Hierarchy**: Posts beyond the 3rd have reduced opacity for better focus
- **Improved Spacing**: Tighter gaps (3px) and better padding for dense content

### 🔧 TECHNICAL IMPROVEMENTS
- **Overflow Handling**: Proper overflow management for calendar cells
- **Performance**: Better handling of large numbers of posts per day
- **Z-Index Management**: Proper layering of date numbers and post count indicators
- **Responsive Scrolling**: Smooth scrolling with hidden scrollbars for clean appearance

### 📱 UX IMPROVEMENTS
- **Better Tooltips**: More descriptive tooltips with fallback titles
- **Icon Sizing**: Optimized icon sizes (10px/8px) for compact layout
- **Flexible Layout**: Icons with `flexShrink: 0` to prevent compression
- **Visual Feedback**: Opacity changes for secondary items

Now ALL your WordPress content appears in the calendar with a clean, scalable interface!

## [1.3.6] - 2025-01-28 - ENHANCED CALENDAR UX

### 🎯 MAJOR IMPROVEMENTS
- **WordPress Editor Integration**: Clicking on scheduled drafts or published posts now opens WordPress editor directly (no more popup)
- **Re-draggable Scheduled Drafts**: Scheduled drafts can now be dragged to different dates for rescheduling
- **Published Posts Display**: Published posts now appear on calendar with green indicators
- **Enhanced Visual Design**: Improved calendar layout with better spacing and modern UI
- **Smart Date Detection**: Better handling of calendar dates and scheduling logic

### ✨ NEW FEATURES
- **Direct WordPress Editing**: Click any calendar item to open in WordPress editor in new tab
- **Drag-to-Reschedule**: Move scheduled drafts between dates by dragging
- **Published Content Timeline**: View published posts alongside scheduled drafts
- **Visual Status Indicators**: 
  - 🟡 Yellow = Scheduled drafts (draggable, clickable)
  - 🟢 Green = Published posts (clickable)
  - 🔵 Blue border = Today's date
- **Improved Instructions**: Better user guidance with visual examples

### 🔧 TECHNICAL ENHANCEMENTS
- **Icon System**: Consistent icon usage with Clock, Edit, and Eye indicators
- **Responsive Grid**: Better calendar grid layout for various screen sizes
- **Admin URL Integration**: Proper WordPress admin URL handling for editor links
- **Enhanced Drag & Drop**: Improved drag-and-drop feedback and visual states

### 🎨 UI/UX IMPROVEMENTS
- **Cleaner Calendar Grid**: Modern grid design with better visual separation
- **Compact Item Display**: Optimized space usage for calendar items
- **Hover States**: Better visual feedback for interactive elements
- **Tooltip Enhancements**: Informative tooltips with action guidance

This version transforms the calendar from a basic scheduling tool into a comprehensive content management interface!

## [1.3.5] - 2025-01-28 - COMPREHENSIVE SCHEDULING FIX

### 🎯 MAJOR FIXES
- **CRITICAL**: Completely rewrote draft scheduling to properly handle WordPress timezone management
- **WordPress Integration**: Fixed improper use of date/time functions causing posts to publish immediately
- **Timezone Handling**: Now properly uses WordPress `current_time()` and `get_gmt_from_date()` functions
- **Date Setting**: Added crucial `edit_date => true` parameter for WordPress to accept date changes on drafts
- **Post Status Logic**: Fixed logic that was incorrectly setting post status based on server timezone instead of WordPress timezone

### 🔧 TECHNICAL IMPROVEMENTS
- **WordPress Best Practices**: Implemented proper WordPress date/time handling as recommended by WordPress Codex
- **Timezone Conversion**: Proper conversion between local time and GMT using WordPress functions
- **Date Validation**: Enhanced date parsing and validation with comprehensive error handling
- **Debug Logging**: Added extensive debug logging to track scheduling process step-by-step
- **Error Handling**: Improved error messages and validation for troubleshooting

### 📝 DETAILED TECHNICAL CHANGES
- Replaced manual date/time calculations with WordPress `current_time('timestamp')`
- Added proper `post_date_gmt` field using `get_gmt_from_date()`
- Implemented `edit_date => true` parameter for draft date updates
- Fixed timezone offset issues by using WordPress timezone functions
- Enhanced timestamp comparison logic for future vs current dates
- Improved post status determination based on proper WordPress time

### 🚨 BREAKING CHANGE RESOLUTION
This version completely resolves the critical issue where:
- Dragging drafts to future dates caused immediate publishing instead of scheduling
- Posts were not properly scheduled due to timezone mishandling
- Calendar dates were not being applied to posts correctly
- Scheduled posts disappeared from calendar after drag-and-drop

### 🧪 TESTING NOTES
After this update, scheduled posts should:
- Properly appear on calendar dates with yellow background
- Show correct scheduling time in WordPress admin (future status)
- Automatically publish at the scheduled time via WordPress cron
- Maintain proper post dates that match the calendar selection

## [1.3.4] - 2025-01-28 - Critical Scheduling Fix

### 🎯 Fixed
- **CRITICAL**: Fixed drag-and-drop scheduling that was publishing posts immediately instead of scheduling
- **Date Detection**: Fixed issue where calendar dates at midnight (00:00:00) were treated as past dates
- **Time Setting**: Calendar scheduling now sets posts to 9:00 AM of the target date to ensure proper scheduling
- **Status Logic**: Improved logic to always use 'future' status for today and future dates

### 🔧 Enhanced
- **Debug Logging**: Added comprehensive debug logging for troubleshooting scheduling issues
- **Date Handling**: Better handling of date-only inputs from calendar drag-and-drop
- **WordPress Integration**: More robust WordPress post status management

### 📝 Technical Details
- Fixed date comparison logic in `schedule_draft()` function
- Added automatic time setting (9:00 AM) for calendar dates without specific times
- Enhanced date validation and processing for calendar drag-and-drop operations
- Added debug logging to track scheduling process step-by-step

### 🚨 Breaking Change Fix
This version fixes a critical issue where dragging drafts to calendar dates would publish them immediately instead of scheduling them for the target date.

## [1.3.3] - 2025-01-28 - Content Calendar Fix

### 🎯 Fixed
- **Draft Scheduling Issues**: Fixed drag-and-drop functionality where drafts would disappear after scheduling
- **WordPress Integration**: Proper WordPress post scheduling with `post_status: 'future'` for scheduled posts
- **API Parameter Mismatch**: Fixed backend expecting `date` parameter while frontend sent `scheduledDate`
- **State Management**: Fixed frontend state not updating properly after scheduling operations
- **Calendar Display**: Scheduled drafts now properly appear on calendar dates and remain visible

### ✅ Added
- **Visual Indicators**: Scheduled drafts now show with yellow background to distinguish from published posts
- **Clickable Scheduled Items**: Users can click on scheduled drafts to view and edit them
- **Enhanced Error Handling**: Better error messages and validation for scheduling operations
- **WordPress Scheduling**: Posts are now properly scheduled in WordPress with correct publication dates

### 🔧 Enhanced
- **User Experience**: Improved drag-and-drop feedback and confirmation messages
- **Calendar Navigation**: Better visual distinction between unscheduled, scheduled, and published content
- **API Response**: Backend now returns complete updated draft object after scheduling
- **Instructions Panel**: Updated help text to reflect new scheduling functionality

### 📝 Technical Details
- Updated `schedule_draft()` function to handle both `date` and `scheduledDate` parameters
- Added proper WordPress post scheduling with `wp_update_post()` and `post_status: 'future'`
- Enhanced `format_post_for_api()` to properly handle 'future' status posts
- Updated `get_drafts()` to include both 'draft' and 'future' status posts
- Improved frontend state management in `handleScheduleDraft()` function
- Added visual styling for scheduled drafts with yellow background (#fff3cd)

## [1.3.2] - 2025-01-XX - Content Formatting Fix

### 🎨 Fixed
- **Markdown Formatting Issues**: AI was generating Markdown syntax (`**bold**`, `*italic*`, `[link](url)`) but WordPress needed HTML
- **Content Display Problems**: Generated content was showing raw Markdown instead of formatted text
- **Link Rendering**: Links were displaying as `[text](url)` instead of clickable links
- **List Formatting**: Bullet points were showing as `* item` instead of proper HTML lists

### ✅ Added
- **Automatic Markdown to HTML Conversion**: Smart converter that transforms Markdown to clean HTML
- **Fallback Markdown Parser**: Handles legacy content and edge cases
- **Enhanced AI Prompts**: Updated prompts to request HTML output directly
- **Content Format Detection**: Automatically detects and converts Markdown when present

### 🔧 Enhanced
- **Content Display**: All generated content now displays perfectly in WordPress
- **AI Instructions**: Improved prompts for cleaner HTML output
- **Error Recovery**: Better handling of mixed format content

### 📝 Technical Details
- Added `markdown_to_html()` function in `class-aca-rest-api.php`
- Enhanced content processing with format detection
- Updated AI prompts to specify HTML format requirements
- Added comprehensive debug logging for content conversion

## [1.3.1] - 2025-01-XX - JSON Response Fix

### 🔧 Fixed
- **Invalid JSON Response**: AI responses sometimes contained syntax errors, trailing commas, or markdown code blocks
- **JSON Parsing Failures**: `json_decode()` errors causing draft creation to fail
- **Response Format Issues**: AI occasionally wrapped JSON in markdown code blocks

### ✅ Added
- **Smart JSON Cleaning**: `clean_ai_json_response()` function that fixes common JSON issues
- **Fallback Parsing**: Attempts to clean and re-parse malformed JSON responses
- **Enhanced Error Logging**: Detailed logging of raw AI responses for debugging
- **Response Validation**: Better validation of AI response structure

### 🔧 Enhanced
- **Error Handling**: More robust JSON parsing with multiple recovery attempts
- **Debug Information**: Comprehensive logging of response processing steps
- **User Experience**: Better error messages when AI responses fail

### 📝 Technical Details
- Added JSON cleaning regex patterns for common issues
- Implemented fallback parsing mechanism
- Enhanced error logging with response content inspection
- Improved AI response validation

## [1.3.0] - 2025-01-XX - Category Management Fix

### 🎯 Fixed
- **Fatal Error**: `Call to undefined function wp_create_category()` - function was deprecated/undefined in newer WordPress versions
- **Category Creation Failures**: Plugin couldn't create new categories, causing draft creation to fail
- **WordPress Compatibility**: Issues with newer WordPress versions that removed legacy functions

### ✅ Added
- **Smart Category Selection**: AI now selects from existing WordPress categories
- **Category Context**: AI receives list of available categories with IDs and names
- **Intelligent Matching**: AI chooses most appropriate existing categories for content
- **Fallback Creation**: Creates new categories only when absolutely necessary using modern WordPress functions

### 🔧 Enhanced
- **Category Management**: More intelligent and WordPress-compatible approach
- **AI Category Selection**: Better prompts for category selection
- **WordPress Integration**: Uses modern WordPress category functions

### 📝 Technical Details
- Replaced `wp_create_category()` with `get_categories()` and smart selection
- Added category context to AI prompts
- Enhanced category validation and error handling
- Improved WordPress compatibility

## [1.2.2] - 2025-01-XX - Comprehensive Debug System

### 🔍 Added
- **Extensive Debug Logging**: Added `ACA DEBUG:` messages throughout the codebase
- **Step-by-Step Tracking**: Detailed logging of draft creation process
- **Error Context**: Enhanced error messages with stack traces and context
- **Fatal Error Handling**: `set_error_handler()` to catch and log PHP fatal errors

### 🔧 Enhanced
- **Error Tracking**: Better identification of error sources
- **Troubleshooting**: Comprehensive logging for easier debugging
- **User Support**: Detailed error information for support requests

### 📝 Technical Details
- Added debug logging to all critical functions
- Implemented custom error handler for fatal errors
- Enhanced error context and stack trace logging

## [1.2.1] - 2025-01-XX - Error Handling Improvements

### ✅ Fixed
- **Activity Logs API**: Parameter mismatch between frontend and backend (`details` vs `message`)
- **Draft Creation Errors**: Enhanced error handling for draft creation process
- **Response Handling**: Improved fallback mechanisms for failed operations

### 🔧 Enhanced
- **Error Recovery**: Better fallback mechanisms for API failures
- **User Experience**: More informative error messages
- **API Consistency**: Aligned parameter names between frontend and backend

## [1.2.0] - 2025-01-XX - WordPress Integration

### ✅ Added
- **Complete WordPress Integration**: Full draft creation with WordPress posts
- **Categories and Tags**: Automatic category and tag assignment
- **Internal Linking**: Contextual internal links to existing posts
- **SEO Metadata**: Meta titles, descriptions, and focus keywords
- **Custom Post Meta**: AI generation metadata and SEO data storage

### 🔧 Enhanced
- **WordPress Compatibility**: Native WordPress post creation
- **Content Structure**: Proper WordPress post formatting
- **SEO Integration**: Built-in SEO optimization

## [1.1.0] - 2025-01-XX - AI Model Update

### ✅ Updated
- **Gemini API Model**: Upgraded to `gemini-2.0-flash` for better performance
- **API Integration**: Updated API calls and headers for new model
- **Content Quality**: Improved content generation with latest model

### 🔧 Enhanced
- **WordPress Content Analysis**: Better analysis of existing content
- **Style Guide Generation**: Improved style guide creation
- **Performance**: Faster and more accurate AI responses

## [1.0.0] - 2025-01-XX - Initial Release

### ✅ Added
- **Plugin Structure**: Initial WordPress plugin architecture
- **React Frontend**: Modern React-based admin interface
- **AI Integration**: Basic Google Gemini AI integration
- **WordPress Admin**: Integration with WordPress admin panel
- **Core Features**: Basic content generation and management

### 📝 Technical Details
- React 18 with TypeScript frontend
- WordPress REST API backend
- Vite build system
- Basic AI content generation

---

## Release Notes

### Version Numbering
- **Major (X.0.0)**: Breaking changes or major feature additions
- **Minor (1.X.0)**: New features, significant improvements
- **Patch (1.0.X)**: Bug fixes, minor improvements

### Support
- **WordPress**: 6.0+
- **PHP**: 8.0+
- **Browsers**: Modern browsers (Chrome, Firefox, Safari, Edge)

### Links
- **Repository**: [GitHub](https://github.com/ademisler/aca-ai-content-agent)
- **Documentation**: [AGENTS.md](AGENTS.md)
- **User Guide**: [README.md](README.md)