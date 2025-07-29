# AI Image Generation Setup Guide - v1.8.0

This guide explains how to set up AI image generation using Google's Imagen 3.0 API in the AI Content Agent plugin.

## 🖼️ Latest Updates - v1.8.0

### ✅ **COMPREHENSIVE FEATURE ENHANCEMENTS & IMPROVEMENTS**
- **Author Updates**: Updated plugin author to Adem Isler with website integration (ademisler.com/en)
- **Enhanced Error Handling**: Improved error handling for AI image generation with intelligent retry logic
- **Better API Reliability**: Automatic retry mechanism for image generation API failures
- **User-Friendly Messages**: Clear error messages and recovery instructions for image generation issues
- **Improved Stability**: Enhanced stability when generating images during content creation
- **Timeout Handling**: Better handling of image generation timeouts with automatic retry
- **Fallback Mechanisms**: Graceful fallback to stock photo APIs when AI generation fails
- **Image Source Optimization**: Moved AI generated images to last position, Pexels set as default

## 📋 Overview

The AI Content Agent plugin v1.8.0 supports **real AI image generation** using Google's Imagen 3.0 API through Google Cloud Vertex AI with enhanced error handling and retry capabilities. This replaces the previous placeholder implementation with actual image generation capabilities and robust error management.

### **Supported Image Sources:**
- ✅ **AI Generated** - Google Imagen 3.0 API (Enhanced with retry logic)
- ✅ **Pexels** - Stock photos via Pexels API (Verified working)
- ✅ **Unsplash** - Stock photos via Unsplash API (Verified working)
- ✅ **Pixabay** - Stock photos via Pixabay API (Verified working)

## 🚀 Quick Setup Guide

### Step 1: Google Cloud Setup

