<?php
/**
 * Calendar display template - React Prototype Design
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
                    <a href="<?php echo admin_url('admin.php?page=aca-calendar'); ?>" class="aca-nav-item active">
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
                    <h2 class="aca-page-title">Content Calendar</h2>
                    <p class="aca-page-subtitle">Schedule and manage your content publishing timeline.</p>
                </header>

                <div class="aca-coming-soon">
                    <div class="aca-coming-soon-icon">
                        <svg class="aca-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                    </div>
                    <h3 class="aca-coming-soon-title">Content Calendar Coming Soon</h3>
                    <p class="aca-coming-soon-description">
                        We're working on a beautiful calendar view to help you schedule and manage your content publishing timeline. 
                        This feature will be available in the next update.
                    </p>
                    <div class="aca-coming-soon-features">
                        <div class="aca-feature-item">
                            <svg class="aca-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                            </svg>
                            Visual calendar interface
                        </div>
                        <div class="aca-feature-item">
                            <svg class="aca-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                            </svg>
                            Drag & drop scheduling
                        </div>
                        <div class="aca-feature-item">
                            <svg class="aca-feature-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>
                            </svg>
                            Automated publishing
                        </div>
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

<style>
.aca-coming-soon {
    text-align: center;
    padding: 80px 20px;
    background: rgba(30, 41, 59, 0.3);
    border: 1px solid rgba(51, 65, 85, 0.6);
    border-radius: 12px;
    max-width: 600px;
    margin: 0 auto;
}

.aca-coming-soon-icon {
    margin: 0 auto 32px;
    width: 100px;
    height: 100px;
    background: rgba(37, 99, 235, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.aca-coming-soon-icon .aca-icon {
    width: 50px;
    height: 50px;
    color: #60a5fa;
}

.aca-coming-soon-title {
    font-size: 28px;
    font-weight: 600;
    color: #ffffff;
    margin: 0 0 16px 0;
}

.aca-coming-soon-description {
    color: #94a3b8;
    font-size: 16px;
    margin: 0 0 32px 0;
    line-height: 1.6;
}

.aca-coming-soon-features {
    display: flex;
    flex-direction: column;
    gap: 12px;
    align-items: center;
}

.aca-feature-item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #cbd5e1;
    font-size: 14px;
}

.aca-feature-icon {
    width: 16px;
    height: 16px;
    color: #4ade80;
}

@media (max-width: 768px) {
    .aca-coming-soon {
        padding: 60px 20px;
    }
    
    .aca-coming-soon-title {
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
</script>