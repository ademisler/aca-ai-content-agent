<?php
/**
 * Provide a admin area view for the plugin - React Prototype Design
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
                    <a href="<?php echo admin_url('admin.php?page=aca-ai-content-agent'); ?>" class="aca-nav-item active">
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
                    <a href="<?php echo admin_url('admin.php?page=aca-published'); ?>" class="aca-nav-item">
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
                    <h2 class="aca-page-title">Dashboard</h2>
                    <p class="aca-page-subtitle">Welcome back! Here's a quick overview of your content pipeline.</p>
                </header>

                <?php if (empty($style_guide) || empty($style_guide['tone'])): ?>
                <!-- Get Started Banner -->
                <div class="aca-get-started-banner">
                    <div class="aca-banner-content">
                        <div class="aca-banner-icon">
                            <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>
                            </svg>
                        </div>
                        <div class="aca-banner-text">
                            <h4 class="aca-banner-title">Get Started with AI Content Agent</h4>
                            <p class="aca-banner-description">Create your Style Guide first to enable all features and generate on-brand content.</p>
                        </div>
                    </div>
                    <button id="create-style-guide-btn" class="aca-banner-button">
                        Create Style Guide
                    </button>
                </div>
                <?php endif; ?>

                <div class="aca-dashboard-grid">
                    <!-- Left Column -->
                    <div class="aca-dashboard-left">
                        <!-- Quick Actions -->
                        <section class="aca-section">
                            <h3 class="aca-section-title">Quick Actions</h3>
                            <div class="aca-actions-grid">
                                <button id="analyze-style-btn" class="aca-action-card">
                                    <div class="aca-action-icon">
                                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                        </svg>
                                    </div>
                                    <h3 class="aca-action-title">
                                        <span id="analyze-title"><?php echo !empty($style_guide) ? 'Update Style Guide' : 'Learn My Style'; ?></span>
                                        <span id="analyze-loading" class="aca-loading-spinner hidden">
                                            <svg class="aca-spinner" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </h3>
                                    <p class="aca-action-description">Analyze your content to define or refine your brand's voice.</p>
                                </button>

                                <button id="generate-ideas-btn" class="aca-action-card" <?php echo empty($style_guide) ? 'disabled' : ''; ?>>
                                    <div class="aca-action-icon">
                                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>
                                        </svg>
                                    </div>
                                    <h3 class="aca-action-title">
                                        <span id="ideas-title">Generate New Ideas</span>
                                        <span id="ideas-loading" class="aca-loading-spinner hidden">
                                            <svg class="aca-spinner" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                    </h3>
                                    <p class="aca-action-description">Create a fresh batch of content ideas based on your style.</p>
                                </button>
                            </div>
                        </section>

                        <!-- Content Pipeline -->
                        <section class="aca-section">
                            <h3 class="aca-section-title">Content Pipeline</h3>
                            <div class="aca-pipeline-container">
                                <div class="aca-pipeline-item">
                                    <div class="aca-pipeline-icon aca-pipeline-ideas">
                                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/>
                                        </svg>
                                    </div>
                                    <div class="aca-pipeline-content">
                                        <p class="aca-pipeline-title">Pending Ideas</p>
                                        <p class="aca-pipeline-count"><?php echo esc_html($stats['ideas']); ?> ideas waiting</p>
                                    </div>
                                    <a href="<?php echo admin_url('admin.php?page=aca-ideas'); ?>" class="aca-pipeline-button">View</a>
                                </div>

                                <div class="aca-pipeline-item">
                                    <div class="aca-pipeline-icon aca-pipeline-drafts">
                                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/>
                                        </svg>
                                    </div>
                                    <div class="aca-pipeline-content">
                                        <p class="aca-pipeline-title">Drafts</p>
                                        <p class="aca-pipeline-count"><?php echo esc_html($stats['drafts']); ?> drafts to review</p>
                                    </div>
                                    <a href="<?php echo admin_url('admin.php?page=aca-drafts'); ?>" class="aca-pipeline-button">View</a>
                                </div>

                                <div class="aca-pipeline-item">
                                    <div class="aca-pipeline-icon aca-pipeline-published">
                                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>
                                        </svg>
                                    </div>
                                    <div class="aca-pipeline-content">
                                        <p class="aca-pipeline-title">Published Posts</p>
                                        <p class="aca-pipeline-count"><?php echo esc_html($stats['published']); ?> posts are live</p>
                                    </div>
                                    <a href="<?php echo admin_url('admin.php?page=aca-published'); ?>" class="aca-pipeline-button">View</a>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Right Column -->
                    <div class="aca-dashboard-right">
                        <!-- Status -->
                        <section class="aca-section">
                            <h3 class="aca-section-title">Status</h3>
                            <div class="aca-status-container">
                                <div class="aca-status-item">
                                    <div class="aca-status-icon">
                                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                        </svg>
                                    </div>
                                    <div class="aca-status-content">
                                        <p class="aca-status-title">Style Guide</p>
                                        <?php if (!empty($style_guide) && !empty($style_guide['tone'])): ?>
                                            <p class="aca-status-ready">
                                                <svg class="aca-check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                                                </svg>
                                                Ready
                                            </p>
                                        <?php else: ?>
                                            <p class="aca-status-not-set">Not Set</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($style_guide['last_analyzed'])): ?>
                                <div class="aca-status-details">
                                    <p class="aca-status-label">Last Scanned:</p>
                                    <p class="aca-status-value"><?php echo esc_html(date('M j, Y g:i A', strtotime($style_guide['last_analyzed']))); ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <!-- Recent Activity -->
                        <section class="aca-section">
                            <h3 class="aca-section-title">Recent Activity</h3>
                            <div class="aca-activity-container">
                                <?php if (!empty($activity_logs)): ?>
                                    <?php foreach ($activity_logs as $log): ?>
                                    <div class="aca-activity-item">
                                        <div class="aca-activity-icon">
                                            <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="aca-activity-content">
                                            <p class="aca-activity-text"><?php echo esc_html($log->details); ?></p>
                                            <p class="aca-activity-time"><?php echo esc_html(human_time_diff(strtotime($log->created_at))); ?> ago</p>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="aca-activity-empty">No recent activity</p>
                                <?php endif; ?>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="aca-toast-container" class="aca-toast-container"></div>

    <!-- Sidebar Overlay -->
    <div id="aca-sidebar-overlay" class="aca-sidebar-overlay"></div>
</div>

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

    // Analyze Style Guide
    $('#analyze-style-btn, #create-style-guide-btn').on('click', function() {
        const $btn = $(this);
        const $loading = $('#analyze-loading');
        const $title = $('#analyze-title');
        
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
                    showToast('Style guide analyzed successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showToast('Network error occurred', 'error');
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
                    showToast(response.data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast('Error: ' + response.data, 'error');
                }
            },
            error: function() {
                showToast('Network error occurred', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
                $loading.addClass('hidden');
            }
        });
    });

    // Toast notification system
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

    // Make dismissToast global
    window.dismissToast = function(toastId) {
        const $toast = $(`.aca-toast[data-id="${toastId}"]`);
        $toast.addClass('aca-toast-exit');
        setTimeout(() => {
            $toast.remove();
        }, 300);
    };
});
</script>