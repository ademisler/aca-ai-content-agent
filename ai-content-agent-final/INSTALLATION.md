# AI Content Agent (ACA) - Installation Guide

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Google AI API Key (for content generation)
- Optional: API keys for stock photo services (Pexels, Unsplash, Pixabay)

## Installation Steps

### 1. Upload Plugin Files

Extract the plugin files and upload the `ai-content-agent` folder to your WordPress `wp-content/plugins/` directory.

### 2. Install PHP Dependencies (Optional)

If you want to use actual AI features instead of mock responses, you'll need to install PHP dependencies:

```bash
cd wp-content/plugins/ai-content-agent
composer install
```

### 3. Activate the Plugin

1. Go to your WordPress admin dashboard
2. Navigate to **Plugins** → **Installed Plugins**
3. Find "AI Content Agent (ACA)" and click **Activate**

### 4. Configure API Keys

1. In your WordPress admin, go to **AI Content Agent** in the sidebar
2. Click on the **Settings** tab
3. Add your Google AI API Key (required for content generation)
4. Optionally add stock photo API keys:
   - Pexels API Key
   - Unsplash API Key
   - Pixabay API Key

### 5. Generate Style Guide

1. Go to the **Style Guide** tab
2. Click **"Analyze Style Guide"**
3. The AI will analyze your existing content to create a style guide

### 6. Start Creating Content

1. Navigate to the **Ideas** tab to generate content ideas
2. Use the **Drafts** tab to create and manage blog posts
3. Check the **Dashboard** for an overview of your content

## Getting API Keys

### Google AI API Key

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Sign in with your Google account
3. Create a new API key
4. Copy the key and paste it in the plugin settings

### Stock Photo API Keys

#### Pexels
1. Go to [Pexels API](https://www.pexels.com/api/)
2. Sign up for a free account
3. Generate an API key
4. Copy and paste in plugin settings

#### Unsplash
1. Go to [Unsplash Developers](https://unsplash.com/developers)
2. Create a developer account
3. Create a new application
4. Copy the Access Key and paste in plugin settings

#### Pixabay
1. Go to [Pixabay API](https://pixabay.com/api/docs/)
2. Sign up for a free account
3. Get your API key from your account settings
4. Copy and paste in plugin settings

## Automation Modes

The plugin offers three automation modes:

### Manual Mode (Default)
- Full control over content creation
- Generate ideas and create drafts manually
- Best for users who want complete control

### Semi-Automatic Mode
- Automatic idea generation every 15 minutes
- Manual draft creation and publishing
- Good balance of automation and control

### Full-Automatic Mode
- Complete automation of content creation
- Generates ideas, creates drafts, and optionally publishes
- Runs every 30 minutes
- Enable "Auto Publish" in settings for complete automation

## Troubleshooting

### Plugin Not Working
- Ensure your WordPress and PHP versions meet requirements
- Check that the plugin is properly activated
- Verify your Google AI API key is valid

### No Content Generated
- Make sure you have a valid Google AI API key
- Ensure you've generated a Style Guide first
- Check the Activity Log for error messages

### Images Not Loading
- Verify your stock photo API keys are correct
- Try switching between AI images and stock photos
- Check your server's ability to download external images

## Support

For support and bug reports, please check the plugin documentation or contact the development team.

## License

This plugin is licensed under GPL v2 or later.