1. **Create a Google Cloud Project**:
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select an existing one
   - Note your Project ID (you'll need this later)

2. **Enable Vertex AI API**:
   - In the Google Cloud Console, go to APIs & Services > Library
   - Search for "Vertex AI API"
   - Click "Enable"

3. **Set up Authentication**:
   - Go to IAM & Admin > Service Accounts
   - Create a new service account
   - Download the JSON key file
   - Grant the service account "Vertex AI User" role

### Step 2: Plugin Configuration

1. **Access Plugin Settings**:
   - Go to your WordPress admin panel
   - Navigate to the AI Content Agent plugin
   - Click on "Settings"

2. **Configure Image Source**:
   - Select "AI Generated" as your Featured Image Source
   - Choose your preferred AI Image Style:
     - **Photorealistic**: High-quality, professional photography style
     - **Digital Art**: Creative, artistic illustration style

3. **Enter Google Cloud Details**:
   - **Google Cloud Project ID**: Enter your Google Cloud project ID
   - **Google Cloud Location**: Select the region closest to your users:
     - `us-central1` (recommended for most users)
     - `us-east1`
     - `us-west1`
     - `europe-west1`
     - `asia-southeast1`

4. **API Key Configuration**:
   - **Important**: Imagen API requires a Google Cloud Vertex AI access token, NOT a Google AI Studio API key
   - You need to generate an access token from your service account or use Application Default Credentials
   - This is different from the Google AI API key used for Gemini content generation

### Step 3: Generate Access Token

To generate a proper access token for Imagen API:

1. **Using gcloud CLI** (Recommended for testing):
   ```bash
   # Install Google Cloud CLI if not already installed
   # Then authenticate and generate token:
   gcloud auth login
   gcloud auth application-default print-access-token
   ```

2. **Using Service Account** (Recommended for production):
   - Use the service account JSON key to generate JWT tokens
   - Exchange JWT for access tokens programmatically
   - This requires additional server-side implementation

3. **For WordPress Plugin Users**:
   - Currently, you need to manually generate access tokens
   - Future versions will support automatic service account authentication

## 🔧 Technical Details

### **Imagen Model Used**
- **Model**: `imagen-3.0-generate-002`
- **Provider**: Google Cloud Vertex AI
- **Capabilities**: Text-to-image generation with high quality output

### **Image Specifications**
- **Aspect Ratio**: 16:9 (optimized for featured images)
- **Quality**: High-resolution professional images
- **Safety Filtering**: Enabled with appropriate content filters
- **Person Generation**: Allowed for adults only

### **Authentication Flow**
1. Plugin requires a Google Cloud Vertex AI access token (not AI Studio API key)
2. Access tokens are cached for improved performance (30 minutes)
3. Service account authentication is recommended for production
4. Proper error handling for authentication failures

## 🎨 Image Styles

### **Photorealistic Style**
- **Description**: High-quality, professional photography
- **Keywords**: Photorealistic, 4K, HDR, studio lighting
- **Best For**: Business content, professional blogs, news articles

### **Digital Art Style** 
- **Description**: Creative, artistic illustrations
- **Keywords**: Digital art, illustration, creative, artistic, detailed
- **Best For**: Creative content, entertainment blogs, artistic posts

## 🔍 Troubleshooting

### **Common Issues and Solutions**

#### **"Google Cloud Project ID not configured"**
- **Solution**: Enter your Google Cloud Project ID in the plugin settings
- **Location**: Settings > Featured Image Source > Google Cloud Project ID

#### **"Imagen API error: 403 Forbidden"**
- **Solution**: Check that Vertex AI API is enabled in your Google Cloud project
- **Steps**: Go to Google Cloud Console > APIs & Services > Library > Enable Vertex AI API

#### **"Authentication failed"**
- **Solution**: Verify you're using a Google Cloud Vertex AI access token, not a Google AI Studio API key
- **Check**: Generate a proper access token from your service account credentials
- **Note**: Vertex AI and Google AI Studio use different authentication methods

#### **"No images generated"**
- **Solution**: Check error logs for specific error messages
- **Debug**: Enable WordPress debug logging to see detailed error information

### **Error Handling**

The plugin includes comprehensive error handling:

1. **API Errors**: Detailed error messages logged for debugging
2. **Fallback Mode**: Returns informative placeholder when API fails
3. **Timeout Protection**: 60-second timeout for API calls
4. **Retry Logic**: Built-in retry mechanism for transient failures

## 📊 Performance Considerations

### **Response Times**
- **Typical Generation Time**: 10-30 seconds per image
- **Timeout Setting**: 60 seconds maximum
- **Caching**: Access tokens cached for improved performance

### **Rate Limits**
- **Google Cloud Limits**: Follow Vertex AI API quotas
- **Best Practice**: Monitor usage in Google Cloud Console
- **Optimization**: Use appropriate image generation frequency

## 🔒 Security and Privacy

### **Data Handling**
- **Prompts**: Sent securely to Google Cloud Vertex AI
- **Images**: Generated images stored as base64 in WordPress
- **API Keys**: Stored securely in WordPress options table
- **Logging**: Error information logged for debugging (no sensitive data)

### **Content Safety**
- **Safety Filters**: Enabled to prevent inappropriate content
- **Person Generation**: Limited to adults only
- **Content Moderation**: Built-in Google safety filtering

## 📚 Additional Resources

### **Google Cloud Documentation**
- [Vertex AI Imagen Overview](https://cloud.google.com/vertex-ai/generative-ai/docs/image/overview)
- [Imagen 3.0 API Reference](https://cloud.google.com/vertex-ai/generative-ai/docs/models/imagen/3-0-generate-002)
- [Google Cloud Authentication](https://cloud.google.com/docs/authentication)

### **Plugin Documentation**
- **Setup Guide**: `GOOGLE_SEARCH_CONSOLE_SETUP.md` - Complete GSC integration guide
- **Developer Guide**: `DEVELOPER_GUIDE.md` - Comprehensive development and deployment guide
- **Release Management**: `RELEASES.md` - Release organization and management
- **Changelog**: `CHANGELOG.md` - Detailed version history

## 🆘 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Review WordPress error logs
3. Verify all setup steps were completed
4. Ensure Google Cloud services are properly configured
5. Check that your Google AI API key has the necessary permissions

For additional support, ensure all prerequisites are met and the Google Cloud Vertex AI API is properly enabled in your Google Cloud project.

---

**Last Updated**: January 2025  
**Plugin Version**: 1.6.3+  
**Imagen Model**: imagen-3.0-generate-002