# API Reference - AI Content Agent (ACA)

This document provides comprehensive API documentation for the AI Content Agent plugin, including REST endpoints, PHP classes, and JavaScript APIs.

## 🌐 REST API Endpoints

All endpoints are prefixed with `/wp-json/aca/v1/`

### Authentication
All API endpoints require WordPress authentication and proper user capabilities.

```javascript
// Example API call with nonce
fetch('/wp-json/aca/v1/ideas/generate', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': acaData.nonce
    },
    body: JSON.stringify(data)
});
```

## 📝 Ideas API

### GET /ideas/list
Get all ideas with optional filtering.

**Parameters:**
- `status` (optional): Filter by status (`pending`, `in_progress`, `completed`)
- `limit` (optional): Number of ideas to return (default: 20)
- `offset` (optional): Pagination offset (default: 0)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "AI Content Creation Tips",
            "description": "Comprehensive guide to AI content creation",
            "status": "pending",
            "created_at": "2025-01-30T10:00:00Z",
            "meta_data": {
                "keywords": ["AI", "content", "tips"],
                "category": "Technology"
            }
        }
    ],
    "total": 50,
    "pages": 3
}
```

### POST /ideas/generate
Generate new content ideas using AI.

**Parameters:**
- `niche` (required): Content niche/topic
- `count` (optional): Number of ideas to generate (default: 5, max: 10)
- `language` (optional): Content language (auto-detected if not provided)

**Request Body:**
```json
{
    "niche": "WordPress development",
    "count": 5,
    "language": "en"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "ideas": [
            {
                "title": "Advanced WordPress Hooks",
                "description": "Deep dive into WordPress action and filter hooks",
                "keywords": ["WordPress", "hooks", "development"],
                "estimated_length": 1500
            }
        ],
        "language_detected": "en",
        "generation_time": 2.3
    }
}
```

### DELETE /ideas/{id}
Delete a specific idea.

**Response:**
```json
{
    "success": true,
    "message": "Idea deleted successfully"
}
```

## 📄 Content API

### POST /content/generate
Generate a full blog post from an idea.

**Parameters:**
- `idea_id` (optional): ID of existing idea
- `title` (required if no idea_id): Custom title
- `description` (optional): Additional context
- `length` (optional): Target word count (default: 1000)
- `seo_focus` (optional): Primary SEO keyword

**Request Body:**
```json
{
    "idea_id": 123,
    "length": 1500,
    "seo_focus": "WordPress development"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "post_id": 456,
        "title": "Advanced WordPress Development Techniques",
        "content": "<p>Full blog post content...</p>",
        "excerpt": "Brief excerpt of the post",
        "seo_data": {
            "meta_title": "SEO optimized title",
            "meta_description": "SEO meta description",
            "focus_keyword": "WordPress development",
            "readability_score": 85
        },
        "categories": [12, 15],
        "tags": ["WordPress", "development", "PHP"],
        "featured_image_url": "https://example.com/image.jpg",
        "generation_time": 15.7
    }
}
```

### GET /content/freshness (PRO)
Get content freshness analysis for existing posts.

**Parameters:**
- `post_ids` (optional): Comma-separated list of post IDs
- `limit` (optional): Number of posts to analyze (default: 20)
- `min_score` (optional): Minimum freshness score filter

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "post_id": 123,
            "title": "Old Blog Post",
            "freshness_score": 45,
            "priority_level": 3,
            "last_analyzed": "2025-01-30T10:00:00Z",
            "recommendations": [
                "Update statistics with recent data",
                "Add new section on latest trends",
                "Refresh outdated screenshots"
            ],
            "seo_performance": {
                "clicks": 150,
                "impressions": 2500,
                "ctr": 6.0,
                "average_position": 8.5
            }
        }
    ]
}
```

### POST /content/update
Update content based on freshness recommendations.

**Request Body:**
```json
{
    "post_id": 123,
    "update_type": "refresh", // "refresh", "expand", "rewrite"
    "apply_recommendations": true
}
```

## 📅 Calendar API

### GET /calendar/events
Get scheduled content events.

