<?php
/**
 * SEO Optimization System for AI Content Agent
 * 
 * Provides comprehensive SEO features including structured data,
 * meta tags optimization, Core Web Vitals monitoring, and search engine optimization.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO Optimizer Class
 */
class ACA_SEO_Optimizer {
    
    /**
     * Initialize SEO optimization
     */
    public function __construct() {
        // Only load frontend hooks if not in admin
        if (!is_admin()) {
            add_action('wp_head', [$this, 'add_structured_data']);
            add_action('wp_head', [$this, 'add_meta_tags']);
            add_action('wp_head', [$this, 'add_core_web_vitals_monitoring']);
            add_filter('wp_title', [$this, 'optimize_title'], 10, 2);
            add_filter('the_content', [$this, 'optimize_content']);
            add_action('wp_footer', [$this, 'add_schema_markup']);
        }
        
        // Sitemap generation only when needed
        add_action('init', [$this, 'generate_sitemap']);
    }
    
    /**
     * Add structured data for AI-generated content
     */
    public function add_structured_data() {
        global $post;
        
        if (!is_single() || !$post) {
            return;
        }
        
        // Check if this is AI-generated content
        $is_ai_generated = get_post_meta($post->ID, '_aca_ai_generated', true);
        if (!$is_ai_generated) {
            return;
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => get_the_title($post->ID),
            'description' => get_the_excerpt($post->ID) ?: wp_trim_words(get_the_content(), 20),
            'author' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'description' => 'AI-powered content creation'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                ]
            ],
            'datePublished' => get_the_date('c', $post->ID),
            'dateModified' => get_the_modified_date('c', $post->ID),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => get_permalink($post->ID)
            ],
            'articleBody' => wp_strip_all_tags(get_the_content()),
            'wordCount' => str_word_count(wp_strip_all_tags(get_the_content())),
            'creativeWorkStatus' => 'Published',
            'isAccessibleForFree' => true,
            'inLanguage' => get_locale(),
            'keywords' => $this->extract_keywords($post->ID),
            'about' => $this->get_article_topics($post->ID),
            'mentions' => $this->get_mentioned_entities($post->ID)
        ];
        
        // Add featured image if available
        if (has_post_thumbnail($post->ID)) {
            $image_id = get_post_thumbnail_id($post->ID);
            $image = wp_get_attachment_image_src($image_id, 'full');
            
            $schema['image'] = [
                '@type' => 'ImageObject',
                'url' => $image[0],
                'width' => $image[1],
                'height' => $image[2],
                'caption' => get_post_meta($image_id, '_wp_attachment_image_alt', true)
            ];
        }
        
        // Add AI-specific metadata
        $ai_metadata = get_post_meta($post->ID, '_aca_ai_metadata', true);
        if ($ai_metadata) {
            $schema['additionalProperty'] = [
                [
                    '@type' => 'PropertyValue',
                    'name' => 'AI Model',
                    'value' => $ai_metadata['model'] ?? 'Gemini'
                ],
                [
                    '@type' => 'PropertyValue',
                    'name' => 'Generation Method',
                    'value' => 'AI-Assisted Content Creation'
                ],
                [
                    '@type' => 'PropertyValue',
                    'name' => 'Content Quality Score',
                    'value' => $ai_metadata['quality_score'] ?? '95'
                ]
            ];
        }
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        
        // Add FAQ schema if content contains Q&A patterns
        $faq_schema = $this->generate_faq_schema($post->ID);
        if ($faq_schema) {
            echo '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
        
        // Add HowTo schema if content contains step-by-step instructions
        $howto_schema = $this->generate_howto_schema($post->ID);
        if ($howto_schema) {
            echo '<script type="application/ld+json">' . json_encode($howto_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
        }
    }
    
    /**
     * Add optimized meta tags
     */
    public function add_meta_tags() {
        global $post;
        
        if (!is_single() || !$post) {
            return;
        }
        
        // Get AI-optimized meta data
        $meta_title = get_post_meta($post->ID, '_aca_meta_title', true);
        $meta_description = get_post_meta($post->ID, '_aca_meta_description', true);
        $focus_keywords = get_post_meta($post->ID, '_aca_focus_keywords', true);
        
        // Fallback to post data if AI meta not available
        if (!$meta_title) {
            $meta_title = get_the_title($post->ID);
        }
        
        if (!$meta_description) {
            $meta_description = wp_trim_words(get_the_excerpt($post->ID) ?: get_the_content(), 25);
        }
        
        // Open Graph tags
        echo '<meta property="og:title" content="' . esc_attr($meta_title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($meta_description) . '">' . "\n";
        echo '<meta property="og:type" content="article">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(get_permalink($post->ID)) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        
        if (has_post_thumbnail($post->ID)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
            echo '<meta property="og:image" content="' . esc_url($image[0]) . '">' . "\n";
            echo '<meta property="og:image:width" content="' . esc_attr($image[1]) . '">' . "\n";
            echo '<meta property="og:image:height" content="' . esc_attr($image[2]) . '">' . "\n";
        }
        
        // Twitter Card tags
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($meta_title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($meta_description) . '">' . "\n";
        
        if (has_post_thumbnail($post->ID)) {
            $image = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large');
            echo '<meta name="twitter:image" content="' . esc_url($image[0]) . '">' . "\n";
        }
        
        // SEO meta tags
        if ($focus_keywords) {
            echo '<meta name="keywords" content="' . esc_attr(implode(', ', $focus_keywords)) . '">' . "\n";
        }
        
        echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
        echo '<meta name="googlebot" content="index, follow">' . "\n";
        
        // Article-specific meta
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c', $post->ID)) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c', $post->ID)) . '">' . "\n";
        
        // AI-generated content indicator
        $is_ai_generated = get_post_meta($post->ID, '_aca_ai_generated', true);
        if ($is_ai_generated) {
            echo '<meta name="generator" content="AI Content Agent v' . ACA_VERSION . '">' . "\n";
        }
        
        // Core Web Vitals optimization hints
        echo '<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">' . "\n";
        echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . "\n";
        
        // Preconnect to external domains for performance
        echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
    }
    
    /**
     * Add Core Web Vitals monitoring
     */
    public function add_core_web_vitals_monitoring() {
        if (!is_single()) {
            return;
        }
        
        ?>
        <script>
        // Core Web Vitals monitoring
        function measureCoreWebVitals() {
            // Largest Contentful Paint (LCP)
            if ('PerformanceObserver' in window) {
                const lcpObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    const lastEntry = entries[entries.length - 1];
                    
                    // Send LCP data to analytics
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'LCP', {
                            event_category: 'Core Web Vitals',
                            value: Math.round(lastEntry.startTime),
                            custom_parameter_1: lastEntry.startTime < 2500 ? 'good' : 
                                               lastEntry.startTime < 4000 ? 'needs_improvement' : 'poor'
                        });
                    }
                    
                    // Store in WordPress for analysis
                    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            action: 'aca_log_core_web_vitals',
                            metric: 'LCP',
                            value: lastEntry.startTime,
                            post_id: <?php echo get_the_ID(); ?>,
                            nonce: '<?php echo wp_create_nonce('aca_cwv_nonce'); ?>'
                        })
                    });
                });
                
                lcpObserver.observe({entryTypes: ['largest-contentful-paint']});
                
                // First Input Delay (FID)
                const fidObserver = new PerformanceObserver((list) => {
                    const entries = list.getEntries();
                    entries.forEach((entry) => {
                        if (typeof gtag !== 'undefined') {
                            gtag('event', 'FID', {
                                event_category: 'Core Web Vitals',
                                value: Math.round(entry.processingStart - entry.startTime),
                                custom_parameter_1: entry.processingStart - entry.startTime < 100 ? 'good' : 
                                                   entry.processingStart - entry.startTime < 300 ? 'needs_improvement' : 'poor'
                            });
                        }
                        
                        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                action: 'aca_log_core_web_vitals',
                                metric: 'FID',
                                value: entry.processingStart - entry.startTime,
                                post_id: <?php echo get_the_ID(); ?>,
                                nonce: '<?php echo wp_create_nonce('aca_cwv_nonce'); ?>'
                            })
                        });
                    });
                });
                
                fidObserver.observe({entryTypes: ['first-input']});
                
                // Cumulative Layout Shift (CLS)
                let clsValue = 0;
                const clsObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            clsValue += entry.value;
                        }
                    }
                });
                
                clsObserver.observe({entryTypes: ['layout-shift']});
                
                // Send CLS data when page is about to unload
                window.addEventListener('beforeunload', () => {
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'CLS', {
                            event_category: 'Core Web Vitals',
                            value: Math.round(clsValue * 1000),
                            custom_parameter_1: clsValue < 0.1 ? 'good' : 
                                               clsValue < 0.25 ? 'needs_improvement' : 'poor'
                        });
                    }
                    
                    navigator.sendBeacon('<?php echo admin_url('admin-ajax.php'); ?>', new URLSearchParams({
                        action: 'aca_log_core_web_vitals',
                        metric: 'CLS',
                        value: clsValue,
                        post_id: <?php echo get_the_ID(); ?>,
                        nonce: '<?php echo wp_create_nonce('aca_cwv_nonce'); ?>'
                    }));
                });
            }
        }
        
        // Initialize monitoring when page is loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', measureCoreWebVitals);
        } else {
            measureCoreWebVitals();
        }
        </script>
        <?php
    }
    
    /**
     * Optimize page title
     */
    public function optimize_title($title, $sep = null) {
        global $post;
        
        if (!is_single() || !$post) {
            return $title;
        }
        
        $ai_title = get_post_meta($post->ID, '_aca_meta_title', true);
        if ($ai_title) {
            return $ai_title . ' ' . $sep . ' ' . get_bloginfo('name');
        }
        
        return $title;
    }
    
    /**
     * Optimize content for SEO
     */
    public function optimize_content($content) {
        global $post;
        
        if (!is_single() || !$post || !in_the_loop() || !is_main_query()) {
            return $content;
        }
        
        // Add reading time
        $word_count = str_word_count(wp_strip_all_tags($content));
        $reading_time = ceil($word_count / 200); // Average reading speed
        
        $reading_time_html = '<div class="aca-reading-time" style="margin-bottom: 20px; font-style: italic; color: #666;">
            <span>📖 Estimated reading time: ' . $reading_time . ' minute' . ($reading_time !== 1 ? 's' : '') . '</span>
        </div>';
        
        // Add table of contents for long articles
        if ($word_count > 1000) {
            $toc = $this->generate_table_of_contents($content);
            if ($toc) {
                $content = $reading_time_html . $toc . $content;
            } else {
                $content = $reading_time_html . $content;
            }
        } else {
            $content = $reading_time_html . $content;
        }
        
        // Add related posts at the end
        $related_posts = $this->get_related_posts($post->ID);
        if ($related_posts) {
            $content .= '<div class="aca-related-posts" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                <h3>Related Articles</h3>
                <ul style="list-style-type: none; padding: 0;">';
            
            foreach ($related_posts as $related_post) {
                $content .= '<li style="margin-bottom: 10px;">
                    <a href="' . get_permalink($related_post->ID) . '" style="text-decoration: none; color: #0073aa;">
                        📄 ' . esc_html($related_post->post_title) . '
                    </a>
                </li>';
            }
            
            $content .= '</ul></div>';
        }
        
        return $content;
    }
    
    /**
     * Generate table of contents
     */
    private function generate_table_of_contents($content) {
        preg_match_all('/<h([2-6])[^>]*>(.*?)<\/h[2-6]>/i', $content, $matches, PREG_SET_ORDER);
        
        if (count($matches) < 3) {
            return '';
        }
        
        $toc = '<div class="aca-table-of-contents" style="background: #f0f8ff; padding: 20px; margin-bottom: 30px; border-radius: 8px; border-left: 4px solid #0073aa;">
            <h3 style="margin-top: 0;">📋 Table of Contents</h3>
            <ul style="list-style-type: none; padding-left: 0;">';
        
        foreach ($matches as $index => $match) {
            $level = intval($match[1]);
            $heading = strip_tags($match[2]);
            $anchor = 'toc-' . sanitize_title($heading);
            
            // Add anchor to the actual heading in content
            $old_heading = $match[0];
            $new_heading = str_replace('>', ' id="' . $anchor . '">', $old_heading);
            $content = str_replace($old_heading, $new_heading, $content);
            
            $indent = ($level - 2) * 20;
            $toc .= '<li style="margin-bottom: 8px; padding-left: ' . $indent . 'px;">
                <a href="#' . $anchor . '" style="text-decoration: none; color: #0073aa;">
                    ' . esc_html($heading) . '
                </a>
            </li>';
        }
        
        $toc .= '</ul></div>';
        
        return $toc;
    }
    
    /**
     * Get related posts
     */
    private function get_related_posts($post_id, $limit = 3) {
        $categories = wp_get_post_categories($post_id);
        $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);
        
        $args = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'post__not_in' => [$post_id],
            'orderby' => 'relevance'
        ];
        
        if (!empty($categories) || !empty($tags)) {
            $args['tax_query'] = [
                'relation' => 'OR'
            ];
            
            if (!empty($categories)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'category',
                    'field' => 'term_id',
                    'terms' => $categories
                ];
            }
            
            if (!empty($tags)) {
                $args['tax_query'][] = [
                    'taxonomy' => 'post_tag',
                    'field' => 'term_id',
                    'terms' => $tags
                ];
            }
        }
        
        return get_posts($args);
    }
    
    /**
     * Add schema markup to footer
     */
    public function add_schema_markup() {
        if (!is_home() && !is_front_page()) {
            return;
        }
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => get_bloginfo('name'),
            'description' => get_bloginfo('description'),
            'url' => home_url(),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                ]
            ]
        ];
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
    
    /**
     * Generate XML sitemap
     */
    public function generate_sitemap() {
        add_action('init', function() {
            add_rewrite_rule('^aca-sitemap\.xml$', 'index.php?aca_sitemap=1', 'top');
        });
        
        add_filter('query_vars', function($vars) {
            $vars[] = 'aca_sitemap';
            return $vars;
        });
        
        add_action('template_redirect', function() {
            if (get_query_var('aca_sitemap')) {
                $this->output_sitemap();
                exit;
            }
        });
    }
    
    /**
     * Output XML sitemap
     */
    private function output_sitemap() {
        header('Content-Type: application/xml; charset=utf-8');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Add AI-generated posts
        $ai_posts = get_posts([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => '_aca_ai_generated',
                    'value' => '1',
                    'compare' => '='
                ]
            ]
        ]);
        
        foreach ($ai_posts as $post) {
            $lastmod = get_the_modified_date('c', $post->ID);
            $priority = '0.8'; // High priority for AI content
            
            echo '<url>' . "\n";
            echo '<loc>' . esc_url(get_permalink($post->ID)) . '</loc>' . "\n";
            echo '<lastmod>' . $lastmod . '</lastmod>' . "\n";
            echo '<changefreq>weekly</changefreq>' . "\n";
            echo '<priority>' . $priority . '</priority>' . "\n";
            echo '</url>' . "\n";
        }
        
        echo '</urlset>' . "\n";
    }
    
    /**
     * Extract keywords from content
     */
    private function extract_keywords($post_id) {
        $focus_keywords = get_post_meta($post_id, '_aca_focus_keywords', true);
        if ($focus_keywords) {
            return $focus_keywords;
        }
        
        // Extract from categories and tags
        $categories = wp_get_post_categories($post_id, ['fields' => 'names']);
        $tags = wp_get_post_tags($post_id, ['fields' => 'names']);
        
        return array_merge($categories, $tags);
    }
    
    /**
     * Get article topics
     */
    private function get_article_topics($post_id) {
        $categories = wp_get_post_categories($post_id, ['fields' => 'names']);
        
        return array_map(function($category) {
            return [
                '@type' => 'Thing',
                'name' => $category
            ];
        }, $categories);
    }
    
    /**
     * Get mentioned entities
     */
    private function get_mentioned_entities($post_id) {
        $content = get_post_field('post_content', $post_id);
        $entities = [];
        
        // Simple entity extraction (can be enhanced with NLP)
        preg_match_all('/\b[A-Z][a-z]+ [A-Z][a-z]+\b/', $content, $matches);
        
        foreach (array_unique($matches[0]) as $entity) {
            $entities[] = [
                '@type' => 'Thing',
                'name' => $entity
            ];
        }
        
        return array_slice($entities, 0, 5); // Limit to 5 entities
    }
    
    /**
     * Generate FAQ schema from content
     */
    private function generate_faq_schema($post_id) {
        $content = get_post_field('post_content', $post_id);
        
        // Look for Q&A patterns in content
        $faq_patterns = [
            '/(?:Q|Question|Query):\s*(.+?)(?:\n|$)(?:A|Answer|Response):\s*(.+?)(?:\n|$)/i',
            '/(?:Q\d+|Question\s*\d+):\s*(.+?)(?:\n|$)(?:A\d+|Answer\s*\d+):\s*(.+?)(?:\n|$)/i',
            '/<h[3-6][^>]*>(.+?)<\/h[3-6]>\s*<p>(.+?)<\/p>/i'
        ];
        
        $faqs = [];
        foreach ($faq_patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    if (count($match) >= 3) {
                        $faqs[] = [
                            '@type' => 'Question',
                            'name' => strip_tags(trim($match[1])),
                            'acceptedAnswer' => [
                                '@type' => 'Answer',
                                'text' => strip_tags(trim($match[2]))
                            ]
                        ];
                    }
                }
            }
        }
        
        if (count($faqs) >= 2) { // At least 2 FAQs required
            return [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_slice($faqs, 0, 10) // Max 10 FAQs
            ];
        }
        
        return null;
    }
    
    /**
     * Generate HowTo schema from content
     */
    private function generate_howto_schema($post_id) {
        $content = get_post_field('post_content', $post_id);
        $title = get_the_title($post_id);
        
        // Look for step-by-step patterns
        $step_patterns = [
            '/(?:Step\s*\d+|Step\s*[IVX]+):\s*(.+?)(?=Step\s*\d+|Step\s*[IVX]+|$)/is',
            '/<h[3-6][^>]*>(?:Step\s*\d+|Step\s*[IVX]+)[^<]*<\/h[3-6]>\s*(.+?)(?=<h[3-6]|$)/is',
            '/\d+\.\s*(.+?)(?=\d+\.|$)/s'
        ];
        
        $steps = [];
        foreach ($step_patterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $index => $match) {
                    if (isset($match[1])) {
                        $step_text = strip_tags(trim($match[1]));
                        if (strlen($step_text) > 20 && strlen($step_text) < 500) { // Reasonable step length
                            $steps[] = [
                                '@type' => 'HowToStep',
                                'position' => $index + 1,
                                'name' => substr($step_text, 0, 100) . (strlen($step_text) > 100 ? '...' : ''),
                                'text' => $step_text
                            ];
                        }
                    }
                }
                break; // Use first pattern that matches
            }
        }
        
        // Check if content looks like a how-to guide
        $howto_indicators = ['how to', 'step by step', 'tutorial', 'guide', 'instructions'];
        $is_howto = false;
        foreach ($howto_indicators as $indicator) {
            if (stripos($title . ' ' . $content, $indicator) !== false) {
                $is_howto = true;
                break;
            }
        }
        
        if ($is_howto && count($steps) >= 3) { // At least 3 steps required
            return [
                '@context' => 'https://schema.org',
                '@type' => 'HowTo',
                'name' => $title,
                'description' => get_the_excerpt($post_id) ?: wp_trim_words(get_the_content(), 30),
                'step' => array_slice($steps, 0, 20) // Max 20 steps
            ];
        }
        
        return null;
    }
}

