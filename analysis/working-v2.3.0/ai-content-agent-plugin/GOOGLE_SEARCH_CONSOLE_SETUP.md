# Google Search Console Integration Setup Guide - v2.0.5

This guide will walk you through setting up Google Search Console integration with the AI Content Agent (ACA) plugin to access real search performance data for your content creation.

## 🚨 Latest Updates - v2.0.3

**COMPREHENSIVE FEATURE ENHANCEMENTS & IMPROVEMENTS**
- **Author Updates**: Updated plugin author to Adem Isler with website integration (ademisler.com/en)
- Enhanced error handling for GSC API calls with intelligent retry logic
- Improved user feedback when GSC integration encounters issues
- Better error messages and recovery instructions for GSC connection problems
- Enhanced stability when using GSC data for AI content generation
- Automatic retry mechanism for GSC API timeouts and temporary failures
- **Feature Verification**: Confirmed all GSC integration features are working correctly

## 🎯 Overview

The Google Search Console integration allows the AI Content Agent (ACA) to:
- Access real search query data from your website
- Identify top-performing keywords
- Find underperforming pages that need optimization
- Generate content ideas based on actual search data with enhanced reliability
- Improve SEO-focused content creation with robust error handling

## 📋 Prerequisites

- WordPress admin access
- Google account with access to Google Search Console
- Your website verified in Google Search Console
- Basic understanding of Google Cloud Console
- **OPTIONAL**: Composer (plugin includes placeholder dependencies)

## 🚀 Step-by-Step Setup

### Step 1: Dependencies (Optional - Plugin Includes Placeholders)

**ℹ️ NOTE**: The plugin now includes placeholder dependencies for basic functionality. Full Google API client installation is optional but recommended for enhanced features.

**Option A: Use Plugin Placeholders (Recommended for Basic Use)**
- Plugin includes built-in placeholder dependencies
- Basic GSC integration works without additional setup
- No server-side installation required

**Option B: Full Google API Client (Advanced Features)**
1. Navigate to your plugin directory:
   ```bash
   cd /path/to/wordpress/wp-content/plugins/ai-content-agent-plugin/
   ```

2. Install dependencies using Composer:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. Verify installation:
   ```bash
   ls -la vendor/google/
   ```
   You should see `apiclient` and other Google libraries.

### Step 2: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing one
3. Name it something like "AI Content Agent (ACA) GSC"

### Step 3: Enable Google Search Console API

1. In Google Cloud Console, go to **APIs & Services > Library**
2. Search for "Google Search Console API"
3. Click on it and press **Enable**

### Step 4: Create OAuth2 Credentials

1. Go to **APIs & Services > Credentials**
2. Click **Create Credentials > OAuth 2.0 Client IDs**
3. Configure the OAuth consent screen if prompted:
   - User Type: External (for most cases)
   - App name: "AI Content Agent (ACA)"
   - User support email: Your email
   - Developer contact: Your email
4. Create OAuth 2.0 Client ID:
   - Application type: **Web application**
   - Name: "AI Content Agent (ACA) GSC"
   - Authorized redirect URIs: Add your WordPress admin URL:
     ```
     https://yourdomain.com/wp-admin/admin.php?page=ai-content-agent&gsc_auth=callback
     ```
     Replace `yourdomain.com` with your actual domain

### Step 5: Configure Plugin Settings

1. In WordPress admin, go to **AI Content Agent (ACA) > Settings**
2. Scroll to the **Google Search Console** section
3. Enter your OAuth2 credentials:
   - **Client ID**: Copy from Google Cloud Console
   - **Client Secret**: Copy from Google Cloud Console
4. Click **Save Settings**

### Step 6: Connect to Google Search Console

1. After saving credentials, click **Connect to Google Search Console**
2. You'll be redirected to Google for authorization
3. Grant permissions to access your Search Console data
4. You'll be redirected back to your WordPress admin

### Step 7: Verify Connection

1. Check that your email appears in the connection status
2. Test the integration by generating new content ideas
3. Ideas should now be based on your actual search data

## 🔧 Troubleshooting

### Common Issues

#### 1. "Google API client library not installed" Error
**Solution**: Run composer install in the plugin directory:
```bash
cd wp-content/plugins/ai-content-agent-plugin/
composer install --no-dev
```

#### 2. "Redirect URI mismatch" Error
**Solution**: Ensure the redirect URI in Google Cloud Console exactly matches:
```
https://yourdomain.com/wp-admin/admin.php?page=ai-content-agent&gsc_auth=callback
```

#### 3. "No data found" Message
**Possible causes**:
- Your site isn't verified in Google Search Console
- Your site has very little search traffic
- The site URL format doesn't match GSC (try with/without trailing slash)

#### 4. "Authentication failed" Error
**Solutions**:
- Check that your Client ID and Secret are correct
- Ensure the Google Search Console API is enabled
- Verify your OAuth consent screen is configured

#### 5. "Site not found in Search Console" Error
**Solution**: 
- Verify your website in Google Search Console first
- Ensure the domain matches exactly (www vs non-www)

### Debug Information

The plugin logs detailed error information. Check your WordPress debug log for entries starting with "ACA GSC".

To enable WordPress debugging, add these lines to your `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📊 How It Works

### Data Collection
- **Top Queries**: Fetches your top 20 search queries with actual clicks
- **Underperforming Pages**: Identifies pages ranking below position 10 with decent impressions
- **Real-time Integration**: Data is fetched fresh each time you generate ideas

### AI Integration
When you generate content ideas, the AI receives:
- Your top-performing search queries
- Pages that need optimization
- Your style guide preferences
- Historical content to avoid duplicates

This results in SEO-optimized content ideas based on actual user search behavior.

## 🔒 Security Notes

- OAuth2 tokens are stored securely in your WordPress database
- Only read-only access to Search Console data is requested
- Tokens are automatically refreshed when needed
- You can disconnect at any time from the Settings page

## 📈 Best Practices

1. **Regular Monitoring**: Check your GSC connection monthly
2. **Data Quality**: Ensure your site has at least 1000+ monthly impressions for meaningful data
3. **Site Verification**: Keep your site verified in Google Search Console
4. **Multiple Properties**: If you have multiple sites, connect the most relevant one

## 🆘 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review WordPress error logs
3. Verify all setup steps were completed
4. Ensure composer dependencies are installed

For additional support, check that all prerequisites are met and the Google Search Console API is properly enabled in your Google Cloud project.

---

**Last Updated**: January 2025
**Plugin Version**: 1.6.3+