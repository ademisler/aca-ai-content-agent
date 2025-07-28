# AI Content Agent (ACA) - Agent Documentation

## 📋 Plugin Overview

**AI Content Agent** is a comprehensive WordPress plugin that automates content creation using Google Gemini AI. It provides a complete workflow from idea generation to published posts with WordPress integration.

### Current Version: 1.0.9
### Last Updated: January 2025

---

## 🏗️ Architecture Overview

### Core Components

1. **Frontend (React/TypeScript)**
   - Built with Vite bundler
   - WordPress admin integration
   - Real-time UI updates

2. **Backend (PHP)**
   - WordPress REST API endpoints
   - Database integration
   - AI service integration

3. **AI Integration**
   - Google Gemini 2.0 Flash model
   - Content generation and analysis
   - Style guide creation

---

## 📁 File Structure

```
ai-content-agent-plugin/
├── admin/                          # Compiled assets
│   ├── css/index.css               # Compiled CSS
│   └── js/index.js                 # Compiled JavaScript
├── components/                     # React components
│   ├── ActivityLog.tsx             # Activity logging UI
│   ├── ContentCalendar.tsx         # Calendar view
│   ├── Dashboard.tsx               # Main dashboard
│   ├── DraftModal.tsx              # Draft editing modal
│   ├── DraftsList.tsx              # Drafts management
│   ├── IdeaBoard.tsx               # Ideas management
│   ├── Icons.tsx                   # SVG icon components
│   ├── PublishedList.tsx           # Published posts view
│   ├── Settings.tsx                # Plugin settings
│   ├── Sidebar.tsx                 # Navigation sidebar
│   ├── StyleGuideManager.tsx       # Style guide management
│   └── Toast.tsx                   # Notification system
├── includes/                       # PHP backend
│   ├── class-aca-activator.php     # Plugin activation
│   ├── class-aca-cron.php          # Scheduled tasks
│   ├── class-aca-deactivator.php   # Plugin deactivation
│   └── class-aca-rest-api.php      # REST API endpoints
├── services/                       # Frontend services
│   ├── aiService.ts                # AI service interface
│   ├── geminiService.ts            # Google Gemini integration
│   ├── stockPhotoService.ts        # Stock photo services
│   └── wordpressApi.ts             # WordPress API wrapper
├── App.tsx                         # Main React application
├── ai-content-agent.php            # Main plugin file
├── index.css                       # Global styles
├── index.tsx                       # React entry point
├── package.json                    # Node.js dependencies
├── types.ts                        # TypeScript type definitions
└── vite.config.ts                  # Build configuration
```

---

## 🔌 WordPress Integration

### Database Tables

The plugin creates custom tables:

1. **`wp_aca_ideas`** - Content ideas storage
2. **`wp_aca_activity_logs`** - Activity logging
3. **WordPress Posts** - Drafts and published content
4. **WordPress Options** - Settings and style guide

### REST API Endpoints

All endpoints use prefix: `/wp-json/aca/v1/`

#### Settings
- `GET/POST /settings` - Plugin settings management

#### Style Guide
- `GET /style-guide` - Get current style guide
- `POST /style-guide/analyze` - Analyze website content for style
- `POST /style-guide` - Save style guide

#### Ideas
- `GET /ideas` - Get all ideas
- `POST /ideas/generate` - Generate new ideas with AI
- `POST /ideas/similar` - Generate similar ideas
- `POST /ideas` - Create manual idea
- `PUT /ideas/{id}` - Update idea
- `DELETE /ideas/{id}` - Archive idea

#### Drafts
- `GET /drafts` - Get all drafts
- `POST /drafts/create` - Create draft from idea
- `PUT /drafts/{id}` - Update draft
- `POST /drafts/{id}/schedule` - Schedule draft
- `POST /drafts/{id}/publish` - Publish draft

#### Published Posts
- `GET /published` - Get published posts

#### Activity Logs
- `GET /activity-logs` - Get activity logs
- `POST /activity-logs` - Add activity log entry

---

## 🤖 AI Integration Details

### Google Gemini Configuration

**Model:** `gemini-2.0-flash`
**API Endpoint:** `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent`

