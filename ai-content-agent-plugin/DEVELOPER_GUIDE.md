# Developer Guide - AI Content Agent (ACA) Plugin

This comprehensive guide covers development, deployment, and maintenance procedures for the AI Content Agent (ACA) WordPress plugin.

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
│   ├── ai-content-agent-v2.0.3-*.zip  # Latest release
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
- **Latest Version**: v2.0.4
- **Release File**: `ai-content-agent-v2.0.4-console-errors-fixed.zip`
- **Status**: Production ready with complete license management and enhanced UX
- **Key Features**: Smart license validation, toast notifications, backend exception handling, UX improvements, console error fixes

### Release Process

#### 1. Version Update
```bash
# Update version in package.json
npm version patch  # or minor/major

# Update WordPress plugin header
# In ai-content-agent.php:
# Version: 2.0.3
# define('ACA_VERSION', '2.0.3');
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
mv releases/ai-content-agent-v2.0.2-*.zip releases/archive/

# Create new release zip
zip -r ai-content-agent-v2.0.3-asset-deployment-and-cache-invalidation.zip \
  ai-content-agent-plugin/ \
  -x "*/node_modules/*" "*/.git/*" "*/dist/*" \
     "*/package-lock.json" "*/.gitignore" "*/.DS_Store" \
     "*/tsconfig.json" "*/vite.config.ts"

# Move to releases directory
mv ai-content-agent-v2.0.3-*.zip releases/
```

#### 5. Git Operations
```bash
# Commit all changes
git add .
git commit -m "🚀 RELEASE v2.0.3: ASSET DEPLOYMENT & CACHE INVALIDATION"

# Push to main branch
git push origin main

# Create and push tag
git tag v2.0.3
git push origin v2.0.3
```

### Release Naming Convention
```
ai-content-agent-v{MAJOR}.{MINOR}.{PATCH}-{DESCRIPTION}.zip

Examples:
- ai-content-agent-v2.0.3-asset-deployment-and-cache-invalidation.zip
- ai-content-agent-v2.0.2-build-fix-and-proper-deployment.zip
- ai-content-agent-v2.0.1-icon-contrast-fixes-and-improvements.zip
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

### Manual Testing Checklist

#### Plugin Activation
- [ ] Plugin activates without errors
- [ ] Admin menu appears correctly
- [ ] No JavaScript console errors
- [ ] All assets load properly

#### Core Functionality
- [ ] Settings page loads and saves correctly
- [ ] API keys can be configured
- [ ] Style guide creation works
- [ ] Idea generation functions
- [ ] Draft creation with retry logic works
- [ ] Error handling displays user-friendly messages

#### AI Service Testing
- [ ] Test Gemini API overload scenarios
- [ ] Verify automatic model fallback
- [ ] Check retry logic with network issues
- [ ] Validate error message display

#### WordPress Integration
- [ ] Compatible with latest WordPress version
- [ ] Works with common themes
- [ ] Integrates with SEO plugins (Yoast, RankMath)
- [ ] No conflicts with other plugins

### Error Scenarios to Test
1. **API Overload (503 Error)**
   - Expected: Automatic retry with fallback model
   - User sees: "🤖 AI service is temporarily overloaded..."

2. **Network Timeout**
   - Expected: Retry with exponential backoff
   - User sees: "⏱️ Request timed out..."

3. **Invalid API Key**
   - Expected: Clear error message
   - User sees: "🔑 AI API key is not configured..."

4. **Missing Style Guide**
   - Expected: Helpful guidance
   - User sees: "📋 Style guide is required..."

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