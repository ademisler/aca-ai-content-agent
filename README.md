# Role and Goal

You are an expert full-stack WordPress plugin developer. Your primary specialty is building modern, secure, and high-performance plugins that use a React-based Single Page Application (SPA) for the admin interface, communicating with a robust PHP backend via the WordPress REST API.

Your goal is to generate the complete, production-ready code for a WordPress plugin named "ACA - AI Content Agent". This plugin must be a pixel-perfect, feature-perfect, one-to-one replica of the provided React prototype. All files from the prototype (React components, services, types, configs) and the detailed technical specification document ("WordPress Plugin Development Guide v2.0") are provided as your context. You must use this context as the single source of truth.

# Core Principles (The Golden Rules)

Prototype is the Ultimate Truth: If there is any ambiguity, the observable behavior and code structure of the React prototype (.tsx files) is the correct implementation. The UI, UX, animations, and state management logic must be identical.

Guide is the Blueprint: The "WordPress Plugin Development Guide v2.0" is your architectural blueprint. Follow its structure for database schemas, REST endpoints, and WordPress integration logic.

No New Features: Do not add any features, UI elements, or logic not present in the prototype or the guide. Your task is replication, not innovation.

Adhere to Coding Standards: Use official WordPress coding standards for all PHP code and modern, clean, standard practices for all React/TypeScript and JavaScript code. Ensure all code is well-commented.

# Project Architecture Overview

Backend (PHP): A main plugin file, activation/deactivation hooks, custom database tables, WP-Cron jobs, and a comprehensive set of REST API endpoints under the /aca/v1 namespace.

Frontend (React): A Single Page Application (SPA) that runs on a single WordPress admin menu page. It will be built using the provided React/TypeScript source files. Navigation within the plugin MUST NOT cause a page reload.

Data Storage:

wp_options for aca_settings and aca_style_guide.

wp_posts and wp_postmeta for drafts and published content.

Two custom tables: {$wpdb->prefix}aca_ideas and {$wpdb->prefix}aca_activity_logs.

Communication: The React frontend communicates exclusively with the PHP backend via REST API endpoints, secured with WordPress nonces.

# Step-by-Step Implementation Plan

You are to generate the code following this precise plan.

Step 1: Create the Plugin Foundation (PHP)

Create the main plugin file (ai-content-agent.php).

Implement the plugin header comments.

Implement the register_activation_hook to call a function that executes the tasks from Section 2.1 of the guide.

Implement the register_deactivation_hook to clear cron jobs as per Section 2.2.

Create the admin menu page that will serve as the root <div> for our React application.

Step 2: Implement Database and Data Structures (PHP)

In the activation function, write the PHP and SQL code to create the two custom tables (wp_aca_ideas, wp_aca_activity_logs) exactly as defined in Section 5.2 of the guide.

Also in the activation function, create the default aca_settings and aca_style_guide options in wp_options. The default settings structure must match Section 7 of the guide.

Step 3: Implement All WordPress REST API Endpoints (PHP)

Create a new class or set of files to manage all REST API registrations.

Register all endpoints listed in Section 5.3 of the guide under the /aca/v1 namespace.

For EVERY endpoint, implement permission checks (current_user_can('manage_options')) and nonce validation (wp_verify_nonce).

Endpoint Logic:

Settings (/settings): GET and POST handlers for the aca_settings option. CRITICAL: On the POST handler, do not overwrite saved API keys if the incoming value is empty/null. On the GET handler, return boolean true if an API key exists, but NEVER return the key itself.

Style Guide (/style-guide, /analyze-style): Implement CRUD for the aca_style_guide option. For /analyze-style, implement the logic described in Section 4.2, using the exact prompt from services/geminiService.ts -> analyzeStyle.

Ideas (/ideas, etc.): Implement all CRUD operations for the wp_aca_ideas table. For generation endpoints (/ideas and /ideas/similar), use the exact prompts from services/geminiService.ts.

Create Draft (/create-draft): This is the most complex endpoint. Implement the entire 9-step process detailed in Section 5.3 of the guide. This involves calling the Gemini API (using the exact createDraft prompt), handling image generation/sideloading (media_handle_sideload), creating the post (wp_insert_post), and setting all post meta (_aca_ prefixes).

Posts (/posts, etc.): Implement handlers to get posts by status, publish a post, and schedule a post (by updating the _aca_scheduled_for meta field).

