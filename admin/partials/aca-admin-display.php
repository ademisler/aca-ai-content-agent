<?php
/**
 * Provide a admin area view for the plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Stats Cards -->
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                <div class="flex items-center">
                    <div class="bg-blue-600 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo esc_html($stats['ideas']); ?></h3>
                        <p class="text-gray-600">Content Ideas</p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200">
                <div class="flex items-center">
                    <div class="bg-yellow-600 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo esc_html($stats['drafts']); ?></h3>
                        <p class="text-gray-600">Draft Posts</p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                <div class="flex items-center">
                    <div class="bg-green-600 p-3 rounded-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-2xl font-bold text-gray-900"><?php echo esc_html($stats['published']); ?></h3>
                        <p class="text-gray-600">Published Posts</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <button id="analyze-style-btn" class="bg-slate-800 p-6 rounded-lg border border-slate-700 shadow-md flex flex-col items-start text-left w-full hover:bg-slate-700 hover:border-slate-600 transition-all transform hover:-translate-y-1">
                <div class="bg-blue-600 p-3 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-1">
                    <span>Analyze Style Guide</span>
                    <span id="analyze-loading" class="hidden">
                        <svg class="animate-spin h-5 w-5 ml-2 inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </h3>
                <p class="text-slate-400 text-sm">Learn your site's writing style from recent posts</p>
            </button>

            <button id="generate-ideas-btn" class="bg-slate-800 p-6 rounded-lg border border-slate-700 shadow-md flex flex-col items-start text-left w-full hover:bg-slate-700 hover:border-slate-600 transition-all transform hover:-translate-y-1">
                <div class="bg-yellow-600 p-3 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-1">
                    <span>Generate Ideas</span>
                    <span id="ideas-loading" class="hidden">
                        <svg class="animate-spin h-5 w-5 ml-2 inline" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </h3>
                <p class="text-slate-400 text-sm">Create new blog post ideas using AI</p>
            </button>

            <a href="<?php echo admin_url('admin.php?page=aca-settings'); ?>" class="bg-slate-800 p-6 rounded-lg border border-slate-700 shadow-md flex flex-col items-start text-left w-full hover:bg-slate-700 hover:border-slate-600 transition-all transform hover:-translate-y-1 text-decoration-none">
                <div class="bg-gray-600 p-3 rounded-lg mb-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-1">Settings</h3>
                <p class="text-slate-400 text-sm">Configure API keys and automation settings</p>
            </a>
        </div>

        <!-- Style Guide Status -->
        <?php if (!empty($style_guide) && !empty($style_guide['tone'])): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="text-green-800 font-medium">Style Guide Active</h4>
                    <p class="text-green-700 text-sm mt-1">
                        <strong>Tone:</strong> <?php echo esc_html($style_guide['tone']); ?><br>
                        <strong>Last analyzed:</strong> <?php echo esc_html($style_guide['last_analyzed'] ?? 'Unknown'); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h4 class="text-yellow-800 font-medium">Style Guide Not Set</h4>
                    <p class="text-yellow-700 text-sm mt-1">Click "Analyze Style Guide" to learn your site's writing style before generating content.</p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Activity -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
            <?php if (!empty($activity_logs)): ?>
            <div class="space-y-3">
                <?php foreach ($activity_logs as $log): ?>
                <div class="flex items-start p-3 bg-gray-50 rounded-lg">
                    <div class="bg-blue-100 p-2 rounded-lg mr-3">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-900"><?php echo esc_html($log->details); ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo esc_html(human_time_diff(strtotime($log->created_at))); ?> ago</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-500 text-sm">No recent activity</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="aca-messages" class="hidden mb-4"></div>
</div>

<script>
jQuery(document).ready(function($) {
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

    // Generate Ideas
    $('#generate-ideas-btn').on('click', function() {
        const $btn = $(this);
        const $loading = $('#ideas-loading');
        
        $btn.prop('disabled', true);
        $loading.removeClass('hidden');
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_generate_ideas',
                nonce: aca_ajax.nonce,
                count: 5
            },
            success: function(response) {
                if (response.success) {
                    showMessage(response.data.message, 'success');
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