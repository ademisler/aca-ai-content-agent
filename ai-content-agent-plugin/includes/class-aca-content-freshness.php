<?php
/**
 * Content Freshness Analysis and Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Content_Freshness {
    
    /**
     * Analyze post freshness and return comprehensive analysis
     * 
     * @param int $post_id WordPress post ID
     * @return array Freshness analysis results
     */
    public function analyze_post_freshness($post_id) {
        // Check if Pro license is active (content freshness is a Pro feature)
        if (!is_aca_pro_active()) {
            return new WP_Error('pro_required', 'Content freshness analysis requires Pro license');
        }
        
        $post = get_post($post_id);
        if (!$post) {
            return new WP_Error('invalid_post', 'Post not found');
        }
        
        $content = $post->post_content;
        $title = $post->post_title;
        $published_date = $post->post_date;
        
        // Calculate age-based score
        $days_old = (time() - strtotime($published_date)) / (60 * 60 * 24);
        $age_score = max(0, 100 - ($days_old / 30 * 10)); // Decrease by 10 points per month
        
        // Get current SEO performance from GSC if available
        $seo_performance = $this->get_gsc_performance($post_id);
        
        // AI analysis for content relevance
        $ai_analysis = $this->analyze_with_ai($content, $title);
        
        if (is_wp_error($ai_analysis)) {
            $ai_score = 50; // Default score if AI analysis fails
            $suggestions = array('AI analysis temporarily unavailable');
        } else {
            $ai_data = json_decode($ai_analysis, true);
            $ai_score = $ai_data['freshness_score'] ?? 50;
            $suggestions = $ai_data['specific_suggestions'] ?? array();
        }
        
        $freshness_score = ($age_score + $seo_performance + $ai_score) / 3;
        
        $analysis_result = array(
            'score' => round($freshness_score, 2),
            'needs_update' => $freshness_score < 70,
            'priority' => $this->calculate_priority($freshness_score, $seo_performance),
            'suggestions' => $this->get_update_suggestions($ai_analysis),
            'age_score' => round($age_score, 2),
            'seo_score' => $seo_performance,
            'ai_score' => $ai_score,
            'days_old' => round($days_old, 0)
        );
        
        // Save analysis to database
        $this->save_freshness_analysis($post_id, $analysis_result);
        
        return $analysis_result;
    }
    
    /**
     * Get GSC performance score for a post
     * 
     * @param int $post_id
     * @return float GSC performance score (0-100)
     */
    private function get_gsc_performance($post_id) {
        // Try to get GSC data if available
        if (class_exists('ACA_Google_Search_Console')) {
            $gsc = new ACA_Google_Search_Console();
            $post_url = get_permalink($post_id);
            
            try {
                $performance_data = $gsc->get_page_performance($post_url);
                if ($performance_data && !is_wp_error($performance_data)) {
                    // Calculate score based on clicks, impressions, and CTR
                    $clicks = $performance_data['clicks'] ?? 0;
                    $impressions = $performance_data['impressions'] ?? 0;
                    $ctr = $performance_data['ctr'] ?? 0;
                    
                    // Improved scoring algorithm with realistic thresholds
                    $click_score = min(40, $clicks / 5); // Max 40 points for clicks (more realistic threshold)
                    $impression_score = min(35, $impressions / 50); // Max 35 points for impressions (balanced threshold)
                    $ctr_score = min(25, $ctr * 100); // Max 25 points for CTR (correct multiplier: 100x not 1000x)
                    
                    return $click_score + $impression_score + $ctr_score;
                }
            } catch (Exception $e) {
                error_log('ACA Content Freshness: GSC performance error: ' . $e->getMessage());
            }
        }
        
        // Fallback: Use WordPress view count or comments as performance indicator
        $view_count = get_post_meta($post_id, '_aca_view_count', true) ?: 0;
        $comment_count = wp_count_comments($post_id)->approved;
        
        return min(100, ($view_count / 100) + ($comment_count * 5));
    }
    
    /**
     * Analyze content with AI for freshness and relevance
     * 
     * @param string $content Post content
     * @param string $title Post title
     * @return string|WP_Error JSON response from AI or error
     */
    private function analyze_with_ai($content, $title) {
        // Check if Gemini API is configured
        $settings = get_option('aca_settings', array());
        if (empty($settings['geminiApiKey'])) {
            return $this->get_fallback_analysis($content, $title);
        }
        
        // Try to call the real AI analysis first, fallback to heuristic
        $ai_result = $this->call_gemini_ai_analysis($content, $title);
        if (!is_wp_error($ai_result)) {
            return $ai_result;
        }
        
        // Fallback to enhanced heuristic analysis
        return $this->get_enhanced_heuristic_analysis($content, $title);
    }
    
    /**
     * Get fallback analysis when AI is not available
     * 
     * @param string $content Post content
     * @param string $title Post title
     * @return string JSON response
     */
    private function get_fallback_analysis($content, $title) {
        $content_length = strlen($content);
        $word_count = str_word_count(strip_tags($content));
        
        // Simple heuristic analysis
        $freshness_score = 75; // Base score
        
        // Adjust based on content length
        if ($word_count < 300) {
            $freshness_score -= 20; // Too short
        } elseif ($word_count > 2000) {
            $freshness_score += 10; // Comprehensive content
        }
        
        // Check for recent dates in content
        $current_year = date('Y');
        $last_year = $current_year - 1;
        
        if (strpos($content, $current_year) !== false) {
            $freshness_score += 15; // Contains current year
        } elseif (strpos($content, $last_year) !== false) {
            $freshness_score += 5; // Contains last year
        } else {
            $freshness_score -= 10; // No recent dates
        }
        
        $analysis_result = array(
            'freshness_score' => min(100, max(0, $freshness_score)),
            'update_priority' => $freshness_score < 60 ? 5 : ($freshness_score < 80 ? 3 : 1),
            'specific_suggestions' => array(
                'Update statistics and data with current information',
                'Add recent examples and case studies',
                'Review and update external links',
                'Enhance content with new insights and trends'
            ),
            'outdated_information' => array(
                'Check for outdated statistics',
                'Verify external links are still valid',
                'Update software versions and tool references'
            ),
            'seo_improvements' => array(
                'Optimize meta description for current search trends',
                'Update focus keywords based on current search volume',
                'Add internal links to newer content'
            ),
            'readability_improvements' => array(
                'Break up long paragraphs',
                'Add subheadings for better structure',
                'Include bullet points and lists for better readability'
            )
        );
        
        return json_encode($analysis_result);
    }
    
    /**
     * Get enhanced heuristic analysis with more sophisticated checks
     * 
     * @param string $content Post content
     * @param string $title Post title
     * @return string JSON response
     */
    private function get_enhanced_heuristic_analysis($content, $title) {
        $content_text = strip_tags($content);
        $word_count = str_word_count($content_text);
        
        // Base score
        $freshness_score = 70;
        
        // Content length analysis
        if ($word_count < 300) {
            $freshness_score -= 25;
        } elseif ($word_count > 1500) {
            $freshness_score += 15;
        }
        
        // Date and time analysis
        $current_year = date('Y');
        $last_year = $current_year - 1;
        $two_years_ago = $current_year - 2;
        
        if (preg_match('/\b' . $current_year . '\b/', $content_text)) {
            $freshness_score += 20;
        } elseif (preg_match('/\b' . $last_year . '\b/', $content_text)) {
            $freshness_score += 10;
        } elseif (preg_match('/\b' . $two_years_ago . '\b/', $content_text)) {
            $freshness_score -= 5;
        } else {
            $freshness_score -= 15;
        }
        
        // Technology and trend keywords
        $tech_keywords = array('AI', 'machine learning', 'blockchain', 'cloud', 'mobile', 'app', 'digital', 'online', 'social media', 'SEO', 'analytics');
        $tech_mentions = 0;
        foreach ($tech_keywords as $keyword) {
            if (stripos($content_text, $keyword) !== false) {
                $tech_mentions++;
            }
        }
        $freshness_score += min(10, $tech_mentions * 2);
        
        // Link analysis (simplified)
        $link_count = substr_count($content, '<a ');
        if ($link_count > 0) {
            $freshness_score += min(10, $link_count);
        } else {
            $freshness_score -= 5;
        }
        
        // Image analysis
        $image_count = substr_count($content, '<img ');
        if ($image_count > 0) {
            $freshness_score += min(5, $image_count);
        }
        
        // Ensure score is within bounds
        $freshness_score = min(100, max(0, $freshness_score));
        
        // Generate priority based on score
        $priority = 1;
        if ($freshness_score < 40) {
            $priority = 5;
        } elseif ($freshness_score < 60) {
            $priority = 4;
        } elseif ($freshness_score < 80) {
            $priority = 3;
        } elseif ($freshness_score < 90) {
            $priority = 2;
        }
        
        // Generate contextual suggestions
        $suggestions = array();
        $outdated_info = array();
        $seo_improvements = array();
        $readability_improvements = array();
        
        if ($freshness_score < 70) {
            $suggestions[] = 'Update with current industry trends and developments';
            $suggestions[] = 'Add recent statistics and research findings';
            $suggestions[] = 'Include contemporary examples and case studies';
        }
        
        if ($word_count < 500) {
            $suggestions[] = 'Expand content with more detailed information';
            $readability_improvements[] = 'Add more comprehensive explanations';
        }
        
        if ($link_count < 3) {
            $seo_improvements[] = 'Add more relevant internal and external links';
        }
        
        if ($image_count == 0) {
            $readability_improvements[] = 'Add relevant images to improve visual appeal';
        }
        
        $analysis_result = array(
            'freshness_score' => $freshness_score,
            'update_priority' => $priority,
            'specific_suggestions' => $suggestions ?: array('Content appears to be relatively fresh'),
            'outdated_information' => array(
                'Verify all statistics and data points are current',
                'Check external links for validity',
                'Review technology references for accuracy'
            ),
            'seo_improvements' => array_merge($seo_improvements, array(
                'Optimize meta description for current search trends',
                'Update focus keywords based on search volume',
                'Enhance title for better click-through rates'
            )),
            'readability_improvements' => array_merge($readability_improvements, array(
                'Use clear headings and subheadings',
                'Break up long paragraphs',
                'Add bullet points where appropriate'
            )),
            'content_gaps' => array(
                'Consider adding FAQ section',
                'Include actionable takeaways',
                'Add conclusion or summary section'
            ),
            'technical_updates' => array(
                'Ensure mobile responsiveness',
                'Optimize loading speed',
                'Check for broken links'
            )
        );
        
        return json_encode($analysis_result);
    }
    
    /**
     * Get update suggestions from AI analysis
     * 
     * @param string $ai_analysis JSON string from AI analysis
     * @return array Array of suggestions
     */
    private function get_update_suggestions($ai_analysis) {
        if (is_wp_error($ai_analysis)) {
            return array('AI analysis not available, using default suggestions');
        }
        
        $ai_data = json_decode($ai_analysis, true);
        if (!$ai_data || !isset($ai_data['specific_suggestions'])) {
            return array(
                'Update statistics and data with current information',
                'Add recent examples and case studies', 
                'Review and update external links',
                'Enhance content with new insights and trends'
            );
        }
        
        return $ai_data['specific_suggestions'];
    }
    
    /**
     * Call real Gemini AI analysis for content freshness
     * 
     * @param string $content Post content
     * @param string $title Post title
     * @return string|WP_Error JSON response from AI or error
     */
    private function call_gemini_ai_analysis($content, $title) {
        $settings = get_option('aca_settings', array());
        if (empty($settings['geminiApiKey'])) {
            return new WP_Error('no_api_key', 'Gemini API key not configured');
        }
        
        try {
            // Prepare the API request
            $api_url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent';
            $api_key = $settings['geminiApiKey'];
            
            $prompt = "Analyze this published content for freshness and provide comprehensive update recommendations:

Title: {$title}
Content: " . substr(strip_tags($content), 0, 2000) . "...

Provide a detailed JSON response with:
1. freshness_score (0-100) - Overall content freshness score
2. update_priority (1-5, 5 being urgent) - Priority level for updates  
3. specific_suggestions (array of actionable improvements)
4. outdated_information (array of potentially outdated facts/statistics)
5. seo_improvements (array of SEO enhancement suggestions)
6. readability_improvements (array of readability enhancements)
7. content_gaps (array of missing topics that should be added)
8. technical_updates (array of technical aspects that need updating)

Consider factors like:
- Date references and time-sensitive information
- Industry trends and developments
- Technology changes and updates
- Statistical data and research findings
- Link relevance and availability
- Content comprehensiveness
- Search intent alignment
- User engagement potential

Return only valid JSON format.";

            $request_body = array(
                'contents' => array(
                    array(
                        'parts' => array(
                            array('text' => $prompt)
                        )
                    )
                ),
                'generationConfig' => array(
                    'temperature' => 0.3,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                    'responseMimeType' => 'application/json'
                )
            );
            
            $response = wp_remote_post($api_url . '?key=' . $api_key, array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body' => json_encode($request_body),
                'timeout' => 30,
            ));
            
            if (is_wp_error($response)) {
                error_log('ACA Content Freshness: Gemini API request failed: ' . $response->get_error_message());
                return new WP_Error('api_request_failed', $response->get_error_message());
            }
            
            $response_code = wp_remote_retrieve_response_code($response);
            if ($response_code !== 200) {
                error_log('ACA Content Freshness: Gemini API returned error code: ' . $response_code);
                return new WP_Error('api_error', 'API request failed with code: ' . $response_code);
            }
            
            $response_body = wp_remote_retrieve_body($response);
            $data = json_decode($response_body, true);
            
            if (!$data || !isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                error_log('ACA Content Freshness: Invalid Gemini API response format');
                return new WP_Error('invalid_response', 'Invalid API response format');
            }
            
            $ai_response = $data['candidates'][0]['content']['parts'][0]['text'];
            
            // Validate that the response is valid JSON
            $parsed_response = json_decode($ai_response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log('ACA Content Freshness: AI returned invalid JSON: ' . json_last_error_msg());
                return new WP_Error('invalid_json', 'AI response is not valid JSON');
            }
            
            // Validate required fields
            $required_fields = array('freshness_score', 'update_priority', 'specific_suggestions');
            foreach ($required_fields as $field) {
                if (!isset($parsed_response[$field])) {
                    error_log('ACA Content Freshness: Missing required field in AI response: ' . $field);
                    return new WP_Error('missing_field', 'Missing required field: ' . $field);
                }
            }
            
            return $ai_response;
            
        } catch (Exception $e) {
            error_log('ACA Content Freshness: Exception in AI analysis: ' . $e->getMessage());
            return new WP_Error('exception', $e->getMessage());
        }
    }
    
    /**
     * Calculate update priority based on freshness and SEO performance
     * 
     * @param float $freshness_score
     * @param float $seo_performance
     * @return int Priority level (1-5, 5 being highest)
     */
    private function calculate_priority($freshness_score, $seo_performance) {
        if ($freshness_score < 40 || $seo_performance < 20) {
            return 5; // Urgent
        } elseif ($freshness_score < 60 || $seo_performance < 40) {
            return 4; // High
        } elseif ($freshness_score < 80 || $seo_performance < 60) {
            return 3; // Medium
        } elseif ($freshness_score < 90 || $seo_performance < 80) {
            return 2; // Low
        } else {
            return 1; // Very low
        }
    }
    
    /**
     * Save freshness analysis to database
     * 
     * @param int $post_id
     * @param array $analysis_result
     */
    private function save_freshness_analysis($post_id, $analysis_result) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_content_freshness';
        
        $wpdb->replace(
            $table_name,
            array(
                'post_id' => $post_id,
                'freshness_score' => $analysis_result['score'],
                'last_analyzed' => current_time('mysql'),
                'needs_update' => $analysis_result['needs_update'] ? 1 : 0,
                'update_priority' => $analysis_result['priority']
            ),
            array('%d', '%f', '%s', '%d', '%d')
        );
    }
    
    /**
     * Get freshness data for a post
     * 
     * @param int $post_id
     * @return array|null Freshness data or null if not found
     */
    public function get_freshness_data($post_id) {
        // Check if Pro license is active
        if (!is_aca_pro_active()) {
            return null;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_content_freshness';
        
        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table_name WHERE post_id = %d",
                $post_id
            ),
            ARRAY_A
        );
        
        return $result;
    }
    
    /**
     * Get posts that need freshness analysis
     * 
     * @param int $limit Number of posts to return
     * @return array Array of post IDs
     */
    public function get_posts_needing_analysis($limit = 10) {
        // Check if Pro license is active
        if (!is_aca_pro_active()) {
            return array();
        }
        
        global $wpdb;
        
        $freshness_table = $wpdb->prefix . 'aca_content_freshness';
        $postmeta_table = $wpdb->prefix . 'postmeta';
        
        // Get published posts that haven't been analyzed in the last week
        // Check both the freshness table and the meta field
        $posts = $wpdb->get_col($wpdb->prepare("
            SELECT p.ID 
            FROM {$wpdb->posts} p
            LEFT JOIN $freshness_table f ON p.ID = f.post_id
            LEFT JOIN $postmeta_table pm ON p.ID = pm.post_id AND pm.meta_key = '_aca_last_freshness_check'
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            AND (
                f.last_analyzed IS NULL 
                OR f.last_analyzed < DATE_SUB(NOW(), INTERVAL 7 DAY)
                OR pm.meta_value IS NULL
                OR pm.meta_value < DATE_SUB(NOW(), INTERVAL 7 DAY)
            )
            ORDER BY p.post_date DESC
            LIMIT %d
        ", $limit));
        
        return $posts;
    }
    
    /**
     * Get posts that need updates based on freshness score
     * 
     * @param int $limit Number of posts to return
     * @return array Array of post data with freshness information
     */
    public function get_posts_needing_updates($limit = 20) {
        // Check if Pro license is active
        if (!is_aca_pro_active()) {
            return array();
        }
        
        global $wpdb;
        
        $freshness_table = $wpdb->prefix . 'aca_content_freshness';
        
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT p.ID, p.post_title, p.post_date, f.freshness_score, f.update_priority, f.last_analyzed
            FROM {$wpdb->posts} p
            INNER JOIN $freshness_table f ON p.ID = f.post_id
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            AND f.needs_update = 1
            ORDER BY f.update_priority DESC, f.freshness_score ASC
            LIMIT %d
        ", $limit), ARRAY_A);
        
        return $results;
    }
    
    /**
     * Queue content update for a post
     * 
     * @param int $post_id
     * @param array $analysis_data
     * @return bool Success status
     */
    public function queue_content_update($post_id, $analysis_data) {
        // Check if Pro license is active
        if (!is_aca_pro_active()) {
            return false;
        }
        
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_content_updates';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'last_updated' => current_time('mysql'),
                'update_type' => 'freshness_analysis',
                'ai_suggestions' => json_encode($analysis_data['suggestions']),
                'status' => 'pending'
            ),
            array('%d', '%s', '%s', '%s', '%s')
        );
        
        return $result !== false;
    }
}