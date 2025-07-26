/**
 * ACA - AI Content Agent Admin JavaScript
 * Modern, Professional & User-Friendly Interactions
 * Based on Prototype Design
 */

jQuery(function($) {
    'use strict';

    // ===== GLOBAL VARIABLES =====
    const ACA = {
        ajaxUrl: aca_ai_content_agent_admin_ajax.ajax_url,
        nonce: aca_ai_content_agent_admin_ajax.nonce,
        strings: aca_ai_content_agent_admin_ajax.strings || {},
        isProcessing: false,
        pendingIdeasCount: 5,
        totalDraftsCount: 12
    };

    // ===== UTILITY FUNCTIONS =====
    
    /**
     * Show notification
     */
    function showNotification(message, type = 'success', duration = 4000) {
        const container = $('#aca-notification-container');
        if (container.length === 0) {
            $('body').append('<div id="aca-notification-container"></div>');
        }
        
        const notifId = 'notif-' + Date.now();
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
        const notif = $(`
            <div class="aca-notification ${type}" id="${notifId}">
                <i class="bi ${icon}"></i>
                <div>${message}</div>
            </div>
        `);

        $('#aca-notification-container').append(notif);
        
        // Show notification
        setTimeout(() => notif.addClass('show'), 10);

        // Auto hide
        if (duration > 0) {
            setTimeout(() => {
                notif.removeClass('show');
                setTimeout(() => notif.remove(), 400);
            }, duration);
        }

        return notif;
    }

    /**
     * Update counters
     */
    function updateCounters() {
        $('#pending-ideas-count').text(ACA.pendingIdeasCount);
        $('#total-drafts-count').text(ACA.totalDraftsCount);
        $('#overview-pending-ideas').text(ACA.pendingIdeasCount);
        $('#overview-total-drafts').text(ACA.totalDraftsCount);
    }

    /**
     * Check idea list state
     */
    function checkIdeaListState() {
        const ideaList = $('#idea-list');
        const emptyState = $('#idea-empty-state');
        
        if (ideaList.children().length === 0) {
            emptyState.show();
        } else {
            emptyState.hide();
        }
    }

    /**
     * Show loading state for buttons
     */
    function showButtonLoading($button, loadingText = 'Processing...') {
        const originalText = $button.find('.button-text').text();
        $button.prop('disabled', true)
               .data('original-text', originalText);
        
        $button.find('.button-text').text(loadingText);
        $button.find('.button-icon').hide();
        $button.find('.button-loader').show();
    }

    /**
     * Hide loading state for buttons
     */
    function hideButtonLoading($button) {
        const originalText = $button.data('original-text');
        $button.prop('disabled', false);
        $button.find('.button-text').text(originalText);
        $button.find('.button-icon').show();
        $button.find('.button-loader').hide();
    }

    /**
     * Make AJAX request with error handling
     */
    function makeAjaxRequest(action, data = {}, successCallback = null, errorCallback = null) {
        if (ACA.isProcessing) {
            showNotification('Please wait, another operation is in progress...', 'error');
            return;
        }

        ACA.isProcessing = true;

        const requestData = {
            action: action,
            nonce: ACA.nonce,
            ...data
        };

        $.ajax({
            url: ACA.ajaxUrl,
            type: 'POST',
            data: requestData,
            success: function(response) {
                ACA.isProcessing = false;
                
                if (response.success) {
                    if (successCallback) {
                        successCallback(response.data);
                    }
                } else {
                    const errorMessage = response.data?.message || 'An error occurred.';
                    showNotification(errorMessage, 'error');
                    if (errorCallback) {
                        errorCallback(response.data);
                    }
                }
            },
            error: function(xhr, status, error) {
                ACA.isProcessing = false;
                const errorMessage = 'Server error: ' + error;
                showNotification(errorMessage, 'error');
                if (errorCallback) {
                    errorCallback({ message: errorMessage });
                }
            }
        });
    }

    /**
     * Handle AJAX errors
     */
    function handleAjaxError(response, error) {
        console.error('AJAX Error:', response, error);
        
        let errorMessage;
        if (response && response.data && response.data.message) {
            errorMessage = response.data.message;
        } else if (error) {
            errorMessage = 'Server error: ' + error;
        } else {
            errorMessage = 'An error occurred.';
        }
        
        showNotification(errorMessage, 'error');
    }

    // ===== TAB NAVIGATION =====
    function initTabNavigation() {
        $('.aca-nav-tab').on('click', function() {
            const $tab = $(this);
            const targetTab = $tab.data('tab');
            
            // Update active tab
            $('.aca-nav-tab').removeClass('aca-nav-tab-active');
            $tab.addClass('aca-nav-tab-active');
            
            // Show target content
            $('.aca-tab-content').removeClass('active');
            $(`#${targetTab}-content`).addClass('active');
        });
    }

    // ===== DASHBOARD ACTIONS =====
    function initDashboardActions() {
        // Generate ideas button
        $('#generate-ideas-btn, #generate-ideas-btn-empty').on('click', function() {
            const $button = $(this);
            handleGenerateIdeas($button);
        });

        // Update style guide button
        $('#update-style-guide-btn').on('click', function() {
            const $button = $(this);
            handleUpdateStyleGuide($button);
        });

        // Cluster planner
        $('#generate-cluster-btn').on('click', function() {
            const $button = $(this);
            handleGenerateCluster($button);
        });

        // Idea stream actions (event delegation)
        $('#idea-list').on('click', function(e) {
            const $target = $(e.target).closest('button');
            if (!$target.length) return;

            const $ideaItem = $target.closest('li');
            const ideaId = $ideaItem.data('id');
            
            if ($target.hasClass('write-draft-btn')) {
                handleWriteDraft($target, $ideaItem);
            }

            if ($target.hasClass('reject-idea-btn')) {
                handleRejectIdea($target, $ideaItem);
            }
        });
    }

    /**
     * Handle generate ideas
     */
    function handleGenerateIdeas($button) {
        showButtonLoading($button, 'Generating...');

        // Simulate API call
        setTimeout(() => {
            const newIdeas = [
                { 
                    id: Date.now() + 1, 
                    title: 'The Place of Video Marketing in Content Strategy', 
                    keywords: 'video marketing, content strategy'
                },
                { 
                    id: Date.now() + 2, 
                    title: 'Enhancements for User Experience (UX) for E-Commerce Sites', 
                    keywords: 'e-commerce, ux, user experience'
                }
            ];
            
            newIdeas.forEach(idea => {
                const $li = $(`
                    <li data-id="${idea.id}">
                        <div class="aca-idea-content">
                            <div class="aca-idea-title">${idea.title}</div>
                            <div class="aca-idea-meta">
                                <i class="bi bi-clock"></i> now &nbsp;&nbsp; 
                                <i class="bi bi-tags"></i> ${idea.keywords}
                            </div>
                        </div>
                        <div class="aca-idea-actions">
                            <button class="aca-action-button write-draft-btn">
                                <i class="bi bi-pencil-square"></i> Write Draft
                            </button>
                            <button class="aca-action-button secondary reject-idea-btn">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                            <button class="aca-feedback-btn" title="Good idea">👍</button>
                            <button class="aca-feedback-btn" title="Bad idea">👎</button>
                        </div>
                    </li>
                `);
                $('#idea-list').prepend($li);
            });
            
            ACA.pendingIdeasCount += newIdeas.length;
            updateCounters();
            checkIdeaListState();
            showNotification(`${newIdeas.length} new ideas generated successfully!`);

            hideButtonLoading($button);
        }, 2000);
    }

    /**
     * Handle update style guide
     */
    function handleUpdateStyleGuide($button) {
        showButtonLoading($button, 'Updating...');

        setTimeout(() => {
            showNotification('Style guide updated successfully.');
            hideButtonLoading($button);
        }, 1500);
    }

    /**
     * Handle generate cluster
     */
    function handleGenerateCluster($button) {
        const topic = $('#cluster-topic-input').val().trim();
        
        if (!topic) {
            showNotification('Please enter a main topic.', 'error');
            return;
        }

        showButtonLoading($button, 'Generating cluster...');

        setTimeout(() => {
            showNotification(`Content cluster generated for "${topic}" topic.`);
            $('#cluster-topic-input').val('');
            hideButtonLoading($button);
        }, 2500);
    }

    /**
     * Handle write draft
     */
    function handleWriteDraft($button, $ideaItem) {
        $button.html('<span class="aca-loading-spinner"></span> Writing...');
        $button.prop('disabled', true);

        setTimeout(() => {
            $ideaItem.css('transition', 'opacity 0.5s ease').css('opacity', '0');
            
            setTimeout(() => {
                $ideaItem.remove();
                ACA.pendingIdeasCount--;
                ACA.totalDraftsCount++;
                updateCounters();
                checkIdeaListState();
                
                const ideaTitle = $ideaItem.find('.aca-idea-title').text();
                showNotification(`Draft created for "${ideaTitle}" title.`);
            }, 500);
        }, 2500);
    }

    /**
     * Handle reject idea
     */
    function handleRejectIdea($button, $ideaItem) {
        $ideaItem.css('transition', 'opacity 0.5s ease').css('opacity', '0');
        
        setTimeout(() => {
            $ideaItem.remove();
            ACA.pendingIdeasCount--;
            updateCounters();
            checkIdeaListState();
            showNotification('Idea rejected.', 'error');
        }, 500);
    }

    // ===== SETTINGS PAGE =====
    function initSettingsPage() {
        // Test connection button
        $('#test-connection-btn').on('click', function() {
            const $statusEl = $('#connection-test-status');
            $statusEl.html('<span class="aca-status-indicator loading"><span class="aca-loading-spinner"></span> Testing connection...</span>');
            
            setTimeout(() => {
                const success = Math.random() > 0.2; // 80% success rate
                if (success) {
                    $statusEl.html('<span class="aca-status-indicator success"><i class="bi bi-check-circle-fill"></i> Connection Successful</span>');
                } else {
                    $statusEl.html('<span class="aca-status-indicator error"><i class="bi bi-x-circle-fill"></i> Connection Error</span>');
                }
            }, 1500);
        });

        // Settings form
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();
            showNotification('Settings saved successfully.');
        });
    }

    // ===== LICENSE PAGE =====
    function initLicensePage() {
        $('#validate-license-btn').on('click', function() {
            const $keyInput = $('#license-key-input');
            const $button = $(this);
            
            if (!$keyInput.val().trim()) {
                showNotification('Please enter a license key.', 'error');
                return;
            }

            $button.text('Validating...');
            $button.prop('disabled', true);

            setTimeout(() => {
                const $statusBox = $('#license-status-box');
                const $statusText = $('#license-status-text');
                
                $statusBox.removeClass('free').addClass('pro');
                $statusText.text('PRO LICENSE ACTIVE');
                
                $('#license-form-container').hide();
                showNotification('License key validated successfully. Pro features activated!');
            }, 2000);
        });
    }

    // ===== IDEAS PAGE =====
    function initIdeasPage() {
        // Ideas table actions
        $('.wp-list-table').on('click', '.aca-action-button', function() {
            const $button = $(this);
            const $row = $button.closest('tr');
            const title = $row.find('td:first strong').text();
            
            if (!$button.prop('disabled')) {
                $button.prop('disabled', true).text('Processed');
                showNotification(`"${title}" content processed.`);
            }
        });
    }

    // ===== CONNECTION TEST =====
    $(document).on('click', '#test-connection-btn', function() {
        const $button = $(this);
        const $statusEl = $('#connection-test-status');
        
        showButtonLoading($button, 'Testing...');
        $statusEl.html('<span class="aca-status-indicator loading"><i class="bi bi-arrow-repeat"></i> Testing connection...</span>');
        
        $.ajax({
            url: ACA.ajaxUrl,
            type: 'POST',
            data: {
                action: 'aca_ai_content_agent_test_connection',
                nonce: ACA.nonce
            },
            success: function(response) {
                if (response.success) {
                    $statusEl.html('<span class="aca-status-indicator success"><i class="bi bi-check-circle-fill"></i> Connection Successful</span>');
                } else {
                    $statusEl.html('<span class="aca-status-indicator error"><i class="bi bi-x-circle-fill"></i> Connection Error</span>');
                }
            },
            error: function(xhr, status, error) {
                handleAjaxError(null, error);
                $statusEl.html('<span class="aca-status-indicator error"><i class="bi bi-x-circle-fill"></i> Connection Failed</span>');
            },
            complete: function() {
                hideButtonLoading($button);
            }
        });
    });

    // ===== INITIALIZATION =====
    function init() {
        // Initialize all components
        initTabNavigation();
        initDashboardActions();
        initSettingsPage();
        initLicensePage();
        initIdeasPage();

        // Update initial state
        updateCounters();
        checkIdeaListState();

        // Add Bootstrap Icons if not already loaded
        if (!$('link[href*="bootstrap-icons"]').length) {
            $('head').append('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">');
        }

        // Add Inter font if not already loaded
        if (!$('link[href*="Inter"]').length) {
            $('head').append('<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">');
        }
    }

    // ===== DOCUMENT READY =====
    $(document).ready(function() {
        init();
    });

    // ===== EXPOSE FUNCTIONS FOR EXTERNAL USE =====
    window.ACA_Admin = {
        showNotification,
        updateCounters,
        makeAjaxRequest
    };

});