### API Headers
```php
'headers' => array(
    'Content-Type' => 'application/json',
    'x-goog-api-key' => $api_key
)
```

### Key AI Functions

1. **Style Analysis** (`call_gemini_analyze_style`)
   - Analyzes last 20 WordPress posts
   - Generates style guide JSON
   - Determines tone, structure, formatting

2. **Content Generation** (`call_gemini_create_draft`)
   - Creates full blog posts (800-1500 words)
   - Generates SEO metadata
   - Includes internal links
   - Creates tags and categories

3. **Idea Generation** (`call_gemini_generate_ideas`)
   - Generates content ideas based on style guide
   - Avoids duplicate titles
   - SEO-optimized suggestions

---

## 🎨 Frontend Architecture

### State Management

The main `App.tsx` manages global state:

```typescript
// Core state
const [view, setView] = useState<ViewType>('dashboard');
const [ideas, setIdeas] = useState<ContentIdea[]>([]);
const [posts, setPosts] = useState<Draft[]>([]);
const [settings, setSettings] = useState<AppSettings | null>(null);
const [styleGuide, setStyleGuide] = useState<StyleGuide | null>(null);
const [activityLogs, setActivityLogs] = useState<ActivityLog[]>([]);
const [isLoading, setIsLoading] = useState<LoadingStates>({});
```

### Key React Components

1. **Dashboard** - Overview with metrics and recent activity
2. **StyleGuideManager** - AI-powered style analysis
3. **IdeaBoard** - Content idea management with AI generation
4. **DraftsList** - Draft management and editing
5. **PublishedList** - Published content overview
6. **Settings** - Plugin configuration

### Component Communication

- Props drilling for data and callbacks
- Centralized state in App.tsx
- Toast notifications for user feedback
- Activity logging for all actions

---

## 🔧 Build System

### Development Setup
```bash
npm install
npm run dev    # Development server
npm run build  # Production build
```

### Build Process
1. Vite compiles React/TypeScript to JavaScript
2. CSS is processed and minified
3. Assets are output to `dist/assets/`
4. Files are copied to `admin/css/` and `admin/js/`

### WordPress Integration
```php
// Enqueue compiled assets
wp_enqueue_style('aca-styles', ACA_PLUGIN_URL . 'admin/css/index.css', array(), ACA_VERSION . '-' . filemtime(ACA_PLUGIN_PATH . 'admin/css/index.css'));
wp_enqueue_script('aca-app', ACA_PLUGIN_URL . 'admin/js/index.js', array(), ACA_VERSION . '-' . filemtime(ACA_PLUGIN_PATH . 'admin/js/index.js'), true);
```

---

## 🔄 Content Workflow

### Complete Content Creation Flow

1. **Style Analysis**
   - User clicks "Analyze Content"
   - System fetches last 20 WordPress posts
   - AI analyzes writing style
   - Style guide is generated and saved

2. **Idea Generation**
   - User clicks "Generate Ideas"
   - AI creates ideas based on style guide
   - Ideas are saved to database
   - User can generate similar ideas

3. **Draft Creation**
   - User clicks "Create Draft" on an idea
   - AI generates full blog post content
   - WordPress draft post is created with:
     - Full content (800-1500 words)
     - SEO metadata (title, description, keywords)
     - Categories (auto-created if needed)
     - Tags
     - Internal links to existing posts
     - Featured image (if configured)

4. **Publishing**
   - User can edit draft in WordPress
   - Publish manually or schedule

---

## 🐛 Error Handling & Debugging

### PHP Error Logging
```php
error_log('ACA Draft Creation Error: ' . $e->getMessage());
error_log('ACA Draft Creation Stack Trace: ' . $e->getTraceAsString());
```

### Frontend Error Handling
```typescript
try {
    const result = await apiCall();
    // Success handling
} catch (error) {
    console.error('Error:', error);
    addToast({ message: errorMessage, type: 'error' });
}
```

### Common Issues & Solutions

1. **Gemini API Errors**
   - Check API key validity
   - Verify model name (`gemini-2.0-flash`)
   - Check request headers (`x-goog-api-key`)

