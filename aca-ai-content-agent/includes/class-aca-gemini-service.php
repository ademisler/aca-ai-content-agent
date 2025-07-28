<?php
/**
 * ACA Gemini Service
 * Handles all AI API calls to Google Gemini
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Gemini_Service {
    
    private $api_key;
    private $text_model = 'gemini-2.5-flash';
    private $image_model = 'imagen-3.0-generate-002';
    private $base_url = 'https://generativelanguage.googleapis.com/v1beta/models/';
    
    public function __construct() {
        $settings = get_option('aca_settings', array());
        $this->api_key = isset($settings['geminiApiKey']) ? $settings['geminiApiKey'] : '';
    }
    
    /**
     * Analyzes the style of a website's content to create a style guide
     * This simulates reading the 20 most recent posts to determine the style
     */
    public function analyze_style() {
        $prompt = '
        Analyze the common writing style of a professional tech and marketing blog and generate a JSON object that describes it. 
        Imagine you have read the 20 most recent articles from the blog to gather this information.
        This JSON object will be used as a "Style Guide" for generating new content.
        The JSON object must strictly follow this schema:
        {
          "tone": "string (e.g., \'Friendly and conversational\', \'Formal and professional\', \'Technical and informative\', \'Witty and humorous\')",
          "sentenceStructure": "string (e.g., \'Mix of short, punchy sentences and longer, more descriptive ones\', \'Primarily short and direct sentences\', \'Complex sentences with multiple clauses\')",
          "paragraphLength": "string (e.g., \'Short, 2-3 sentences per paragraph\', \'Medium, 4-6 sentences per paragraph\')",
          "formattingStyle": "string (e.g., \'Uses bullet points, bold text for emphasis, and subheadings (H2, H3)\', \'Minimal formatting, relies on plain text paragraphs\')"
        }
        ';
        
        $response = $this->make_api_request($this->text_model . ':generateContent', array(
            'contents' => array(
                array('parts' => array(array('text' => $prompt)))
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json',
                'response_schema' => array(
                    'type' => 'OBJECT',
                    'properties' => array(
                        'tone' => array('type' => 'STRING'),
                        'sentenceStructure' => array('type' => 'STRING'),
                        'paragraphLength' => array('type' => 'STRING'),
                        'formattingStyle' => array('type' => 'STRING')
                    ),
                    'required' => array('tone', 'sentenceStructure', 'paragraphLength', 'formattingStyle')
                )
            )
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to analyze style: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        throw new Exception('Invalid response from Gemini API');
    }
    
    /**
     * Generates new blog post ideas based on a style guide and optional search data
     */
    public function generate_ideas($style_guide_json, $existing_titles, $count = 5, $search_console_data = null) {
        $search_console_prompt = '';
        if ($search_console_data) {
            $search_console_prompt = '
    **Leverage Search Console Data:**
    Use the following Google Search Console data to generate highly relevant and strategic ideas. Focus on answering popular user questions or improving underperforming content.
    - Top Performing Queries (create content on related topics): ' . implode(', ', $search_console_data['topQueries']) . '
    - Underperforming Pages (consider creating improved/updated versions or related topics): ' . implode(', ', $search_console_data['underperformingPages']) . '
    ';
        }
        
        $prompt = "
        Based on the following Style Guide, generate a JSON array of $count new, interesting, and SEO-friendly blog post titles.
        $search_console_prompt
        The titles should be unique and not present in the \"existing titles\" list.
        The output must be a valid JSON array of strings.

        **Style Guide:**
        $style_guide_json

        **Existing Titles to avoid:**
        " . implode(', ', $existing_titles);
        
        $response = $this->make_api_request($this->text_model . ':generateContent', array(
            'contents' => array(
                array('parts' => array(array('text' => $prompt)))
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json',
                'response_schema' => array(
                    'type' => 'ARRAY',
                    'items' => array('type' => 'STRING')
                )
            )
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to generate ideas: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        throw new Exception('Invalid response from Gemini API');
    }
    
    /**
     * Generates new blog post ideas based on an existing idea
     */
    public function generate_similar_ideas($base_title, $existing_titles) {
        $prompt = '
        Based on the blog post title "' . $base_title . '", generate a JSON array of 3 unique and creative new blog post titles that are similar in topic or angle.
        The new titles must be distinct from the original title and from each other.
        These titles must also not be in the following list of already existing titles: ' . implode(', ', $existing_titles) . '.
        The output must be a valid JSON array of strings. For example: ["New Title 1", "New Title 2", "New Title 3"]
        ';
        
        $response = $this->make_api_request($this->text_model . ':generateContent', array(
            'contents' => array(
                array('parts' => array(array('text' => $prompt)))
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json',
                'response_schema' => array(
                    'type' => 'ARRAY',
                    'items' => array('type' => 'STRING')
                )
            )
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to generate similar ideas: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        throw new Exception('Invalid response from Gemini API');
    }
    
    /**
     * Creates a full blog post draft from a title and style guide
     */
    public function create_draft($title, $style_guide_json, $existing_published_posts = array()) {
        $internal_link_prompt = '';
        if (!empty($existing_published_posts)) {
            $internal_link_prompt = '5.  **Internal Links:** Directly embed at least 3 relevant internal links within the article content. The links MUST be in markdown format: [anchor text](URL). Choose the most appropriate anchor text from the content you write. You must link to the provided existing published posts. Analyze their content to ensure the links are contextually appropriate. Do NOT add a list of links at the end; they must be embedded in the text.
        
        **Existing Posts Available for Linking:**';
            
            foreach ($existing_published_posts as $post) {
                $internal_link_prompt .= "
---
Title: {$post['title']}
URL: {$post['url']}
Content:
{$post['content']}
---";
            }
        } else {
            $internal_link_prompt = '5.  **Internal Links:** No existing posts were provided, so do not add any internal links.';
        }
        
        $prompt = "
        **Task:** Write a blog post draft based on the provided title and style guide.

        **Requirements:**
        1.  **Title:** $title
        2.  **Style Guide:** Adhere strictly to the following style: $style_guide_json
        3.  **Length:** The article should be approximately 800 words.
        4.  **Structure:** The article must have a clear structure:
            - An engaging introduction.
            - A body with multiple sections using H2 and H3 subheadings.
            - A concluding summary.
        $internal_link_prompt
        6.  **SEO:** Generate a concise, SEO-friendly meta title (under 60 characters) and meta description (under 160 characters).
        7.  **Focus Keywords:** Generate exactly 5 relevant SEO focus keywords for the article. The keywords should be a mix of short-tail and long-tail and be highly relevant to the main topic.

        **Output Format:**
        Your entire response MUST be a single, valid JSON object following this exact schema:
        {
          \"content\": \"The full blog post content in markdown format, WITH THE INTERNAL LINKS EMBEDDED as requested.\",
          \"metaTitle\": \"The generated SEO meta title.\",
          \"metaDescription\": \"The generated SEO meta description.\",
          \"focusKeywords\": [\"An array of 5 relevant SEO focus keywords.\"]
        }
        ";
        
        $system_instruction = "You are a professional blog writer and SEO expert. Your persona is that of an experienced human writer. Under NO circumstances should you ever mention that you are an AI or a language model. Your response must be direct and in the requested format, written from a human perspective.";
        
        $response = $this->make_api_request($this->text_model . ':generateContent', array(
            'system_instruction' => array(
                'parts' => array(array('text' => $system_instruction))
            ),
            'contents' => array(
                array('parts' => array(array('text' => $prompt)))
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json',
                'response_schema' => array(
                    'type' => 'OBJECT',
                    'properties' => array(
                        'content' => array('type' => 'STRING'),
                        'metaTitle' => array('type' => 'STRING'),
                        'metaDescription' => array('type' => 'STRING'),
                        'focusKeywords' => array(
                            'type' => 'ARRAY',
                            'items' => array('type' => 'STRING')
                        )
                    ),
                    'required' => array('content', 'metaTitle', 'metaDescription', 'focusKeywords')
                )
            )
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to create draft: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        throw new Exception('Invalid response from Gemini API');
    }
    
    /**
     * Generates an image using AI based on a text prompt and a specified style
     */
    public function generate_image($prompt, $style = 'digital_art') {
        $critical_rule = "CRITICAL RULE: The image MUST NOT contain any text, letters, words, or symbols. It must be a purely graphical illustration or photograph. The presence of any text is a failure.";
        
        $refined_prompt = '';
        switch ($style) {
            case 'photorealistic':
                $refined_prompt = "A high-quality, photorealistic stock photo, suitable for a professional blog post about \"$prompt\". Clean aesthetic, professional lighting. $critical_rule";
                break;
            case 'digital_art':
            default:
                $refined_prompt = "A professional blog post image for an article titled: \"$prompt\". Digital art style, high quality, visually appealing. $critical_rule";
                break;
        }
        
        $response = $this->make_api_request($this->image_model . ':generateImages', array(
            'prompt' => $refined_prompt,
            'config' => array(
                'numberOfImages' => 1,
                'outputMimeType' => 'image/jpeg',
                'aspectRatio' => '16:9'
            )
        ));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to generate image: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['generatedImages'][0]['image']['imageBytes'])) {
            return $data['generatedImages'][0]['image']['imageBytes'];
        }
        
        throw new Exception('Invalid response from Gemini API');
    }
    
    /**
     * Make API request to Gemini
     */
    private function make_api_request($endpoint, $data) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Gemini API key not configured');
        }
        
        $url = $this->base_url . $endpoint . '?key=' . $this->api_key;
        
        $args = array(
            'body' => json_encode($data),
            'headers' => array(
                'Content-Type' => 'application/json'
            ),
            'timeout' => 60
        );
        
        return wp_remote_post($url, $args);
    }
}