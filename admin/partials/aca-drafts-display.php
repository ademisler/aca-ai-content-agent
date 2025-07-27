<?php
/**
 * Drafts management page
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <!-- Drafts Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Draft Posts</h2>
            <span class="text-sm text-gray-500"><?php echo count($drafts); ?> drafts</span>
        </div>

        <?php if (!empty($drafts)): ?>
        <div class="space-y-4">
            <?php foreach ($drafts as $draft): ?>
            <div class="draft-item bg-gray-50 p-4 rounded-lg border border-gray-200" data-id="<?php echo esc_attr($draft->id); ?>">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo esc_html($draft->title); ?></h3>
                        <div class="text-sm text-gray-600 mb-3">
                            <p><strong>Meta Title:</strong> <?php echo esc_html($draft->meta_title); ?></p>
                            <p><strong>Meta Description:</strong> <?php echo esc_html($draft->meta_description); ?></p>
                            <?php if ($draft->focus_keywords): ?>
                            <p><strong>Focus Keywords:</strong> <?php echo esc_html($draft->focus_keywords); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span><?php echo esc_html(human_time_diff(strtotime($draft->created_at))); ?> ago</span>
                            <span><?php echo esc_html(str_word_count(wp_strip_all_tags($draft->content))); ?> words</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 ml-4">
                        <button class="view-draft-btn bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" data-id="<?php echo esc_attr($draft->id); ?>">
                            View/Edit
                        </button>
                        <button class="publish-draft-btn bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" data-id="<?php echo esc_attr($draft->id); ?>">
                            Publish
                        </button>
                        <button class="delete-draft-btn text-red-500 hover:text-red-700 p-1" data-id="<?php echo esc_attr($draft->id); ?>" title="Delete">
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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No drafts yet</h3>
            <p class="text-gray-500 mb-4">Create your first draft from an idea.</p>
            <a href="<?php echo admin_url('admin.php?page=aca-ideas'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors inline-block">
                Go to Ideas
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Published Posts Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Published Posts</h2>
            <span class="text-sm text-gray-500"><?php echo count($published); ?> published</span>
        </div>

        <?php if (!empty($published)): ?>
        <div class="space-y-4">
            <?php foreach ($published as $post): ?>
            <div class="published-item bg-green-50 p-4 rounded-lg border border-green-200">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900 mb-2"><?php echo esc_html($post->title); ?></h3>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span>Published <?php echo esc_html(human_time_diff(strtotime($post->updated_at))); ?> ago</span>
                            <?php if ($post->post_id): ?>
                            <a href="<?php echo get_permalink($post->post_id); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">
                                View Post →
                            </a>
                            <a href="<?php echo get_edit_post_link($post->post_id); ?>" class="text-blue-600 hover:text-blue-800">
                                Edit in WordPress
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">Published</span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-8">
            <p class="text-gray-500">No published posts yet.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Success/Error Messages -->
    <div id="aca-messages" class="hidden mb-4"></div>
</div>

<!-- Draft View/Edit Modal -->
<div id="draft-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Draft</h3>
                    <button id="close-modal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form id="draft-form">
                    <input type="hidden" id="draft-id" name="draft_id">
                    
                    <div class="mb-4">
                        <label for="draft-title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="draft-title" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="draft-meta-title" class="block text-sm font-medium text-gray-700 mb-2">Meta Title</label>
                            <input type="text" id="draft-meta-title" name="meta_title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="draft-meta-description" class="block text-sm font-medium text-gray-700 mb-2">Meta Description</label>
                            <textarea id="draft-meta-description" name="meta_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="draft-content" class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                        <textarea id="draft-content" name="content" rows="15" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"></textarea>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" id="close-modal-btn" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            Close
                        </button>
                        <div class="space-x-2">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                Save Changes
                            </button>
                            <button type="button" id="publish-from-modal" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                                Publish Now
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    let currentDraftId = null;

    // View/Edit Draft
    $('.view-draft-btn').on('click', function() {
        const draftId = $(this).data('id');
        currentDraftId = draftId;
        
        // Find the draft data from the page
        const $draftItem = $(this).closest('.draft-item');
        const title = $draftItem.find('h3').text();
        
        // Load draft data via AJAX (you'd implement this)
        loadDraftData(draftId);
        
        $('#draft-modal').removeClass('hidden');
    });

    // Close Modal
    $('#close-modal, #close-modal-btn').on('click', function() {
        $('#draft-modal').addClass('hidden');
        currentDraftId = null;
    });

    // Save Draft Changes
    $('#draft-form').on('submit', function(e) {
        e.preventDefault();
        
        if (!currentDraftId) return;
        
        const formData = {
            action: 'aca_save_draft',
            nonce: aca_ajax.nonce,
            draft_id: currentDraftId,
            title: $('#draft-title').val(),
            content: $('#draft-content').val(),
            meta_title: $('#draft-meta-title').val(),
            meta_description: $('#draft-meta-description').val()
        };
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    showMessage('Draft saved successfully!', 'success');
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            }
        });
    });

    // Publish Draft
    $('.publish-draft-btn, #publish-from-modal').on('click', function() {
        let draftId;
        
        if ($(this).hasClass('publish-draft-btn')) {
            draftId = $(this).data('id');
        } else {
            draftId = currentDraftId;
        }
        
        if (!draftId) return;
        
        if (!confirm('Are you sure you want to publish this draft?')) {
            return;
        }
        
        const $btn = $(this);
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Publishing...');
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_publish_draft',
                nonce: aca_ajax.nonce,
                draft_id: draftId
            },
            success: function(response) {
                if (response.success) {
                    showMessage('Post published successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Delete Draft
    $('.delete-draft-btn').on('click', function() {
        const draftId = $(this).data('id');
        const $draftItem = $(this).closest('.draft-item');
        
        if (!confirm('Are you sure you want to delete this draft? This action cannot be undone.')) {
            return;
        }
        
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_delete_draft',
                nonce: aca_ajax.nonce,
                draft_id: draftId
            },
            success: function(response) {
                if (response.success) {
                    $draftItem.fadeOut();
                    showMessage('Draft deleted successfully', 'success');
                } else {
                    showMessage('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showMessage('Network error occurred', 'error');
            }
        });
    });

    function loadDraftData(draftId) {
        $.ajax({
            url: aca_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'aca_get_draft',
                nonce: aca_ajax.nonce,
                draft_id: draftId
            },
            success: function(response) {
                if (response.success) {
                    const draft = response.data;
                    $('#draft-id').val(draft.id);
                    $('#draft-title').val(draft.title);
                    $('#draft-meta-title').val(draft.meta_title);
                    $('#draft-meta-description').val(draft.meta_description);
                    $('#draft-content').val(draft.content);
                }
            }
        });
    }

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