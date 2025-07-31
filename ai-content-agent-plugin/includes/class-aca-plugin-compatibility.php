<?php
/**
 * Plugin Ecosystem Compatibility for AI Content Agent
 * 
 * Provides comprehensive compatibility with popular WordPress plugins
 * including WooCommerce, Elementor, Gutenberg, Yoast SEO, and others.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Compatibility Manager
 */
class ACA_Plugin_Compatibility {
    
    /**
     * Detected plugins
     */
    private $detected_plugins = [];
    
    /**
     * Compatibility handlers
     */
    private $handlers = [];
    
    /**
     * Initialize compatibility system
     */
    public function __construct() {
        add_action('plugins_loaded', [$this, 'detect_plugins'], 5);
        add_action('init', [$this, 'initialize_compatibility'], 10);
        add_action('wp_loaded', [$this, 'late_compatibility_init'], 20);
    }
    
    /**
     * Detect active plugins
     */
    public function detect_plugins() {
        $this->detected_plugins = [
            'woocommerce' => class_exists('WooCommerce'),
            'elementor' => defined('ELEMENTOR_VERSION'),
            'gutenberg' => function_exists('gutenberg_init'),
            'block_editor' => function_exists('register_block_type'),
            'yoast_seo' => defined('WPSEO_VERSION'),
            'rankmath' => defined('RANK_MATH_VERSION'),
            'wpml' => defined('ICL_SITEPRESS_VERSION'),
            'polylang' => function_exists('pll_current_language'),
            'acf' => class_exists('ACF'),
            'beaver_builder' => class_exists('FLBuilder'),
            'divi' => defined('ET_BUILDER_VERSION'),
            'oxygen' => defined('CT_VERSION'),
            'wpbakery' => defined('WPB_VC_VERSION'),
            'contact_form_7' => class_exists('WPCF7'),
            'gravity_forms' => class_exists('GFForms'),
            'ninja_forms' => class_exists('Ninja_Forms'),
            'wp_rocket' => defined('WP_ROCKET_VERSION'),
            'w3_total_cache' => defined('W3TC'),
            'wp_super_cache' => function_exists('wp_super_cache_init'),
            'litespeed_cache' => defined('LSCWP_V'),
            'jetpack' => defined('JETPACK__VERSION'),
            'wordfence' => defined('WORDFENCE_VERSION'),
            'sucuri' => defined('SUCURISCAN_VERSION'),
            'bbpress' => class_exists('bbPress'),
            'buddypress' => class_exists('BuddyPress'),
            'learndash' => defined('LEARNDASH_VERSION'),
            'lifterlms' => class_exists('LifterLMS'),
            'memberpress' => defined('MEPR_VERSION'),
            'restrict_content_pro' => function_exists('rcp_get_subscription')
        ];
        
        // Register compatibility handlers
        $this->register_handlers();
    }
    
    /**
     * Register compatibility handlers
     */
    private function register_handlers() {
        if ($this->detected_plugins['woocommerce']) {
            $this->handlers['woocommerce'] = new ACA_WooCommerce_Compatibility();
        }
        
        if ($this->detected_plugins['elementor']) {
            $this->handlers['elementor'] = new ACA_Elementor_Compatibility();
        }
        
        if ($this->detected_plugins['block_editor'] || $this->detected_plugins['gutenberg']) {
            $this->handlers['gutenberg'] = new ACA_Gutenberg_Compatibility();
        }
        
        if ($this->detected_plugins['yoast_seo']) {
            $this->handlers['yoast_seo'] = new ACA_Yoast_SEO_Compatibility();
        }
        
        if ($this->detected_plugins['rankmath']) {
            $this->handlers['rankmath'] = new ACA_RankMath_Compatibility();
        }
        
        if ($this->detected_plugins['wpml']) {
            $this->handlers['wpml'] = new ACA_WPML_Compatibility();
        }
        
        if ($this->detected_plugins['acf']) {
            $this->handlers['acf'] = new ACA_ACF_Compatibility();
        }
        
        // Add more handlers as needed
        $this->handlers['caching'] = new ACA_Caching_Compatibility($this->detected_plugins);
        $this->handlers['page_builders'] = new ACA_Page_Builders_Compatibility($this->detected_plugins);
    }
    
