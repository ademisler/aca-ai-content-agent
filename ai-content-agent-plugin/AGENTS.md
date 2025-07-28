# AI Content Agent (ACA) - Agent Documentation

## 📋 Plugin Overview

**AI Content Agent** is a comprehensive WordPress plugin that automates content creation using Google Gemini AI. It provides a complete workflow from idea generation to published posts with WordPress integration.

### Current Version: 1.3.2
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
   - Smart content formatting (Markdown to HTML)

3. **AI Integration**
   - Google Gemini 2.0 Flash model
   - Content generation and analysis
   - Style guide creation
   - Advanced error recovery

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
├── vite.config.ts                  # Build configuration
├── README.md                       # User documentation
├── AGENTS.md                       # This file - Agent documentation
└── README.txt                      # WordPress plugin readme
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
   - Creates tags and selects categories intelligently
   - Outputs proper HTML format (not Markdown)

3. **Idea Generation** (`call_gemini_generate_ideas`)
   - Generates content ideas based on style guide
   - Avoids duplicate titles
   - SEO-optimized suggestions

### Recent AI Enhancements (v1.3.x)

#### v1.3.2 - Content Formatting
- **HTML Output**: AI now generates proper HTML instead of Markdown
- **Fallback Parser**: Automatic Markdown to HTML conversion for legacy responses
- **Better Prompts**: Enhanced AI instructions for clean HTML output

#### v1.3.1 - Response Parsing
- **JSON Cleaning**: Smart JSON response cleaning and parsing
- **Error Recovery**: Fallback mechanisms for malformed JSON
- **Enhanced Logging**: Detailed response debugging

#### v1.3.0 - Category Management
- **Smart Categories**: AI selects from existing WordPress categories
- **No Fatal Errors**: Removed deprecated `wp_create_category()` function
- **Intelligent Selection**: AI chooses most appropriate existing categories

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
// Enqueue compiled assets with cache busting
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
   - AI generates full blog post content in HTML format
   - System fetches existing categories for AI selection
   - WordPress draft post is created with:
     - Full content (800-1500 words) in proper HTML
     - SEO metadata (title, description, keywords)
     - Categories (selected from existing ones)
     - Tags (newly generated)
     - Internal links to existing posts
     - Featured image (if configured)

4. **Publishing**
   - User can edit draft in WordPress
   - Publish manually or schedule

---

## 🐛 Error Handling & Debugging

### PHP Error Logging
```php
error_log('ACA DEBUG: Starting create_draft_from_idea for ID: ' . $idea_id);
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

### Recent Error Handling Improvements

#### v1.3.2 - Content Formatting
```php
// Markdown to HTML conversion
private function markdown_to_html($content) {
    // Convert headings: ## → <h2>, ### → <h3>
    $content = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $content);
    $content = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $content);
    
    // Convert bold: **text** → <strong>text</strong>
    $content = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $content);
    
    // Convert links: [text](url) → <a href="url">text</a>
    $content = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $content);
    
    // Convert lists and paragraphs...
}
```

#### v1.3.1 - JSON Response Cleaning
```php
private function clean_ai_json_response($response) {
    // Remove markdown code blocks
    $response = preg_replace('/^```json\s*/m', '', $response);
    $response = preg_replace('/\s*```$/m', '', $response);
    
    // Fix trailing commas
    $response = preg_replace('/,\s*}/', '}', $response);
    $response = preg_replace('/,\s*]/', ']', $response);
    
    // More cleaning...
}
```

#### v1.3.0 - Category Management
```php
// Smart category selection instead of creation
$existing_categories = get_categories(array(
    'hide_empty' => false,
    'number' => 20
));