**Parameters:**
- `start_date` (required): Start date (YYYY-MM-DD)
- `end_date` (required): End date (YYYY-MM-DD)
- `status` (optional): Filter by post status

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 123,
            "title": "Scheduled Post",
            "scheduled_date": "2025-02-01T09:00:00Z",
            "status": "scheduled",
            "post_type": "post",
            "categories": ["Technology"],
            "author": "John Doe"
        }
    ]
}
```

### POST /calendar/schedule
Schedule content for publication.

**Request Body:**
```json
{
    "post_id": 123,
    "scheduled_date": "2025-02-01T09:00:00Z",
    "timezone": "America/New_York"
}
```

## ⚙️ Settings API

### GET /settings/all
Get all plugin settings.

**Response:**
```json
{
    "success": true,
    "data": {
        "gemini_api_key": "***masked***",
        "automation_mode": "semi_automatic",
        "default_language": "auto",
        "seo_integration": {
            "detected_plugins": ["yoast", "rankmath"],
            "auto_transfer": true
        },
        "image_generation": {
            "provider": "google_imagen",
            "auto_generate": true
        }
    }
}
```

### POST /settings/update
Update plugin settings.

**Request Body:**
```json
{
    "automation_mode": "full_automatic",
    "default_post_status": "draft",
    "seo_integration": {
        "auto_transfer": true
    }
}
```

## 🔧 PHP Class API

### ACA_Core Class

Main plugin class for core functionality.

```php
class ACA_Core {
    /**
     * Get plugin instance
     */
    public static function get_instance()
    
    /**
     * Initialize plugin
     */
    public function init()
    
    /**
     * Check if PRO features are available
     */
    public function is_pro_active(): bool
}
```

### ACA_Content_Generator Class

Content generation functionality.

```php
class ACA_Content_Generator {
    /**
     * Generate content ideas
     */
    public function generate_ideas(string $niche, int $count = 5): array
    
    /**
     * Generate full blog post
     */
    public function generate_post(array $params): array
    
    /**
     * Get supported languages
     */
    public function get_supported_languages(): array
}
```

### ACA_SEO_Integration Class

SEO plugin integration.

```php
class ACA_SEO_Integration {
    /**
     * Detect installed SEO plugins
     */
    public function detect_seo_plugins(): array
    
    /**
     * Transfer metadata to SEO plugin
     */
    public function transfer_metadata(int $post_id, array $metadata): bool
    
    /**
     * Get SEO plugin capabilities
     */
    public function get_plugin_capabilities(string $plugin): array
}
```

### ACA_Content_Freshness Class (PRO)

Content freshness analysis.

```php
class ACA_Content_Freshness {
    /**
     * Analyze content freshness
     */
    public function analyze_content(int $post_id): array
    
    /**
     * Get freshness recommendations
     */
    public function get_recommendations(int $post_id): array
    
    /**
     * Update content based on recommendations
     */
    public function update_content(int $post_id, array $options): bool
}
```

## 🎯 JavaScript API

### Frontend Services

#### ContentService

```javascript
class ContentService {
    /**
     * Generate content ideas
     */
    static async generateIdeas(niche, count = 5)
    
    /**
     * Generate blog post
     */
    static async generatePost(params)
    
    /**
     * Get content freshness data
     */
    static async getFreshnessData(postIds = [])
}
```

#### CalendarService

```javascript
class CalendarService {
    /**
     * Get calendar events
     */
    static async getEvents(startDate, endDate)
    
    /**
     * Schedule content
     */
    static async scheduleContent(postId, date)
    
    /**
     * Reschedule content
     */
    static async rescheduleContent(postId, newDate)
}
```

### WordPress Hooks

#### Actions

```php
// Fired after content generation
do_action('aca_content_generated', $post_id, $content_data);

// Fired after idea generation
do_action('aca_ideas_generated', $ideas_data);

// Fired after freshness analysis (PRO)
do_action('aca_freshness_analyzed', $post_id, $analysis_data);
```

#### Filters

```php
// Modify generated content before saving
$content = apply_filters('aca_generated_content', $content, $post_id);

// Modify SEO metadata before transfer
$seo_data = apply_filters('aca_seo_metadata', $seo_data, $post_id);

// Modify freshness analysis parameters (PRO)
$params = apply_filters('aca_freshness_params', $params, $post_id);
```

## 🔐 Error Handling

### Error Response Format

```json
{
    "success": false,
    "error": {
        "code": "INVALID_API_KEY",
        "message": "The provided API key is invalid",
        "data": {
            "field": "gemini_api_key",
            "details": "API key must be 39 characters long"
        }
    }
}
```

### Common Error Codes

- `INVALID_API_KEY`: Invalid or missing API key
- `RATE_LIMIT_EXCEEDED`: API rate limit exceeded
- `INSUFFICIENT_PERMISSIONS`: User lacks required capabilities
- `INVALID_REQUEST`: Malformed request data
- `SERVICE_UNAVAILABLE`: External service temporarily unavailable
- `PRO_FEATURE_REQUIRED`: Feature requires PRO license

## 📊 Rate Limiting

### API Limits
- **Content Generation**: 10 requests per minute per user
- **Idea Generation**: 20 requests per minute per user
- **Freshness Analysis**: 50 requests per minute per user (PRO)

### Headers
```http
X-RateLimit-Limit: 10
X-RateLimit-Remaining: 7
X-RateLimit-Reset: 1643723400
```

---

**For Developers**: This API reference covers all public endpoints and classes. See [Architecture Guide](architecture.md) for implementation details.