Activity Logs (/activity-logs): Implement a GET handler to retrieve logs from the wp_aca_activity_logs table.

Step 4: Implement Background Jobs (WP-Cron) (PHP)

Create functions for the periodic jobs described in Section 5.4 (Style Analysis, Semi-Auto Ideas, Full-Auto Cycle).

Register these functions with WP-Cron, ensuring they are scheduled and unscheduled correctly on plugin activation/deactivation and settings changes.

Step 5: Configure Frontend Build and Integration (PHP/Build Process)

Write the PHP function to enqueue the compiled JavaScript and CSS from the React build process onto the plugin's admin page.

Use wp_localize_script to pass essential data from PHP to React, including the REST API URL (get_rest_url(null, 'aca/v1/')) and a security nonce (wp_create_nonce('wp_rest')).

Step 6: Refactor Frontend Services to Use REST API (React/TypeScript)

Go through all files in prototype/services/.

Remove the direct calls to the Gemini API and stock photo APIs.

Replace them with fetch calls to the corresponding WordPress REST API endpoints you defined in Step 3.

Ensure every fetch request includes the X-WP-Nonce header with the nonce provided via wp_localize_script.

# Critical Implementation Details (Pay Close Attention)

UI/UX Fidelity: The final plugin's appearance and feel must be indistinguishable from the prototype. This includes all Tailwind CSS classes, the responsive layout (mobile slide-out menu), and the global styles for the background and scrollbars. All animations, especially animate-fade-in-fast and the Toast.tsx timings, are mandatory.

Granular State Management: Replicate the prototype's state management perfectly. The isLoading object with dynamic keys (e.g., isLoading['draft-123']) is not optional; it's essential for the UX. The isDirty, publishingId, and isSaving states must control button states and visual feedback exactly as in the prototype.

Verbatim API Prompts: The prompts sent to the Gemini API from your PHP backend MUST be copied VERBATIM from the services/geminiService.ts file. This includes the structure, wording, JSON schema definitions, and the critical system instructions. This is non-negotiable for correct AI output.

Security: Do not forget wp_verify_nonce and current_user_can checks on every single REST endpoint that modifies data. Sanitize all inputs and escape all outputs.

# Final Output Format

Please provide the complete and final code for all necessary PHP, TypeScript, and configuration files. Organize the files into a logical directory structure (e.g., /build, /src, /includes, ai-content-agent.php). Add comments to your code where necessary to explain complex logic or to reference the specific section of the guide it implements.



---

# ACA - AI Content Agent: WordPress Plugin Development Guide (v2.0)

## 1. Project Vision & The Golden Rule

**Project Name:** AI Content Agent (ACA)
**Prototype Source Code:** The complete React/TypeScript project provided.
**Final Goal:** To create a fully functional WordPress plugin that is a **one-to-one, pixel-perfect, feature-perfect replica** of the provided React prototype. All features, functions, UI elements, animations, interactions, and the overall user experience (UX) must be flawlessly copied.

**The Golden Rule:** This document is your primary specification, but the ultimate source of truth is the prototype's source code and its observable behavior. **When in doubt, the React prototype's behavior is the correct behavior.** You must analyze the provided `.tsx` files to understand state management, component logic, and the user flow. The goal is a perfect clone.

---

## 2. Plugin Lifecycle & Initialization

### 2.1. Plugin Activation (`register_activation_hook`)
Upon activation, the plugin MUST perform the following actions:
1.  **Create Custom Tables:** Execute the SQL commands in **Section 5.2** to create the `wp_aca_ideas` and `wp_aca_activity_logs` tables.
2.  **Set Default Settings:** Create the `aca_settings` option in `wp_options` using `add_option()`. The default value should be a serialized PHP array matching the structure detailed in **Section 7**.
3.  **Initialize Style Guide:** Create an empty `aca_style_guide` option (`add_option('aca_style_guide', null)`).
4.  **Schedule Cron Jobs:** Set up the initial WP-Cron schedules (see Section 5.4).

### 2.2. Plugin Deactivation (`register_deactivation_hook`)
1.  **Clear Cron Jobs:** All custom WP-Cron schedules related to ACA must be unscheduled to prevent errors.

---

## 3. Core Functional Requirements (Replicating `App.tsx`)

The plugin must exactly mirror all features from the prototype. The React state management in `App.tsx` is the blueprint for the backend data flow and frontend reactivity.

