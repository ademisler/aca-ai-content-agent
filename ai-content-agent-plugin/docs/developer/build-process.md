# Build Process Documentation

This document explains the build process, asset management, and deployment procedures for the AI Content Agent plugin.

## 🛠️ Build System Overview

### Technology Stack
- **Frontend**: React 18+ with TypeScript
- **Build Tool**: Vite 6.2.0
- **CSS**: Standard CSS with modern features
- **Target**: WordPress plugin integration

### Build Configuration
The plugin uses **Vite** with WordPress-specific optimizations:

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

## 📦 Build Scripts

### Available Scripts
```json
{
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "build:wp": "vite build && rm -f admin/assets/index-*.js && rm -f admin/js/index.js && cp dist/assets/index-*.js admin/assets/ && cp dist/assets/index-*.js admin/js/index.js",
    "preview": "vite preview"
  }
}
```

### Development Build
```bash
# Start development server with hot reload
npm run dev

# Build for development (with source maps)
npm run build:dev
```

### Production Build
```bash
# Recommended: Use WordPress build script
npm run build:wp

# Manual build process
npm run build
# Then manually copy assets (see Asset Management section)
```

## 🎯 Asset Management

### Dual-Asset System
WordPress plugin uses a **dual-asset system** for maximum compatibility:

1. **Primary**: `admin/assets/index-[hash].js` - Main build file with cache busting
2. **Fallback**: `admin/js/index.js` - Fallback file for compatibility

### Why Both Files Are Needed
- `admin/assets/` - Used when build files are detected (cache busting)
- `admin/js/` - Used as fallback when assets directory is not available
- Plugin automatically selects the most recent file by modification time

### Asset Update Process
**CRITICAL**: After any style changes or frontend modifications:

```bash
# Build the project
npm run build

# Remove old files
rm admin/assets/index-*.js
rm admin/js/index.js

# Copy new build to both locations
cp dist/assets/index-*.js admin/assets/
cp dist/assets/index-*.js admin/js/index.js
```

**Or use the automated script:**
```bash
npm run build:wp
```

## 🔧 Build Features

### Key Optimizations
- **No Minification**: Prevents variable hoisting issues and temporal dead zone problems
- **IIFE Format**: Better variable isolation in WordPress environment
- **Hash-based Filenames**: Automatic cache busting for browser caches
- **ES2020 Target**: Modern browser compatibility while maintaining stability

### Bundle Analysis
- **Unminified Size**: ~320KB (for stability)
- **Minified Would Be**: ~95KB (but causes TDZ issues)
- **Gzipped**: ~85KB (served by web servers)
- **Assets Cached**: By WordPress and browsers automatically

## 🚀 Development Workflow

### Local Development
1. **Setup Environment**:
   ```bash
   npm install
   npm run dev
   ```

2. **Development Server**: Runs on `http://localhost:5173`
3. **Hot Reload**: Automatic reload on file changes
4. **Source Maps**: Available in development mode

### Production Preparation
1. **Clean Build**:
   ```bash
   npm run build:wp
   ```

2. **Test Build**: Verify assets are copied correctly
3. **WordPress Testing**: Test in actual WordPress environment
4. **Performance Check**: Verify loading times and functionality

## 📁 Directory Structure

### Build-Related Directories
```
ai-content-agent-plugin/
├── admin/                     # WordPress admin assets
│   ├── assets/               # Primary build output (with hash)
│   │   └── index-[hash].js   # Main build file
│   └── js/                   # Fallback location
│       └── index.js          # Fallback build file
├── dist/                     # Vite build output
│   └── assets/               # Generated build files
├── src/                      # Source files (if using src structure)
├── components/               # React components
├── services/                 # Frontend services
├── package.json              # Dependencies and scripts
├── vite.config.ts           # Build configuration
└── tsconfig.json            # TypeScript configuration
```

## ⚡ Performance Considerations

### Build Performance
- **Fast Builds**: Vite provides fast development builds
- **Incremental Updates**: Only changed files are rebuilt
- **Hot Module Replacement**: Instant updates during development
- **Optimized Dependencies**: Pre-bundled dependencies for faster builds

### Runtime Performance
- **Bundle Size**: Optimized for WordPress hosting environments
- **Loading Strategy**: Efficient asset loading with fallbacks
- **Cache Strategy**: Browser and WordPress caching integration
- **Error Boundaries**: Graceful error handling prevents page crashes

## 🔍 Troubleshooting

### Common Build Issues

**Build Fails**
```bash
# Clear node modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf dist
npm run build
```

**Assets Not Loading**
1. Check if both asset locations have files
2. Verify file permissions
3. Clear WordPress cache
4. Check browser console for errors

**Development Server Issues**
```bash
# Kill any existing processes
killall node

# Restart development server
npm run dev
```

**TypeScript Errors**
1. Check `tsconfig.json` configuration
2. Verify all dependencies are installed
3. Run TypeScript check: `npx tsc --noEmit`

### Build Verification
After building, verify:
- [ ] Files exist in `admin/assets/`
- [ ] Files exist in `admin/js/`
- [ ] File sizes are reasonable (~320KB)
- [ ] No console errors in WordPress admin
- [ ] Plugin functionality works correctly

## 🛡️ Build Security

### Asset Integrity
- **Hash-based Names**: Prevents cache poisoning
- **File Verification**: WordPress verifies file modification times
- **Secure Paths**: All assets served from secure WordPress directories
- **No External Dependencies**: All dependencies bundled locally

### Code Quality
- **TypeScript**: Type safety and better code quality
- **Linting**: Code quality checks (if configured)
- **Error Boundaries**: Prevent crashes from affecting WordPress
- **Graceful Degradation**: Plugin works even if assets fail to load

## 📊 Build Monitoring

### Build Success Indicators
- Exit code 0 from build command
- Assets generated in both locations
- File sizes within expected ranges
- No TypeScript or build errors

### Performance Metrics
- **Build Time**: Typically < 30 seconds
- **Asset Size**: ~320KB unminified
- **Load Time**: < 2 seconds in WordPress admin
- **Memory Usage**: Optimized for shared hosting

## 🔄 Continuous Integration

### Automated Builds
For CI/CD pipelines:
```bash
# Install dependencies
npm ci

# Run build
npm run build:wp

# Verify assets
ls -la admin/assets/
ls -la admin/js/
```

### Build Validation
```bash
# Check if assets exist
test -f admin/assets/index-*.js && echo "Primary assets OK"
test -f admin/js/index.js && echo "Fallback assets OK"
```

## 📞 Support

Need help with the build process? Check:
- [Development Guide](development-guide.md) for setup instructions
- [Troubleshooting Guide](../reference/troubleshooting.md) for common issues
- [Plugin Architecture](architecture.md) for technical details

---

**Ready to build?** Use `npm run build:wp` for the complete WordPress-optimized build process! 🚀