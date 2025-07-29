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
- **Composer**: For PHP dependencies
- **Git**: Version control
- **WordPress**: 5.0+ (for testing)

### Setup Instructions
```bash
# Clone repository
git clone https://github.com/ademisler/aca-ai-content-agent.git
cd ai-content-agent-plugin

# Install frontend dependencies
npm install

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Start development server
npm run dev
```

### Directory Structure
```
ai-content-agent-plugin/
├── admin/                     # Compiled assets
│   ├── css/index.css         # Compiled CSS
│   └── js/index.js           # Compiled JavaScript
├── components/               # React components
├── services/                 # Frontend services
├── includes/                 # PHP backend classes
├── releases/                 # Release management
│   ├── ai-content-agent-v1.4.9-*.zip  # Latest release
│   └── archive/              # Previous versions
├── dist/                     # Vite build output
├── node_modules/             # Node dependencies
├── vendor/                   # Composer dependencies
└── *.md                      # Documentation files
```

## 🔨 Build Process

### Frontend Build
```bash
# Development build
npm run dev

# Production build
npm run build

# Copy assets to admin directory
cp dist/assets/*.css admin/css/index.css
cp dist/assets/*.js admin/js/index.js
```

### Backend Dependencies
```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Development dependencies (if needed)
composer install
```

### Build Verification
- [ ] CSS file generated in `admin/css/`
- [ ] JavaScript file generated in `admin/js/`
- [ ] All React components compile without errors
- [ ] PHP syntax validation passes
- [ ] WordPress activation test successful

## 📦 Release Management

### For Developers
```bash
# Latest release
releases/ai-content-agent-v1.6.4-javascript-initialization-error-fix.zip

# Archived versions
releases/archive/ai-content-agent-v1.3.x-*.zip
releases/archive/ai-content-agent-v1.4.x-*.zip
releases/archive/ai-content-agent-v1.5.x-*.zip
releases/archive/ai-content-agent-v1.6.[0-3]-*.zip
```

### Version Update Process
1. **Update Version Numbers**:
   ```bash
   # Update in ai-content-agent.php
   # Line 5: * Version: 1.6.4
   # Line 18: define('ACA_VERSION', '1.6.4');
   
   # Update in package.json
   # Line 4: "version": "1.6.4",
   ```

2. **Update Documentation**:
   - [ ] README.md - Latest Updates section
   - [ ] README.txt - Stable tag and changelog
   - [ ] CHANGELOG.md - New version entry
   - [ ] GOOGLE_SEARCH_CONSOLE_SETUP.md - Plugin version
   - [ ] All version references in documentation

3. **Build and Test**:
   ```bash
   npm run build
   cp dist/assets/*.css admin/css/index.css
   cp dist/assets/*.js admin/js/index.js
   ```

4. **Create Release**:
   ```bash
   # Move previous version to archive
   mv releases/ai-content-agent-v*.zip releases/archive/
   
   # Create new release zip
   cd ..
   zip -r ai-content-agent-v1.6.4-description.zip ai-content-agent-plugin/ \
     -x "*/node_modules/*" "*/.git/*" "*/dist/*" "*/package-lock.json"
   
   # Move to releases directory
   mv ai-content-agent-v*.zip releases/
   ```

5. **Git Management**:
   ```bash
   git add .
   git commit -m "🚀 RELEASE v1.6.4: Description"
   git push origin main
   git tag v1.6.4
   git push origin v1.6.4
   ```

### Release Naming Convention
```
ai-content-agent-v{MAJOR}.{MINOR}.{PATCH}-{DESCRIPTION}.zip

Examples:
- ai-content-agent-v1.6.4-javascript-initialization-error-fix.zip
- ai-content-agent-v1.6.3-documentation-update-and-build-optimization.zip
- ai-content-agent-v1.6.2-fixed-javascript-initialization-error.zip
```

## 📚 Documentation Standards

### Documentation Update Rule
**CRITICAL**: Every code change MUST be accompanied by documentation updates.

