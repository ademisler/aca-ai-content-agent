<?php
/**
 * Published posts display template - React Prototype Design
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="aca-app-container">
    <!-- Mobile Header -->
    <header class="aca-mobile-header">
        <button id="aca-sidebar-toggle" class="aca-mobile-menu-btn">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <span class="aca-mobile-title">AI Content Agent</span>
    </header>

    <div class="aca-main-layout">
        <!-- Sidebar -->
        <aside class="aca-sidebar" id="aca-sidebar">
            <div class="aca-sidebar-content">
                <div class="aca-sidebar-header">
                    <h1 class="aca-sidebar-title">AI Content Agent</h1>
                    <a href="https://ademisler.com" target="_blank" rel="noopener noreferrer" class="aca-sidebar-credit">by Adem Isler</a>
                </div>
                <nav class="aca-sidebar-nav">
                    <a href="<?php echo admin_url('admin.php?page=aca-ai-content-agent'); ?>" class="aca-nav-item">
                        <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aca-style-guide'); ?>" class="aca-nav-item">
                        <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                        Style Guide
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aca-ideas'); ?>" class="aca-nav-item">
                        <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>
                        </svg>
                        Idea Board
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aca-drafts'); ?>" class="aca-nav-item">
                        <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>
                        </svg>
                        Drafts
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aca-calendar'); ?>" class="aca-nav-item">
                        <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                        Calendar
                    </a>
                    <a href="<?php echo admin_url('admin.php?page=aca-published'); ?>" class="aca-nav-item active">
                        <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                        </svg>
                        Published
                    </a>
                </nav>
            </div>
            <div class="aca-sidebar-footer">
                <a href="<?php echo admin_url('admin.php?page=aca-settings'); ?>" class="aca-nav-item">
                    <svg class="aca-nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 0 2.82l-.15.08a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1 0-2.82l.15.08a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    Settings
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="aca-main-content">
            <div class="aca-dashboard-content">
                <header class="aca-page-header">
                    <h2 class="aca-page-title">Published Posts</h2>
                    <p class="aca-page-subtitle">View and manage your published content.</p>
                </header>

                <?php if (!empty($published)): ?>
                <div class="aca-published-grid">
                    <?php foreach ($published as $post): ?>
                    <div class="aca-published-card">
                        <div class="aca-published-header">
                            <h3 class="aca-published-title"><?php echo esc_html($post->title); ?></h3>
                            <div class="aca-published-status">
                                <svg class="aca-status-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                </svg>
                                Published
                            </div>
                        </div>
                        
                        <div class="aca-published-meta">
                            <div class="aca-meta-item">
                                <svg class="aca-meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                                <?php echo esc_html(date('M j, Y', strtotime($post->created_at))); ?>
                            </div>
                        </div>
                        
                        <div class="aca-published-content">
                            <p><?php echo esc_html(wp_trim_words(strip_tags($post->content), 20)); ?></p>
                        </div>
                        
                        <div class="aca-published-actions">
                            <?php if (!empty($post->url)): ?>
                            <a href="<?php echo esc_url($post->url); ?>" target="_blank" class="aca-action-button aca-action-view">
                                <svg class="aca-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                                View Post
                            </a>
                            <?php endif; ?>
                            <button class="aca-action-button aca-action-edit" onclick="editPost(<?php echo $post->id; ?>)">
                                <svg class="aca-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                </svg>
                                Edit
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="aca-empty-state">
                    <div class="aca-empty-icon">
                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                        </svg>
                    </div>
                    <h3 class="aca-empty-title">No Published Posts Yet</h3>
                    <p class="aca-empty-description">
                        Once you publish drafts, they'll appear here for easy management and review.
                    </p>
                    <a href="<?php echo admin_url('admin.php?page=aca-drafts'); ?>" class="aca-empty-action">
                        View Drafts
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="aca-toast-container" class="aca-toast-container"></div>

    <!-- Sidebar Overlay -->
    <div id="aca-sidebar-overlay" class="aca-sidebar-overlay"></div>
</div>

<style>
.aca-published-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
}

.aca-published-card {
    background: #1e293b;
    border: 1px solid rgba(51, 65, 85, 0.8);
    border-radius: 8px;
    padding: 24px;
    transition: all 0.2s ease;
}

.aca-published-card:hover {
    border-color: #475569;
    transform: translateY(-2px);
}

.aca-published-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
    gap: 12px;
}

.aca-published-title {
    font-size: 18px;
    font-weight: 600;
    color: #ffffff;
    margin: 0;
    line-height: 1.4;
    flex: 1;
}

.aca-published-status {
    display: flex;
    align-items: center;
    gap: 4px;
    background: rgba(34, 197, 94, 0.1);
    color: #4ade80;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    flex-shrink: 0;
}

.aca-status-icon {
    width: 14px;
    height: 14px;
}

.aca-published-meta {
    margin-bottom: 16px;
}

.aca-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #94a3b8;
    font-size: 14px;
}

.aca-meta-icon {
    width: 16px;
    height: 16px;
}

.aca-published-content {
    margin-bottom: 20px;
}

.aca-published-content p {
    color: #cbd5e1;
    margin: 0;
    line-height: 1.6;
    font-size: 14px;
}

.aca-published-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.aca-action-button {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.aca-action-view {
    background: #0f172a;
    color: #cbd5e1;
    border: 1px solid #334155;
}

.aca-action-view:hover {
    background: #1e293b;
    color: #ffffff;
}

.aca-action-edit {
    background: #2563eb;
    color: #ffffff;
}

.aca-action-edit:hover {
    background: #1d4ed8;
}

.aca-button-icon {
    width: 14px;
    height: 14px;
}

.aca-empty-state {
    text-align: center;
    padding: 80px 20px;
    background: rgba(30, 41, 59, 0.3);
    border: 1px solid rgba(51, 65, 85, 0.6);
    border-radius: 12px;
    max-width: 500px;
    margin: 0 auto;
}

.aca-empty-icon {
    margin: 0 auto 32px;
    width: 100px;
    height: 100px;
    background: rgba(37, 99, 235, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aca-empty-icon .aca-icon {
    width: 50px;
    height: 50px;
    color: #60a5fa;
}

.aca-empty-title {
    font-size: 28px;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 16px 0;
}

.aca-empty-description {
    color: #94a3b8;
    font-size: 16px;
    margin: 0 0 32px 0;
    line-height: 1.6;
}

.aca-empty-action {
    background: #2563eb;
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: background-color 0.2s ease;
}

.aca-empty-action:hover {
    background: #1d4ed8;
}

@media (max-width: 768px) {
    .aca-published-grid {
        grid-template-columns: 1fr;
    }
    
    .aca-published-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .aca-empty-state {
        padding: 60px 20px;
    }
    
    .aca-empty-title {
        font-size: 24px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Mobile sidebar toggle
    $('#aca-sidebar-toggle').on('click', function() {
        $('#aca-sidebar').addClass('open');
        $('#aca-sidebar-overlay').addClass('active');
    });

    $('#aca-sidebar-overlay').on('click', function() {
        $('#aca-sidebar').removeClass('open');
        $('#aca-sidebar-overlay').removeClass('active');
    });
});

function editPost(postId) {
    // This would open a modal or redirect to edit page
    console.log('Edit post:', postId);
    // For now, just show a toast
    showToast('Edit functionality coming soon!', 'info');
}

function showToast(message, type) {
    const toastId = Date.now();
    const iconMap = {
        success: '<svg class="aca-toast-icon aca-toast-success" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
        error: '<svg class="aca-toast-icon aca-toast-error" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
        warning: '<svg class="aca-toast-icon aca-toast-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m21.73 18-8-14a2 2 0 0 0-3.46 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>',
        info: '<svg class="aca-toast-icon aca-toast-info" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>'
    };

    const toast = $(`
        <div class="aca-toast aca-toast-${type}" data-id="${toastId}">
            ${iconMap[type]}
            <p class="aca-toast-message">${message}</p>
            <button class="aca-toast-close" onclick="dismissToast(${toastId})">
                <svg class="aca-toast-close-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>
    `);

    $('#aca-toast-container').append(toast);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        dismissToast(toastId);
    }, 5000);
}

function dismissToast(toastId) {
    const $toast = $(`.aca-toast[data-id="${toastId}"]`);
    $toast.addClass('aca-toast-exit');
    setTimeout(() => {
        $toast.remove();
    }, 300);
}
</script>