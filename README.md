### **Project Implementation Document: ACA – AI Content Agent**

**Version:** 1.0
**Date:** 23.07.2025
**Project Owner:** Adem Isler (developer's personal website: ademisler.com)

### **Section 1: Project Vision and Core Philosophy**

#### **1.1. Project Name**
ACA – AI Content Agent

#### **1.2. Vision**
To create an intelligent plugin for WordPress sites that learns the existing content tone and style to autonomously generate new, SEO-friendly, high-quality, reliable, and consistent content, acting as a "digital content strategist and editor."

#### **1.3. Core Philosophy: "User-Controlled Automation"**
ACA will not be a "black box" that takes control away from the user. It will provide transparency at every step, granting the user the authority to intervene and make the final decision. ACA is designed not to replace a writer, but to be a super-assistant that saves them time and sparks their creativity.

#### **1.4. Target Audience**
Bloggers, content creators, SME and corporate website owners, digital marketing and SEO agencies, and content managers of e-commerce sites.

---

### **Section 2: Basic Configuration and Setup (Admin Panel)**

This is the first and most crucial step where the user customizes ACA for their own site.

#### **2.1. API and Connection Settings**
*   **Google Gemini API Key:** A secure text input field.
*   **Connection Test:** A "Test Connection" button that checks the validity of the entered API key and whether there is access to the API.

#### **2.2. Operating Mode and Automation Level**
*   **Manual Mode:** The plugin will not perform any background processes until the user manually triggers it from the ACA Control Panel.
*   **Semi-Automatic Mode (Idea and Approval):** The plugin only generates new blog post ideas at specified intervals. When the user approves these ideas from the panel, a draft post is created for the selected ones.
*   **Fully Automatic Mode (Draft Creation):** The processes of finding ideas and creating draft posts are fully automated. **Critical Note:** All generated content, without exception, is always saved as a "Draft" and is never published directly.

#### **2.3. Content Analysis and Learning Rules**
*   **Analysis Targeting:** Areas where the user can select which content ACA should read to "learn the style":
    *   **Content Types:** Posts, Pages (selectable via checkboxes).
    *   **Categories:** Selection of categories to be scanned or excluded for style analysis.
*   **Analysis Depth:** The number of recent posts to be used as a basis for learning the style (e.g., 10, 20, 50).

#### **2.4. Content Generation Rules**
*   **Automation Frequency:** (For automatic modes) The frequency of generating new ideas/drafts using WordPress's internal scheduler (WP-Cron) (e.g., Every day at 03:00, Once a week, Once a month).
*   **Default Author:** Selection of the WordPress user under whose name the created drafts will be saved.
*   **Generation Limit:** The maximum number of ideas/drafts to be generated in each automation cycle (to keep API costs under control).

---

### **Section 3: Content Generation Engine: Learning, Ideation, and Writing**

This section defines the core working mechanism of the plugin.

#### **3.1. Step 1: Style Guide Generation (Learning Phase)**
*   **Process:** A WP-Cron task that runs in the background at specified intervals (e.g., once a week).
*   **Operation:** The plugin retrieves the content of the last X posts according to the user's settings. It sends this content to the Gemini API with a special prompt:
    *   *"Analyze the following texts. Create a 'Style Guide' that defines their writing tone (e.g., friendly, formal, humorous), sentence structure (short, long), paragraph length, and general formatting style (use of lists, bold text, etc.). This guide should be like an instructional text given to another writer to mimic this style."*
*   **Output:** This "Style Guide Prompt" returned from the API is stored in the database and used as the core identity for all subsequent content generation.

#### **3.2. Step 2: Idea Generation (Creativity Phase)**
*   **Process:** Triggered during an automation cycle or manually.
*   **Operation:** The plugin analyzes the site's recent post titles and categories. It sends the following prompt to Gemini:
    *   *"The current blog post titles are: [...]. Suggest 5 new, SEO-friendly, and engaging blog post titles that are related to these topics but do not repeat them."*
*   **Output:** The generated titles are saved in the "Ideas" section of the ACA Control Panel.

#### **3.3. Step 3: Content Writing (Production Phase)**
*   **Process:** Triggered by user approval or in fully automatic mode.
*   **Operation:** When an idea is to be converted into a post, the plugin combines the following parts to create a final, complex prompt:
    1.  **Style Guide:** The prompt created in Section 3.1.
    2.  **Writing Task:** *"Adhering to the style guide above, write an SEO-friendly blog post of approximately 800 words with the title '[Selected Post Idea]'. Structure the post with an introduction, a main body containing H2 and H3 subheadings, and a conclusion."*
    3.  **Metadata and Source Request:** *"At the end of the post, add 5 tags related to the article, a 155-character meta description, and at least 2 reliable source URLs for any significant data mentioned in the article."*
    4.  **Formatting Instruction:** *"To help me parse the output, provide it in the following format: ---ARTICLE CONTENT--- [Article] ---TAGS--- [Tags] ---META DESCRIPTION--- [Description] ---SOURCES--- [URLs]"*
*   **Output:** The plugin receives this structured response, creates the draft using `wp_insert_post()`, and saves the metadata to the respective fields.

---

### **Section 4: Content Quality, Reliability, and Enrichment**

This layer ensures that the generated content goes beyond standard text.

#### **4.1. Reliability and Originality**
*   **Sourced Content:** Links to reliable sources (e.g., .gov, .edu, scientific publications) are added to the generated articles, especially for sections containing data and statistics. These sources are listed at the end of the article.
*   **Automatic Plagiarism Check:** Each generated draft is automatically scanned with the API of a service like Copyscape. The "Plagiarism Score" is displayed in the ACA panel to assure the user of the content's originality.

#### **4.2. Content Enrichment**
*   **Smart Featured Image:** Suggests royalty-free stock images suitable for the content via Pexels/Unsplash APIs. At an advanced level, it offers the option to generate completely original images specific to the article using APIs like DALL-E 3.
*   **Automatic Internal Linking:** Adds SEO-friendly internal links to the site's older and relevant posts within new drafts. The maximum number of links to be added can be set in the settings panel.
*   **Data-Driven Sections:** Increases the content's authority by finding and adding current statistics, data, or simple tables relevant to the article's topic.

---

### **Section 5: Strategic Planning and Advanced Management**

Features that turn ACA into a "content strategist."

#### **5.1. Strategic Planning Tools**
*   **Content Cluster Planner:** Suggests sub-topic titles ("Cluster Content") to support a main topic ("Pillar Content") defined by the user and plans the interlinking of this content.
*   **Content Update Assistant:** Identifies outdated posts on the site and provides concrete suggestions to update them with the latest information.
*   **Google Search Console Integration:** Analyzes search performance to identify topics that users are searching for but are not answered on the site, and generates new content ideas accordingly.

#### **5.2. Advanced Adaptability**
*   **"Prompt Editor" Interface:** An interface for advanced users to manually edit the base prompts used by ACA in the background (e.g., Style Guide, Content Writing).
*   **Brand Voice Profiles:** The ability to define and save different writing styles and tones of voice for various content types (e.g., blog, technical documentation, product description).
*   **User Feedback Loop:** "👍/👎" buttons for each generated idea/draft. This feedback is used to help the system produce more accurate results over time.

---

### **Section 6: Governance, Security, and Accessibility**

#### **6.1. Management and Cost Control**
*   **Role-Based Permissions:** Defines different permissions within the ACA panel based on WordPress user roles (Admin, Editor, Author) (e.g., an Author only sees drafts, an Editor can approve ideas, an Admin changes all settings).
*   **API Usage Management:** An option to set a monthly API token/call limit in the settings panel and a warning system when this limit is approached. A counter in the panel displays the current month's API usage in real-time.

#### **6.2. Technical Architecture and Standards**
*   **API Communication Architecture:** All API calls are managed through a central and reusable function detailed in Section 7.
*   **Tech Stack:** PHP 7.4+, Google Gemini API, WP-Cron. A modern JS library (React/Vue.js) or Vanilla JS for the admin panel.
*   **Accessibility and Mobile Responsiveness:** All admin panels are designed to be responsive for easy use on mobile devices and will fully comply with accessibility (a11y) standards, such as keyboard navigation and the use of aria-labels.

---

### **Section 7: Central API Communication Architecture**

#### **7.1. Purpose**
To manage all Gemini API calls through a single, central function that prevents code repetition, is easy to maintain, and is reusable.

#### **7.2. Function Structure: `aca_call_gemini_api( $prompt, $system_instruction = '' )`**
This function:
1.  Checks the API key.
2.  Prepares the JSON `$payload` based on the given `$prompt` and optional `$system_instruction`.
3.  Makes the API request securely using WordPress's `wp_remote_post()` function. The timeout duration is increased for potentially long-running operations like content generation (e.g., 60-120 seconds).
4.  Comprehensively checks the response from the API: `WP_Error` check, HTTP status code check (200), and Gemini API's own error messages.
5.  If all checks pass, it returns the generated text cleanly. Otherwise, it returns a loggable `WP_Error` object.

#### **7.3. Implementation**
All other features of the plugin (Style Guide, Idea Generation, Content Writing, etc.) will operate by simply calling this central function, rather than repeating complex API code. This maximizes the code's readability, security, and scalability.

---

### **Section 8: User Experience (UX) and Interface (UI) Philosophy**

#### **8.1. Core Interface Philosophy**
*   **Clarity and Focus:** The interface will not overwhelm the user with unnecessary information. Each screen will have a single primary purpose (e.g., Settings, Ideas, Reports).
*   **Guided Experience:** Especially for new users, tips, tooltips, and brief descriptions will be used to help them understand what to do.
*   **Visual Hierarchy:** Important actions (e.g., "Create Draft") and information (e.g., "API Limit") will be visually highlighted.

#### **8.2. Onboarding Wizard**
When the plugin is first activated, a setup wizard runs, guiding the user step-by-step through the basic settings:
1.  **Welcome:** A brief introduction to the project.
2.  **API Connection:** Entering and testing the API key.
3.  **Basic Learning Settings:** Quickly selecting which content types to analyze.
4.  **Mode Selection:** Choosing one of the Manual, Semi-Automatic, or Fully Automatic modes.
5.  **Completed:** Directing the user to the main ACA Control Panel.

#### **8.3. Central Dashboard**
The main panel, located in the WordPress admin menu under "ACA," will include the following components:
*   **Overview:** Quick stats like API usage status, number of pending ideas, and number of created drafts.
*   **Idea Stream:** A section listing new content ideas awaiting approval, with "Approve and Write" or "Reject" buttons.
*   **Recent Activities:** A log of recent actions like "Style guide updated," "3 new ideas generated."
*   **Quick Actions:** Shortcut buttons like "Generate New Ideas Now," "Manually Update Style Guide."

#### **8.4. Notification Center**
Informs the user when the API key is invalid, when the API usage limit reaches 80%, when new ideas are ready for approval (optional), or when a content creation process fails.

---

### **Section 9: Monetization and Support Model**

#### **9.1. Licensing Model: Freemium**
*   **ACA (Free Version):** Published on WordPress.org. Includes limitations such as 5 ideas and 2 drafts per month. Operates in manual mode. Advanced strategy and enrichment features are disabled.
*   **ACA Pro (Premium Version):** Based on an annual subscription. Unlocks all features (automatic modes, unlimited generation, plagiarism check, strategy tools, etc.).

#### **9.2. Sales and Licensing Platform: Gumroad**
*   **Platform:** The **Gumroad** platform will be used for the sale of the ACA Pro version, payment processing, and license key management.
*   **License Key Mechanism:** Users will receive a unique, individual license key upon purchase through Gumroad. This license key will be used to unlock Pro features and receive updates by entering it into the relevant field in the plugin's WordPress admin panel.
*   **Validation:** The plugin will have a mechanism to verify the validity of the entered license key.

#### **9.3. Pricing and License Types**
*   Single Site License
*   3-Site License
*   Agency License (Unlimited Sites)

#### **9.4. Support and Update Policy**
Users with an active and valid **Gumroad license key** will have access to plugin updates and technical support via a ticket system for 1 year.

---

### **Section 10: Performance, Optimization, and Resource Management**

#### **10.1. Asynchronous Operations (Background Tasks)**
Long-running processes such as content analysis, style guide creation, and content writing will run asynchronously in the background using WordPress's Action Scheduler library, without locking the user interface.

#### **10.2. Database Optimization**
The plugin will create custom database tables to store its own data (settings, ideas, logs, etc.), preventing the bloating of the `wp_posts` and `wp_postmeta` tables and improving query performance.

#### **10.3. Smart Caching**
Frequently accessed but rarely changing data (e.g., the Style Guide) will be temporarily cached using the WordPress Transients API. This prevents unnecessary API and database calls.

---

### **Section 11: Security, Data Privacy, and Legal Compliance**

#### **11.1. Data Security**
*   **API Key Storage:** The API key will be stored in the database in an encrypted format.
*   **Secure API Calls:** All API calls will be made with SSL verification enabled.
*   **Capability Checks:** All actions will be controlled according to WordPress's capability system.

#### **11.2. Data Privacy (GDPR Compliance)**
*   **Transparency:** The user is clearly informed that an external API is being used.
*   **Data Minimization:** Only the minimum data required for the task is sent to the API.
*   **No-Data-Retention Policy:** The full text of site content taken for analysis is not permanently stored in the plugin's database.
*   **GDPR Tools Compliance:** The plugin will be compatible with WordPress's "Export/Erase Personal Data" tools.

#### **11.3. Legal Disclaimer**
It will be clearly stated in the admin panel and documentation that all generated content is a "draft" and must be checked, edited, and verified by the user before publication. It will be emphasized that the final responsibility lies with the user.
