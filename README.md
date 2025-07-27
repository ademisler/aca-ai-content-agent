# ACA - AI Content Agent for WordPress

An intelligent content generation and management assistant for WordPress. ACA analyzes your existing content to learn your site's unique voice and style, then uses that knowledge to generate high-quality, SEO-friendly blog posts, automating content creation while maintaining brand identity.

## Features

### 🤖 AI-Powered Content Generation
- **Style Guide Analysis**: Automatically analyzes your existing posts to learn your writing style
- **Smart Content Ideas**: Generates relevant blog post ideas based on your style and niche
- **Full Draft Creation**: Creates complete blog posts with proper structure, SEO metadata, and internal links
- **Powered by Google Gemini**: Uses advanced AI for natural, human-like content generation

### 📊 Content Management Dashboard
- **Intuitive Interface**: Modern, user-friendly dashboard with real-time statistics
- **Ideas Management**: Organize and track content ideas from multiple sources
- **Draft Editor**: Built-in editor for reviewing and refining AI-generated content
- **Activity Tracking**: Monitor all content generation activities

### ⚙️ Automation Options
- **Manual Mode**: Full control over when to generate ideas and create drafts
- **Semi-Automatic**: Auto-generate ideas with manual review and publishing
- **Full-Automatic**: Complete automation with scheduled content creation

### 🔍 SEO Integration
- **Meta Optimization**: Automatically generates SEO titles and descriptions
- **Focus Keywords**: AI-generated relevant keywords for each post
- **Internal Linking**: Smart internal link suggestions based on existing content
- **SEO Plugin Support**: Compatible with Yoast SEO and Rank Math

### 🖼️ Image Integration
- **AI Image Generation**: Create featured images using Google Imagen (when available)
- **Stock Photo APIs**: Integration with Pexels, Unsplash, and Pixabay
- **Flexible Options**: Choose between AI-generated or stock photos

## Installation

1. **Download** the plugin files to your WordPress plugins directory
2. **Activate** the plugin through the WordPress admin panel
3. **Navigate** to "AI Content Agent" in your WordPress admin menu
4. **Configure** your Gemini API key in the settings

## Setup Guide

### 1. Get Your Gemini API Key
1. Visit [Google AI Studio](https://ai.google.dev/)
2. Create a new project or use an existing one
3. Generate an API key for Gemini
4. Copy the API key for use in the plugin settings

### 2. Initial Configuration
1. Go to **AI Content Agent > Settings**
2. Enter your **Gemini API Key**
3. Choose your **automation mode**
4. Configure **image settings** (optional)
5. Select your **SEO plugin** integration
6. Save settings

### 3. Analyze Your Style Guide
1. Go to **AI Content Agent > Dashboard**
2. Click **"Analyze Style Guide"**
3. The AI will analyze your recent posts to learn your writing style
4. Review the generated style guide in Settings

### 4. Start Creating Content
1. **Generate Ideas**: Click "Generate Ideas" to create new content topics
2. **Create Drafts**: Select ideas and convert them to full drafts
3. **Review & Edit**: Use the built-in editor to refine content
4. **Publish**: Publish directly or save as WordPress drafts

## Usage

### Dashboard
The main dashboard provides:
- **Statistics**: Overview of ideas, drafts, and published posts
- **Quick Actions**: One-click access to common tasks
- **Activity Log**: Recent plugin activities
- **Style Guide Status**: Current writing style analysis

### Ideas Management
- **AI Generation**: Create multiple ideas based on your style guide
- **Manual Entry**: Add your own content ideas
- **Organization**: Archive or delete ideas as needed
- **Bulk Actions**: Convert multiple ideas to drafts

### Draft Management
- **AI-Generated Content**: Full blog posts with proper structure
- **SEO Metadata**: Automatically generated titles and descriptions
- **Internal Links**: Smart linking to existing content
- **Edit & Refine**: Built-in editor for content customization
- **One-Click Publishing**: Direct publishing to WordPress

### Settings Configuration

#### Automation Modes
- **Manual**: Complete control over content generation
- **Semi-Automatic**: Scheduled idea generation with manual approval
- **Full-Automatic**: Complete automation (use with caution)

#### Image Settings
- **AI Generated**: Use Google Imagen for custom images
- **Stock Photos**: Integration with major stock photo APIs
- **Style Options**: Choose between photorealistic or digital art

#### SEO Integration
- **Plugin Support**: Works with popular SEO plugins
- **Metadata Generation**: Automatic SEO titles and descriptions
- **Keyword Research**: AI-generated focus keywords

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **Gemini API Key**: Required for AI functionality
- **Internet Connection**: Required for API communications

## API Keys Setup

### Gemini API (Required)
1. Visit [Google AI Studio](https://ai.google.dev/)
2. Create an account and project
3. Generate an API key
4. Add key to plugin settings

### Stock Photo APIs (Optional)
- **Pexels**: [Get API Key](https://www.pexels.com/api/)
- **Unsplash**: [Get API Key](https://unsplash.com/developers)
- **Pixabay**: [Get API Key](https://pixabay.com/api/docs/)

## Frequently Asked Questions

### How accurate is the style analysis?
The AI analyzes your 20 most recent posts to understand your writing patterns, tone, and structure. The more content you have, the more accurate the analysis becomes.

### Can I edit AI-generated content?
Yes! All generated content can be fully edited using the built-in editor before publishing. You have complete control over the final output.

### What happens if I don't have existing posts?
The plugin will use a default professional style guide and adapt as you publish more content. You can also add custom instructions to guide the AI.

### Is my content data secure?
Content is only sent to Google's Gemini API for processing. No data is stored by third parties beyond what's necessary for content generation.

### Can I use this with any WordPress theme?
Yes! The plugin works with any WordPress theme and integrates seamlessly with the standard WordPress posting system.

## Troubleshooting

### Common Issues

**API Key Not Working**
- Verify the key is correct and active
- Check that billing is enabled for your Google Cloud project
- Ensure the Gemini API is enabled

**Style Guide Not Generating**
- Make sure you have published posts on your site
- Check that your API key has sufficient credits
- Try regenerating after publishing more content

**Drafts Not Creating**
- Verify your style guide is set up
- Check API key permissions
- Look for error messages in the activity log

**Plugin Not Loading**
- Ensure WordPress and PHP meet minimum requirements
- Check for plugin conflicts
- Verify file permissions are correct

### Getting Help
1. Check the **Activity Log** for error messages
2. Verify your **API credentials** are correct
3. Review the **plugin settings** for configuration issues
4. Check WordPress **error logs** for technical issues

## Changelog

### Version 1.0.0
- Initial release
- Gemini AI integration
- Style guide analysis
- Content idea generation
- Draft creation and management
- SEO optimization features
- Multi-mode automation
- Stock photo integration

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support, feature requests, or bug reports, please contact the plugin developer or check the plugin documentation.

---

**Note**: This plugin requires a Google Gemini API key to function. API usage may incur costs based on Google's pricing structure. Please review Google's terms of service and pricing before use.
