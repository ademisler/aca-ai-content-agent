# AI Content Agent - Debugging Guide

## Error Analysis

The error `at Ik (index.js?ver=1.6.2-1753793829:9:40628)` indicates a JavaScript error in the minified React application. This guide provides steps to identify and resolve the issue.

## Recent Changes Made

### 1. Enhanced Error Handling
- Added `ErrorBoundary` component to catch React errors gracefully
- Added global error handlers for unhandled errors and promise rejections
- Improved WordPress data validation with detailed error logging

### 2. Source Maps
- Enabled source maps in Vite configuration for better debugging
- Built application with source maps included

### 3. Better Logging
- Enhanced error messages with context information
- Added validation for required WordPress data properties

## Debugging Steps

### Step 1: Check Browser Console
1. Open WordPress admin and navigate to AI Content Agent
2. Open browser Developer Tools (F12)
3. Check the Console tab for detailed error messages

### Step 2: Use Debug Script
1. Copy the contents of `debug-script.js`
2. Paste it into the browser console on the AI Content Agent admin page
3. Review the debug output for clues about the issue

### Step 3: Check WordPress Data
Verify that WordPress is properly providing data to the React app:
```javascript
console.log('WordPress data:', window.acaData);
```

Expected structure:
```javascript
{
  nonce: "...",
  api_url: "https://yoursite.com/wp-json/aca/v1/",
  admin_url: "https://yoursite.com/wp-admin/",
  plugin_url: "https://yoursite.com/wp-content/plugins/ai-content-agent-plugin/"
}
```

### Step 4: Check Network Requests
1. Go to Network tab in Developer Tools
2. Reload the page
3. Look for failed requests to:
   - `admin/js/index.js`
   - WordPress REST API endpoints (`/wp-json/aca/v1/`)

### Step 5: Verify File Integrity
Check that the built files are properly deployed:
```bash
ls -la admin/js/
ls -la admin/css/
```

Should show:
- `index.js` (React application)
- `index.js.map` (Source map for debugging)
- `index.css` (Styles)

## Common Issues and Solutions

### Issue 1: WordPress Data Not Available
**Symptoms:** Error about `window.acaData` not being available
**Solution:** 
- Check that the plugin is properly activated
- Verify you're on the correct admin page
- Check for conflicts with other plugins

### Issue 2: REST API Not Working
**Symptoms:** Network errors when loading data
**Solution:**
- Check WordPress REST API is enabled
- Verify nonce is valid
- Check for server-side errors in WordPress error log

### Issue 3: React Build Issues
**Symptoms:** JavaScript errors in the application
**Solution:**
- Rebuild the application: `npm run build`
- Copy files to admin directory
- Clear browser cache

### Issue 4: Plugin Conflicts
**Symptoms:** Errors only occur with other plugins active
**Solution:**
- Deactivate other plugins temporarily
- Check for JavaScript conflicts
- Look for console errors from other plugins

## Rebuilding the Application

If you need to rebuild the application:

```bash
# Install dependencies
npm install

# Build for production
npm run build

# Copy built files to admin directory
cp dist/assets/index-*.js admin/js/index.js
cp dist/assets/index-*.js.map admin/js/index.js.map
cp index.css admin/css/index.css
```

## Advanced Debugging

### Using Source Maps
With source maps enabled, browser errors will show the original TypeScript/React code location instead of minified code.

### React Developer Tools
Install React Developer Tools browser extension to inspect React component state and props.

### WordPress Debug Mode
Enable WordPress debug mode in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

## Getting Help

When reporting issues, please include:
1. Browser console output (with debug script results)
2. WordPress version and active plugins
3. Server environment (PHP version, etc.)
4. Steps to reproduce the error
5. Any recent changes to the site

## Error Boundary

The application now includes an Error Boundary that will:
- Catch React errors gracefully
- Display a user-friendly error message
- Provide error details for debugging
- Offer a "Refresh Page" button

If you see the Error Boundary message, check the browser console for detailed error information.