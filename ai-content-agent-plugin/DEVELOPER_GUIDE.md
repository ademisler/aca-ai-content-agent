# Developer Guide - AI Content Agent Plugin

This comprehensive guide covers development, deployment, and maintenance procedures for the AI Content Agent WordPress plugin.

## 📋 Table of Contents

1. [Development Environment](#development-environment)
2. [Build Process](#build-process)
3. [Release Management](#release-management)
4. [Documentation Standards](#documentation-standards)
5. [Testing Procedures](#testing-procedures)
6. [Deployment Checklist](#deployment-checklist)

## 🛠️ Development Environment

### Prerequisites
- **Node.js**: 18+ (for React frontend)
- **PHP**: 7.4+ (for WordPress backend)
- **Composer**: For PHP dependencies (optional)
- **Git**: Version control
- **WordPress**: 5.0+ (for testing)

### Setup Instructions
```bash
# Clone repository
git clone https://github.com/ademisler/aca-ai-content-agent.git
cd ai-content-agent-plugin

# Install frontend dependencies
npm install

# Install PHP dependencies (optional, plugin includes placeholders)
composer install --no-dev --optimize-autoloader

# Start development server
npm run dev
```

### Directory Structure
```
ai-content-agent-plugin/
├── admin/                     # Compiled assets
│   ├── assets/               # Build output (JS files)
│   ├── css/index.css         # Fallback CSS
│   └── js/index.js           # Fallback JavaScript
├── components/               # React components
├── services/                 # Frontend services
├── includes/                 # PHP backend classes
├── releases/                 # Release management
│   ├── ai-content-agent-v1.6.8-*.zip  # Latest release
│   └── archive/              # Previous versions
├── vendor/                   # PHP dependencies (placeholders)
├── dist/                     # Vite build output
└── node_modules/             # Node.js dependencies
```

## 🔧 Build Process

### Development Build
```bash
# Start development server with hot reload
npm run dev

# Build for development (with source maps)
npm run build:dev
```

### Production Build

#### Option 1: Use WordPress Build Script (Recommended)
```bash
# Build and copy to both locations automatically
npm run build:wp
```

#### Option 2: Manual Build Process
```bash
# Build for production
npm run build

# CRITICAL: Copy assets to BOTH locations for WordPress compatibility
# 1. Copy to admin/assets/ (primary location)
cp dist/assets/index-*.js admin/assets/

# 2. Copy to admin/js/index.js (fallback location)
cp dist/assets/index-*.js admin/js/index.js
```

### ⚠️ IMPORTANT: Asset Management
WordPress plugin uses a **dual-asset system**:

1. **Primary**: `admin/assets/index-[hash].js` - Main build file with cache busting
2. **Fallback**: `admin/js/index.js` - Fallback file for compatibility

**BOTH files must be updated** when making style changes or any frontend modifications:

```bash
# After any style changes, ALWAYS run:
npm run build
rm admin/assets/index-*.js  # Remove old files
cp dist/assets/index-*.js admin/assets/  # Copy new build
cp dist/assets/index-*.js admin/js/index.js  # Update fallback
```

**Why Both Files Are Needed:**
- `admin/assets/` - Used when build files are detected (cache busting)
- `admin/js/` - Used as fallback when assets directory is not available
- Plugin automatically selects the most recent file by modification time

### Build Configuration
The plugin uses **Vite** with the following key configurations:

```typescript
// vite.config.ts key settings
export default defineConfig({
  build: {
    target: 'es2020',
    minify: false, // Disabled to prevent temporal dead zone issues
    rollupOptions: {
      output: {
        format: 'iife', // Better variable isolation
        name: 'ACAApp',
        entryFileNames: 'assets/[name]-[hash].js',
      }
    },
    sourcemap: false, // Disabled for production
    outDir: 'dist',
  }
});
```

### Key Build Features
- **No Minification**: Prevents variable hoisting issues
- **IIFE Format**: Better variable isolation
- **Hash-based Filenames**: Automatic cache busting
- **ES2020 Target**: Modern browser compatibility

## 📦 Release Management

### Current Release Information
- **Latest Version**: v1.8.0
- **Release File**: `ai-content-agent-v1.8.0-comprehensive-feature-enhancements-and-improvements.zip`
- **Status**: Production ready with comprehensive feature enhancements and improvements
- **Key Features**: Author updates, dashboard optimization, feature verification, quality assurance

### Release Process

#### 1. Version Update
```bash
# Update version in package.json
npm version patch  # or minor/major

# Update WordPress plugin header
# In ai-content-agent.php:
# Version: 1.6.8
# define('ACA_VERSION', '1.6.8');
```

#### 2. Build and Test
```bash
# Clean build
npm run build

# CRITICAL: Copy assets to BOTH locations
# Remove old files first
rm admin/assets/index-*.js
rm admin/js/index.js

# Copy new build to both locations
cp dist/assets/index-*.js admin/assets/
cp dist/assets/index-*.js admin/js/index.js

# Test functionality
npm run test  # if tests are available
```

#### 3. Documentation Update
```bash
# Update all documentation files
# - README.md
# - CHANGELOG.md
# - RELEASES.md
# - README.txt
# - DEVELOPER_GUIDE.md (this file)
```

#### 4. Create Release Package
```bash
# Move previous release to archive
mv releases/ai-content-agent-v1.7.0-*.zip releases/archive/

# Create new release zip
zip -r ai-content-agent-v1.8.0-comprehensive-feature-enhancements-and-improvements.zip \
  ai-content-agent-plugin/ \
  -x "*/node_modules/*" "*/.git/*" "*/dist/*" \
     "*/package-lock.json" "*/.gitignore" "*/.DS_Store" \
     "*/tsconfig.json" "*/vite.config.ts"

# Move to releases directory
mv ai-content-agent-v1.8.0-*.zip releases/
```

#### 5. Git Operations
```bash
# Commit all changes
git add .
git commit -m "🚀 RELEASE v1.8.0: COMPREHENSIVE FEATURE ENHANCEMENTS & IMPROVEMENTS"

# Push to main branch
git push origin main

# Create and push tag
git tag v1.8.0
git push origin v1.8.0
```

### Release Naming Convention
```
ai-content-agent-v{MAJOR}.{MINOR}.{PATCH}-{DESCRIPTION}.zip

Examples:
- ai-content-agent-v1.8.0-comprehensive-feature-enhancements-and-improvements.zip
- ai-content-agent-v1.7.0-comprehensive-feature-enhancements-and-improvements.zip
- ai-content-agent-v1.6.8-gemini-api-retry-logic-and-improved-error-handling.zip
```

## 📚 Documentation Standards

### File Structure
```
├── README.md                 # Main project documentation
├── CHANGELOG.md             # Detailed version history
├── RELEASES.md              # Release management info
├── README.txt               # WordPress plugin readme
├── DEVELOPER_GUIDE.md       # This file
├── GOOGLE_SEARCH_CONSOLE_SETUP.md
├── SEO_INTEGRATION_GUIDE.md
└── AI_IMAGE_GENERATION_SETUP.md
```

### Documentation Update Checklist
- [ ] Update version badges and references
- [ ] Add new features to feature lists
- [ ] Update installation instructions
- [ ] Add changelog entries
- [ ] Update troubleshooting sections
- [ ] Verify all links and references
- [ ] Check for consistency across all files

## 🧪 Testing Procedures

### Automated Testing Setup

#### Unit Testing with Vitest
```bash
# Install dependencies
npm install

# Run unit tests
npm run test

# Run tests with coverage
npm run test:coverage

# Run tests in watch mode
npm run test:watch

# Run tests with UI
npm run test:ui
```

#### End-to-End Testing with Playwright
```bash
# Install Playwright browsers
npx playwright install

# Run E2E tests
npm run test:e2e

# Run E2E tests in headed mode
npx playwright test --headed

# Run specific test file
npx playwright test tests/e2e/settings.spec.ts

# Generate test report
npx playwright show-report
```

#### Linting and Type Checking
```bash
# Run ESLint
npm run lint

# Fix linting issues
npm run lint:fix

# Check TypeScript types
npm run type-check

# Check code formatting
npm run format:check

# Format code
npm run format
```

#### Pre-commit Hooks
The project uses Husky for pre-commit hooks that automatically run:
- TypeScript type checking
- ESLint linting
- Prettier formatting
- Staged file linting

### Manual Testing Checklist

#### Plugin Activation & Setup
- [ ] Plugin activates without errors
- [ ] Admin menu appears correctly
- [ ] No JavaScript console errors on load
- [ ] All assets load properly (CSS, JS)
- [ ] Database tables created successfully
- [ ] Default settings initialized correctly
- [ ] Security headers applied correctly

#### Core Functionality Testing
- [ ] Settings page loads and saves correctly
- [ ] API keys can be configured and validated
- [ ] Style guide creation and analysis works
- [ ] Idea generation functions with retry logic
- [ ] Draft creation with error handling works
- [ ] Publishing workflow functions correctly
- [ ] Activity logs are created properly

#### Performance Testing
- [ ] Bundle size is optimized (<300KB)
- [ ] Page load times are acceptable (<2s)
- [ ] Memory usage stays within limits
- [ ] Database queries are optimized
- [ ] Caching layer functions correctly
- [ ] Asset loading is optimized

#### Security Testing
- [ ] Nonce validation works on all endpoints
- [ ] Capability checks prevent unauthorized access
- [ ] Input sanitization prevents XSS
- [ ] SQL injection prevention works
- [ ] Rate limiting functions properly
- [ ] Security headers are present

#### Accessibility Testing
- [ ] WCAG 2.1 AA compliance verified
- [ ] Keyboard navigation works throughout
- [ ] Screen reader compatibility tested
- [ ] High contrast mode supported
- [ ] Focus management works in modals
- [ ] Skip links function properly

#### Mobile Responsiveness Testing
- [ ] Touch targets meet minimum size (44px)
- [ ] Responsive design works on all breakpoints
- [ ] Touch gestures function correctly
- [ ] Mobile keyboard behavior optimized
- [ ] Performance acceptable on mobile devices

#### AI Service Testing
- [ ] Test Gemini API overload scenarios
- [ ] Verify automatic retry with exponential backoff
- [ ] Check fallback strategies (cache, mock, alternative)
- [ ] Validate user-friendly error messages
- [ ] Test rate limiting protection
- [ ] Verify API key validation

#### WordPress Integration Testing
- [ ] Compatible with WordPress 6.8+
- [ ] Works with popular themes (Twenty Twenty-Four, Astra, etc.)
- [ ] Integrates with SEO plugins (Yoast, RankMath, AIOSEO)
- [ ] No conflicts with common plugins
- [ ] Multisite compatibility verified
- [ ] PHP 8.2 compatibility confirmed

#### Component Architecture Testing
- [ ] React.lazy() components load correctly
- [ ] Context API state management works
- [ ] Component breakdown maintains functionality
- [ ] Settings components render properly
- [ ] Error boundaries catch and display errors

### Comprehensive Error Scenarios

#### API Error Handling
1. **Service Overload (503 Error)**
   - Expected: Automatic retry with exponential backoff
   - Fallback: Use cached data if available
   - User sees: "🤖 AI service is temporarily overloaded. Using cached data..."

2. **Network Timeout**
   - Expected: Retry with increasing delays (1s, 2s, 4s)
   - Fallback: Graceful degradation to mock data
   - User sees: "⏱️ Request timed out. Retrying..."

3. **Rate Limiting (429 Error)**
   - Expected: Wait and retry with longer delays
   - Fallback: Queue requests for later processing
   - User sees: "🚦 Rate limit reached. Please wait..."

4. **Authentication Error (401/403)**
   - Expected: Clear error message with solution
   - Fallback: Disable AI features gracefully
   - User sees: "🔑 Authentication failed. Please check your API keys in Settings."

5. **Invalid Request (400 Error)**
   - Expected: Input validation and sanitization
   - Fallback: Use default parameters
   - User sees: "⚠️ Invalid request parameters. Using defaults..."

#### WordPress Integration Errors
1. **Plugin Conflicts**
   - Expected: Graceful degradation
   - Fallback: Disable conflicting features
   - User sees: "⚡ Plugin conflict detected. Some features disabled."

2. **Database Connection Issues**
   - Expected: Use transient cache
   - Fallback: Read-only mode
   - User sees: "💾 Database temporarily unavailable. Running in read-only mode."

3. **Permission Errors**
   - Expected: Clear capability requirements
   - Fallback: Show appropriate message
   - User sees: "🔒 Insufficient permissions. Contact your administrator."

### Performance Testing Procedures

#### Bundle Analysis
```bash
# Generate bundle analysis
npm run build:analyze

# Check bundle size
ls -la dist/assets/

# Verify code splitting
grep -r "React.lazy" components/
```

#### Core Web Vitals Testing
```bash
# Use Lighthouse CLI
npm install -g lighthouse
lighthouse http://your-site.com/wp-admin/admin.php?page=ai-content-agent

# Check specific metrics
# - Largest Contentful Paint (LCP): < 2.5s
# - First Input Delay (FID): < 100ms
# - Cumulative Layout Shift (CLS): < 0.1
```

#### Memory Usage Testing
```php
// Add to wp-config.php for testing
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('SCRIPT_DEBUG', true);
define('SAVEQUERIES', true);

// Monitor memory usage
echo "Memory usage: " . memory_get_usage(true) / 1024 / 1024 . " MB\n";
echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB\n";
```

### Security Testing Procedures

#### Nonce Validation Testing
```javascript
// Test invalid nonce
fetch('/wp-json/aca/v1/settings', {
  method: 'POST',
  headers: {
    'X-WP-Nonce': 'invalid-nonce'
  }
});
// Expected: 403 Forbidden
```

#### SQL Injection Testing
```php
// Test with malicious input
$malicious_input = "'; DROP TABLE wp_posts; --";
// Should be sanitized by ACA_Security::sanitize_input()
```

#### XSS Prevention Testing
```javascript
// Test with malicious script
const maliciousInput = '<script>alert("XSS")</script>';
// Should be escaped by wp_kses_post() or similar
```

## 📚 API Documentation

### REST API Endpoints

#### Settings API (`ACA_Settings_API`)

##### GET `/wp-json/aca/v1/settings`
Get current plugin settings.

**Headers:**
- `X-WP-Nonce`: WordPress REST API nonce

**Response:**
```json
{
  "mode": "manual|semi-auto|full-auto",
  "autoPublish": boolean,
  "geminiApiKey": "string",
  "imageSourceProvider": "pexels|unsplash|pixabay|google-ai",
  "seoPlugin": "none|rankmath|yoast|aioseo",
  "semiAutoIdeaFrequency": "daily|weekly|monthly",
  "fullAutoPostCount": number,
  "contentAnalysisFrequency": "daily|weekly|monthly"
}
```

##### POST `/wp-json/aca/v1/settings`
Save plugin settings with validation and sanitization.

**Headers:**
- `X-WP-Nonce`: WordPress REST API nonce
- `Content-Type`: application/json

**Request Body:**
```json
{
  "mode": "manual",
  "autoPublish": false,
  "geminiApiKey": "your-api-key",
  "imageSourceProvider": "pexels",
  "seoPlugin": "rankmath"
}
```

**Response:**
```json
{
  "success": true,
  "settings": { /* updated settings */ }
}
```

##### GET `/wp-json/aca/v1/style-guide`
Get current style guide configuration.

**Response:**
```json
{
  "tone": "Professional and informative",
  "style": "Clear and concise writing",
  "audience": "Business professionals",
  "topics": ["Content Marketing", "SEO"],
  "keywords": ["content creation", "SEO optimization"],
  "guidelines": "Write in an engaging, helpful tone...",
  "lastAnalyzed": "2024-01-15T10:30:00Z"
}
```

##### POST `/wp-json/aca/v1/style-guide/analyze`
Analyze existing content to generate style guide.

**Headers:**
- `X-WP-Nonce`: WordPress REST API nonce

**Response:**
```json
{
  "success": true,
  "analysis": {
    "tone": "Detected tone",
    "style": "Detected style",
    "audience": "Target audience",
    "recommendations": ["guideline1", "guideline2"]
  }
}
```

#### Content API (`ACA_Content_API`)

##### GET `/wp-json/aca/v1/ideas`
Get all content ideas with filtering support.

**Query Parameters:**
- `status`: "active|archived" (optional)
- `limit`: number (default: 50)
- `offset`: number (default: 0)

**Response:**
```json
[
  {
    "id": 123,
    "title": "How to Optimize WordPress Performance",
    "status": "active",
    "source": "ai|search-console",
    "createdAt": "2024-01-15T10:30:00Z",
    "tags": ["performance", "wordpress"]
  }
]
```

##### POST `/wp-json/aca/v1/ideas/generate`
Generate new content ideas using AI.

**Request Body:**
```json
{
  "count": 5,
  "auto": false
}
```

**Response:**
```json
[
  {
    "id": 124,
    "title": "Generated idea title",
    "status": "active",
    "source": "ai",
    "createdAt": "2024-01-15T10:35:00Z"
  }
]
```

##### POST `/wp-json/aca/v1/drafts/create`
Create a draft from an idea.

**Request Body:**
```json
{
  "ideaId": 123,
  "customTitle": "Optional custom title"
}
```

**Response:**
```json
{
  "success": true,
  "draft": {
    "id": 456,
    "title": "Draft title",
    "content": "Generated content",
    "status": "draft",
    "postId": 789
  }
}
```

#### Security API (`ACA_Security`)

##### Rate Limiting
All API endpoints are protected by rate limiting:
- Default: 60 requests per hour per user/IP
- Configurable per endpoint
- Returns 429 status when exceeded

##### Authentication & Authorization
- **Nonce Validation**: All POST requests require valid WordPress nonce
- **Capability Checks**: User must have appropriate WordPress capabilities
- **Input Sanitization**: All inputs are sanitized based on data type
- **SQL Injection Prevention**: All database queries use prepared statements

### Performance API (`ACA_Performance`)

#### Caching Layer
```php
// Get cached data
$data = ACA_Performance::get_cache('cache_key', $default_value);

// Set cache with expiration
ACA_Performance::set_cache('cache_key', $data, 3600);

// Delete cache entry
ACA_Performance::delete_cache('cache_key');

// Flush all cache
ACA_Performance::flush_cache();
```

#### Database Optimization
```php
// Get cached query results
$results = ACA_Performance::get_cached_query(
    "SELECT * FROM {$wpdb->prefix}aca_ideas WHERE status = %s",
    'active_ideas',
    1800 // 30 minutes cache
);

// Get performance statistics
$stats = ACA_Performance::get_performance_stats();
```

### Error Handling API

#### ApiErrorHandler Class
```typescript
import { ApiErrorHandler, commonFallbackStrategies } from './utils/apiErrorHandler';

// Create error handler with custom config
const errorHandler = new ApiErrorHandler({
  maxRetries: 3,
  baseDelay: 1000,
  maxDelay: 10000
}, {
  enableReporting: true,
  reportToConsole: true,
  reportToServer: true
});

// Register fallback strategies
errorHandler.registerFallbackStrategy(
  '/wp-json/aca/v1/ideas',
  commonFallbackStrategies.cache('ideas_cache')
);

// Use with API calls
const result = await errorHandler.handleApiCall(
  () => fetch('/wp-json/aca/v1/ideas'),
  '/wp-json/aca/v1/ideas',
  {
    context: { user_id: 123 }
  }
);
```

#### User Feedback Manager
```typescript
import { userFeedback } from './utils/apiErrorHandler';

// Show different types of feedback
userFeedback.showError(apiError, 'Custom error message');
userFeedback.showSuccess('Operation completed successfully');
userFeedback.showWarning('Warning message', 'action_button');
userFeedback.showInfo('Information message');

// Get feedback history
const history = userFeedback.getFeedbackHistory();
```

### Component Architecture

#### State Management with Context API
```typescript
// AppContext usage
import { useAppContext, appActions } from './contexts/AppContext';

const MyComponent = () => {
  const { state, dispatch } = useAppContext();
  
  const updateIdeas = (ideas) => {
    dispatch(appActions.setIdeas(ideas));
  };
  
  return <div>{state.ideas.length} ideas</div>;
};
```

#### Settings Components
```typescript
// SettingsProvider usage
import { useSettings } from './components/settings/SettingsProvider';

const SettingsComponent = () => {
  const { settings, updateSetting, saveSettings, hasChanges } = useSettings();
  
  return (
    <div>
      <input 
        value={settings.geminiApiKey}
        onChange={(e) => updateSetting('geminiApiKey', e.target.value)}
      />
      <button onClick={saveSettings} disabled={!hasChanges}>
        Save Settings
      </button>
    </div>
  );
};
```

### Accessibility Utilities

#### Focus Management
```typescript
import { focusUtils, keyboardUtils, ariaUtils } from './utils/accessibility';

// Trap focus in modal
const cleanup = focusUtils.trapFocus(modalElement);

// Handle arrow key navigation
const handleKeyDown = (event) => {
  const newIndex = keyboardUtils.handleArrowNavigation(
    event,
    menuItems,
    currentIndex,
    (index) => selectItem(index)
  );
  setCurrentIndex(newIndex);
};

// Generate unique ARIA IDs
const labelId = ariaUtils.generateId('label');
const descriptionId = ariaUtils.generateId('description');
```

#### Screen Reader Support
```typescript
import { announceToScreenReader } from './utils/accessibility';

// Announce status changes
announceToScreenReader('Settings saved successfully', 'polite');
announceToScreenReader('Error occurred', 'assertive');
```

### Mobile Utilities

#### Touch and Responsive Design
```typescript
import { touchUtils, responsiveUtils, mobileUIUtils } from './utils/mobile';

// Detect touch device
if (touchUtils.isTouchDevice()) {
  // Apply touch-specific behavior
}

// Handle responsive breakpoints
responsiveUtils.onBreakpointChange((breakpoint) => {
  console.log('Breakpoint changed to:', breakpoint);
});

// Create mobile-friendly components
const button = mobileUIUtils.createTouchButton('Click me', handleClick);
const input = mobileUIUtils.createTouchInput('text', 'Enter text...');
```

### Performance Monitoring

#### Web Vitals and Performance Metrics
```typescript
import { performanceDashboard } from './utils/performance';

// Initialize performance monitoring
const monitor = performanceDashboard.initialize({
  enableWebVitals: true,
  enableBundleMonitoring: true,
  enableErrorMonitoring: true,
  onWebVital: (metric) => console.log('Web Vital:', metric),
  onError: (error) => console.error('Performance Error:', error)
});

// Get performance report
const report = monitor.getReport();
```

## 🚀 Deployment Checklist

### Pre-Deployment
- [ ] All tests pass
- [ ] Documentation updated
- [ ] Version numbers consistent
- [ ] Build assets copied correctly
- [ ] No development dependencies in release

### WordPress.org Submission (if applicable)
- [ ] README.txt follows WordPress standards
- [ ] Stable tag updated
- [ ] Tested up to latest WordPress version
- [ ] Screenshots updated
- [ ] Changelog entries added

### Production Deployment
- [ ] Backup current version
- [ ] Test in staging environment
- [ ] Monitor for errors after deployment
- [ ] Verify all functionality works
- [ ] Check error logs

## 🔍 Debugging and Troubleshooting

### Common Development Issues

#### Build Errors
```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf dist
npm run build
```

#### JavaScript Errors
- Check browser console for errors
- Verify asset files are loaded correctly
- Check WordPress `wp_enqueue_script` calls
- Validate React component structure

#### PHP Errors
- Check WordPress debug logs
- Verify PHP syntax
- Test API endpoints directly
- Check WordPress REST API responses

### Logging and Monitoring
```php
// Enable WordPress debugging
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);

// Plugin-specific logging
error_log('ACA Debug: ' . print_r($data, true));
```

## 🔧 Advanced Configuration

### Environment Variables
```bash
# .env file for development
GEMINI_API_KEY=your_api_key_here
NODE_ENV=development
```

### Custom Build Scripts
```json
// package.json scripts
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "build:wp": "vite build && cp dist/assets/index-*.js admin/assets/",
    "preview": "vite preview"
  }
}
```

### WordPress Constants
```php
// wp-config.php for development
define('ACA_DEBUG', true);
define('ACA_API_TIMEOUT', 120);
define('ACA_MAX_RETRIES', 3);
```

## 📊 Performance Considerations

### Bundle Size Optimization
- Unminified build: ~320KB (for stability)
- Minified would be: ~95KB (but causes TDZ issues)
- Assets cached by WordPress and browsers

### API Performance
- Timeout: 120 seconds (increased for complex content)
- Retry logic: 3 attempts with exponential backoff
- Model fallback: gemini-2.0-flash → gemini-1.5-pro
- Token limit: 4096 (increased for longer content)

### WordPress Performance
- Unique script handles prevent caching issues
- File modification time used for cache busting
- Error boundary prevents page crashes
- Graceful degradation when services unavailable

---

**Development Guidelines**: Always test thoroughly, document changes, and maintain backward compatibility. The plugin should work reliably even when AI services are temporarily unavailable. 🚀