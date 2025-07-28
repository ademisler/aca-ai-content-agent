# ACA - AI Content Agent: WordPress Plugin Conversion Guide (v1.0)

## 1. Primary Objective: The Golden Rule of Conversion

**Project Name:** AI Content Agent (ACA)  
**Prototype Source Code:** The React/TypeScript project files provided with these instructions.  
**Ultimate Goal:** Create a fully functional WordPress plugin that is a **one-to-one, pixel-perfect, feature-perfect replica** of the provided React prototype. All features, functions, UI elements, animations, interactions, and the overall user experience (UX) must be flawlessly replicated.

**The Golden Rule:** While this document is your primary specification, the ultimate source of truth is the prototype's source code and observable behavior. **When in doubt, the way the React prototype behaves is the correct behavior.** You must analyze the provided `.tsx` files to understand state management, component logic, and user flow. The goal is to create a perfect clone.

---

## 2. Prerequisites & Setup

Before starting this project, ensure your development environment includes:
- **WordPress Dev Environment:** A local server like Local, MAMP, XAMPP, or a Docker-based setup.
- **Node.js & npm/yarn:** To compile the React app and manage dependencies.
- **Composer:** To manage PHP dependencies (e.g., the Google AI PHP client).
- **Code Editor:** VSCode, PhpStorm, or your preferred editor.

---

## 3. Step 1: WordPress Plugin Backend Setup (PHP)

### 3.1. Plugin File Structure
Create an organized structure for your plugin:
```
/ai-content-agent/
|-- ai-content-agent.php      # Main plugin file
|-- includes/
|   |-- class-aca-activator.php   # Activation logic
|   |-- class-aca-deactivator.php # Deactivation logic
|   |-- class-aca-rest-api.php    # REST API routes and controllers
|   |-- class-aca-cron.php        # WP-Cron jobs
|-- admin/
|   |-- css/                      # Compiled CSS files
|   |-- js/                       # The compiled React app
|-- src/                          # The prototype's React source code
|-- vendor/                       # Composer dependencies
|-- package.json
|-- vite.config.js              # (or similar build config)
```

### 3.2. Main Plugin File (`ai-content-agent.php`)
This file is responsible for bootstrapping the plugin.
- **Plugin Headers:** Add the required headers for WordPress to recognize the plugin.
- **Register Hooks:** Set up `register_activation_hook` and `register_deactivation_hook`.
- **Create Admin Menu:** Use `add_menu_page` to add the admin page that will host the React app.
- **Enqueue Scripts & Styles:** Use the `admin_enqueue_scripts` hook to load the compiled React app's JS and CSS files onto the admin page.

### 3.3. Plugin Activation (`class-aca-activator.php`)
Create a static `activate` method that runs when the plugin is activated.
- **Create Custom Tables:** Use `dbDelta` to create the `wp_aca_ideas` and `wp_aca_activity_logs` tables, using the SQL schemas in **Section 7**.
- **Set Default Options:** Add default settings to the `wp_options` table with `add_option('aca_settings', ...)` and `add_option('aca_style_guide', null)`.
- **Schedule Cron Jobs:** Schedule the WP-Cron tasks as described in **Section 3.5**.

### 3.4. REST API (`class-aca-rest-api.php`)
All data exchange will happen via the WordPress REST API.
- **Example Route Registration:**
  ```php
  add_action('rest_api_init', function () {
      // Route for getting/saving settings
      register_rest_route('aca/v1', '/settings', [
          'methods' => 'GET',
          'callback' => ['AC_Rest_Api_Controller', 'get_settings'],
          'permission_callback' => ['AC_Rest_Api_Controller', 'check_permissions']
      ]);
       register_rest_route('aca/v1', '/settings', [
          'methods' => 'POST',
          'callback' => ['AC_Rest_Api_Controller', 'save_settings'],
          'permission_callback' => ['AC_Rest_Api_Controller', 'check_permissions']
      ]);
      // ...other routes...
  });
  ```