    /**
     * Initialize compatibility features
     */
    public function initialize_compatibility() {
        foreach ($this->handlers as $handler) {
            if (method_exists($handler, 'init')) {
                $handler->init();
            }
        }
        
        // Global compatibility actions
        add_filter('aca_content_generation_context', [$this, 'add_plugin_context']);
        add_action('aca_content_generated', [$this, 'handle_post_generation'], 10, 2);
    }
    
    /**
     * Late compatibility initialization
     */
    public function late_compatibility_init() {
        foreach ($this->handlers as $handler) {
            if (method_exists($handler, 'late_init')) {
                $handler->late_init();
            }
        }
    }
    
    /**
     * Add plugin context to content generation
     */
    public function add_plugin_context($context) {
        $context['detected_plugins'] = $this->detected_plugins;
        $context['active_theme'] = get_template();
        $context['is_ecommerce'] = $this->detected_plugins['woocommerce'];
        $context['has_page_builder'] = $this->has_page_builder();
        $context['seo_plugin'] = $this->get_active_seo_plugin();
        
        return $context;
    }
    
    /**
     * Handle post-generation compatibility
     */
    public function handle_post_generation($post_id, $content_data) {
        foreach ($this->handlers as $handler) {
            if (method_exists($handler, 'handle_generated_content')) {
                $handler->handle_generated_content($post_id, $content_data);
            }
        }
    }
    
    /**
     * Check if any page builder is active
     */
    private function has_page_builder() {
        return $this->detected_plugins['elementor'] || 
               $this->detected_plugins['beaver_builder'] || 
               $this->detected_plugins['divi'] || 
               $this->detected_plugins['oxygen'] || 
               $this->detected_plugins['wpbakery'];
    }
    
    /**
     * Get active SEO plugin
     */
    private function get_active_seo_plugin() {
        if ($this->detected_plugins['yoast_seo']) return 'yoast';
        if ($this->detected_plugins['rankmath']) return 'rankmath';
        return null;
    }
    
    /**
     * Get detected plugins
     */
    public function get_detected_plugins() {
        return $this->detected_plugins;
    }
    
    /**
     * Check if specific plugin is active
     */
    public function is_plugin_active($plugin) {
        return $this->detected_plugins[$plugin] ?? false;
    }
}

/**
 * WooCommerce Compatibility
 */
class ACA_WooCommerce_Compatibility {
    
    public function init() {
        // Add WooCommerce-specific content types
        add_filter('aca_content_types', [$this, 'add_woocommerce_content_types']);
        
        // Handle product content generation
        add_action('aca_generate_product_content', [$this, 'generate_product_content'], 10, 2);
        
        // Add WooCommerce fields to content generation
        add_filter('aca_content_generation_fields', [$this, 'add_woocommerce_fields']);
        
        // Handle WooCommerce SEO
        add_filter('aca_seo_optimization', [$this, 'optimize_product_seo'], 10, 2);
    }
    
    public function add_woocommerce_content_types($types) {
        $types['product_description'] = 'Product Description';
        $types['product_features'] = 'Product Features';
        $types['product_review'] = 'Product Review';
        $types['category_description'] = 'Category Description';
        $types['buying_guide'] = 'Buying Guide';
        
        return $types;
    }
    
    public function generate_product_content($product_id, $content_type) {
        $product = wc_get_product($product_id);
        if (!$product) return;
        
        $context = [
            'product_name' => $product->get_name(),
            'product_price' => $product->get_price(),
            'product_category' => wp_get_post_terms($product_id, 'product_cat', ['fields' => 'names']),
            'product_attributes' => $product->get_attributes(),
            'product_type' => $product->get_type(),
            'is_on_sale' => $product->is_on_sale(),
            'stock_status' => $product->get_stock_status()
        ];
        
        // Generate content based on type
        switch ($content_type) {
            case 'product_description':
                $this->generate_product_description($product_id, $context);
                break;
            case 'product_features':
                $this->generate_product_features($product_id, $context);
                break;
            case 'buying_guide':
                $this->generate_buying_guide($product_id, $context);
                break;
        }
    }
    