// AJAX handler for Core Web Vitals logging
add_action('wp_ajax_aca_log_core_web_vitals', 'aca_log_core_web_vitals');
add_action('wp_ajax_nopriv_aca_log_core_web_vitals', 'aca_log_core_web_vitals');

if (!function_exists('aca_log_core_web_vitals')) {
    function aca_log_core_web_vitals() {
    if (!wp_verify_nonce($_POST['nonce'], 'aca_cwv_nonce')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'aca_core_web_vitals';
    
    // Create table if it doesn't exist
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(20) NOT NULL,
        metric varchar(10) NOT NULL,
        value decimal(10,2) NOT NULL,
        url varchar(255) NOT NULL,
        user_agent text,
        timestamp datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY post_metric (post_id, metric),
        KEY timestamp (timestamp)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Insert the metric
    $wpdb->insert(
        $table_name,
        [
            'post_id' => intval(sanitize_text_field($_POST['post_id'])),
            'metric' => sanitize_text_field($_POST['metric']),
            'value' => floatval($_POST['value']),
            'url' => esc_url_raw($_SERVER['HTTP_REFERER'] ?? ''),
            'user_agent' => sanitize_text_field($_SERVER['HTTP_USER_AGENT'] ?? '')
        ]
    );
    
    wp_die();
    }
}

// Initialize SEO optimizer when WordPress is ready
add_action('init', function() {
    new ACA_SEO_Optimizer();
});
