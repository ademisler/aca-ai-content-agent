# Plugin Architecture - AI Content Agent (ACA)

This document provides a comprehensive overview of the AI Content Agent plugin architecture, including frontend, backend, and integration patterns.

## 🏗️ Overall Architecture

### High-Level Overview
```
WordPress Admin
├── React Frontend (TypeScript/Vite)
│   ├── Components (TSX)
│   ├── Services (API calls)
│   └── State Management
├── PHP Backend
│   ├── Core Classes
│   ├── API Endpoints
│   └── WordPress Hooks
└── External Integrations
    ├── Google Gemini AI
    ├── Google Search Console
    └── Image APIs
```

### Technology Stack
- **Frontend**: React 18+ with TypeScript
- **Build System**: Vite with Terser optimization
- **Backend**: PHP 7.4+ with WordPress APIs
- **Database**: WordPress database with custom tables
- **APIs**: REST API for frontend-backend communication

## 📁 Directory Structure

### Core Directories
```
ai-content-agent-plugin/
├── admin/                    # Compiled frontend assets
│   ├── assets/              # Vite build output
│   ├── css/                 # Fallback CSS
│   └── js/                  # Fallback JavaScript
├── components/              # React components (TSX)
├── services/                # Frontend services
├── includes/                # PHP backend classes
├── vendor/                  # Composer dependencies
├── node_modules/            # NPM dependencies
└── dist/                    # Vite build output
```

### Frontend Structure
```
components/
├── App.tsx                  # Main application component
├── ContentCalendar.tsx      # Calendar functionality
├── ContentFreshnessManager.tsx # PRO freshness system
├── IdeaBoard.tsx           # Idea management
├── SettingsPanel.tsx       # Plugin settings
└── shared/                 # Shared components
    ├── LoadingSpinner.tsx
    ├── ErrorBoundary.tsx
    └── Modal.tsx
```

### Backend Structure
```
includes/
├── class-aca-core.php              # Core plugin functionality
├── class-aca-api.php               # REST API endpoints
├── class-aca-content-generator.php # Content generation
├── class-aca-content-freshness.php # PRO freshness system
├── class-aca-seo-integration.php   # SEO plugin integration
├── class-aca-gsc-integration.php   # Google Search Console
└── class-aca-license-manager.php   # PRO license management
```

## 🔄 Data Flow

### Content Generation Flow
```
User Input → React Component → API Service → PHP Endpoint → Gemini AI → WordPress Database → UI Update
```

### Detailed Flow
1. **User Interaction**: User interacts with React components
2. **API Call**: Frontend service makes REST API call
3. **PHP Processing**: Backend processes request and validates data
4. **External API**: Calls to Gemini AI or other services
5. **Data Storage**: Results stored in WordPress database
6. **Response**: JSON response sent back to frontend
7. **UI Update**: React components update with new data

## 🎯 Component Architecture

### React Component Hierarchy
```
App.tsx
├── ErrorBoundary
├── NavigationTabs
├── IdeaBoard
│   ├── IdeaList
│   ├── IdeaCard
│   └── GenerateButton
├── ContentCalendar
│   ├── CalendarGrid
│   ├── EventCard
│   └── SchedulingModal
├── ContentFreshnessManager (PRO)
│   ├── FreshnessDashboard
│   ├── ContentList
│   └── UpdateQueue
└── SettingsPanel
    ├── APISettings
    ├── AutomationSettings
    └── LicenseManager
```

### State Management
- **Local State**: React useState for component-specific data
- **Shared State**: Props drilling for parent-child communication
- **Global State**: WordPress options for persistent settings
- **Cache Management**: Browser localStorage for temporary data

## 🔌 API Architecture

### REST API Endpoints
```php
/wp-json/aca/v1/
├── ideas/                   # Idea management
│   ├── GET /list           # Get all ideas
│   ├── POST /generate      # Generate new ideas
│   └── DELETE /{id}        # Delete idea
├── content/                # Content management
│   ├── POST /generate      # Generate blog post
│   ├── GET /freshness      # Get freshness scores (PRO)
│   └── POST /update        # Update content
├── calendar/               # Calendar operations
│   ├── GET /events         # Get scheduled content
│   └── POST /schedule      # Schedule content
└── settings/               # Settings management
    ├── GET /all            # Get all settings
    └── POST /update        # Update settings
```