    private function generate_product_description($product_id, $context) {
        $prompt = "Generate a compelling product description for {$context['product_name']} " .
                 "in the {$context['product_category'][0]} category. " .
                 "Price: {$context['product_price']}. " .
                 "Focus on benefits, features, and value proposition.";
        
        // Use AI service to generate content
        do_action('aca_ai_generate_content', $prompt, [
            'post_id' => $product_id,
            'content_type' => 'product_description',
            'context' => $context
        ]);
    }
    
    public function add_woocommerce_fields($fields) {
        if (is_admin() && isset($_GET['post_type']) && $_GET['post_type'] === 'product') {
            $fields['woocommerce'] = [
                'product_highlights' => 'Product Highlights',
                'technical_specs' => 'Technical Specifications',
                'usage_instructions' => 'Usage Instructions',
                'warranty_info' => 'Warranty Information'
            ];
        }
        
        return $fields;
    }
    
    public function optimize_product_seo($seo_data, $post_id) {
        if (get_post_type($post_id) !== 'product') {
            return $seo_data;
        }
        
        $product = wc_get_product($post_id);
        if (!$product) return $seo_data;
        
        // Add product-specific schema
        $seo_data['schema']['@type'] = 'Product';
        $seo_data['schema']['name'] = $product->get_name();
        $seo_data['schema']['description'] = $product->get_short_description();
        $seo_data['schema']['sku'] = $product->get_sku();
        
        if ($product->get_price()) {
            $seo_data['schema']['offers'] = [
                '@type' => 'Offer',
                'price' => $product->get_price(),
                'priceCurrency' => get_woocommerce_currency(),
                'availability' => $product->is_in_stock() ? 'InStock' : 'OutOfStock',
                'url' => get_permalink($post_id)
            ];
        }
        
        return $seo_data;
    }
}

/**
 * Elementor Compatibility
 */
class ACA_Elementor_Compatibility {
    
    public function init() {
        // Add Elementor widgets
        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
        
        // Handle Elementor content generation
        add_filter('aca_content_generation_context', [$this, 'add_elementor_context']);
        
        // Add Elementor templates support
        add_action('aca_generate_elementor_template', [$this, 'generate_elementor_template']);
    }
    
    public function register_widgets() {
        require_once ACA_PLUGIN_DIR . 'includes/elementor/class-aca-elementor-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new ACA_Elementor_Widget());
    }
    
    public function add_elementor_context($context) {
        if (defined('ELEMENTOR_VERSION')) {
            $context['elementor_version'] = ELEMENTOR_VERSION;
            $context['is_elementor_page'] = \Elementor\Plugin::$instance->db->is_built_with_elementor(get_the_ID());
        }
        
        return $context;
    }
    
    public function generate_elementor_template($template_type) {
        // Generate Elementor-compatible content structure
        $template_data = [
            'version' => '0.4',
            'title' => 'AI Generated Template',
            'type' => $template_type,
            'content' => $this->build_elementor_structure($template_type)
        ];
        
        return $template_data;
    }
    
    private function build_elementor_structure($template_type) {
        // Build Elementor JSON structure
        switch ($template_type) {
            case 'landing_page':
                return $this->build_landing_page_structure();
            case 'blog_post':
                return $this->build_blog_post_structure();
            default:
                return $this->build_default_structure();
        }
    }
    
