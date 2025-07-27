<?php
/**
 * Style Guide display template - React Prototype Design
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
                    <a href="<?php echo admin_url('admin.php?page=aca-style-guide'); ?>" class="aca-nav-item active">
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
                    <h2 class="aca-page-title">Style Guide</h2>
                    <p class="aca-page-subtitle">Define your brand's voice and writing style for consistent content generation.</p>
                </header>

                <div class="aca-style-guide-container">
                    <!-- Analysis Section -->
                    <section class="aca-section">
                        <div class="aca-section-header">
                            <h3 class="aca-section-title">Content Analysis</h3>
                            <button id="analyze-style-btn" class="aca-analyze-button">
                                <svg class="aca-button-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                </svg>
                                <span id="analyze-text">Analyze My Writing Style</span>
                                <span id="analyze-loading" class="aca-loading-spinner hidden">
                                    <svg class="aca-spinner" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div class="aca-analysis-description">
                            <p>AI will analyze your recent posts to understand your unique writing style, tone, and formatting preferences.</p>
                        </div>
                    </section>

                    <?php if (!empty($style_guide) && !empty($style_guide['tone'])): ?>
                    <!-- Style Guide Display -->
                    <section class="aca-section">
                        <h3 class="aca-section-title">Current Style Guide</h3>
                        <div class="aca-style-guide-grid">
                            <div class="aca-style-card">
                                <h4 class="aca-style-card-title">Tone & Voice</h4>
                                <p class="aca-style-card-content"><?php echo esc_html($style_guide['tone']); ?></p>
                            </div>
                            
                            <div class="aca-style-card">
                                <h4 class="aca-style-card-title">Sentence Structure</h4>
                                <p class="aca-style-card-content"><?php echo esc_html($style_guide['sentenceStructure'] ?? 'Not analyzed'); ?></p>
                            </div>
                            
                            <div class="aca-style-card">
                                <h4 class="aca-style-card-title">Paragraph Length</h4>
                                <p class="aca-style-card-content"><?php echo esc_html($style_guide['paragraphLength'] ?? 'Not analyzed'); ?></p>
                            </div>
                            
                            <div class="aca-style-card">
                                <h4 class="aca-style-card-title">Formatting Style</h4>
                                <p class="aca-style-card-content"><?php echo esc_html($style_guide['formattingStyle'] ?? 'Not analyzed'); ?></p>
                            </div>
                        </div>
                        
                        <?php if (!empty($style_guide['customInstructions'])): ?>
                        <div class="aca-custom-instructions">
                            <h4 class="aca-style-card-title">Custom Instructions</h4>
                            <p class="aca-style-card-content"><?php echo esc_html($style_guide['customInstructions']); ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <div class="aca-style-meta">
                            <p class="aca-style-meta-text">
                                Last analyzed: <strong><?php echo esc_html(date('M j, Y g:i A', strtotime($style_guide['last_analyzed']))); ?></strong>
                            </p>
                        </div>
                    </section>
                    <?php else: ?>
                    <!-- Empty State -->
                    <section class="aca-section">
                        <div class="aca-empty-state">
                            <div class="aca-empty-icon">
                                <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                </svg>
                            </div>
                            <h3 class="aca-empty-title">No Style Guide Yet</h3>
                            <p class="aca-empty-description">
                                Click "Analyze My Writing Style" to automatically generate your style guide based on your existing content.
                            </p>
                        </div>
                    </section>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="aca-toast-container" class="aca-toast-container"></div>

    <!-- Sidebar Overlay -->
    <div id="aca-sidebar-overlay" class="aca-sidebar-overlay"></div>
</div>

<style>
/* Style Guide Specific Styles */
.aca-style-guide-container {
    display: flex;
    flex-direction: column;
    gap: 32px;
    max-width: 1200px;
}

.aca-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 16px;
}

.aca-analyze-button {
    background: #2563eb;
    color: #ffffff;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    font-size: 14px;
}

.aca-analyze-button:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
}

.aca-analyze-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.aca-button-icon {
    width: 20px;
    height: 20px;
}

.aca-analysis-description {
    background: rgba(30, 41, 59, 0.5);
    border: 1px solid rgba(51, 65, 85, 0.6);
    border-radius: 8px;
    padding: 16px;
    color: #94a3b8;
}

.aca-analysis-description p {
    margin: 0;
    line-height: 1.5;
}

.aca-style-guide-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 24px;
}

.aca-style-card {
    background: #1e293b;
    border: 1px solid rgba(51, 65, 85, 0.8);
    border-radius: 8px;
    padding: 20px;
    transition: all 0.2s ease;
}

.aca-style-card:hover {
    border-color: #475569;
    transform: translateY(-2px);
}

.aca-style-card-title {
    font-size: 16px;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 12px 0;
}

.aca-style-card-content {
    color: #cbd5e1;
    margin: 0;
    line-height: 1.6;
    font-size: 14px;
}

.aca-custom-instructions {
    background: #1e293b;
    border: 1px solid rgba(51, 65, 85, 0.8);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 24px;
}

.aca-style-meta {
    padding-top: 16px;
    border-top: 1px solid rgba(51, 65, 85, 0.6);
}

.aca-style-meta-text {
    color: #94a3b8;
    font-size: 14px;
    margin: 0;
}

.aca-empty-state {
    text-align: center;
    padding: 60px 20px;
    background: rgba(30, 41, 59, 0.3);
    border: 1px solid rgba(51, 65, 85, 0.6);
    border-radius: 12px;
}

.aca-empty-icon {
    margin: 0 auto 24px;
    width: 80px;
    height: 80px;
    background: rgba(37, 99, 235, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aca-empty-icon .aca-icon {
    width: 40px;
    height: 40px;
    color: #60a5fa;
}

.aca-empty-title {
    font-size: 24px;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 12px 0;
}

.aca-empty-description {
    color: #94a3b8;
    font-size: 16px;
    margin: 0;
    line-height: 1.5;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

@media (max-width: 768px) {
    .aca-section-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .aca-analyze-button {
        width: 100%;
        justify-content: center;
    }
    
    .aca-style-guide-grid {
        grid-template-columns: 1fr;
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

    // Analyze Style Guide
    $('#analyze-style-btn').on('click', function() {
        const $btn = $(this);
        const $loading = $('#analyze-loading');
        const $text = $('#analyze-text');
        
        $btn.prop('disabled', true);
        $loading.removeClass('hidden');
        $text.text('Analyzing...');
        
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
                $text.text('Analyze My Writing Style');
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