<?php
/**
 * Ideas management page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Content Ideas</h2>
            <button id="generate-ideas-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <span>Generate New Ideas</span>
                <span id="generate-loading" class="hidden">
                    <svg class="animate-spin h-4 w-4 ml-2 inline" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>

        <!-- Manual Idea Input -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <h3 class="text-lg font-medium text-gray-900 mb-3">Add Manual Idea</h3>
            <div class="flex gap-3">
                <input type="text" id="manual-idea-input" placeholder="Enter your blog post idea..." class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <button id="add-manual-idea-btn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Add Idea
                </button>
            </div>
        </div>

        <!-- Ideas List -->
        <?php if (!empty($ideas)): ?>
        <div class="space-y-4">
            <?php foreach ($ideas as $idea): ?>
            <div class="idea-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-id="<?php echo esc_attr($idea->id); ?>">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo esc_html($idea->title); ?></h3>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span class="flex items-center">
                                <?php if ($idea->source === 'ai'): ?>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                AI Generated
                                <?php else: ?>
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Manual
                                <?php endif; ?>
                            </span>
                            <span><?php echo esc_html(human_time_diff(strtotime($idea->created_at))); ?> ago</span>
                            <span class="px-2 py-1 text-xs rounded-full <?php echo $idea->status === 'new' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                <?php echo esc_html(ucfirst($idea->status)); ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <button class="create-draft-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" data-title="<?php echo esc_attr($idea->title); ?>">
                            Create Draft
                        </button>
                        <button class="archive-idea-btn text-gray-500 hover:text-gray-700 p-1" data-id="<?php echo esc_attr($idea->id); ?>" title="Archive">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4 4 4-4"></path>
                            </svg>
                        </button>
                        <button class="delete-idea-btn text-red-500 hover:text-red-700 p-1" data-id="<?php echo esc_attr($idea->id); ?>" title="Delete">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No ideas yet</h3>
            <p class="text-gray-500 mb-4">Generate some AI-powered content ideas or add your own manually.</p>
            <button id="generate-first-ideas-btn" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                Generate Your First Ideas
            </button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Success/Error Messages -->
    <div id="aca-messages" class="hidden mb-4"></div>
</div>

<script>
jQuery(document).ready(function($) {
    // Generate Ideas
    $('#generate-ideas-btn, #generate-first-ideas-btn').on('click', function() {
        const $btn = $(this);
        const $loading = $('#generate-loading');
        
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

    // Add Manual Idea
    $('#add-manual-idea-btn').on('click', function() {
        const title = $('#manual-idea-input').val().trim();
        if (!title) {
            showMessage('Please enter an idea title', 'error');
            return;
        }

        const $btn = $(this);
        $btn.prop('disabled', true).text('Adding...');

        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_add_manual_idea',
                nonce: aca_ajax.nonce,
                title: title
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Idea added successfully!', 'success');
                    $('#manual-idea-input').val('');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Add Idea');
            }
        });
    });

    // Create Draft from Idea
    $('.create-draft-btn').on('click', function() {
        const title = $(this).data('title');
        const $btn = $(this);
        
        $btn.prop('disabled', true).text('Creating...');
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_create_draft',
                nonce: aca_ajax.nonce,
                title: title
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Draft created successfully!', 'success');
                    setTimeout(() => {
                        window.location.href = '<?php echo admin_url('admin.php?page=aca-drafts'); ?>';
                    }, 1500);
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Create Draft');
            }
        });
    });

    // Archive Idea
    $('.archive-idea-btn').on('click', function() {
        const ideaId = $(this).data('id');
        const $ideaItem = $(this).closest('.idea-item');
        
        if (!confirm('Are you sure you want to archive this idea?')) {
            return;
        }
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_archive_idea',
                nonce: aca_ajax.nonce,
                idea_id: ideaId
            },
            success: function(response) {
                if (response.success) {
                    $ideaItem.fadeOut();
                    showMessage('Idea archived successfully', 'success');
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            }
        });
    });

    // Delete Idea
    $('.delete-idea-btn').on('click', function() {
        const ideaId = $(this).data('id');
        const $ideaItem = $(this).closest('.idea-item');
        
        if (!confirm('Are you sure you want to delete this idea? This action cannot be undone.')) {
            return;
        }
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_delete_idea',
                nonce: aca_ajax.nonce,
                idea_id: ideaId
            },
            success: function(response) {
                if (response.success) {
                    $ideaItem.fadeOut();
                    showMessage('Idea deleted successfully', 'success');
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            }
        });
    });

    // Enter key for manual idea input
    $('#manual-idea-input').on('keypress', function(e) {
        if (e.which === 13) {
            $('#add-manual-idea-btn').click();
        }
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