    private function build_landing_page_structure() {
        return [
            [
                'id' => uniqid(),
                'elType' => 'section',
                'elements' => [
                    [
                        'id' => uniqid(),
                        'elType' => 'column',
                        'elements' => [
                            [
                                'id' => uniqid(),
                                'elType' => 'widget',
                                'widgetType' => 'heading',
                                'settings' => [
                                    'title' => 'AI Generated Headline',
                                    'size' => 'large'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}

/**
 * Gutenberg Compatibility
 */
class ACA_Gutenberg_Compatibility {
    
    public function init() {
        // Register Gutenberg blocks
        add_action('init', [$this, 'register_blocks']);
        
        // Add block editor assets
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_editor_assets']);
        
        // Handle block-based content generation
        add_filter('aca_content_generation_format', [$this, 'format_for_blocks']);
    }
    
    public function register_blocks() {
        if (function_exists('register_block_type')) {
            register_block_type('aca/ai-content-block', [
                'editor_script' => 'aca-gutenberg-blocks',
                'render_callback' => [$this, 'render_ai_content_block']
            ]);
        }
    }
    
    public function enqueue_block_editor_assets() {
        wp_enqueue_script(
            'aca-gutenberg-blocks',
            ACA_PLUGIN_URL . 'admin/js/gutenberg-blocks.js',
            ['wp-blocks', 'wp-element', 'wp-editor'],
            ACA_VERSION
        );
    }
    
    public function format_for_blocks($content) {
        // Convert content to Gutenberg blocks format
        $blocks = [];
        $paragraphs = explode("\n\n", $content);
        
        foreach ($paragraphs as $paragraph) {
            if (empty(trim($paragraph))) continue;
            
            // Detect heading
            if (preg_match('/^#{1,6}\s+(.+)/', $paragraph, $matches)) {
                $level = strlen(trim($matches[0])) - strlen(trim($matches[1])) - 1;
                $blocks[] = [
                    'blockName' => 'core/heading',
                    'attrs' => ['level' => $level],
                    'innerHTML' => '<h' . $level . '>' . trim($matches[1]) . '</h' . $level . '>'
                ];
            } else {
                // Regular paragraph
                $blocks[] = [
                    'blockName' => 'core/paragraph',
                    'attrs' => [],
                    'innerHTML' => '<p>' . trim($paragraph) . '</p>'
                ];
            }
        }
        
        return serialize_blocks($blocks);
    }
    
    public function render_ai_content_block($attributes) {
        return '<div class="aca-ai-content-block">' . 
               '<p>AI-generated content will appear here.</p>' . 
               '</div>';
    }
}

/**
 * Yoast SEO Compatibility
 */
class ACA_Yoast_SEO_Compatibility {
    
    public function init() {
        // Integrate with Yoast SEO analysis
        add_filter('wpseo_metadesc', [$this, 'use_ai_meta_description'], 10, 2);
        add_filter('wpseo_title', [$this, 'use_ai_title'], 10, 2);
        
        // Add AI-generated content to Yoast analysis
        add_filter('wpseo_pre_analysis_post_content', [$this, 'include_ai_content']);
    }
    
    public function use_ai_meta_description($meta_desc, $post_id) {
        $ai_meta_desc = get_post_meta($post_id, '_aca_meta_description', true);
        return $ai_meta_desc ?: $meta_desc;
    }
    
    public function use_ai_title($title, $post_id) {
        $ai_title = get_post_meta($post_id, '_aca_meta_title', true);
        return $ai_title ?: $title;
    }
    
    public function include_ai_content($content) {
        // Include AI-generated content in Yoast analysis
        global $post;
        if ($post) {
            $ai_content = get_post_meta($post->ID, '_aca_ai_content', true);
            if ($ai_content) {
                $content .= ' ' . $ai_content;
            }
        }
        return $content;
    }
}

/**
 * WPML Compatibility
 */
class ACA_WPML_Compatibility {
    
    public function init() {
        // Handle multilingual content generation
        add_filter('aca_content_generation_context', [$this, 'add_language_context']);
        add_action('aca_content_generated', [$this, 'handle_translation'], 10, 2);
    }
    
    public function add_language_context($context) {
        if (function_exists('icl_get_current_language')) {
            $context['current_language'] = icl_get_current_language();
            $context['default_language'] = icl_get_default_language();
            $context['active_languages'] = icl_get_languages('skip_missing=0');
        }
        return $context;
    }
    
    public function handle_translation($post_id, $content_data) {
        if (!function_exists('icl_get_current_language')) return;
        
        $current_lang = icl_get_current_language();
        $default_lang = icl_get_default_language();
        
        // If not default language, create translation
        if ($current_lang !== $default_lang) {
            $this->create_translation($post_id, $current_lang, $content_data);
        }
    }
    
    private function create_translation($post_id, $language, $content_data) {
        // Register post for translation
        do_action('wpml_register_single_string', 'ai-content-agent', 'generated-content-' . $post_id, $content_data['content']);
    }
}

/**
 * ACF Compatibility
 */
class ACA_ACF_Compatibility {
    
    public function init() {
        // Add ACF fields to content generation context
        add_filter('aca_content_generation_context', [$this, 'add_acf_context']);
        
        // Handle ACF field population
        add_action('aca_content_generated', [$this, 'populate_acf_fields'], 10, 2);
    }
    
    public function add_acf_context($context) {
        global $post;
        if ($post && function_exists('get_fields')) {
            $context['acf_fields'] = get_fields($post->ID);
        }
        return $context;
    }
    
    public function populate_acf_fields($post_id, $content_data) {
        if (!function_exists('update_field')) return;
        
        // Auto-populate common ACF fields
        if (isset($content_data['excerpt'])) {
            update_field('excerpt', $content_data['excerpt'], $post_id);
        }
        
        if (isset($content_data['featured_text'])) {
            update_field('featured_text', $content_data['featured_text'], $post_id);
        }
    }
}

/**
 * Caching Compatibility
 */
class ACA_Caching_Compatibility {
    
    private $detected_plugins;
    
    public function __construct($detected_plugins) {
        $this->detected_plugins = $detected_plugins;
    }
    
    public function init() {
        // Clear cache after content generation
        add_action('aca_content_generated', [$this, 'clear_cache'], 10, 2);
        
        // Handle cache warming
        add_action('aca_content_published', [$this, 'warm_cache']);
    }
    
    public function clear_cache($post_id, $content_data) {
        $post_url = get_permalink($post_id);
        
        // WP Rocket
        if ($this->detected_plugins['wp_rocket'] && function_exists('rocket_clean_post')) {
            rocket_clean_post($post_id);
        }
        
        // W3 Total Cache
        if ($this->detected_plugins['w3_total_cache'] && function_exists('w3tc_flush_post')) {
            w3tc_flush_post($post_id);
        }
        
        // WP Super Cache
        if ($this->detected_plugins['wp_super_cache'] && function_exists('wp_cache_post_change')) {
            wp_cache_post_change($post_id);
        }
        
        // LiteSpeed Cache
        if ($this->detected_plugins['litespeed_cache'] && class_exists('LiteSpeed_Cache_API')) {
            LiteSpeed_Cache_API::purge_post($post_id);
        }
    }
    
    public function warm_cache($post_id) {
        $post_url = get_permalink($post_id);
        
        // Warm cache by making a request
        wp_remote_get($post_url, [
            'timeout' => 30,
            'blocking' => false
        ]);
    }
}

/**
 * Page Builders Compatibility
 */
class ACA_Page_Builders_Compatibility {
    
    private $detected_plugins;
    
    public function __construct($detected_plugins) {
        $this->detected_plugins = $detected_plugins;
    }
    
    public function init() {
        // Handle page builder content
        add_filter('aca_content_generation_format', [$this, 'format_for_page_builder']);
    }
    
    public function format_for_page_builder($content) {
        // Detect which page builder is active and format accordingly
        if ($this->detected_plugins['divi']) {
            return $this->format_for_divi($content);
        }
        
        if ($this->detected_plugins['beaver_builder']) {
            return $this->format_for_beaver_builder($content);
        }
        
        return $content;
    }
    
    private function format_for_divi($content) {
        // Convert content to Divi modules format
        $divi_content = '[et_pb_section][et_pb_row][et_pb_column type="4_4"]';
        
        $paragraphs = explode("\n\n", $content);
        foreach ($paragraphs as $paragraph) {
            if (!empty(trim($paragraph))) {
                $divi_content .= '[et_pb_text]' . trim($paragraph) . '[/et_pb_text]';
            }
        }
        
        $divi_content .= '[/et_pb_column][/et_pb_row][/et_pb_section]';
        
        return $divi_content;
    }
    
    private function format_for_beaver_builder($content) {
        // Format for Beaver Builder
        return $content; // Beaver Builder uses standard content format
    }
}
