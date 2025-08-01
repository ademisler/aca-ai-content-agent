# Initial Setup Guide

This comprehensive guide will walk you through the complete setup process for AI Content Agent (ACA), ensuring you get the most out of your content creation powerhouse.

## 📋 Pre-Installation Checklist

Before installing ACA, ensure your system meets these requirements:

### System Requirements
- ✅ **WordPress**: Version 5.0 or higher (6.7+ recommended)
- ✅ **PHP**: Version 7.4 or higher (8.0+ recommended)
- ✅ **Memory**: 256MB or higher recommended
- ✅ **Disk Space**: 50MB free space for plugin files
- ✅ **SSL Certificate**: Required for secure API communications

### Required Accounts
- ✅ **Google Account**: For Gemini AI API access
- ✅ **WordPress Admin Access**: To install and configure the plugin

### Optional but Recommended
- 📊 **Google Search Console**: For advanced SEO insights
- 🖼️ **Image Provider Accounts**: Pexels, Unsplash, or Pixabay for images
- 🔍 **SEO Plugin**: Yoast SEO, RankMath, or All in One SEO

## 🚀 Installation Process

### Method 1: WordPress Admin Dashboard (Recommended)

1. **Download the Plugin**
   - Get the latest version: `ai-content-agent-v2.4.5.zip`
   - Ensure you have the complete zip file (approximately 2-3MB)

2. **Upload and Install**
   ```
   WordPress Admin → Plugins → Add New → Upload Plugin
   ```
   - Click **"Choose File"** and select your zip file
   - Click **"Install Now"**
   - Wait for the installation to complete

3. **Activate the Plugin**
   - Click **"Activate Plugin"** after installation
   - You should see a success message
   - **"AI Content Agent (ACA)"** will appear in your admin menu

### Method 2: FTP Installation

1. **Extract Files**
   - Unzip `ai-content-agent-v2.4.5.zip` on your computer
   - You should see an `ai-content-agent-plugin` folder

2. **Upload via FTP**
   ```
   Upload to: /wp-content/plugins/ai-content-agent-plugin/
   ```
   - Use your preferred FTP client
   - Ensure all files are uploaded correctly

3. **Activate**
   - Go to **WordPress Admin → Plugins**
   - Find **"AI Content Agent (ACA)"** and click **"Activate"**

### Method 3: Developer Installation

For developers who want to work with the source code:

```bash
# Clone the repository
git clone https://github.com/ademisler/aca-ai-content-agent.git
cd ai-content-agent-plugin

# Install dependencies
npm install
composer install --no-dev --optimize-autoloader

# Build assets for production
npm run build:wp

# Copy to WordPress plugins directory
cp -r . /path/to/wordpress/wp-content/plugins/ai-content-agent-plugin/
```

## ⚙️ Initial Configuration

### Step 1: Access Settings

1. Go to **AI Content Agent** in your WordPress admin menu
2. Click on **"Settings"** (or the gear icon)
3. You'll see multiple configuration tabs

### Step 2: Essential API Configuration

#### Gemini AI API Key (Required)

1. **Get Your API Key**
   - Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
   - Sign in with your Google account
   - Click **"Create API Key"**
   - Copy the key (starts with `AIza...`)

2. **Configure in ACA**
   - Navigate to **Settings → Integrations**
   - Paste your API key in **"Gemini AI API Key"**
   - Click **"Test API Key"** to verify
   - Save settings when you see the green checkmark

> ⚠️ **Security Note**: Keep your API key secure. Never share it publicly or commit it to version control.

#### API Usage Limits
- **Free Tier**: 15 requests per minute, 1,500 requests per day
- **Paid Tier**: Higher limits available through Google Cloud
- ACA includes intelligent rate limiting and retry logic

### Step 3: Basic Content Settings

#### Language Configuration
```
Settings → Content & SEO → Language Settings
```
- **Auto-Detection**: ACA automatically detects your WordPress locale
- **Manual Override**: Choose from 50+ supported languages
- **Cultural Context**: AI considers regional writing styles

#### Content Preferences
```
Settings → Content & SEO → Content Preferences
```
- **Default Categories**: Choose categories for generated content
- **Tag Generation**: Enable automatic tag creation
- **Content Length**: Set preferred word count (300-2000 words)
- **Writing Style**: Formal, casual, or professional tone

#### Featured Images
```
Settings → Content & SEO → Featured Images
```
- **Default Source**: Pexels (recommended for beginners)
- **Image Size**: Choose preferred dimensions
- **Alt Text**: Automatic SEO-friendly alt text generation

### Step 4: SEO Integration

#### Automatic SEO Plugin Detection
ACA automatically detects and integrates with:
- **Yoast SEO** (Free & Premium)
- **RankMath** (Free & Pro)
- **All in One SEO** (AIOSEO)

#### Manual SEO Configuration
If auto-detection fails:
1. Go to **Settings → Integrations → SEO Plugins**
2. Select your preferred SEO plugin
3. Configure meta data transfer preferences
4. Test the integration

### Step 5: Automation Settings

#### Choose Your Automation Mode
```
Settings → Automation → Mode Selection
```

