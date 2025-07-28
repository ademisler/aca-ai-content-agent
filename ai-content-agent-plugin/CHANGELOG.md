# Changelog

All notable changes to the AI Content Agent WordPress plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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