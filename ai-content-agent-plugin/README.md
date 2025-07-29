# AI Content Agent (ACA) WordPress Plugin

![Version](https://img.shields.io/badge/version-1.4.0-blue.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-blue.svg)
![License](https://img.shields.io/badge/license-GPL%20v2%2B-green.svg)

AI-powered content creation and management plugin that generates blog posts, ideas, and manages your content workflow automatically with an intelligent Content Calendar system and **real Google Search Console integration**.

## 🚀 Latest Updates - v1.4.0 - GOOGLE SEARCH CONSOLE INTEGRATION 🔍

### ✅ **MAJOR NEW FEATURE: REAL GOOGLE SEARCH CONSOLE INTEGRATION**
- **Real GSC Data**: Complete replacement of simulated data with actual Google Search Console API integration
- **OAuth2 Authentication**: Secure OAuth2 flow for connecting to user's Google Search Console account
- **Live Search Analytics**: Access to real search queries, clicks, impressions, CTR, and position data
- **Dynamic Content Ideas**: AI content generation now uses actual search performance data from your website

### 🔐 **AUTHENTICATION SYSTEM**
- **Google OAuth2 Flow**: Full OAuth2 implementation with proper token management
- **Secure Token Storage**: Access and refresh tokens stored securely in WordPress database
- **Automatic Token Refresh**: Handles token expiration and refresh automatically
- **Connection Management**: Easy connect/disconnect functionality with proper cleanup

### 📊 **SEARCH CONSOLE DATA INTEGRATION**
- **Top Performing Queries**: Fetches actual top search queries from your GSC account
- **Underperforming Pages**: Identifies pages with high impressions but low CTR for optimization
- **Real-time Data**: Fresh data fetched directly from Google Search Console API
- **SEO-Focused Content**: AI generates content ideas based on your actual search performance

## 🎯 Key Features

### 🔍 **Google Search Console Integration (NEW!)**
- **Real Search Data**: Connect your actual Google Search Console account
- **OAuth2 Security**: Secure authentication with Google's OAuth2 system
- **Performance Insights**: 
  - 📈 **Top Search Queries**: Your actual top-performing search terms
  - 📉 **Underperforming Pages**: Pages with high impressions but low CTR
  - 🎯 **SEO Opportunities**: Content gaps identified from real search data
- **AI Enhancement**: Search Console data directly improves AI content suggestions
- **Easy Setup**: Step-by-step guide for Google Cloud Console configuration

### 📅 **Intelligent Content Calendar**
- **Smart Multi-Post Management**: Expandable cells handle unlimited posts per day
- **Visual Post Types**: 
  - 🟡 **Scheduled Drafts**: Yellow with clock icon
  - 🟢 **Published Posts**: Green with eye icon  
  - 🔵 **Today Indicator**: Blue border highlights current date
- **Drag & Drop Scheduling**: Reschedule any content by dragging to new dates
- **Direct WordPress Integration**: Click any post to edit directly in WordPress
- **Real-time Updates**: Instant calendar updates with success/error notifications

### 🤖 **AI-Powered Content Creation**
- **Multi-AI Support**: Gemini, ChatGPT, Claude integration
- **Smart Content Generation**: Blog posts, ideas, headlines with SEO optimization
- **Search Console Enhancement**: Real search data improves AI content suggestions
- **Style Guide Management**: Consistent brand voice across all content
- **Automated Workflow**: From idea to published post with minimal intervention

### 📊 **Advanced Management**
- **Comprehensive Dashboard**: Real-time statistics and content overview
- **Activity Logging**: Detailed tracking of all plugin activities
- **Draft Management**: Full lifecycle management from creation to publication
- **Published Content**: Complete overview of all published posts with analytics

### ⚙️ **Professional Features**
- **Stock Photo Integration**: Automatic image sourcing and optimization
- **SEO Optimization**: Built-in SEO analysis and recommendations enhanced with GSC data
- **Bulk Operations**: Mass content creation and management
- **Scheduling System**: Advanced WordPress cron-based automation

## 📋 Installation

### Method 1: Upload Plugin (Recommended)

1. **Download** the latest release: [`ai-content-agent-v1.4.0-gsc-integration.zip`](../ai-content-agent-v1.4.0-gsc-integration.zip) (173KB)
2. **WordPress Admin** → Plugins → Add New → Upload Plugin
3. **Choose File** → Select the downloaded zip
4. **Install Now** → **Activate Plugin**
5. **Configure** → AI Content Agent settings page

### Method 2: Manual Installation

1. **Extract** the zip file to `/wp-content/plugins/`
2. **Activate** the plugin through WordPress admin
3. **Configure** your AI API keys and Google Search Console credentials in settings

## 🔧 Configuration

### Required Settings
- **AI Service**: Choose your preferred AI provider (Gemini recommended)
- **API Keys**: Configure your AI service API credentials

### Google Search Console Setup (NEW!)
1. **Google Cloud Console**: Create project and enable Search Console API
2. **OAuth2 Credentials**: Generate Client ID and Client Secret
3. **Plugin Settings**: Enter credentials and connect your GSC account
4. **Enhanced Content**: AI now uses your real search data for better content ideas

📖 **[Complete GSC Setup Guide](GOOGLE_SEARCH_CONSOLE_SETUP.md)** - Step-by-step instructions

### Optional Enhancements
- **Stock Photos**: Enable automatic image integration
- **SEO Settings**: Configure meta descriptions and keyword optimization
- **Scheduling**: Set up automated content publication times

## 📖 How to Use

### Google Search Console Workflow (NEW!)

1. **Connect GSC**: Follow setup guide to connect your Google Search Console account
2. **Real Data Access**: Plugin automatically fetches your search performance data
3. **Enhanced Ideas**: AI generates content based on your actual top search queries
4. **SEO Opportunities**: Identify and address underperforming pages with new content
5. **Performance Tracking**: Monitor how AI-generated content performs in search

### Content Calendar Workflow

1. **View All Content**: Calendar displays drafts, scheduled posts, and published content
2. **Schedule Content**: Drag drafts to any date for automatic scheduling
3. **Reschedule**: Drag existing scheduled posts to new dates
4. **Edit Content**: Click any post to open WordPress editor
5. **Track Progress**: Visual indicators show content status at a glance

### AI Content Creation

1. **Generate Ideas**: Use the AI idea generator enhanced with your GSC data
2. **Create Drafts**: Convert ideas to full blog posts with AI assistance
3. **Optimize Content**: Apply SEO recommendations based on real search data
4. **Schedule & Publish**: Use the calendar for strategic content timing

## 🎨 User Interface

### Smart Calendar Design
- **Expandable Cells**: Days with multiple posts show compact view initially
- **Click to Expand**: Reveal all posts with smooth animation
- **Visual Hierarchy**: Different post types have distinct styling
- **Responsive Layout**: Optimized for all screen sizes

### Google Search Console Integration
- **Connection Status**: Real-time display of GSC connection status
- **Credential Management**: Secure input fields for Google OAuth2 credentials
- **Data Visualization**: Clear indicators when GSC data is being used
- **Setup Assistance**: Direct links to Google Cloud Console for easy setup

## 🔄 Recent Changelog

### v1.4.0 - Google Search Console Integration
- **MAJOR**: Real Google Search Console API integration
- **NEW**: OAuth2 authentication system
- **ENHANCED**: AI content generation with real search data
- **ADDED**: Comprehensive GSC setup documentation

### v1.3.9 - Automation Mode Fixes + Debug System
- **FIXED**: All 3 automation modes working correctly
- **ADDED**: Debug system for automation testing
- **ENHANCED**: Error handling and logging

### v1.3.8 - Smart Calendar UI + Published Posts Fix
- **CRITICAL**: Fixed published posts visibility issue
- **NEW**: Expandable calendar cells for multiple posts
- **ENHANCED**: Smart space management and visual hierarchy

[View Full Changelog](CHANGELOG.md)

## 🛠️ Technical Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher
- **Memory**: 128MB minimum (256MB recommended)
- **Storage**: 15MB for plugin files (including Google API client)
- **Google Cloud**: Project with Search Console API enabled (for GSC integration)

## 🤝 Support & Documentation

- **GSC Setup Guide**: [Complete Google Search Console setup instructions](GOOGLE_SEARCH_CONSOLE_SETUP.md)
- **Plugin Settings**: Comprehensive in-plugin help system
- **WordPress Compatibility**: Tested with latest WordPress versions
- **Performance Optimized**: Minimal impact on site speed
- **Security**: Following WordPress security best practices

## 📝 License

This plugin is licensed under the GPL v2 or later.

---

**AI Content Agent** - Transform your WordPress content workflow with intelligent automation, beautiful calendar management, and real Google Search Console insights. 🚀
