# AI Content Agent (ACA) WordPress Plugin

![Version](https://img.shields.io/badge/version-1.3.2-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0+-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![React](https://img.shields.io/badge/React-18+-61DAFB.svg)

**AI Content Agent** is a comprehensive WordPress plugin that automates your content creation workflow using Google Gemini AI. From analyzing your writing style to generating complete blog posts with SEO optimization, ACA handles the entire content creation process.

## ✨ Key Features

### 🤖 AI-Powered Content Creation
- **Style Analysis**: Automatically analyzes your existing content to understand your unique writing style
- **Idea Generation**: Creates content ideas based on your style guide and SEO best practices
- **Full Draft Creation**: Generates complete blog posts (800-1500 words) with proper HTML structure
- **SEO Optimization**: Automatic meta titles, descriptions, and focus keywords
- **Content Formatting**: Smart Markdown to HTML conversion for perfect WordPress display

### 📝 WordPress Integration
- **Native WordPress Posts**: Creates real WordPress draft posts with full metadata
- **Smart Categories**: AI selects from existing categories or creates new ones intelligently
- **Automatic Tags**: Generates and assigns relevant tags based on content
- **Internal Linking**: Contextually adds internal links to your existing content
- **Featured Images**: Supports AI-generated or stock photo integration
- **Custom Meta Fields**: Stores SEO data and AI generation metadata
- **Perfect Formatting**: Converts Markdown formatting to clean HTML automatically

### 🎯 Smart Workflow Management
- **Dashboard Overview**: Real-time metrics and activity monitoring
- **Idea Board**: Visual management of content ideas with AI assistance
- **Draft Management**: Edit and schedule your AI-generated content
- **Activity Logging**: Track all AI operations and content creation activities
- **Bulk Operations**: Generate multiple ideas and drafts efficiently
- **Error Recovery**: Advanced error handling with detailed logging

### 🔧 Advanced Configuration
- **Google Gemini 2.0 Flash**: Latest AI model for superior content quality
- **Style Guide Management**: Customizable writing style parameters
- **Multi-Source Images**: AI generation, Pexels, Unsplash, or Pixabay integration
- **Flexible Settings**: Automated or manual content creation workflows
- **Debug Mode**: Comprehensive logging for troubleshooting

## 🚀 Quick Start

### Installation

1. **Download** the latest `ai-content-agent-v1.3.2-markdown-fix.zip` file
2. **Upload** to WordPress Admin → Plugins → Add New → Upload Plugin
3. **Activate** the plugin
4. **Configure** your Google Gemini API key in Settings

### Initial Setup

1. **Get Gemini API Key**
   - Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Create a new API key
   - Copy the key to plugin settings

2. **Analyze Your Content**
   - Go to Style Guide section
   - Click "Analyze Content"
   - Let AI understand your writing style

3. **Start Creating**
   - Generate ideas in the Idea Board
   - Create drafts with one click
   - Edit and publish in WordPress

## 📋 Requirements

- **WordPress**: 6.0 or higher
- **PHP**: 8.0 or higher
- **Google Gemini API Key**: Required for AI functionality
- **Modern Browser**: Chrome, Firefox, Safari, Edge (latest versions)

## 🏗️ Technical Architecture

### Frontend
- **React 18** with TypeScript
- **Vite** build system for optimal performance
- **WordPress Admin** integration
- **Real-time UI** updates and notifications

### Backend
- **WordPress REST API** for all data operations
- **Custom Database Tables** for plugin data
- **Google Gemini 2.0 Flash** integration
- **Comprehensive Error Handling** and logging
- **Smart Content Formatting** with Markdown to HTML conversion

### AI Integration
- **Model**: Google Gemini 2.0 Flash
- **Content Analysis**: Style guide generation from existing posts
- **Content Generation**: Full blog posts with SEO optimization and proper HTML formatting
- **Smart Category Management**: Uses existing categories intelligently
- **Advanced Error Recovery**: JSON parsing with fallback mechanisms

## 🔧 Development

### Build from Source

```bash
# Clone the repository
git clone https://github.com/ademisler/aca-ai-content-agent

# Install dependencies
cd ai-content-agent-plugin
npm install

# Development server
npm run dev

# Production build
npm run build

# Package for WordPress
zip -r ai-content-agent.zip ai-content-agent-plugin/ -x "*/node_modules/*" "*/.git/*" "*/dist/*"
```

### Development Environment
- **Node.js**: 18+ required
- **npm**: Package management
- **TypeScript**: Type safety
- **Vite**: Fast build and hot reload

## 📚 Documentation

- **[AGENTS.md](AGENTS.md)** - Comprehensive technical documentation for developers and AI agents
- **[Troubleshooting Guide](#-troubleshooting)** - Common issues and solutions

## 🔍 Troubleshooting

### Common Issues

**Plugin Not Loading**
- Check WordPress and PHP version requirements
- Verify plugin activation in WordPress admin
- Check browser console for JavaScript errors

**AI Features Not Working**
- Verify Gemini API key is correctly configured
- Check WordPress error logs for API issues
- Ensure internet connectivity for AI requests

**Content Not Generating**
- Run style analysis first to create style guide
- Check if ideas exist before creating drafts
- Review activity logs for detailed error information

**Content Formatting Issues**
- Plugin automatically converts Markdown to HTML
- Check browser console for formatting errors
- Verify WordPress post content in admin

**Category/Tag Issues**
- Plugin uses existing categories when possible
- New categories are created only when necessary
- Check WordPress Categories admin for verification

### Debug Mode

Enable WordPress debug mode for detailed error logging:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check logs at: `/wp-content/debug.log`

### Recent Fixes (v1.3.x)

**v1.3.2 - Content Formatting Fix**
- Fixed Markdown formatting issues in generated content
- Added automatic Markdown to HTML conversion
- Improved content display in WordPress

**v1.3.1 - JSON Response Fix**
- Enhanced AI response parsing with fallback mechanisms
- Fixed JSON syntax errors from AI responses
- Added comprehensive error logging

**v1.3.0 - Category Management Fix**
- Fixed wp_create_category() fatal error
- Implemented smart category selection from existing categories
- Enhanced category management workflow

## 🤝 Contributing

We welcome contributions! Please read our contributing guidelines and submit pull requests for any improvements.

### Development Workflow
1. Fork the repository
2. Create feature branch
3. Make changes and test thoroughly
4. Submit pull request with detailed description

## 📄 License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

- **Documentation**: Check [AGENTS.md](AGENTS.md) for technical details
- **Issues**: Report bugs via GitHub Issues
- **Community**: Join our WordPress community discussions

## 🔄 Changelog

### Version 1.3.2 (Current) - Content Formatting Fix
- 🎨 **FIXED**: Markdown formatting in generated content
- ✅ **Added**: Automatic Markdown to HTML conversion
- ✅ **Enhanced**: Content display in WordPress
- ✅ **Improved**: AI prompt instructions for HTML output
- ✅ **Added**: Fallback Markdown parser for legacy content

### Version 1.3.1 - JSON Response Fix
- 🔧 **FIXED**: Invalid JSON response from AI service
- ✅ **Added**: Smart JSON cleaning and parsing
- ✅ **Enhanced**: Error handling for AI responses
- ✅ **Improved**: Debug logging for troubleshooting

### Version 1.3.0 - Category Management Fix
- 🎯 **FIXED**: Fatal error with wp_create_category() function
- ✅ **Added**: Smart category selection from existing categories
- ✅ **Enhanced**: Category management workflow
- ✅ **Improved**: AI category selection process

### Version 1.2.2 - Comprehensive Debug System
- 🔍 **Added**: Extensive debug logging system
- ✅ **Enhanced**: Error tracking and reporting
- ✅ **Improved**: Fatal error handling

### Version 1.2.1 - Error Handling Improvements
- ✅ **Fixed**: Activity logs API parameter mismatch
- ✅ **Enhanced**: Error handling for draft creation
- ✅ **Improved**: Response handling with fallback mechanisms

### Version 1.2.0 - WordPress Integration
- ✅ **Added**: Complete WordPress integration for drafts
- ✅ **Added**: Categories and tags support
- ✅ **Added**: Internal linking implementation
- ✅ **Added**: SEO metadata integration

### Version 1.1.0 - AI Model Update
- ✅ **Updated**: Gemini API model to 2.0 Flash
- ✅ **Enhanced**: WordPress content analysis
- ✅ **Improved**: Style guide generation

### Version 1.0.0 - Initial Release
- ✅ **Initial**: Plugin structure and core functionality
- ✅ **Added**: React frontend implementation
- ✅ **Added**: Basic AI integration
- ✅ **Added**: WordPress admin integration

## 🌟 Features in Detail

### Style Analysis
The plugin analyzes your last 20 published posts to understand:
- **Tone**: Friendly, professional, technical, or humorous
- **Structure**: Sentence patterns and paragraph organization
- **Formatting**: Use of headings, lists, and emphasis
- **Voice**: Unique characteristics of your writing

### Content Generation
AI creates comprehensive blog posts including:
- **Structured Content**: H2/H3 headings, proper paragraphs in HTML format
- **SEO Elements**: Meta title, description, focus keywords
- **Internal Links**: Contextual links to your existing content
- **Categories & Tags**: Smart selection from existing or creation of new ones
- **Word Count**: 800-1500 words optimized for engagement
- **Perfect Formatting**: Clean HTML output for WordPress

### WordPress Integration
Seamlessly integrates with WordPress:
- **Native Posts**: Creates actual WordPress draft posts
- **Media Library**: Manages featured images
- **Custom Fields**: Stores AI metadata and SEO data
- **User Permissions**: Respects WordPress user capabilities
- **Admin Interface**: Clean, intuitive admin experience
- **Category Management**: Smart use of existing categories
- **Content Formatting**: Perfect HTML output

### Error Recovery & Debugging
- **Comprehensive Logging**: Detailed error tracking
- **Fallback Mechanisms**: Multiple recovery strategies
- **User-Friendly Messages**: Clear error communication
- **Debug Mode**: Extensive troubleshooting information

---

**Made with ❤️ for the WordPress community**

*Transform your content creation workflow with the power of AI*
