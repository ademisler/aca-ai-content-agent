<?php

/**
 * Gemini AI Service for content generation
 */
class ACA_Gemini_Service {

    private $api_key;
    private $base_url = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct($api_key = null) {
        $this->api_key = $api_key ?: $this->get_api_key();
    }

    private function get_api_key() {
        $settings = get_option('aca_settings', array());
        return isset($settings['gemini_api_key']) ? $settings['gemini_api_key'] : '';
    }

    /**
     * Analyze website content to create a style guide
     */
    public function analyze_style() {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Gemini API key is not configured');
        }

        // Get recent posts to analyze
        $recent_posts = get_posts(array(
            'numberposts' => 20,
            'post_status' => 'publish',
            'post_type' => 'post'
        ));

        $content_sample = '';
        foreach ($recent_posts as $post) {
            $content_sample .= $post->post_title . "\n" . wp_strip_all_tags($post->post_content) . "\n\n";
        }

        if (empty($content_sample)) {
            $content_sample = "This is a professional tech and marketing blog focused on providing valuable insights and actionable advice.";
        }

        $prompt = "Analyze the following content from a website and generate a JSON object that describes the writing style. This JSON object will be used as a 'Style Guide' for generating new content.\n\nContent to analyze:\n" . substr($content_sample, 0, 4000) . "\n\nThe JSON object must strictly follow this schema:\n{\n  \"tone\": \"string (e.g., 'Friendly and conversational', 'Formal and professional', 'Technical and informative', 'Witty and humorous')\",\n  \"sentenceStructure\": \"string (e.g., 'Mix of short, punchy sentences and longer, more descriptive ones', 'Primarily short and direct sentences', 'Complex sentences with multiple clauses')\",\n  \"paragraphLength\": \"string (e.g., 'Short, 2-3 sentences per paragraph', 'Medium, 4-6 sentences per paragraph')\",\n  \"formattingStyle\": \"string (e.g., 'Uses bullet points, bold text for emphasis, and subheadings (H2, H3)', 'Minimal formatting, relies on plain text paragraphs')\"\n}";

