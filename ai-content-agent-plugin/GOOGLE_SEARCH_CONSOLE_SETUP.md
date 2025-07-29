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

1. **Go to Google Cloud Console**
   - Visit [Google Cloud Console](https://console.cloud.google.com/)
   - Sign in with your Google account

2. **Create New Project**
   - Click on the project dropdown at the top
   - Click "New Project"
   - Enter project name: "AI Content Agent GSC Integration"
   - Click "Create"

### Step 2: Enable Google Search Console API

1. **Navigate to APIs & Services**
   - In the Google Cloud Console, go to "APIs & Services" > "Library"
   - Search for "Google Search Console API"
   - Click on "Google Search Console API"
   - Click "Enable"

### Step 3: Configure OAuth Consent Screen

1. **Go to OAuth Consent Screen**
   - Navigate to "APIs & Services" > "OAuth consent screen"
   - Choose "External" user type (unless you have Google Workspace)
   - Click "Create"

2. **Fill Out App Information**
   - App name: "AI Content Agent"
   - User support email: Your email
   - Developer contact information: Your email
   - Click "Save and Continue"

3. **Scopes (Optional)**
   - Click "Save and Continue" (no additional scopes needed)

4. **Test Users (Optional)**
   - Add your email as a test user
   - Click "Save and Continue"

### Step 4: Create OAuth2 Credentials

1. **Go to Credentials**
   - Navigate to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "OAuth client ID"

2. **Configure OAuth Client**
   - Application type: "Web application"
   - Name: "AI Content Agent GSC"
   - Authorized redirect URIs: Add your WordPress admin URL:
     ```
     https://yourdomain.com/wp-admin/admin.php?page=ai-content-agent&gsc_auth=callback
     ```
     (Replace `yourdomain.com` with your actual domain)

3. **Save Credentials**
   - Click "Create"
   - Copy the Client ID and Client Secret
   - Keep these secure - you'll need them in the next step

### Step 5: Configure Plugin Settings

1. **Go to WordPress Admin**
   - Navigate to "AI Content Agent" > "Settings"
   - Scroll to "Google Search Console" section

2. **Enter Credentials**
   - Paste your Client ID in the "Client ID" field
   - Paste your Client Secret in the "Client Secret" field
   - Click "Save Settings"

3. **Connect to Google Search Console**
   - Click the "Connect" button
   - You'll be redirected to Google for authorization
   - Grant permissions to access your Search Console data
   - You'll be redirected back to WordPress

### Step 6: Verify Connection

1. **Check Connection Status**
   - The connection status should show "Connected as [your-email]"
   - Green checkmark indicates successful connection

2. **Test Data Access**
   - Go to "Ideas" section and generate new ideas
   - The AI should now use your real Search Console data
   - Look for ideas based on your actual search queries

## 🔧 Troubleshooting

### Common Issues

**"redirect_uri_mismatch" Error**
- Ensure your redirect URI exactly matches your WordPress admin URL
- Check for http vs https
- Verify the domain name is correct

**"OAuth Error: invalid_client"**
- Double-check your Client ID and Client Secret
- Ensure they're copied correctly without extra spaces

**"Not authenticated" Errors**
- Your OAuth consent screen might still be in "Testing" mode
- Publish your app or add yourself as a test user

**"API not enabled" Error**
- Ensure Google Search Console API is enabled in your Google Cloud project
- Wait a few minutes after enabling for changes to propagate

### Token Refresh Issues

If you get disconnected frequently:
1. Set your Google Cloud project to "Production" status
2. This prevents tokens from expiring every 7 days

## 📊 Using Search Console Data

Once connected, the plugin will automatically:

1. **Enhance Idea Generation**
   - Use your top-performing search queries
   - Identify content gaps based on underperforming pages
   - Generate SEO-focused content ideas

2. **Improve Content Quality**
   - AI prompts include real search data
   - Content suggestions based on actual user queries
   - Better keyword targeting

3. **Performance Insights**
   - Track which generated content performs well
   - Identify successful content patterns
   - Optimize future content creation

## 🔒 Security & Privacy

- **Data Storage**: Search Console data is fetched in real-time and not stored permanently
- **Token Security**: Access tokens are encrypted and stored securely in WordPress
- **Permissions**: Only read-only access to your Search Console data
- **Revocation**: You can disconnect anytime from the Settings page

## 📈 Best Practices

1. **Regular Monitoring**
   - Check connection status monthly
   - Monitor API usage in Google Cloud Console

2. **Content Strategy**
   - Use top queries to inform content calendar
   - Address underperforming pages with new content
   - Focus on high-impression, low-CTR opportunities

3. **Data Interpretation**
   - Combine Search Console data with your content expertise
   - Consider seasonal trends in search data
   - Balance search volume with content relevance

## 🆘 Support

If you encounter issues:

1. **Check Plugin Logs**
   - Enable WordPress debug logging
   - Check for GSC-related errors in logs

2. **Verify Google Setup**
   - Ensure your website is verified in Search Console
   - Check API quotas in Google Cloud Console

3. **Contact Support**
   - Include error messages and steps to reproduce
   - Mention your WordPress and plugin versions

---

**Note**: This integration requires your website to have sufficient search data in Google Search Console. New websites may have limited data initially.