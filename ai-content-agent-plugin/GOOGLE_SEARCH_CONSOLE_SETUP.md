# Google Search Console Integration Setup Guide

This guide will walk you through setting up Google Search Console integration with the AI Content Agent plugin to access real search performance data for your content creation.

## 🎯 Overview

The Google Search Console integration allows the AI Content Agent to:
- Access real search query data from your website
- Identify top-performing keywords
- Find underperforming pages that need optimization
- Generate content ideas based on actual search data
- Improve SEO-focused content creation

## 📋 Prerequisites

- WordPress admin access
- Google account with access to Google Search Console
- Your website verified in Google Search Console
- Basic understanding of Google Cloud Console

## 🚀 Step-by-Step Setup

### Step 1: Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select an existing one
3. Enable the **Google Search Console API**:
   - Navigate to "APIs & Services" > "Library"
   - Search for "Google Search Console API"
   - Click "Enable"

### Step 2: Create OAuth 2.0 Credentials

1. Go to "APIs & Services" > "Credentials"
2. Click "Create Credentials" > "OAuth 2.0 Client IDs"
3. If prompted, configure the OAuth consent screen:
   - Choose "External" user type
   - Fill in the required information
   - Add your domain to authorized domains
4. For Application type, select "Web application"
5. Add authorized redirect URIs:
   ```
   https://yourdomain.com/wp-admin/admin.php?page=ai-content-agent&gsc_auth=callback
   ```
   Replace `yourdomain.com` with your actual domain

6. Click "Create" and copy the **Client ID** and **Client Secret**

### Step 3: Configure Plugin Settings

1. In your WordPress admin, go to **AI Content Agent** > **Settings**
2. Scroll to the **Google Search Console** section
3. Enter your **Client ID** and **Client Secret**
4. Click the **Connect** button
5. You'll be redirected to Google to authorize access
6. Grant permissions and you'll be redirected back to your site

## ✅ Verification

Once connected, you should see:
- ✅ Connection status showing "Connected as [your-email]"
- ✅ Real search data being used in content generation
- ✅ Top queries from your actual Search Console data

## 🔧 Troubleshooting

### Common Issues

**1. "OAuth error: redirect_uri_mismatch"**
- Ensure the redirect URI in Google Cloud Console exactly matches your WordPress admin URL
- Check for HTTP vs HTTPS differences

**2. "Access denied" or "insufficient permissions"**
- Make sure your Google account has access to the Search Console property
- Verify the website is properly verified in Google Search Console

**3. "API not enabled"**
- Ensure Google Search Console API is enabled in your Google Cloud project
- Wait a few minutes after enabling for changes to propagate

**4. Connection expires**
- The plugin automatically refreshes tokens
- If issues persist, disconnect and reconnect

### Debug Information

You can check the connection status and debug information in the **Settings** > **Automation Debug Panel** section.

## 🔒 Security Notes

- Client credentials are stored securely in your WordPress database
- Access tokens are automatically refreshed
- You can disconnect at any time to revoke access
- Only read-only access to Search Console data is requested

## 📊 Data Usage

The integration fetches:
- **Top Queries**: Your most searched keywords (last 30 days)
- **Underperforming Pages**: Pages ranking below position 10
- **Search Analytics**: Clicks, impressions, CTR, and position data

This data is used to:
- Generate content ideas based on real search trends
- Identify content gaps and opportunities
- Improve AI-generated content relevance

## 🔄 Updates and Compatibility

- **Latest Version**: Uses modern Google API PHP client v2.x
- **Namespaced Classes**: Updated for better compatibility
- **Automatic Token Management**: Handles token refresh automatically
- **Error Handling**: Comprehensive error logging and user feedback

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section above
2. Review WordPress error logs
3. Use the debug panel in plugin settings
4. Ensure all prerequisites are met

---

**Note**: This integration requires the `google/apiclient` PHP library, which is automatically included with the plugin.