- **`useState` to Data Mapping:** Every `useState` in `App.tsx` maps to a WordPress data source. Your REST API will be the bridge.
    - `const [ideas, setIdeas]` -> `wp_aca_ideas` table
    - `const [posts, setPosts]` -> `wp_posts` table (`post_type='post'`) and associated post meta.
    - `const [styleGuide, setStyleGuide]` -> `aca_style_guide` in `wp_options`
    - `const [settings, setSettings]` -> `aca_settings` in `wp_options`
    - `const [activityLogs, setActivityLogs]` -> `wp_aca_activity_logs` table
    - **Granular Loading States:** The `isLoading` state is an object (`{ [key: string]: boolean }`). This is CRITICAL. It allows for granular feedback, e.g., showing a spinner on a specific idea card being turned into a draft (`isLoading['draft-123']`) without disabling the whole UI. This must be replicated.
    - **UX States:** `isSaving`, `publishingId`, `isConnecting` etc. are crucial for UX. The frontend must set these to `true` before an API call and `false` on completion/error. This state is client-side only but is controlled by the asynchronous nature of API calls.

---

## 4. Feature Replication (Component by Component)

### 4.1. Dashboard (`components/Dashboard.tsx`)
- **Functionality:** Replicate this component by fetching data from the corresponding REST endpoints for stats, style guide status, and activity logs. The "Quick Action" buttons must trigger the correct API calls (`/analyze-style`, `/generate-ideas`) and perfectly replicate the `isLoadingStyle` and `isLoadingIdeas` states.

### 4.2. Style Guide (`components/StyleGuideManager.tsx`)
- **Functionality:**
  - **Automatic Analysis (`handleAnalyzeStyle`):** The backend must fetch the 20 most recent posts (`post_type='post'`, `post_status='publish'`), combine their content, and send it to the Gemini API using the **exact same prompt** found in `services/geminiService.ts -> analyzeStyle`. The JSON result must be saved in the `aca_style_guide` option.
  - **Manual Editing & State:** The UI must be an exact copy.
    - **`isDirty` State:** The "Save Changes" button must be disabled until a user modifies a field. This state must be meticulously managed.
    - **Sentence Structure Slider:** This is not a simple input. Its value (`0`, `25`, `50`, `75`, `100`) maps directly to specific text descriptions found in the `sentenceStructureMap` object. This mapping logic is mandatory.

### 4.3. Idea Board (`components/IdeaBoard.tsx`)
- **Functionality:**
  - **Idea Generation (`handleGenerateIdeas`, `handleGenerateSimilarIdeas`):** Backend must use the exact prompts from `geminiService.ts`.
  - **In-Place Title Editing:** Clicking an idea title must turn it into a text input. The change must be saved on blur or when the 'Enter' key is pressed. This is a key interactive feature.
  - **Manual Add:** The form to add a new idea manually must be present and call the `/ideas/manual` endpoint. It should prevent adding empty or duplicate titles.

### 4.4. Drafts & Published (`components/DraftsList.tsx`, `components/PublishedList.tsx`)
- **Functionality:** These components list WordPress posts with `post_status = 'draft'` or `'publish'`. The "Publish" button must change the post's status and replicate the `publishingId` loading state, which grays out and disables actions on the specific post being published.

### 4.5. Content Calendar (`components/ContentCalendar.tsx`)
- **Functionality:**
  - **Visuals:** Must display drafts with a `_aca_scheduled_for` post meta value and published posts based on their `post_date`.
  - **Drag and Drop:** The `DraggableDraft` logic is critical. `onDragStart` must store the post ID. `onDrop` onto a calendar day must call an API endpoint (`/posts/:id/schedule`) to update the `_aca_scheduled_for` post meta for that post. The `dragOverDate` state, which highlights the target calendar day, must be replicated for visual feedback.

### 4.6. Settings (`components/Settings.tsx`)
- **Functionality:** All settings are stored in the `aca_settings` option.
  - **Conditional UI:** The UI logic must be copied exactly. For example, the "Auto-Publish" checkbox only appears if `mode === 'full-automatic'`. The API key input fields only appear when the corresponding `imageSourceProvider` is selected, with a fade-in animation.
  - **API Keys:** Sensitive data like API keys must be handled in `type="password"` fields and saved securely in the backend. They must never be sent back to the frontend in the `GET /settings` call. The frontend should only know *if* a key is present, not what it is.