- **Permissions Check:** All endpoints must ensure the user has the authority to manage the plugin.
  ```php
  public static function check_permissions() {
      return current_user_can('manage_options');
  }
  ```
- **Nonce Verification:** For security, check a nonce on all `POST`, `PUT`, `DELETE` requests.

### 3.5. Background Tasks (`class-aca-cron.php`)
Replace the `useEffect` timers in `App.tsx` with WP-Cron.
- **Schedule Events:** Create periodic tasks using `wp_schedule_event`.
  - **`aca_thirty_minute_event`:** Auto-analyzes the style guide and runs the content cycle in Full-Automatic Mode.
  - **`aca_fifteen_minute_event`:** Generates ideas in Semi-Automatic Mode.
- Conditionally run the logic for these tasks based on the mode in the plugin settings (`aca_settings`).

---

## 4. Step 2: React Frontend Integration

### 4.1. Refactor API Calls
Replace all direct API calls in `services/geminiService.ts` and `services/stockPhotoService.ts` with `fetch` calls to the WordPress REST API endpoints created in **Section 3.4**.

### 4.2. Build & Compilation Process
- **Vite Config (`vite.config.js`):** Configure the build output to target the `admin/js` and `admin/css` folders.
- **`package.json` Scripts:**
  ```json
  "scripts": {
    "dev": "vite",
    "build": "tsc && vite build"
  }
  ```
- Running `npm run build` will generate the static assets that WordPress will use.

### 4.3. Loading the App in WordPress
- **Admin Page HTML:** The callback function for the page created with `add_menu_page` should only contain `<div id="root"></div>`.
- **Script Enqueueing (`admin_enqueue_scripts`):**
  ```php
  function enqueue_admin_scripts($hook) {
      // Only load on our plugin page
      if ('toplevel_page_ai-content-agent' != $hook) {
          return;
      }
      wp_enqueue_style('aca-styles', plugin_dir_url(__FILE__) . 'admin/css/index.css');
      wp_enqueue_script('aca-app', plugin_dir_url(__FILE__) . 'admin/js/index.js', [], '1.0.0', true);
  }
  ```

---

## 5. Step 3: Bridging PHP and React

### 5.1. Nonce and Data Transfer
Securely pass data from PHP to React (specifically the nonce) using `wp_localize_script`. This is done where you enqueue your script.
```php
wp_localize_script('aca-app', 'aca_object', [
    'nonce' => wp_create_nonce('wp_rest'),
    'api_url' => rest_url('aca/v1/'),
]);
```

### 5.2. Frontend API Wrapper
In your React app, create a helper function that automatically sends `fetch` requests with the nonce.
```typescript
// api.ts
export async function apiFetch(path: string, options: RequestInit = {}): Promise<any> {
    const headers = {
        'X-WP-Nonce': (window as any).aca_object.nonce,
        'Content-Type': 'application/json',
        ...options.headers,
    };
    const response = await fetch(`${(window as any).aca_object.api_url}${path}`, { ...options, headers });
    
    if (!response.ok) {
        // Handle API errors gracefully
        const errorData = await response.json();
        throw new Error(errorData.message || 'An API error occurred');
    }

    return response.json();
}
```

---

## 6. Detailed Feature Implementation Guide

### 6.1. Create Draft (`POST /create-draft`)
This is the most complex flow. The backend must perform these steps in order:
1.  **Generate Content:** Call the Gemini API using the **exact same prompt** found in `geminiService.ts -> createDraft`.
2.  **Generate Image:** Based on `aca_settings`, call the Gemini image API or a stock photo service.
3.  **Download Image:** If it's an external URL, download the image to the server.
4.  **Upload Media:** Use `media_handle_sideload` to add the image to the WordPress Media Library. This will return an attachment ID.
5.  **Create Post:** Create a new post (`post_status = 'draft'`) with `wp_insert_post`.
6.  **Set Featured Image:** Use `set_post_thumbnail()` with the attachment ID from step 4 to set the featured image.
7.  **Save Metadata:** Use `update_post_meta()` to save all custom fields: `_aca_meta_title`, `_aca_meta_description`, etc.
8.  **Add Activity Log:** Make an entry in the `wp_aca_activity_logs` table.
9.  Return the newly created post object to the frontend.