// AI selects from existing categories
foreach ($draft_data['categoryIds'] as $category_id) {
    $category = get_category($category_id);
    if ($category && !is_wp_error($category)) {
        $category_ids[] = $category_id;
    }
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

3. **Content Formatting Issues**
   - Plugin automatically converts Markdown to HTML
   - Check AI response format in logs
   - Verify HTML output in WordPress

4. **Category/Tag Issues**
   - Plugin uses existing categories intelligently
   - Check category selection in debug logs
   - Verify WordPress categories admin

5. **Cache Issues**
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
$safe_category_name = sanitize_text_field($category_name);
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
- Timeout configurations (90 seconds)
- Error retry mechanisms
- Smart response parsing

---

## 🚀 Deployment & Updates

### Plugin Packaging
```bash
# Build and package
npm run build
cp dist/assets/* admin/
zip -r ai-content-agent-v1.3.2-markdown-fix.zip ai-content-agent-plugin/ -x "*/node_modules/*" "*/.git/*" "*/dist/*"
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

4. **"Invalid JSON response from AI service"**
   - Check AI response in logs
   - JSON cleaning will attempt automatic fix
   - Verify Gemini API key and model

5. **"Call to undefined function wp_create_category()"**
   - Fixed in v1.3.0
   - Update to latest version
   - Uses existing categories now

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

4. **Plugin Debug Logs**
   - Extensive `ACA DEBUG:` messages
   - Step-by-step execution tracking
   - AI response analysis

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

1. **Always check current version** in `ai-content-agent.php` (Currently 1.3.2)
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

### Recent Critical Fixes (v1.3.x)

#### v1.3.2 - Content Formatting
- **Problem**: AI generated Markdown, WordPress needed HTML
- **Solution**: Added Markdown to HTML converter + better AI prompts
- **Files**: `class-aca-rest-api.php` (markdown_to_html function)

#### v1.3.1 - JSON Parsing
- **Problem**: AI responses had invalid JSON syntax
- **Solution**: Smart JSON cleaning with fallback parsing
- **Files**: `class-aca-rest-api.php` (clean_ai_json_response function)

#### v1.3.0 - Category Management
- **Problem**: `wp_create_category()` function was deprecated/undefined
- **Solution**: Smart selection from existing categories
- **Files**: `class-aca-rest-api.php` (category management logic)

### Testing Workflow
1. Install plugin in WordPress
2. Configure Gemini API key
3. Test style analysis
4. Test idea generation
5. Test draft creation (check for proper HTML formatting)
6. Verify WordPress integration (categories, tags, content display)
7. Check error logs for any issues

---

## 📝 Change Log Summary

### Version 1.3.2 (Current) - Content Formatting Fix
- 🎨 **FIXED**: Markdown formatting in generated content
- ✅ **Added**: Automatic Markdown to HTML conversion
- ✅ **Enhanced**: Content display in WordPress
- ✅ **Improved**: AI prompt instructions for HTML output
- ✅ **Added**: Fallback Markdown parser for legacy content

### Version 1.3.1 - JSON Response Fix
- 🔧 **FIXED**: Invalid JSON response from AI service
- ✅ **Added**: Smart JSON cleaning and parsing
- ✅ **Enhanced**: Error handling for AI responses
- ✅ **Improved**: Debug logging for troubleshooting

### Version 1.3.0 - Category Management Fix
- 🎯 **FIXED**: Fatal error with wp_create_category() function
- ✅ **Added**: Smart category selection from existing categories
- ✅ **Enhanced**: Category management workflow
- ✅ **Improved**: AI category selection process

### Version 1.2.2 - Comprehensive Debug System
- 🔍 **Added**: Extensive debug logging system
- ✅ **Enhanced**: Error tracking and reporting
- ✅ **Improved**: Fatal error handling

### Version 1.2.1 - Error Handling Improvements
- ✅ **Fixed**: Activity logs API parameter mismatch
- ✅ **Enhanced**: Error handling for draft creation
- ✅ **Improved**: Response handling with fallback mechanisms

### Version 1.2.0 - WordPress Integration
- ✅ **Added**: Complete WordPress integration for drafts
- ✅ **Added**: Categories and tags support
- ✅ **Added**: Internal linking implementation
- ✅ **Added**: SEO metadata integration

### Version 1.1.0 - AI Model Update
- ✅ **Updated**: Gemini API model to 2.0 Flash
- ✅ **Enhanced**: WordPress content analysis
- ✅ **Improved**: Style guide generation

### Version 1.0.0 - Initial Release
- ✅ **Initial**: Plugin structure and core functionality
- ✅ **Added**: React frontend implementation
- ✅ **Added**: Basic AI integration
- ✅ **Added**: WordPress admin integration

---

*This documentation should be updated whenever significant changes are made to the plugin architecture or functionality.*