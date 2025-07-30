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
        $seo_performance = $this->get_seo_performance($post_id);
        
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
            'suggestions' => $suggestions,
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
     * Get SEO performance score for a post
     * 
     * @param int $post_id
     * @return float SEO performance score (0-100)
     */
    private function get_seo_performance($post_id) {
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
                    
                    // Simple scoring algorithm
                    $click_score = min(50, $clicks / 10); // Max 50 points for clicks
                    $impression_score = min(30, $impressions / 100); // Max 30 points for impressions
                    $ctr_score = min(20, $ctr * 1000); // Max 20 points for CTR
                    
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
        
        // In a real implementation, this would call the Gemini API via JavaScript/AJAX
        // For now, we'll use a more sophisticated heuristic analysis
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
        global $wpdb;
        
        $freshness_table = $wpdb->prefix . 'aca_content_freshness';
        
        // Get published posts that haven't been analyzed in the last week
        $posts = $wpdb->get_col($wpdb->prepare("
            SELECT p.ID 
            FROM {$wpdb->posts} p
            LEFT JOIN $freshness_table f ON p.ID = f.post_id
            WHERE p.post_status = 'publish'
            AND p.post_type = 'post'
            AND (f.last_analyzed IS NULL OR f.last_analyzed < DATE_SUB(NOW(), INTERVAL 7 DAY))
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