### Authentication & Security
- **WordPress Nonces**: CSRF protection for all API calls
- **Capability Checks**: WordPress user capability verification
- **Data Sanitization**: Input validation and sanitization
- **Rate Limiting**: API rate limiting for external services

## 🗄️ Database Schema

### Custom Tables
```sql
-- Content freshness tracking (PRO)
wp_aca_content_freshness
├── id (PRIMARY KEY)
├── post_id (WordPress post ID)
├── freshness_score (0-100)
├── last_analyzed (DATETIME)
├── priority_level (1-5)
└── recommendations (JSON)

-- Idea tracking
wp_aca_ideas
├── id (PRIMARY KEY)
├── title (VARCHAR)
├── description (TEXT)
├── status (ENUM: pending, in_progress, completed)
├── created_at (DATETIME)
└── meta_data (JSON)
```

### WordPress Integration
- **Post Meta**: SEO data and AI-generated content metadata
- **Options**: Plugin settings and configuration
- **Transients**: Temporary data and caching
- **User Meta**: User-specific preferences and settings

## 🔧 Integration Patterns

### SEO Plugin Integration
```php
class ACA_SEO_Integration {
    private $detected_plugins = [];
    
    public function detect_seo_plugins() {
        // Auto-detect installed SEO plugins
        if (class_exists('RankMath')) {
            $this->detected_plugins[] = 'rankmath';
        }
        if (class_exists('WPSEO_Frontend')) {
            $this->detected_plugins[] = 'yoast';
        }
    }
    
    public function transfer_metadata($post_id, $metadata) {
        // Transfer AI-generated metadata to detected SEO plugins
    }
}
```

### External API Integration
```php
class ACA_API_Client {
    private $retry_attempts = 3;
    private $backoff_multiplier = 2;
    
    public function make_request($endpoint, $data) {
        // Retry logic with exponential backoff
        // Error handling and fallback mechanisms
    }
}
```

## 🚀 Build System

### Vite Configuration
```javascript
// vite.config.ts
export default defineConfig({
    build: {
        rollupOptions: {
            output: {
                entryFileNames: 'assets/index-[hash].js',
                chunkFileNames: 'assets/[name]-[hash].js',
                assetFileNames: 'assets/[name]-[hash].[ext]'
            }
        },
        minify: 'terser'
    }
});
```

### Dual Asset System
- **Primary**: `admin/assets/` - Vite build output with hashing
- **Fallback**: `admin/js/index.js` - Non-hashed fallback for compatibility
- **Build Script**: `npm run build:wp` copies assets to both locations

## 🔒 Security Architecture

### Frontend Security
- **XSS Prevention**: React's built-in XSS protection
- **Content Security Policy**: Strict CSP headers
- **Input Validation**: Client-side validation with server-side verification

### Backend Security
- **SQL Injection**: WordPress prepared statements
- **CSRF Protection**: WordPress nonces for all forms
- **Capability Checks**: Proper WordPress permission verification
- **Data Sanitization**: WordPress sanitization functions

## 📊 Performance Optimization

### Frontend Optimization
- **Code Splitting**: Dynamic imports for large components
- **Tree Shaking**: Unused code elimination via Vite
- **Minification**: Terser for JavaScript minification
- **Caching**: Browser caching for static assets

### Backend Optimization
- **Database Queries**: Efficient WordPress queries with caching
- **Transient Caching**: WordPress transients for expensive operations
- **API Rate Limiting**: Respect external API limits
- **Background Processing**: WordPress cron for heavy tasks

## 🧪 Testing Architecture

### Frontend Testing
- **Unit Tests**: Jest with React Testing Library
- **Component Tests**: Individual component testing
- **Integration Tests**: API integration testing

### Backend Testing
- **PHPUnit**: WordPress plugin testing framework
- **API Tests**: REST API endpoint testing
- **Database Tests**: WordPress database testing utilities

---

**For Developers**: This architecture supports scalable development while maintaining WordPress best practices. See [Development Guide](development-guide.md) for implementation details.