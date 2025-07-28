# ACA - AI Content Agent WordPress Plugin

A powerful WordPress plugin that provides AI-powered content creation and management with automated idea generation, draft creation, and publishing workflows.

## Features

- **AI-Powered Style Analysis**: Automatically analyzes your existing content to create a consistent style guide
- **Intelligent Idea Generation**: Uses AI to generate relevant blog post ideas based on your style and optionally Google Search Console data
- **Automated Draft Creation**: Converts ideas into full blog posts with SEO optimization
- **Multi-Source Image Generation**: Supports AI-generated images or stock photos from Pexels, Unsplash, and Pixabay
- **Content Calendar**: Visual calendar for scheduling and managing your content pipeline
- **Three Automation Modes**:
  - **Manual**: Full control over every step
  - **Semi-Automatic**: Automated idea generation with manual approval
  - **Full-Automatic**: Complete automation from idea to publication
- **Activity Logging**: Track all actions and changes in your content workflow
- **Modern React SPA Interface**: Single-page application with responsive design

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Node.js and npm (for building the frontend)
- Google Gemini API key (for AI functionality)
- Optional: API keys for stock photo services (Pexels, Unsplash, Pixabay)

## Installation

1. **Download or Clone** this repository to your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone [repository-url] aca-ai-content-agent
   ```

2. **Build the Frontend**:
   ```bash
   cd aca-ai-content-agent
   ./build.sh
   ```

3. **Activate the Plugin** in your WordPress admin dashboard under Plugins.

4. **Configure API Keys** in the plugin settings:
   - Navigate to "AI Content Agent" in your WordPress admin menu
   - Go to Settings and add your Google Gemini API key
   - Optionally add API keys for stock photo services

## Configuration

### Required API Keys

#### Google Gemini API
1. Visit [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key
3. Add it to the plugin settings under "Gemini API Key"

#### Stock Photo APIs (Optional)

**Pexels:**
1. Visit [Pexels API](https://www.pexels.com/api/)
2. Create an account and get your API key
3. Add it to plugin settings

**Unsplash:**
1. Visit [Unsplash Developers](https://unsplash.com/developers)
2. Create an application and get your Access Key
3. Add it to plugin settings

**Pixabay:**
1. Visit [Pixabay API](https://pixabay.com/api/docs/)
2. Create an account and get your API key
3. Add it to plugin settings

## Usage

### Getting Started

1. **Generate Style Guide**: Click "Analyze Style" on the dashboard to analyze your existing content
2. **Generate Ideas**: Use the "Generate Ideas" button to create content ideas
3. **Create Drafts**: Convert ideas to full blog posts with the "Create Draft" button
4. **Review and Publish**: Edit drafts as needed and publish when ready

### Automation Modes

#### Manual Mode
- Full control over every step
- Generate ideas and drafts on demand
- Perfect for careful content curation

#### Semi-Automatic Mode
- Automatically generates ideas every 15 minutes
- Manual approval required for draft creation
- Balances automation with control

#### Full-Automatic Mode
- Complete automation every 30 minutes
- Generates ideas, creates drafts, and optionally publishes
- Ideal for high-volume content needs

### Content Calendar

- Drag and drop drafts to schedule publication dates
- Visual overview of your content pipeline
- Integrated with WordPress scheduling

## File Structure

```
aca-ai-content-agent/
├── ai-content-agent.php          # Main plugin file
├── includes/                     # PHP backend classes
│   ├── class-aca-database.php    # Database operations
│   ├── class-aca-rest-api.php    # REST API endpoints
│   ├── class-aca-gemini-service.php # AI service integration
│   ├── class-aca-stock-photo-service.php # Stock photo APIs
│   └── class-aca-cron.php        # Background automation
├── src/                          # React frontend source
│   ├── components/               # React components
│   ├── services/                 # API service layer
│   ├── App.tsx                   # Main React application
│   ├── types.ts                  # TypeScript definitions
│   └── index.tsx                 # Entry point
├── build/                        # Compiled frontend assets
│   ├── main.js                   # Compiled JavaScript
│   └── main.css                  # Compiled CSS
├── build.sh                      # Build script
└── README.md                     # This file
```

## Development

### Building the Frontend

```bash
cd src
npm install
npm run build
```

### Development Mode

```bash
cd src
npm run dev
```

### Adding New Features

1. **Backend**: Add new REST API endpoints in `class-aca-rest-api.php`
2. **Frontend**: Create new React components in `src/components/`
3. **Database**: Extend database operations in `class-aca-database.php`

## Database Schema

### Custom Tables

#### `wp_aca_ideas`
- `id`: Primary key
- `title`: Idea title
- `status`: 'new' or 'archived'
- `source`: 'ai', 'search-console', 'similar', or 'manual'
- `created_at`: Timestamp

#### `wp_aca_activity_logs`
- `id`: Primary key
- `timestamp`: Action timestamp
- `type`: Activity type
- `details`: Description
- `icon`: Icon identifier

### WordPress Options
- `aca_settings`: Plugin configuration
- `aca_style_guide`: AI-generated style guide

### Post Meta Fields
- `_aca_meta_title`: SEO meta title
- `_aca_meta_description`: SEO meta description
- `_aca_focus_keywords`: SEO keywords (JSON)
- `_aca_scheduled_for`: Scheduled publication date

## API Endpoints

All endpoints are under the `/wp-json/aca/v1/` namespace:

- `GET|POST /settings` - Plugin settings
- `GET|POST /style-guide` - Style guide management
- `POST /analyze-style` - Trigger style analysis
- `GET|POST /ideas` - Ideas management
- `POST /ideas/similar` - Generate similar ideas
- `POST /ideas/manual` - Add manual idea
- `PUT|DELETE /ideas/{id}` - Update/delete idea
- `GET /posts` - Get posts
- `PUT /posts/{id}` - Update post
- `POST /posts/{id}/publish` - Publish post
- `POST /posts/{id}/schedule` - Schedule post
- `POST /create-draft` - Create draft from idea
- `GET /activity-logs` - Get activity logs

## Security

- All API endpoints require `manage_options` capability
- CSRF protection via WordPress nonces
- Input sanitization and output escaping
- API keys stored securely and never returned to frontend

## Troubleshooting

### Common Issues

**Plugin doesn't load:**
- Ensure build files exist in `/build/` directory
- Run `./build.sh` to compile frontend assets

**API errors:**
- Check that API keys are correctly configured
- Verify WordPress REST API is enabled
- Check browser console for detailed error messages

**Automation not working:**
- Ensure WP-Cron is enabled
- Check that automation mode is set correctly
- Verify style guide has been generated

### Debug Mode

Add this to your `wp-config.php` for debugging:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support and bug reports, please use the GitHub issues page.