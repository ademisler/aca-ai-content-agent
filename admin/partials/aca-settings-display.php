<?php
/**
 * Settings page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <form id="aca-settings-form">
        <!-- API Configuration -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">API Configuration</h2>
            
            <div class="mb-4">
                <label for="gemini_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                    Gemini API Key <span class="text-red-500">*</span>
                </label>
                <input type="password" id="gemini_api_key" name="gemini_api_key" 
                       value="<?php echo esc_attr($settings['gemini_api_key'] ?? ''); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter your Gemini API key">
                <p class="text-sm text-gray-500 mt-1">
                    Get your API key from <a href="https://ai.google.dev/" target="_blank" class="text-blue-600 hover:text-blue-800">Google AI Studio</a>
                </p>
            </div>
        </div>

        <!-- Automation Settings -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Automation Settings</h2>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Automation Mode</label>
                <div class="space-y-3">
                    <label class="flex items-start p-4 rounded-lg border-2 transition-all cursor-pointer <?php echo ($settings['mode'] ?? 'manual') === 'manual' ? 'bg-blue-50 border-blue-500' : 'bg-gray-50 border-gray-200 hover:border-gray-300'; ?>">
                        <input type="radio" name="mode" value="manual" 
                               <?php checked($settings['mode'] ?? 'manual', 'manual'); ?>
                               class="mt-1 h-4 w-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">Manual Mode</h4>
                            <p class="text-gray-600 text-sm mt-1">You control when to generate ideas and create drafts. Full manual control over the content creation process.</p>
                        </div>
                    </label>
                    
                    <label class="flex items-start p-4 rounded-lg border-2 transition-all cursor-pointer <?php echo ($settings['mode'] ?? 'manual') === 'semi-automatic' ? 'bg-blue-50 border-blue-500' : 'bg-gray-50 border-gray-200 hover:border-gray-300'; ?>">
                        <input type="radio" name="mode" value="semi-automatic" 
                               <?php checked($settings['mode'] ?? 'manual', 'semi-automatic'); ?>
                               class="mt-1 h-4 w-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">Semi-Automatic Mode</h4>
                            <p class="text-gray-600 text-sm mt-1">Automatically generate ideas periodically, but you review and approve drafts before publishing.</p>
                        </div>
                    </label>
                    
                    <label class="flex items-start p-4 rounded-lg border-2 transition-all cursor-pointer <?php echo ($settings['mode'] ?? 'manual') === 'full-automatic' ? 'bg-blue-50 border-blue-500' : 'bg-gray-50 border-gray-200 hover:border-gray-300'; ?>">
                        <input type="radio" name="mode" value="full-automatic" 
                               <?php checked($settings['mode'] ?? 'manual', 'full-automatic'); ?>
                               class="mt-1 h-4 w-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500">
                        <div class="ml-3">
                            <h4 class="font-semibold text-gray-900">Full-Automatic Mode</h4>
                            <p class="text-gray-600 text-sm mt-1">Fully automated content creation and publishing. Use with caution and monitor regularly.</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="auto_publish" value="1" 
                           <?php checked($settings['auto_publish'] ?? false, true); ?>
                           class="h-4 w-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Auto-publish approved drafts</span>
                </label>
                <p class="text-sm text-gray-500 mt-1">When enabled, drafts will be automatically published without manual approval.</p>
            </div>
        </div>

        <!-- Image Settings -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Image Settings</h2>
            
            <div class="mb-4">
                <label for="image_source_provider" class="block text-sm font-medium text-gray-700 mb-2">Image Source Provider</label>
                <select id="image_source_provider" name="image_source_provider" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="ai" <?php selected($settings['image_source_provider'] ?? 'ai', 'ai'); ?>>AI Generated (Imagen)</option>
                    <option value="pexels" <?php selected($settings['image_source_provider'] ?? 'ai', 'pexels'); ?>>Pexels</option>
                    <option value="unsplash" <?php selected($settings['image_source_provider'] ?? 'ai', 'unsplash'); ?>>Unsplash</option>
                    <option value="pixabay" <?php selected($settings['image_source_provider'] ?? 'ai', 'pixabay'); ?>>Pixabay</option>
                </select>
            </div>

            <div class="mb-4" id="ai-image-style-section">
                <label for="ai_image_style" class="block text-sm font-medium text-gray-700 mb-2">AI Image Style</label>
                <select id="ai_image_style" name="ai_image_style" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="photorealistic" <?php selected($settings['ai_image_style'] ?? 'photorealistic', 'photorealistic'); ?>>Photorealistic</option>
                    <option value="digital_art" <?php selected($settings['ai_image_style'] ?? 'photorealistic', 'digital_art'); ?>>Digital Art</option>
                </select>
            </div>

            <!-- Stock Photo API Keys -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="pexels_api_key" class="block text-sm font-medium text-gray-700 mb-2">Pexels API Key</label>
                    <input type="password" id="pexels_api_key" name="pexels_api_key" 
                           value="<?php echo esc_attr($settings['pexels_api_key'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="unsplash_api_key" class="block text-sm font-medium text-gray-700 mb-2">Unsplash API Key</label>
                    <input type="password" id="unsplash_api_key" name="unsplash_api_key" 
                           value="<?php echo esc_attr($settings['unsplash_api_key'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="pixabay_api_key" class="block text-sm font-medium text-gray-700 mb-2">Pixabay API Key</label>
                    <input type="password" id="pixabay_api_key" name="pixabay_api_key" 
                           value="<?php echo esc_attr($settings['pixabay_api_key'] ?? ''); ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <!-- SEO Integration -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">SEO Integration</h2>
            
            <div class="mb-4">
                <label for="seo_plugin" class="block text-sm font-medium text-gray-700 mb-2">SEO Plugin</label>
                <select id="seo_plugin" name="seo_plugin" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="none" <?php selected($settings['seo_plugin'] ?? 'none', 'none'); ?>>None</option>
                    <option value="yoast" <?php selected($settings['seo_plugin'] ?? 'none', 'yoast'); ?>>Yoast SEO</option>
                    <option value="rank_math" <?php selected($settings['seo_plugin'] ?? 'none', 'rank_math'); ?>>Rank Math</option>
                </select>
                <p class="text-sm text-gray-500 mt-1">Select your SEO plugin to automatically populate meta fields.</p>
            </div>
        </div>

        <!-- Style Guide -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Style Guide</h2>
                <button type="button" id="analyze-style-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <span>Re-analyze Style</span>
                    <span id="analyze-loading" class="hidden">
                        <svg class="animate-spin h-4 w-4 ml-2 inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>

            <?php if (!empty($style_guide) && !empty($style_guide['tone'])): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <h4 class="text-green-800 font-medium mb-2">Current Style Guide</h4>
                <div class="text-sm text-green-700 space-y-1">
                    <p><strong>Tone:</strong> <?php echo esc_html($style_guide['tone']); ?></p>
                    <p><strong>Sentence Structure:</strong> <?php echo esc_html($style_guide['sentenceStructure'] ?? $style_guide['sentence_structure'] ?? ''); ?></p>
                    <p><strong>Paragraph Length:</strong> <?php echo esc_html($style_guide['paragraphLength'] ?? $style_guide['paragraph_length'] ?? ''); ?></p>
                    <p><strong>Formatting Style:</strong> <?php echo esc_html($style_guide['formattingStyle'] ?? $style_guide['formatting_style'] ?? ''); ?></p>
                    <?php if (!empty($style_guide['last_analyzed'])): ?>
                    <p><strong>Last Analyzed:</strong> <?php echo esc_html(human_time_diff(strtotime($style_guide['last_analyzed']))); ?> ago</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <h4 class="text-yellow-800 font-medium mb-2">Style Guide Not Set</h4>
                <p class="text-yellow-700 text-sm">Click "Re-analyze Style" to analyze your recent posts and create a style guide for AI content generation.</p>
            </div>
            <?php endif; ?>

            <div class="mb-4">
                <label for="custom_instructions" class="block text-sm font-medium text-gray-700 mb-2">Custom Instructions (Optional)</label>
                <textarea id="custom_instructions" name="custom_instructions" rows="4" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Add any specific instructions for content generation..."><?php echo esc_textarea($style_guide['custom_instructions'] ?? ''); ?></textarea>
                <p class="text-sm text-gray-500 mt-1">These instructions will be included when generating content to ensure it matches your specific requirements.</p>
            </div>
        </div>

        <!-- Save Button -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <span>Save Settings</span>
                    <span id="save-loading" class="hidden">
                        <svg class="animate-spin h-4 w-4 ml-2 inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </div>
    </form>

    <!-- Success/Error Messages -->
    <div id="aca-messages" class="hidden mb-4"></div>
</div>

<script>
jQuery(document).ready(function($) {
    // Toggle AI image style visibility
    function toggleAiImageStyle() {
        const provider = $('#image_source_provider').val();
        $('#ai-image-style-section').toggle(provider === 'ai');
    }
    
    $('#image_source_provider').on('change', toggleAiImageStyle);
    toggleAiImageStyle(); // Initial state

    // Save Settings
    $('#aca-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $saveBtn = $form.find('button[type="submit"]');
        const $loading = $('#save-loading');
        
        $saveBtn.prop('disabled', true);
        $loading.removeClass('hidden');
        
        const formData = $form.serialize() + '&action=aca_save_settings&nonce=' + aca_ajax.nonce;
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showMessage('Settings saved successfully!', 'success');
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            },
            complete: function() {
                $saveBtn.prop('disabled', false);
                $loading.addClass('hidden');
            }
        });
    });

    // Analyze Style Guide
    $('#analyze-style-btn').on('click', function() {
        const $btn = $(this);
        const $loading = $('#analyze-loading');
        
        $btn.prop('disabled', true);
        $loading.removeClass('hidden');
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_analyze_style',
                nonce: aca_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Style guide analyzed successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $loading.addClass('hidden');
            }
        });
    });

    function showMessage(message, type) {
        const $messages = $('#aca-messages');
        const alertClass = type === 'success' ? 'notice-success' : 'notice-error';
        
        $messages.html(`<div class="notice ${alertClass} is-dismissible"><p>${message}</p></div>`);
        $messages.removeClass('hidden');
        
        setTimeout(() => {
            $messages.addClass('hidden');
        }, 5000);
    }
});
</script>