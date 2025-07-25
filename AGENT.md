# Developer Guide: ACA - AI Content Agent

This document provides an overview of the plugin structure and key features for developers.

## Overview

ACA is a WordPress plugin that analyses existing content to learn a site's writing style and then uses Google Gemini to generate new post ideas and full drafts. It supports manual, semi-automatic and fully automatic modes and enriches drafts with internal links, featured images and more.

Core features and Pro-only additions are listed in `readme.txt`.

## Folder Structure

- `aca-ai-content-agent.php` – main plugin bootstrap and hook registration.
- `admin/` – admin facing assets (`css/`, `js/`), loaded from `includes/admin/` classes.
- `includes/`
  - `admin/` – renders the settings/dashboard pages and onboarding wizard.
  - `api/` – Gemini and Gumroad API clients.
  - `core/` – activation/deactivation/uninstall logic.
  - `integrations/` – privacy hooks and post meta boxes.
  - `services/` – business logic for idea generation, draft writing and style guides.
  - `utils/` – helper utilities (encryption, logging, license checks).
  - `class-aca-cron.php` – cron scheduling for automation tasks.
  - `class-aca-plugin.php` – main loader which wires everything together.
- `languages/` – translation templates.
- `templates/` – placeholder template directory.
- `vendor/` – Composer dependencies (Action Scheduler).

## Activation & Database

On activation, the plugin creates custom tables and adds capabilities:

```php
class ACA_Activator {
    public static function activate() {
        self::create_custom_tables();
        self::add_custom_capabilities();
        set_transient('aca_ai_content_agent_activation_redirect', true, 30);
    }
    private static function create_custom_tables() {
        // ideas, logs, clusters and cluster_items tables
    }
}
```
【F:includes/core/class-aca-activator.php†L22-L103】

The uninstaller drops those tables and cleans plugin options:

```php
class ACA_Uninstaller {
    public static function uninstall() {
        // DROP TABLE ... and remove options
    }
}
```
【F:includes/core/class-aca-uninstaller.php†L22-L71】

## Automation

`ACA_AI_Content_Agent_Cron` schedules recurring jobs for idea generation, draft creation and housekeeping. Events run via Action Scheduler when available or WP‑Cron as a fallback:

```php
class ACA_AI_Content_Agent_Cron {
    public function __construct() {
        add_action('init', [$this, 'schedule_events']);
        add_action('aca_ai_content_agent_run_main_automation', [$this, 'run_main_automation']);
        // ... other hooks
    }
    public function run_main_automation() {
        $working_mode = $options['working_mode'] ?? 'manual';
        if ($working_mode === 'semi-auto') {
            ACA_Idea_Service::generate_ideas();
        } elseif ($working_mode === 'full-auto') {
            $idea_ids = ACA_Idea_Service::generate_ideas();
            if (!is_wp_error($idea_ids)) {
                foreach ($idea_ids as $id) {
                    ACA_Draft_Service::write_post_draft($id);
                }
            }
        }
    }
}
```
【F:includes/class-aca-cron.php†L16-L137】

## Services

- **Idea Service** – Generates post titles and stores them in the custom ideas table. Titles come from Gemini prompts using recent post titles as context.
- **Draft Service** – Writes full drafts, enriches them with sources, internal links and optional images via Unsplash/Pexels or DALL‑E. Also supports plagiarism checks with Copyscape.
- **Style Guide Service** – Builds a style guide by analyzing existing posts and stores it for use in draft prompts.

Example from idea generation:

```php
ACA_Log_Service::add('Attempting to generate new ideas.');
$prompts = ACA_Style_Guide_Service::get_default_prompts();
$posts = get_posts([...]);
$prompt = sprintf($prompts['idea_generation'], $existing_titles, $limit);
$response = ACA_Gemini_Api::call($prompt);
```
【F:includes/services/class-aca-idea-service.php†L28-L60】

## API Clients

The plugin communicates with external services via small wrappers. The Gemini client handles key storage and request payloads. Example call flow:

```php
$api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $api_key;
$response = wp_remote_post($api_url, [...]);
```
【F:includes/api/class-aca-gemini-api.php†L19-L72】

License validation uses Gumroad:

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

`includes/admin/` provides the WordPress admin pages, dashboard and AJAX endpoints. The dashboard lists ideas, generates clusters and shows logs. AJAX actions include testing the API connection, generating ideas, writing drafts and validating licenses. Frontend scripts are in `admin/js/aca-admin.js`.

## Security & Utilities

Sensitive keys are encrypted using `ACA_Encryption_Util` before storage:

```php
$sanitized_key = sanitize_text_field($input);
return aca_ai_content_agent_encrypt($sanitized_key);
```
【F:includes/admin/class-aca-admin-init.php†L213-L229】

## Composer

Dependencies are managed via Composer (`composer.json`). Run `composer install` to fetch Action Scheduler.

## Translations

`languages/aca.pot` contains translation strings. Place translated `.po` files in the same directory.

## Uninstallation

Running the `uninstall.php` script fully cleans plugin data:

```php
require_once plugin_dir_path(__FILE__) . 'includes/core/class-aca-uninstaller.php';
ACA_Uninstaller::uninstall();
```
【F:uninstall.php†L9-L17】

## Further Reading

User-facing features and installation steps are detailed in `readme.txt`. Lines 23‑54 summarize all major capabilities of the plugin.

