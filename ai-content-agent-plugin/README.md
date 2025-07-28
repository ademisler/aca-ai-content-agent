# AI Content Agent (ACA) WordPress Plugin

![Version](https://img.shields.io/badge/version-1.0.9-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-6.0+-green.svg)
![PHP](https://img.shields.io/badge/PHP-8.0+-purple.svg)
![React](https://img.shields.io/badge/React-18+-61DAFB.svg)

**AI Content Agent** is a comprehensive WordPress plugin that automates your content creation workflow using Google Gemini AI. From analyzing your writing style to generating complete blog posts with SEO optimization, ACA handles the entire content creation process.

## ✨ Key Features

### 🤖 AI-Powered Content Creation
- **Style Analysis**: Automatically analyzes your existing content to understand your unique writing style
- **Idea Generation**: Creates content ideas based on your style guide and SEO best practices
- **Full Draft Creation**: Generates complete blog posts (800-1500 words) with proper structure
- **SEO Optimization**: Automatic meta titles, descriptions, and focus keywords

### 📝 WordPress Integration
- **Native WordPress Posts**: Creates real WordPress draft posts
- **Categories & Tags**: Automatically generates and assigns relevant categories and tags
- **Internal Linking**: Contextually adds internal links to your existing content
- **Featured Images**: Supports AI-generated or stock photo integration
- **Custom Meta Fields**: Stores SEO data and AI generation metadata

### 🎯 Smart Workflow Management
- **Dashboard Overview**: Real-time metrics and activity monitoring
- **Idea Board**: Visual management of content ideas with AI assistance
- **Draft Management**: Edit and schedule your AI-generated content
- **Activity Logging**: Track all AI operations and content creation activities
- **Bulk Operations**: Generate multiple ideas and drafts efficiently

### 🔧 Advanced Configuration
- **Google Gemini 2.0 Flash**: Latest AI model for superior content quality
- **Style Guide Management**: Customizable writing style parameters
- **Multi-Source Images**: AI generation, Pexels, Unsplash, or Pixabay integration
- **Flexible Settings**: Automated or manual content creation workflows

## 🚀 Quick Start

### Installation

1. **Download** the latest `ai-content-agent.zip` file
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
- **Google Gemini AI** integration
- **Comprehensive Error Handling** and logging

### AI Integration
- **Model**: Google Gemini 2.0 Flash
- **Content Analysis**: Style guide generation from existing posts
- **Content Generation**: Full blog posts with SEO optimization
- **Smart Linking**: Contextual internal link placement

## 🔧 Development

### Build from Source

```bash
# Clone the repository
git clone https://github.com/your-repo/ai-content-agent

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
- **[API Documentation](#)** - REST API endpoint reference
- **[Troubleshooting Guide](#)** - Common issues and solutions

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

### Debug Mode

Enable WordPress debug mode for detailed error logging:

```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check logs at: `/wp-content/debug.log`

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

### Version 1.0.9 (Current)
- ✅ Fixed activity logs API parameter mismatch
- ✅ Enhanced error handling for draft creation
- ✅ Improved response handling with fallback mechanisms
- ✅ Added comprehensive debug logging

### Version 1.0.8
- ✅ Complete WordPress integration for drafts
- ✅ Added categories and tags support
- ✅ Internal linking implementation
- ✅ SEO metadata integration

### Version 1.0.7
- ✅ Activity logs endpoint fixes
- ✅ Better error handling

### Version 1.0.6
- ✅ Gemini API model updates to 2.0 Flash
- ✅ WordPress content analysis improvements
- ✅ Style guide generation enhancements

[View Full Changelog](CHANGELOG.md)

## 🌟 Features in Detail

### Style Analysis
The plugin analyzes your last 20 published posts to understand:
- **Tone**: Friendly, professional, technical, or humorous
- **Structure**: Sentence patterns and paragraph organization
- **Formatting**: Use of headings, lists, and emphasis
- **Voice**: Unique characteristics of your writing

### Content Generation
AI creates comprehensive blog posts including:
- **Structured Content**: H2/H3 headings, proper paragraphs
- **SEO Elements**: Meta title, description, focus keywords
- **Internal Links**: Contextual links to your existing content
- **Categories & Tags**: Relevant taxonomies for organization
- **Word Count**: 800-1500 words optimized for engagement

### WordPress Integration
Seamlessly integrates with WordPress:
- **Native Posts**: Creates actual WordPress draft posts
- **Media Library**: Manages featured images
- **Custom Fields**: Stores AI metadata and SEO data
- **User Permissions**: Respects WordPress user capabilities
- **Admin Interface**: Clean, intuitive admin experience

---

**Made with ❤️ for the WordPress community**

*Transform your content creation workflow with the power of AI*