### Required Documentation Updates
- [ ] **README.md**: Main documentation with latest features
- [ ] **README.txt**: WordPress directory format
- [ ] **CHANGELOG.md**: Detailed version history
- [ ] **RELEASES.md**: Release management information
- [ ] **Setup Guides**: Feature-specific instructions

### Documentation Checklist
- [ ] Version numbers updated across all files
- [ ] Download links point to correct release
- [ ] Feature descriptions reflect current functionality
- [ ] Installation instructions are accurate
- [ ] Troubleshooting section is up-to-date

### Writing Standards
- Use clear, concise language
- Include code examples where appropriate
- Maintain consistent formatting
- Use emojis for visual organization
- Keep technical accuracy high

## 🧪 Testing Procedures

### Pre-Release Testing
1. **Plugin Activation**:
   - [ ] Clean WordPress install
   - [ ] Plugin activates without errors
   - [ ] Admin page loads correctly

2. **Core Functionality**:
   - [ ] AI content generation works
   - [ ] Content calendar functions
   - [ ] Google Search Console integration
   - [ ] Settings save and load properly

3. **Browser Compatibility**:
   - [ ] Chrome (latest)
   - [ ] Firefox (latest)
   - [ ] Safari (latest)
   - [ ] Edge (latest)

4. **WordPress Compatibility**:
   - [ ] WordPress 5.0+
   - [ ] PHP 7.4+
   - [ ] Common themes compatibility
   - [ ] Plugin conflict testing

### Error Testing
- [ ] Missing dependencies handling
- [ ] API key validation
- [ ] Network connectivity issues
- [ ] Invalid user input handling

## ✅ Deployment Checklist

### Pre-Deployment
- [ ] All tests pass
- [ ] Documentation updated
- [ ] Version numbers consistent
- [ ] Build process completed
- [ ] Release notes prepared

### Deployment Steps
1. [ ] Create release zip file
2. [ ] Test zip file installation
3. [ ] Upload to releases directory
4. [ ] Update documentation links
5. [ ] Commit and push changes
6. [ ] Create git tag
7. [ ] Notify stakeholders

### Post-Deployment
- [ ] Monitor for issues
- [ ] User feedback collection
- [ ] Performance monitoring
- [ ] Documentation review

## 🔧 Common Development Tasks

### Adding New Features
1. **Plan Implementation**:
   - [ ] Design document
   - [ ] API changes needed
   - [ ] UI/UX considerations
   - [ ] Testing strategy

2. **Development**:
   - [ ] Backend implementation
   - [ ] Frontend components
   - [ ] API integration
   - [ ] Error handling

3. **Documentation**:
   - [ ] Feature documentation
   - [ ] API documentation
   - [ ] User guide updates
   - [ ] Changelog entry

### Bug Fixes
1. **Identify Issue**:
   - [ ] Reproduce bug
   - [ ] Determine root cause
   - [ ] Plan fix approach

2. **Implement Fix**:
   - [ ] Code changes
   - [ ] Testing verification
   - [ ] Regression testing

3. **Documentation**:
   - [ ] Update troubleshooting
   - [ ] Changelog entry
   - [ ] Release notes

### Dependency Updates
1. **Frontend Dependencies**:
   ```bash
   npm update
   npm audit fix
   npm run build
   ```

2. **Backend Dependencies**:
   ```bash
   composer update
   composer audit
   ```

3. **Testing**:
   - [ ] Full functionality test
   - [ ] Compatibility verification
   - [ ] Performance impact assessment

## 📞 Support and Resources

### Internal Resources
- **Main Repository**: GitHub repository
- **Documentation**: All .md files in plugin root
- **Release History**: `releases/archive/` directory
- **Changelog**: `CHANGELOG.md` for detailed history

### External Resources
- **WordPress Codex**: Plugin development standards
- **React Documentation**: Frontend framework reference
- **Google APIs**: Search Console integration docs
- **Composer**: PHP dependency management

### Contact Information
- **Development Team**: Internal team communication
- **Issue Tracking**: GitHub Issues
- **Documentation**: This developer guide

---

**Last Updated**: January 2025  
**Plugin Version**: 1.6.4  
**Guide Version**: 1.2