# Developer Guide: ACA - AI Content Agent

ACA (AI Content Agent) is a WordPress plugin that learns from your existing posts and automatically drafts new content using Google Gemini. This guide explains the code base and how to work with it.

- [Overview](#overview)
- [Folder Structure](#folder-structure)
- [Setup](#setup)
- [Activation & Database](#activation--database)
- [Automation](#automation)
- [Services](#services)
- [API Clients](#api-clients)
- [Admin Interface](#admin-interface)
- [Security & Utilities](#security--utilities)
- [Translations](#translations)
- [Uninstallation](#uninstallation)
- [Further Reading](#further-reading)

## Overview

The plugin analyzes your published posts to build a style guide and then generates ideas and full drafts that match that style. You can run ACA manually, semi-automatically or fully automatically. Generated drafts are enriched with internal links, sources, optional images and plagiarism checks.

Feature highlights (see `readme.txt` for the full list) include:

- Style analysis and prompt customization
- Idea generation and draft creation
- Optional data sections, featured images and internal links
- Dashboard for ideas, clusters and logs
- Google Search Console integration
- Pro features such as content clusters, DALL‑E images and plagiarism checks

## Folder Structure

```
aca-ai-content-agent/
├─ aca-ai-content-agent.php    # Plugin bootstrap
├─ admin/                      # CSS/JS assets for the admin UI
├─ includes/
│  ├─ admin/                   # Dashboard, settings and onboarding
│  ├─ api/                     # Gemini & Gumroad clients
│  ├─ core/                    # Activation, deactivation, uninstall
│  ├─ integrations/            # Privacy hooks and meta boxes
│  ├─ services/                # Idea, draft and style guide logic
│  ├─ utils/                   # Helper, encryption and logging
│  ├─ class-aca-cron.php       # Cron event manager
│  └─ class-aca-plugin.php     # Main loader
├─ languages/                  # Translation templates
├─ templates/                  # Placeholder template directory
├─ vendor/                     # Composer dependencies (Action Scheduler)
└─ uninstall.php               # Full data cleanup
```

`aca-ai-content-agent.php` loads Composer autoloading, defines constants and boots the singleton `ACA_Plugin` class.

## Setup

1. Run `composer install` to install Action Scheduler.
2. Activate the plugin in WordPress. On first activation the onboarding wizard will prompt for API keys.
3. API keys and other options are stored in the `aca_ai_content_agent_options` option.

## Activation & Database

Activation creates several custom tables and adds capabilities. The key logic lives in `ACA_Activator::activate()`:

```php
class ACA_Activator {
    public static function activate() {
        self::create_custom_tables();
        self::add_custom_capabilities();
        set_transient('aca_ai_content_agent_activation_redirect', true, 30);
    }
}
```
【F:includes/core/class-aca-activator.php†L22-L54】

Tables for ideas, logs, clusters and cluster items are defined in the same file. The uninstaller removes them and clears all plugin options:

```php
class ACA_Uninstaller {
    public static function uninstall() {
        global $wpdb;
        $tables = [
            $wpdb->prefix . 'aca_ai_content_agent_ideas',
            $wpdb->prefix . 'aca_ai_content_agent_logs',
            $wpdb->prefix . 'aca_ai_content_agent_clusters',
            $wpdb->prefix . 'aca_ai_content_agent_cluster_items',
        ];
        // DROP TABLE ... and delete options
    }
}
```
【F:includes/core/class-aca-uninstaller.php†L22-L62】

## Automation

`ACA_AI_Content_Agent_Cron` schedules recurring jobs such as idea generation, draft writing and log cleanup. Hooks are registered in the constructor:

```php
public function __construct() {
    add_action('init', [$this, 'schedule_events']);
    add_action('aca_ai_content_agent_run_main_automation', [$this, 'run_main_automation']);
    add_action('aca_ai_content_agent_reset_api_usage_counter', [$this, 'reset_api_usage_counter']);
    add_action('aca_ai_content_agent_generate_style_guide', [$this, 'generate_style_guide']);
    add_action('aca_ai_content_agent_verify_license', [$this, 'verify_license']);
    add_action('aca_ai_content_agent_clean_logs', [$this, 'clean_logs']);
}
```
【F:includes/class-aca-cron.php†L18-L30】

The `run_main_automation()` method handles the selected working mode:

```php
public function run_main_automation() {
    $options = get_option('aca_ai_content_agent_options');
    $working_mode = $options['working_mode'] ?? 'manual';
    if ($working_mode === 'semi-auto') {
        ACA_Idea_Service::generate_ideas();
    } elseif ($working_mode === 'full-auto') {
        $idea_ids = ACA_Idea_Service::generate_ideas();
        if (!is_wp_error($idea_ids) && !empty($idea_ids)) {
            foreach ($idea_ids as $idea_id) {
                ACA_Draft_Service::write_post_draft($idea_id);
            }
        }
    }
}
```
【F:includes/class-aca-cron.php†L120-L137】

## Services

### Idea Service
Generates new post titles using recent posts as context and stores them in the `ideas` table.

```php
ACA_Log_Service::add('Attempting to generate new ideas.');
$prompts = ACA_Style_Guide_Service::get_default_prompts();
$posts = get_posts([...]);
$prompt = sprintf($prompts['idea_generation'], $existing_titles, $limit);
$response = ACA_Gemini_Api::call($prompt);
```
【F:includes/services/class-aca-idea-service.php†L24-L60】

### Draft Service
Writes full drafts from an idea and enriches the content with sources, internal links and optional images or plagiarism checks. See `ACA_Draft_Service::write_post_draft()` for the main workflow.

### Style Guide Service
Analyzes existing posts to build a reusable style guide:

```php
public static function generate_style_guide() {
    ACA_Log_Service::add('Attempting to generate style guide.');
    $options  = get_option('aca_ai_content_agent_options');
    $post_types = $options['analysis_post_types'] ?? ['post'];
    $depth      = $options['analysis_depth'] ?? 20;
    // ...query posts and build prompt
    $style_guide = ACA_Gemini_Api::call($prompt);
    if (!is_wp_error($style_guide)) {
        set_transient('aca_ai_content_agent_style_guide', $style_guide, WEEK_IN_SECONDS);
        update_option('aca_ai_content_agent_style_guide', $style_guide);
    }
}
```
【F:includes/services/class-aca-style-guide-service.php†L24-L68】

## API Clients

- **Gemini** – Communicates with Google Gemini and keeps track of API usage. The main `call()` function assembles the payload, sends the request and handles safety blocks.
- **Gumroad** – Verifies Pro licenses via the Gumroad API.

Example Gemini request:

```php
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key;
$response = wp_remote_post($api_url, [...]);
```
【F:includes/api/class-aca-gemini-api.php†L19-L72】

License validation snippet:

```php
$response = wp_remote_post('https://api.gumroad.com/v2/licenses/verify', [
    'body' => [
        'product_id' => ACA_AI_CONTENT_AGENT_GUMROAD_PRODUCT_ID,
        'license_key' => sanitize_text_field($license_key),
    ],
]);
```
【F:includes/api/class-aca-gumroad-api.php†L24-L38】

## Admin Interface

The admin classes render the dashboard, settings pages and onboarding wizard. Settings fields are registered in `ACA_Admin_Init::register_settings()` and sensitive keys are obfuscated before storage:

```php
public function sanitize_and_obfuscate_api_key($input) {
    $existing = get_option('aca_ai_content_agent_gemini_api_key');
    if (!isset($input) || '' === trim($input)) {
        return $existing;
    }
    $sanitized_key = sanitize_text_field($input);
    return aca_ai_content_agent_encrypt($sanitized_key);
}
```
【F:includes/admin/class-aca-admin-init.php†L292-L307】

AJAX handlers located in `ACA_Ajax_Handler` respond to actions like testing the API connection or generating ideas. The JavaScript logic for these requests lives in `admin/js/aca-admin.js`.

## Security & Utilities

- **Encryption** – `ACA_Encryption_Util` encrypts API and license keys using `AUTH_KEY` as the secret:

```php
$key = defined('AUTH_KEY') ? AUTH_KEY : 'aca_ai_content_agent_default_key';
$cipher = openssl_encrypt($data, 'AES-256-CBC', substr(hash('sha256', $key), 0, 32), 0, $iv);
```
【F:includes/utils/class-aca-encryption-util.php†L20-L36】

- **Helper** – `ACA_Helper::is_pro()` checks license validity and caches the result.
- **Log Service** – `ACA_Log_Service::add()` writes log entries to the `logs` table.

## Translations

Language files live in `languages/`. The `aca.pot` template can be translated to create `.po` and `.mo` files for other locales.

## Uninstallation

Running `uninstall.php` triggers a full cleanup:

```php
require_once plugin_dir_path(__FILE__) . 'includes/core/class-aca-uninstaller.php';
ACA_Uninstaller::uninstall();
```
【F:uninstall.php†L9-L17】

## Further Reading

`readme.txt` explains installation steps, features and screenshots for end users. Consult that file alongside this guide when developing new functionality.