**Manual Mode** (Default - Recommended for beginners)
- Full control over every step
- Review all content before publishing
- Perfect for learning the system

**Semi-Automatic Mode**
- AI generates ideas and drafts automatically
- You review and approve publishing
- Balanced approach for regular content creators

**Full-Automatic Mode** (Pro Feature)
- Complete automation from ideas to publishing
- AI handles scheduling and optimization
- Perfect for high-volume content needs

#### Cron Configuration
```
Settings → Automation → Scheduling
```
- **WordPress Cron**: Enabled by default
- **Frequency**: Choose automation intervals
- **Time Zones**: Set your local timezone
- **Backup Options**: Manual fallback if cron fails

## 🔧 Advanced Configuration

### Google Search Console Integration (Optional)

This integration provides real SEO data for better content decisions:

1. **Enable Integration**
   ```
   Settings → Integrations → Google Search Console
   ```

2. **OAuth Authentication**
   - Click **"Connect to Google Search Console"**
   - Sign in with your Google account
   - Grant necessary permissions
   - Select your website property

3. **Configure Data Usage**
   - Choose which metrics to use for content generation
   - Set data refresh intervals
   - Configure privacy settings

### Image Provider APIs (Optional)

For enhanced image selection:

#### Pexels API
1. Get free API key from [Pexels](https://www.pexels.com/api/)
2. Add to **Settings → Integrations → Image Sources**
3. Configure search preferences

#### Unsplash API
1. Register at [Unsplash Developers](https://unsplash.com/developers)
2. Create application and get access key
3. Configure in ACA settings

#### Google Imagen (Pro Feature)
1. Set up Google Cloud account
2. Enable Imagen API
3. Configure authentication in ACA

### Performance Optimization

#### Memory Management
```php
// wp-config.php - Increase memory limit if needed
define('WP_MEMORY_LIMIT', '256M');
```

#### Caching Configuration
- **WordPress Caching**: Compatible with major caching plugins
- **Browser Caching**: Automatic asset optimization
- **API Caching**: Intelligent response caching

#### Database Optimization
- ACA creates optimized custom tables
- Regular cleanup of temporary data
- Efficient indexing for fast queries

## ✅ Verification Checklist

After completing setup, verify everything is working:

### Basic Functionality
- [ ] Plugin activated successfully
- [ ] Admin menu appears correctly
- [ ] Settings pages load without errors
- [ ] Gemini AI API key validated (green checkmark)

### Content Generation
- [ ] Generate test ideas (should produce 5-10 ideas)
- [ ] Create test draft (should generate complete post)
- [ ] Preview generated content
- [ ] Publish test post successfully

### Integrations
- [ ] SEO plugin detected and configured
- [ ] Featured images loading correctly
- [ ] Meta data transferring to SEO plugin
- [ ] Google Search Console connected (if configured)

### Performance
- [ ] Pages load quickly (< 3 seconds)
- [ ] No JavaScript errors in browser console
- [ ] Memory usage within limits
- [ ] Database queries optimized

## 🚨 Troubleshooting Common Setup Issues

### Plugin Activation Fails
**Error**: "Plugin could not be activated"
```
Solutions:
1. Check PHP version (7.4+ required)
2. Increase memory limit to 256MB
3. Ensure proper file permissions
4. Check for plugin conflicts
```

### API Key Validation Fails
**Error**: "Invalid API key" or connection timeout
```
Solutions:
1. Verify API key is copied correctly
2. Check internet connectivity
3. Ensure API key has proper permissions
4. Try regenerating the API key
```

### SEO Plugin Not Detected
**Error**: "No SEO plugin found"
```
Solutions:
1. Ensure SEO plugin is activated
2. Update SEO plugin to latest version
3. Clear any caching
4. Manually select plugin in settings
```

### Memory or Performance Issues
**Error**: "Fatal error: Allowed memory size exhausted"
```
Solutions:
1. Increase PHP memory limit
2. Disable unnecessary plugins temporarily
3. Check for theme conflicts
4. Contact hosting provider if issues persist
```

## 📞 Getting Help

### Self-Help Resources
- **[User Guides](../user-guides/)** - Comprehensive usage guides
- **[Troubleshooting](../troubleshooting/)** - Common issues and solutions
- **[API Reference](../developer/api-reference.md)** - Technical documentation

### Support Channels
- **GitHub Issues**: [Report bugs and request features](https://github.com/ademisler/aca-ai-content-agent/issues)
- **WordPress Forums**: Community support and discussions
- **Pro Support**: Priority email support for Pro license holders

### Before Contacting Support
Please provide:
1. WordPress version
2. PHP version
3. ACA plugin version
4. List of active plugins
5. Error messages (if any)
6. Steps to reproduce the issue

## 🚀 Next Steps

Once setup is complete:

1. **[Generate Your First Content](../user-guides/content-creation.md)**
2. **[Explore Pro Features](../user-guides/pro-features.md)**
3. **[Optimize for SEO](../user-guides/seo-optimization.md)**
4. **[Set Up Automation](automation.md)**

**Congratulations!** You're now ready to revolutionize your content creation workflow with AI Content Agent! 🎉

---

*Last updated: 2025-01-30 | Version: 2.4.5*