### 4.7. Draft Modal (`components/DraftModal.tsx`)
- **Functionality:**
  - **Editing and Saving:** All fields must be editable. The `isDirty` state logic, which compares every editable field against its original value to enable the "Save" button, is essential. The `isSaving` state must be used to show a spinner during the save operation.
  - **SEO Plugin Simulation:** The "Analyze with [Plugin]" button simulates an API call with a `setTimeout`. This should be implemented to either call the actual SEO plugin's hooks/API if available, or trigger a new AI analysis for SEO suggestions. The UI for displaying the score and suggestions must be replicated.

---

## 5. Technical Architecture & WordPress Integration

### 5.1. General Structure & SPA Requirement
- **Backend (PHP):** Handles all business logic, database operations, and external API calls (to Gemini, Pexels, etc.) via WordPress REST API endpoints.
- **Frontend (React):** The entire plugin admin interface **MUST function as a Single Page Application (SPA)**.
  - A single WordPress admin page will be created to host the React app.
  - All navigation (clicking "Dashboard", "Settings", etc. in the sidebar) **MUST NOT** trigger a page reload. Instead, it will update the React `view` state, which conditionally renders the appropriate component (`<Dashboard />`, `<Settings />`, etc.).
  - The React app will be compiled into static JS and CSS files, loaded onto this single admin page.

### 5.2. Database Schema & Data Storage
- **Custom Tables:** These SQL commands MUST be executed upon plugin activation.
  ```sql
  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}aca_ideas (
      id BIGINT(20) NOT NULL AUTO_INCREMENT,
      title TEXT NOT NULL,
      status VARCHAR(20) DEFAULT 'new' NOT NULL,
      source VARCHAR(20) NOT NULL,
      created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
      PRIMARY KEY (id)
  ) {$charset_collate};

  CREATE TABLE IF NOT EXISTS {$wpdb->prefix}aca_activity_logs (
      id BIGINT(20) NOT NULL AUTO_INCREMENT,
      timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
      type VARCHAR(50) NOT NULL,
      details TEXT NOT NULL,
      icon VARCHAR(50) NOT NULL,
      PRIMARY KEY (id)
  ) {$charset_collate};
  ```
- **WordPress Options (`wp_options`):**
  - `aca_settings`: A serialized array. See Section 7 for structure.
  - `aca_style_guide`: A serialized array. See Section 7 for structure.
- **WordPress Posts (`wp_posts` & `wp_postmeta`):**
  - All drafts/publications are `post_type = 'post'`.
  - **Post Meta Keys:**
    - `_thumbnail_id`: Standard WordPress featured image.
    - `_aca_meta_title`, `_aca_meta_description`, `_aca_focus_keywords` (serialized array), `_aca_scheduled_for` (ISO 8601 date string).

### 5.3. WordPress REST API Endpoints
All endpoints MUST be under the `/aca/v1/` namespace and include robust security checks: `wp_verify_nonce` for preventing CSRF attacks and `current_user_can('manage_options')` to ensure only authorized users can perform actions.

- **Settings:** `GET /settings`, `POST /settings`
- **Style Guide:** `GET /style-guide`, `POST /style-guide`, `POST /analyze-style`
- **Ideas:** `GET /ideas`, `POST /ideas` (generate), `POST /ideas/similar`, `POST /ideas/manual`, `PUT /ideas/:id`, `DELETE /ideas/:id`
- **Posts:** `GET /posts?status=draft|publish`, `PUT /posts/:id`, `POST /posts/:id/publish`, `POST /posts/:id/schedule`
- **Activity Logs:** `GET /activity-logs`
- **Create Draft Endpoint (`POST /create-draft`):** This is the most complex flow. The backend must perform these steps in order:
    1.  Call the AI service using the **exact prompt** from `createDraft` in `geminiService.ts`.
    2.  Based on `aca_settings`, call the appropriate image service (AI or stock photo) to get an image.
    3.  If the image is from an external URL, fetch it using server-side methods (e.g., `wp_remote_get`).
    4.  Upload the image data to the WordPress Media Library using `media_handle_sideload` or equivalent. This returns an attachment ID.
    5.  Create a new post using `wp_insert_post`, saving title and content.
    6.  Set the featured image using `set_post_thumbnail()` with the attachment ID from step 4.
    7.  Save all meta fields (`_aca_meta_title`, etc.) using `update_post_meta()`.
    8.  Add an entry to the `wp_aca_activity_logs` table.
    9.  Return the new post object to the frontend.