2. **WordPress Integration Issues**
   - Verify REST API endpoints are registered
   - Check nonce verification
   - Ensure proper permissions

3. **Cache Issues**
   - Plugin version increment for cache busting
   - File modification time in asset URLs

---

## 🔒 Security Considerations

### Authentication
- WordPress nonce verification for all API calls
- Capability checks for admin access
- API key secure storage in WordPress options

### Data Sanitization
```php
$title = sanitize_text_field($params['title']);
$content = wp_kses_post($params['content']);
```

### API Security
- Rate limiting considerations
- Input validation
- Error message sanitization

---

## 📊 Performance Optimizations

### Frontend
- React component memoization where needed
- Efficient state updates
- Lazy loading for large datasets

### Backend
- Database query optimization
- Caching for frequently accessed data
- Efficient WordPress post creation

### AI API
- Request batching where possible
- Timeout configurations (60 seconds)
- Error retry mechanisms

---

## 🚀 Deployment & Updates

### Plugin Packaging
```bash
# Build and package
npm run build
cp dist/assets/* admin/
zip -r ai-content-agent.zip ai-content-agent-plugin/ -x "*/node_modules/*" "*/.git/*" "*/dist/*"
```

### Version Management
- Update version in `ai-content-agent.php`
- Update `ACA_VERSION` constant
- Increment for cache busting

### WordPress Installation
1. Upload zip file to WordPress
2. Activate plugin
3. Configure settings (Gemini API key)
4. Run style analysis
5. Start generating content

---

## 🔍 Troubleshooting Guide

### Common Error Messages

1. **"Failed to load resource: 500"**
   - Check PHP error logs
   - Verify database tables exist
   - Check Gemini API configuration

2. **"Missing required parameters"**
   - Check API call parameter structure
   - Verify frontend-backend parameter matching

3. **"Critical error on this website"**
   - PHP fatal error - check error logs
   - Memory limit issues
   - Plugin conflicts

### Debug Steps

1. **Enable WordPress Debug**
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

2. **Check Browser Console**
   - Network tab for API calls
   - Console for JavaScript errors
   - Response inspection

3. **PHP Error Logs**
   - Location: `/wp-content/debug.log`
   - Look for ACA prefixed errors

---

## 📈 Future Development Areas

### Planned Enhancements
1. **Multi-language Support**
2. **Advanced SEO Features**
3. **Content Templates**
4. **Bulk Operations**
5. **Analytics Integration**
6. **Custom Post Types Support**

### Technical Debt
1. **Type Safety Improvements**
2. **Error Boundary Implementation**
3. **Performance Monitoring**
4. **Unit Test Coverage**

---

## 🤝 Agent Collaboration Notes

### For Future AI Agents

When working on this plugin:

1. **Always check current version** in `ai-content-agent.php`
2. **Run build process** after frontend changes
3. **Update version number** for cache busting
4. **Test both frontend and backend** functionality
5. **Check WordPress error logs** for PHP issues
6. **Verify API parameter structures** match between frontend/backend

### Key Files to Understand First
1. `App.tsx` - Main application logic
2. `class-aca-rest-api.php` - Backend API endpoints
3. `wordpressApi.ts` - Frontend API wrapper
4. `types.ts` - TypeScript definitions

### Testing Workflow
1. Install plugin in WordPress
2. Configure Gemini API key
3. Test style analysis
4. Test idea generation
5. Test draft creation
6. Verify WordPress integration

---

## 📝 Change Log Summary

### Version 1.0.9 (Current)
- Fixed activity logs API parameter mismatch
- Enhanced error handling for create draft
- Improved response handling with fallback
- Added comprehensive debug logging

### Version 1.0.8
- Complete WordPress integration for drafts
- Added categories and tags support
- Internal linking implementation
- SEO metadata integration

### Version 1.0.7
- Activity logs endpoint fixes
- Better error handling

### Version 1.0.6
- Gemini API model updates
- WordPress content analysis
- Style guide improvements

### Earlier Versions
- Initial plugin structure
- React frontend implementation
- Basic AI integration
- WordPress admin integration

---

*This documentation should be updated whenever significant changes are made to the plugin architecture or functionality.*