### 6.2. External API Handling (Backend Responsibility)
All external API calls must be made in PHP to protect API keys and avoid CORS issues.
- **Gemini API:** Use the official Google AI PHP client library. Configure the models as specified in `geminiService.ts`: `gemini-2.5-flash` for text, `imagen-3.0-generate-002` for images. **It is critical to replicate the prompts verbatim.**
- **Stock Photo APIs:** Make server-side requests to Pexels, Unsplash, and Pixabay using the API keys stored in `aca_settings`.

---

## 7. Data Schema Reference

- **Custom Tables:** Must be created with `dbDelta` in `register_activation_hook`.
  ```sql
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  
  $ideas_table_name = $wpdb->prefix . 'aca_ideas';
  $sql_ideas = "CREATE TABLE $ideas_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    title text NOT NULL,
    status varchar(20) DEFAULT 'new' NOT NULL,
    source varchar(20) DEFAULT 'ai' NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  $logs_table_name = $wpdb->prefix . 'aca_activity_logs';
  $sql_logs = "CREATE TABLE $logs_table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    timestamp datetime NOT NULL,
    type varchar(50) NOT NULL,
    details text NOT NULL,
    icon varchar(50) NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql_ideas );
  dbDelta( $sql_logs );
  ```
- **WordPress Options (`wp_options`):**
  - `aca_settings`: A serialized PHP array.
  - `aca_style_guide`: A serialized PHP array.
- **Post Meta Keys (`wp_postmeta`):**
  - `_thumbnail_id`: Standard WordPress featured image.
  - `_aca_meta_title`, `_aca_meta_description`, `_aca_focus_keywords` (serialized array), `_aca_scheduled_for` (ISO 8601 date string).

- **Data Type Mapping (TypeScript -> PHP Array):**
  - **`AppSettings`**:
    ```php
    [
        'mode' => (string) 'manual' | 'semi-automatic' | 'full-automatic',
        'autoPublish' => (bool),
        'searchConsoleUser' => null | ['email' => (string)],
        'imageSourceProvider' => (string) 'ai' | 'pexels' | 'unsplash' | 'pixabay',
        'aiImageStyle' => (string) 'digital_art' | 'photorealistic',
        'pexelsApiKey' => (string),
        'unsplashApiKey' => (string),
        'pixabayApiKey' => (string),
        'seoPlugin' => (string) 'none' | 'rank_math' | 'yoast',
        'geminiApiKey' => (string),
    ]
    ```

---

## 8. Critical UI/UX Replication Guide

The look, feel, and behavior must be copied **without exception**.

- **Styling:** The global styles in `index.html` (background `#020617`, custom scrollbars) and all Tailwind CSS classes must be preserved.
- **Responsiveness:** The mobile-first design is mandatory, including the collapsible sidebar (`Sidebar.tsx`).
- **Component-Specific Interactions:**
  - **`Toast.tsx`:** The notification must appear, remain for ~4.2 seconds, then initiate a 400ms fade-out animation before being removed. This timing is critical for good UX.
  - **`StyleGuideManager.tsx`:** The sentence structure slider is not a simple input. Its value (`0`, `25`, `50`, `75`, `100`) maps directly to specific text descriptions in the `sentenceStructureMap` object. This mapping must be replicated.
  - **`IdeaBoard.tsx`:** The idea title must become an input field on click (`isEditing: true`) and save on blur or 'Enter' key press. This in-place editing is a key interaction.
- **Stateful UI:** All loading spinners, disabled buttons, and visual state changes (like `isSaving`, `isPublishing`, `isDirty`) must be perfectly replicated to provide clear feedback to the user.