### 5.4. Background Jobs (WP-Cron)
- WP-Cron tasks MUST be created to periodically execute server-side logic, replacing the `useEffect` timers in `App.tsx`.
- **Style Analysis:** A cron job runs every 30 minutes to replicate `handleAnalyzeStyle(true)`.
- **Semi-Automatic Mode:** If `mode` is `semi-automatic`, a cron job runs every 15 minutes to generate ideas.
- **Full-Automatic Mode:** If `mode` is `full-automatic`, a cron job runs every 30 minutes to execute the full content cycle.

### 5.5. Frontend (React Application)
- **Refactoring:** All API calls in the services must be replaced with `fetch` calls to the `/wp-json/aca/v1/...` REST API endpoints.
- **Security (Nonce):** The main PHP file must use `wp_localize_script` to pass a nonce and the REST API base URL to React. The nonce must be included in every API request header (e.g., `X-WP-Nonce`).
- **Build Process:** Use a build tool (like the existing Vite config) to compile the prototype's `src` folder into `main.js` and `main.css`, which are loaded by WordPress via `wp_enqueue_script` on the single plugin admin page.

---

## 6. Detailed UI/UX Replication Notes

The look, feel, and behavior must be copied **without exception**.

- **Styling:** The global styles in `index.html` (background `#020617`, custom scrollbars) and all Tailwind CSS classes must be preserved.
- **Animations:** The `animate-fade-in-fast` keyframe animation must be included and used where specified in the prototype.
- **Responsiveness:** The mobile-first design, including the slide-out sidebar (`Sidebar.tsx`) and the mobile header, is mandatory.
- **Component-Specific Logic:**
  - **`Toast.tsx`:** The toast notification must appear, remain visible for ~4.2 seconds, then begin a 400ms fade-out animation before being removed from the DOM. This timing is critical for good UX.
  - **Stateful UI:** All loading spinners, disabled buttons, and visual state changes (e.g., `isSaving`, `isPublishing`, `isDirty`, `publishingId`) must be perfectly replicated to provide the user with clear, real-time feedback.

---

## 7. Data Type Mapping (TypeScript to PHP)

For absolute clarity, here is how the TypeScript types from `types.ts` must be represented as PHP associative arrays.

- **`StyleGuide`**:
  ```php
  [
      'tone' => (string),
      'sentenceStructure' => (string),
      'paragraphLength' => (string),
      'formattingStyle' => (string),
      'lastAnalyzed' => (string) // ISO 8601
  ]
  ```
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
      'seoPlugin' => (string) 'none' | 'rank_math' | 'yoast'
  ]
  ```
- **`Draft` / Post Object**: This will be a standard `WP_Post` object from `get_post()` combined with its meta fields.
  ```php
  [
      'id' => (int),
      'title' => (string),
      'content' => (string),
      'status' => (string) 'draft' | 'published',
      // Meta fields
      'metaTitle' => (string),
      'metaDescription' => (string),
      'focusKeywords' => (array),
      'featuredImage' => (string) // URL to image from Media Library
      'scheduledFor' => (string) // ISO 8601
  ]
  ```

---

## 8. External API Handling (Backend Responsibility)

All external API calls must be handled in PHP to protect API keys and avoid CORS issues.

- **Gemini API:**
    - Use the official Google AI PHP client library or a reliable HTTP client.
    - Configure the models exactly as specified in `services/geminiService.ts`: `gemini-2.5-flash` for text and `imagen-3.0-generate-002` for images.
    - **CRITICAL:** You must copy the prompts from each function in `geminiService.ts` **VERBATIM**. The structure, wording, JSON schema definitions, and system instructions are essential for getting the correct output.
    - **`createDraft` System Instruction:** The `systemInstruction` "You are a professional blog writer... Under NO circumstances should you ever mention that you are an AI..." **MUST** be sent with the API call.
    - **`generateImage` Prompt Rule:** The rule `"CRITICAL RULE: The image MUST NOT contain any text..."` **MUST** be included in the image generation prompt.
- **Stock Photo APIs:**
    - **Pexels:** `https://api.pexels.com/v1/search`
    - **Unsplash:** `https://api.unsplash.com/search/photos`
    - **Pixabay:** `https://pixabay.com/api/`
    - The API keys stored in `aca_settings` must be used in the `Authorization` headers for these server-side requests.
