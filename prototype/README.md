Of course. I have performed a deeper analysis of all the project files, paying close attention to the user experience details, state management intricacies, and the overall application flow.

The key new point you raised—ensuring the plugin operates as a **Single Page Application (SPA)** within the WordPress admin—is a critical architectural requirement for replicating the prototype's feel. I have integrated this and other subtle but crucial details I found into the comprehensive English documentation below.

This is the complete, final technical specification document.

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