        $response = $this->make_request('gemini-1.5-flash:generateContent', array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json'
            )
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        return $response;
    }

    /**
     * Generate new blog post ideas
     */
    public function generate_ideas($style_guide, $existing_titles = array(), $count = 5) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Gemini API key is not configured');
        }

        $style_guide_json = is_array($style_guide) ? wp_json_encode($style_guide) : $style_guide;
        $existing_titles_str = implode(', ', $existing_titles);

        $prompt = "Based on the following Style Guide, generate a JSON array of {$count} new, interesting, and SEO-friendly blog post titles. The titles should be unique and not present in the 'existing titles' list. The output must be a valid JSON array of strings.\n\nStyle Guide:\n{$style_guide_json}\n\nExisting Titles to avoid:\n{$existing_titles_str}";

        $response = $this->make_request('gemini-1.5-flash:generateContent', array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json'
            )
        ));

        return $response;
    }

    /**
     * Generate similar ideas based on a base title
     */
    public function generate_similar_ideas($base_title, $existing_titles = array()) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Gemini API key is not configured');
        }

        $existing_titles_str = implode(', ', $existing_titles);

        $prompt = "Based on the blog post title \"{$base_title}\", generate a JSON array of 3 unique and creative new blog post titles that are similar in topic or angle. The new titles must be distinct from the original title and from each other. These titles must also not be in the following list of already existing titles: {$existing_titles_str}. The output must be a valid JSON array of strings. For example: [\"New Title 1\", \"New Title 2\", \"New Title 3\"]";

        $response = $this->make_request('gemini-1.5-flash:generateContent', array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json'
            )
        ));

        return $response;
    }

    /**
     * Create a full blog post draft
     */
    public function create_draft($title, $style_guide, $existing_posts = array()) {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Gemini API key is not configured');
        }

        $style_guide_json = is_array($style_guide) ? wp_json_encode($style_guide) : $style_guide;
        
        $internal_link_prompt = '';
        if (!empty($existing_posts)) {
            $posts_info = '';
            foreach ($existing_posts as $post) {
                $posts_info .= "---\nTitle: {$post['title']}\nURL: {$post['url']}\nContent:\n" . substr($post['content'], 0, 500) . "...\n---\n\n";
            }
            $internal_link_prompt = "5. **Internal Links:** Directly embed at least 2 relevant internal links within the article content. The links MUST be in markdown format: [anchor text](URL). Choose the most appropriate anchor text from the content you write. You must link to the provided existing published posts. Analyze their content to ensure the links are contextually appropriate. Do NOT add a list of links at the end; they must be embedded in the text.\n\n**Existing Posts Available for Linking:**\n{$posts_info}";
        } else {
            $internal_link_prompt = "5. **Internal Links:** No existing posts were provided, so do not add any internal links.";
        }

        $prompt = "**Task:** Write a blog post draft based on the provided title and style guide.\n\n**Requirements:**\n1. **Title:** {$title}\n2. **Style Guide:** Adhere strictly to the following style: {$style_guide_json}\n3. **Length:** The article should be approximately 800 words.\n4. **Structure:** The article must have a clear structure:\n   - An engaging introduction.\n   - A body with multiple sections using H2 and H3 subheadings.\n   - A concluding summary.\n{$internal_link_prompt}\n6. **SEO:** Generate a concise, SEO-friendly meta title (under 60 characters) and meta description (under 160 characters).\n7. **Focus Keywords:** Generate exactly 5 relevant SEO focus keywords for the article. The keywords should be a mix of short-tail and long-tail and be highly relevant to the main topic.\n\n**Output Format:**\nYour entire response MUST be a single, valid JSON object following this exact schema:\n{\n  \"content\": \"The full blog post content in markdown format, WITH THE INTERNAL LINKS EMBEDDED as requested.\",\n  \"metaTitle\": \"The generated SEO meta title.\",\n  \"metaDescription\": \"The generated SEO meta description.\",\n  \"focusKeywords\": [\"An array of 5 relevant SEO focus keywords.\"]\n}";

        $response = $this->make_request('gemini-1.5-flash:generateContent', array(
            'contents' => array(
                array(
                    'parts' => array(
                        array('text' => $prompt)
                    )
                )
            ),
            'generationConfig' => array(
                'response_mime_type' => 'application/json'
            ),
            'systemInstruction' => array(
                'parts' => array(
                    array('text' => 'You are a professional blog writer and SEO expert. Your persona is that of an experienced human writer. Under NO circumstances should you ever mention that you are an AI or a language model. Your response must be direct and in the requested format, written from a human perspective.')
                )
            )
        ));

        return $response;
    }

    /**
     * Generate an image using AI
     */
    public function generate_image($prompt, $style = 'photorealistic') {
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'Gemini API key is not configured');
        }

        $critical_rule = "CRITICAL RULE: The image MUST NOT contain any text, letters, words, or symbols. It must be a purely graphical illustration or photograph. The presence of any text is a failure.";

        switch($style) {
            case 'photorealistic':
                $refined_prompt = "A high-quality, photorealistic stock photo, suitable for a professional blog post about \"{$prompt}\". Clean aesthetic, professional lighting. {$critical_rule}";
                break;
            case 'digital_art':
            default:
                $refined_prompt = "A professional blog post image for an article titled: \"{$prompt}\". Digital art style, high quality, visually appealing. {$critical_rule}";
                break;
        }

        // Note: Imagen API would require different endpoint and authentication
        // For now, we'll return an error indicating this feature needs additional setup
        return new WP_Error('feature_unavailable', 'Image generation requires additional Imagen API setup');
    }

    /**
     * Make API request to Gemini
     */
    private function make_request($endpoint, $data) {
        $url = $this->base_url . $endpoint . '?key=' . $this->api_key;
        
        $response = wp_remote_post($url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => wp_json_encode($data),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $response_code = wp_remote_retrieve_response_code($response);

        if ($response_code !== 200) {
            return new WP_Error('api_error', 'Gemini API returned error: ' . $response_code . ' - ' . $body);
        }

        $decoded = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', 'Failed to decode API response');
        }

        // Extract the generated content from Gemini response
        if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
            return $decoded['candidates'][0]['content']['parts'][0]['text'];
        }

        return new WP_Error('no_content', 'No content generated by API');
    }
}