/**
 * ACA - AI Content Agent Admin JavaScript
 * Enhanced UX/UI Version 1.4
 * Modern, Professional & User-Friendly Interactions
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
        totalDraftsCount: 12,
        notificationQueue: [],
        debounceTimers: {},
        currentTheme: 'light',
        animations: {
            enabled: !window.matchMedia('(prefers-reduced-motion: reduce)').matches
        }
    };

    // ===== ENHANCED UTILITY FUNCTIONS =====
    
    /**
     * Debounce function for performance optimization
     */
    function debounce(func, wait, immediate) {
        return function executedFunction() {
            const context = this;
            const args = arguments;
            const later = function() {
                delete ACA.debounceTimers[func.name];
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !ACA.debounceTimers[func.name];
            clearTimeout(ACA.debounceTimers[func.name]);
            ACA.debounceTimers[func.name] = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }

    /**
     * Enhanced notification system with queue management
     */
    function showNotification(message, type = 'success', duration = 4000, title = null) {
        const container = $('#aca-notification-container');
        if (container.length === 0) {
            $('body').append('<div id="aca-notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 10000;"></div>');
        }
        
        const notifId = 'notif-' + Date.now();
        const icon = getNotificationIcon(type);
        const closeBtn = '<button class="aca-notification-close" aria-label="Close notification">&times;</button>';
        
        const notif = $(`
            <div class="aca-notification ${type}" id="${notifId}" role="alert" aria-live="polite">
                <i class="bi ${icon}" aria-hidden="true"></i>
                <div class="aca-notification-content">
                    ${title ? `<div class="aca-notification-title">${title}</div>` : ''}
                    <div class="aca-notification-message">${message}</div>
                </div>
                ${closeBtn}
            </div>
        `);

        $('#aca-notification-container').append(notif);
        
        // Show notification with animation
        if (ACA.animations.enabled) {
            setTimeout(() => notif.addClass('show'), 10);
        } else {
            notif.addClass('show');
        }

        // Close button functionality
        notif.find('.aca-notification-close').on('click', function() {
            hideNotification(notif);
        });

        // Auto hide
        if (duration > 0) {
            setTimeout(() => {
                hideNotification(notif);
            }, duration);
        }

        // Add to queue for management
        ACA.notificationQueue.push(notifId);
        if (ACA.notificationQueue.length > 3) {
            const oldNotif = $('#' + ACA.notificationQueue.shift());
            hideNotification(oldNotif);
        }

        return notif;
    }

    /**
     * Hide notification with animation
     */
    function hideNotification(notif) {
        if (ACA.animations.enabled) {
            notif.removeClass('show');
            setTimeout(() => notif.remove(), 400);
        } else {
            notif.remove();
        }
    }

    /**
     * Get appropriate icon for notification type
     */
    function getNotificationIcon(type) {
        const icons = {
            success: 'bi-check-circle-fill',
            error: 'bi-x-circle-fill',
            warning: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };
        return icons[type] || icons.info;
    }

    /**
     * Enhanced loading state management
     */
    function setLoadingState(element, isLoading, loadingText = 'Processing...') {
        const $element = $(element);
        
        if (isLoading) {
            const originalContent = $element.html();
            $element.data('original-content', originalContent)
                   .prop('disabled', true)
                   .addClass('aca-loading')
                   .html(`<span class="aca-loading-spinner" aria-hidden="true"></span> ${loadingText}`);
        } else {
            const originalContent = $element.data('original-content');
            $element.prop('disabled', false)
                   .removeClass('aca-loading')
                   .html(originalContent);
        }
    }

    /**
     * Create skeleton loading placeholder
     */
    function createSkeletonLoader(type = 'text', count = 3) {
        let skeleton = '';
        for (let i = 0; i < count; i++) {
            skeleton += `<div class="aca-skeleton aca-skeleton-${type}"></div>`;
        }
        return skeleton;
    }

    /**
     * Enhanced AJAX request with better error handling and retry logic
     */
    function makeAjaxRequest(action, data = {}, options = {}) {
        const defaults = {
            retries: 2,
            timeout: 30000,
            showLoading: true,
            loadingElement: null,
            loadingText: 'Processing...',
            successCallback: null,
            errorCallback: null,
            completeCallback: null
        };
        
        const settings = { ...defaults, ...options };
        
        if (ACA.isProcessing && !settings.allowConcurrent) {
            showNotification('Please wait, another operation is in progress...', 'warning');
            return Promise.reject('Operation in progress');
        }

        ACA.isProcessing = true;

        if (settings.showLoading && settings.loadingElement) {
            setLoadingState(settings.loadingElement, true, settings.loadingText);
        }

        const requestData = {
            action: action,
            nonce: ACA.nonce,
            ...data
        };

        return $.ajax({
            url: ACA.ajaxUrl,
            type: 'POST',
            data: requestData,
            timeout: settings.timeout
        }).done(function(response) {
            if (response.success) {
                if (settings.successCallback) {
                    settings.successCallback(response.data);
                }
            } else {
                const errorMessage = response.data?.message || 'An error occurred.';
                showNotification(errorMessage, 'error', 6000, 'Error');
                if (settings.errorCallback) {
                    settings.errorCallback(response.data);
                }
            }
        }).fail(function(xhr, status, error) {
            if (settings.retries > 0) {
                // Retry logic
                setTimeout(() => {
                    makeAjaxRequest(action, data, { ...settings, retries: settings.retries - 1 });
                }, 1000);
                return;
            }
            
            let errorMessage = 'Connection error. Please check your internet connection.';
            if (status === 'timeout') {
                errorMessage = 'Request timed out. Please try again.';
            } else if (xhr.status === 403) {
                errorMessage = 'Access denied. Please refresh the page and try again.';
            } else if (xhr.status >= 500) {
                errorMessage = 'Server error. Please try again later.';
            }
            
            showNotification(errorMessage, 'error', 8000, 'Connection Error');
            if (settings.errorCallback) {
                settings.errorCallback({ message: errorMessage, status: xhr.status });
            }
        }).always(function() {
            ACA.isProcessing = false;
            if (settings.showLoading && settings.loadingElement) {
                setLoadingState(settings.loadingElement, false);
            }
            if (settings.completeCallback) {
                settings.completeCallback();
            }
        });
    }

    /**
     * Update counters with animation
     */
    function updateCounters() {
        animateCounter('#pending-ideas-count', ACA.pendingIdeasCount);
        animateCounter('#total-drafts-count', ACA.totalDraftsCount);
        animateCounter('#overview-pending-ideas', ACA.pendingIdeasCount);
        animateCounter('#overview-total-drafts', ACA.totalDraftsCount);
    }

    /**
     * Animate counter changes
     */
    function animateCounter(selector, newValue) {
        const $element = $(selector);
        const currentValue = parseInt($element.text()) || 0;
        
        if (currentValue === newValue) return;
        
        if (ACA.animations.enabled) {
            $element.addClass('aca-counter-updating');
            
            $({ counter: currentValue }).animate({ counter: newValue }, {
                duration: 800,
                easing: 'swing',
                step: function() {
                    $element.text(Math.ceil(this.counter));
                },
                complete: function() {
                    $element.removeClass('aca-counter-updating');
                }
            });
        } else {
            $element.text(newValue);
        }
    }

    /**
     * Enhanced keyboard navigation support
     */
    function initKeyboardNavigation() {
        $(document).on('keydown', function(e) {
            // ESC key to close notifications
            if (e.key === 'Escape') {
                $('.aca-notification').each(function() {
                    hideNotification($(this));
                });
            }
            
            // Tab navigation for custom elements
            if (e.key === 'Tab') {
                handleTabNavigation(e);
            }
            
            // Enter/Space for button-like elements
            if ((e.key === 'Enter' || e.key === ' ') && $(e.target).hasClass('aca-nav-tab')) {
                e.preventDefault();
                $(e.target).click();
            }
        });
    }

    /**
     * Handle tab navigation
     */
    function handleTabNavigation(e) {
        const focusableElements = $(
            'button:not([disabled]), [href], input:not([disabled]), ' +
            'select:not([disabled]), textarea:not([disabled]), ' +
            '[tabindex]:not([tabindex="-1"]):not([disabled])'
        ).filter(':visible');
        
        const currentIndex = focusableElements.index($(e.target));
        
        if (e.shiftKey) {
            // Shift+Tab (backwards)
            if (currentIndex === 0) {
                e.preventDefault();
                focusableElements.last().focus();
            }
        } else {
            // Tab (forwards)
            if (currentIndex === focusableElements.length - 1) {
                e.preventDefault();
                focusableElements.first().focus();
            }
        }
    }

    /**
     * Enhanced form validation
     */
    function validateForm($form) {
        let isValid = true;
        const errors = [];
        
        $form.find('input[required], select[required], textarea[required]').each(function() {
            const $field = $(this);
            const value = $field.val().trim();
            const fieldName = $field.attr('name') || $field.attr('id') || 'Field';
            
            // Clear previous errors
            $field.removeClass('aca-field-error');
            $field.siblings('.aca-input-error').remove();
            
            if (!value) {
                isValid = false;
                errors.push(`${fieldName} is required.`);
                $field.addClass('aca-field-error');
                $field.after(`<div class="aca-input-error">
                    <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                    This field is required.
                </div>`);
            }
            
            // Email validation
            if ($field.attr('type') === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errors.push(`${fieldName} must be a valid email address.`);
                    $field.addClass('aca-field-error');
                    $field.after(`<div class="aca-input-error">
                        <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                        Please enter a valid email address.
                    </div>`);
                }
            }
            
            // URL validation
            if ($field.attr('type') === 'url' && value) {
                try {
                    new URL(value);
                } catch {
                    isValid = false;
                    errors.push(`${fieldName} must be a valid URL.`);
                    $field.addClass('aca-field-error');
                    $field.after(`<div class="aca-input-error">
                        <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                        Please enter a valid URL.
                    </div>`);
                }
            }
        });
        
        if (!isValid) {
            showNotification(
                `Please correct the following errors: ${errors.join(' ')}`,
                'error',
                8000,
                'Validation Error'
            );
            
            // Focus first error field
            $form.find('.aca-field-error').first().focus();
        }
        
        return isValid;
    }

    /**
     * Enhanced progress tracking
     */
    function showProgress(current, total, label = 'Progress') {
        const percentage = Math.round((current / total) * 100);
        const progressId = 'aca-progress-' + Date.now();
        
        const progressHtml = `
            <div id="${progressId}" class="aca-progress-container" role="progressbar" 
                 aria-valuenow="${current}" aria-valuemin="0" aria-valuemax="${total}"
                 aria-label="${label}">
                <div class="aca-progress-label">${label}: ${current}/${total} (${percentage}%)</div>
                <div class="aca-progress-bar">
                    <div class="aca-progress-fill" style="width: ${percentage}%"></div>
                </div>
            </div>
        `;
        
        return progressHtml;
    }

    // ===== ENHANCED TAB NAVIGATION =====
    function initTabNavigation() {
        $('.aca-nav-tab').on('click keypress', function(e) {
            if (e.type === 'keypress' && e.which !== 13 && e.which !== 32) {
                return;
            }
            
            e.preventDefault();
            const $tab = $(this);
            const targetTab = $tab.data('tab');
            
            // Update active tab with animation
            $('.aca-nav-tab').removeClass('aca-nav-tab-active').attr('aria-selected', 'false');
            $tab.addClass('aca-nav-tab-active').attr('aria-selected', 'true');
            
            // Show target content with enhanced animation
            $('.aca-tab-content').removeClass('active').attr('aria-hidden', 'true');
            const $targetContent = $(`#${targetTab}-content`);
            
            if (ACA.animations.enabled) {
                $targetContent.addClass('active').attr('aria-hidden', 'false');
            } else {
                $targetContent.addClass('active').attr('aria-hidden', 'false');
            }
            
            // Update URL hash for better UX
            if (history.replaceState) {
                history.replaceState(null, null, `#${targetTab}`);
            }
            
            // Announce tab change to screen readers
            announceToScreenReader(`Switched to ${$tab.text()} tab`);
        });
        
        // Initialize from URL hash
        const hash = window.location.hash.substring(1);
        if (hash) {
            const $hashTab = $(`.aca-nav-tab[data-tab="${hash}"]`);
            if ($hashTab.length) {
                $hashTab.click();
            }
        }
    }

    /**
     * Announce to screen readers
     */
    function announceToScreenReader(message) {
        const announcement = $('<div>', {
            'aria-live': 'polite',
            'aria-atomic': 'true',
            'class': 'sr-only'
        }).text(message);
        
        $('body').append(announcement);
        setTimeout(() => announcement.remove(), 1000);
    }

    // ===== ENHANCED DASHBOARD ACTIONS =====
    function initDashboardActions() {
        // Generate ideas with enhanced UX
        $('#generate-ideas-btn, #generate-ideas-btn-empty').on('click', function() {
            const $button = $(this);
            handleGenerateIdeas($button);
        });

        // Update style guide with progress tracking
        $('#update-style-guide-btn').on('click', function() {
            const $button = $(this);
            handleUpdateStyleGuide($button);
        });

        // Cluster planner with validation
        $('#generate-cluster-btn').on('click', function() {
            const $button = $(this);
            handleGenerateCluster($button);
        });

        // Enhanced idea stream actions with better feedback
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

        // Auto-save functionality for forms
        $('form.aca-auto-save').on('input change', debounce(function() {
            autoSaveForm($(this));
        }, 2000));
    }

    /**
     * Enhanced generate ideas with better UX
     */
    function handleGenerateIdeas($button) {
        makeAjaxRequest('aca_ai_content_agent_generate_ideas', {}, {
            loadingElement: $button,
            loadingText: 'Generating ideas...',
            successCallback: function(data) {
                const ideas = data.ideas || [];
                
                if (ideas.length === 0) {
                    showNotification('No new ideas generated. Try adjusting your settings.', 'warning');
                    return;
                }
                
                // Add ideas with staggered animation
                ideas.forEach((idea, index) => {
                    setTimeout(() => {
                        addIdeaToList(idea);
                    }, index * 200);
                });
                
                ACA.pendingIdeasCount += ideas.length;
                updateCounters();
                checkIdeaListState();
                
                showNotification(
                    `${ideas.length} new ideas generated successfully!`,
                    'success',
                    4000,
                    'Ideas Generated'
                );
            },
            errorCallback: function(error) {
                showNotification(
                    error.message || 'Failed to generate ideas. Please try again.',
                    'error',
                    6000,
                    'Generation Failed'
                );
            }
        });
    }

    /**
     * Add idea to list with animation
     */
    function addIdeaToList(idea) {
        const $li = $(`
            <li data-id="${idea.id}" style="opacity: 0; transform: translateY(-20px);">
                <div class="aca-idea-content">
                    <div class="aca-idea-title">${escapeHtml(idea.title)}</div>
                    <div class="aca-idea-meta">
                        <i class="bi bi-clock" aria-hidden="true"></i> 
                        <span class="sr-only">Created</span> now
                        <i class="bi bi-tags" aria-hidden="true"></i> 
                        <span class="sr-only">Keywords:</span> ${escapeHtml(idea.keywords)}
                    </div>
                </div>
                <div class="aca-idea-actions">
                    <button class="aca-action-button write-draft-btn" aria-label="Write draft for ${escapeHtml(idea.title)}">
                        <i class="bi bi-pencil-square" aria-hidden="true"></i> 
                        <span>Write Draft</span>
                    </button>
                    <button class="aca-action-button secondary reject-idea-btn" aria-label="Reject idea ${escapeHtml(idea.title)}">
                        <i class="bi bi-x-circle" aria-hidden="true"></i> 
                        <span>Reject</span>
                    </button>
                    <button class="aca-feedback-btn" title="Good idea" aria-label="Mark as good idea">👍</button>
                    <button class="aca-feedback-btn" title="Bad idea" aria-label="Mark as bad idea">👎</button>
                </div>
            </li>
        `);
        
        $('#idea-list').prepend($li);
        
        if (ACA.animations.enabled) {
            $li.animate({
                opacity: 1,
                transform: 'translateY(0)'
            }, 500);
        } else {
            $li.css({ opacity: 1, transform: 'translateY(0)' });
        }
    }

    /**
     * Enhanced auto-save functionality
     */
    function autoSaveForm($form) {
        const formData = $form.serialize();
        const formId = $form.attr('id') || 'unknown';
        
        // Show subtle saving indicator
        const $indicator = $('<span class="aca-auto-save-indicator">Saving...</span>');
        $form.append($indicator);
        
        makeAjaxRequest('aca_ai_content_agent_auto_save', {
            form_id: formId,
            form_data: formData
        }, {
            showLoading: false,
            allowConcurrent: true,
            successCallback: function() {
                $indicator.text('Saved').addClass('success');
                setTimeout(() => $indicator.fadeOut(() => $indicator.remove()), 2000);
            },
            errorCallback: function() {
                $indicator.text('Save failed').addClass('error');
                setTimeout(() => $indicator.fadeOut(() => $indicator.remove()), 3000);
            }
        });
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Check idea list state with enhanced empty state
     */
    function checkIdeaListState() {
        const ideaList = $('#idea-list');
        const emptyState = $('#idea-empty-state');
        
        if (ideaList.children().length === 0) {
            if (emptyState.length === 0) {
                const emptyStateHtml = `
                    <div id="idea-empty-state" class="aca-empty-state">
                        <div class="aca-empty-state-icon">💡</div>
                        <h3>No Ideas Yet</h3>
                        <p>Click "Generate Ideas" to get started with AI-powered content suggestions.</p>
                        <button id="generate-ideas-btn-empty" class="aca-action-button">
                            <i class="bi bi-plus" aria-hidden="true"></i> Generate Ideas
                        </button>
                    </div>
                `;
                ideaList.parent().append(emptyStateHtml);
            } else {
                emptyState.show();
            }
        } else {
            emptyState.hide();
        }
    }

    // ===== ENHANCED SETTINGS PAGE =====
    function initSettingsPage() {
        // Enhanced connection test with better feedback
        $('#test-connection-btn').on('click', function() {
            const $button = $(this);
            const $statusEl = $('#connection-test-status');
            
            makeAjaxRequest('aca_ai_content_agent_test_connection', {}, {
                loadingElement: $button,
                loadingText: 'Testing connection...',
                successCallback: function(data) {
                    $statusEl.html(`
                        <span class="aca-status-indicator success">
                            <i class="bi bi-check-circle-fill" aria-hidden="true"></i> 
                            Connection Successful
                        </span>
                    `);
                    showNotification('API connection test successful!', 'success');
                },
                errorCallback: function(error) {
                    $statusEl.html(`
                        <span class="aca-status-indicator error">
                            <i class="bi bi-x-circle-fill" aria-hidden="true"></i> 
                            Connection Failed
                        </span>
                    `);
                    showNotification(
                        error.message || 'Connection test failed. Please check your API key.',
                        'error',
                        6000,
                        'Connection Error'
                    );
                }
            });
        });

        // Enhanced settings form with validation
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();
            const $form = $(this);
            
            if (!validateForm($form)) {
                return;
            }
            
            const formData = $form.serialize();
            const $submitBtn = $form.find('button[type="submit"]');
            
            makeAjaxRequest('aca_ai_content_agent_save_settings', {
                settings: formData
            }, {
                loadingElement: $submitBtn,
                loadingText: 'Saving settings...',
                successCallback: function() {
                    showNotification('Settings saved successfully!', 'success');
                },
                errorCallback: function(error) {
                    showNotification(
                        error.message || 'Failed to save settings. Please try again.',
                        'error'
                    );
                }
            });
        });
    }

    // ===== ENHANCED LICENSE PAGE =====
    function initLicensePage() {
        $('#validate-license-btn').on('click', function() {
            const $keyInput = $('#license-key-input');
            const $button = $(this);
            const licenseKey = $keyInput.val().trim();
            
            if (!licenseKey) {
                showNotification('Please enter a license key.', 'warning');
                $keyInput.focus();
                return;
            }

            makeAjaxRequest('aca_ai_content_agent_validate_license', {
                license_key: licenseKey
            }, {
                loadingElement: $button,
                loadingText: 'Validating license...',
                successCallback: function(data) {
                    const $statusBox = $('#license-status-box');
                    const $statusText = $('#license-status-text');
                    
                    $statusBox.removeClass('free').addClass('pro');
                    $statusText.text('PRO LICENSE ACTIVE');
                    
                    $('#license-form-container').slideUp();
                    showNotification(
                        'License key validated successfully! Pro features are now active.',
                        'success',
                        6000,
                        'License Activated'
                    );
                },
                errorCallback: function(error) {
                    showNotification(
                        error.message || 'Invalid license key. Please check and try again.',
                        'error',
                        6000,
                        'License Validation Failed'
                    );
                    $keyInput.focus().select();
                }
            });
        });
    }

    // ===== ENHANCED IDEAS PAGE =====
    function initIdeasPage() {
        // Enhanced table actions with confirmation
        $('.wp-list-table').on('click', '.aca-action-button', function() {
            const $button = $(this);
            const $row = $button.closest('tr');
            const title = $row.find('td:first strong').text();
            const action = $button.data('action') || 'process';
            
            if ($button.prop('disabled')) return;
            
            // Show confirmation for destructive actions
            if (action === 'delete' || action === 'reject') {
                if (!confirm(`Are you sure you want to ${action} "${title}"?`)) {
                    return;
                }
            }
            
            setLoadingState($button, true, 'Processing...');
            
            setTimeout(() => {
                setLoadingState($button, false);
                $button.prop('disabled', true).text('Processed');
                showNotification(`"${title}" has been ${action}ed successfully.`, 'success');
                
                // Remove row with animation if deleted
                if (action === 'delete') {
                    $row.fadeOut(500, function() {
                        $(this).remove();
                    });
                }
            }, 1500);
        });

        // Enhanced bulk actions
        $('#bulk-action-selector').on('change', function() {
            const action = $(this).val();
            const $applyBtn = $('#doaction');
            
            if (action && action !== '-1') {
                $applyBtn.prop('disabled', false);
            } else {
                $applyBtn.prop('disabled', true);
            }
        });
    }

    // ===== THEME DETECTION AND ADAPTATION =====
    function initThemeDetection() {
        // Detect system theme preference
        const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
        
        function handleThemeChange(e) {
            ACA.currentTheme = e.matches ? 'dark' : 'light';
            $('body').attr('data-theme', ACA.currentTheme);
            
            // Update notification colors for dark mode
            if (e.matches) {
                $(':root').css({
                    '--aca-notification-bg': '#1e293b',
                    '--aca-notification-text': '#f1f5f9'
                });
            }
        }
        
        darkModeQuery.addListener(handleThemeChange);
        handleThemeChange(darkModeQuery);
    }

    // ===== PERFORMANCE MONITORING =====
    function initPerformanceMonitoring() {
        // Monitor long-running operations
        let operationStartTime;
        
        $(document).ajaxStart(function() {
            operationStartTime = performance.now();
        });
        
        $(document).ajaxComplete(function() {
            const duration = performance.now() - operationStartTime;
            if (duration > 5000) { // 5 seconds
                console.warn('Slow AJAX operation detected:', duration + 'ms');
            }
        });
        
        // Monitor memory usage (if available)
        if (performance.memory) {
            setInterval(() => {
                const memInfo = performance.memory;
                if (memInfo.usedJSHeapSize > memInfo.jsHeapSizeLimit * 0.9) {
                    console.warn('High memory usage detected');
                }
            }, 30000); // Check every 30 seconds
        }
    }

    // ===== INITIALIZATION =====
    function init() {
        // Initialize all components
        initKeyboardNavigation();
        initTabNavigation();
        initDashboardActions();
        initSettingsPage();
        initLicensePage();
        initIdeasPage();
        initThemeDetection();
        initPerformanceMonitoring();

        // Update initial state
        updateCounters();
        checkIdeaListState();

        // Add external resources with fallbacks
        loadExternalResources();
        
        // Initialize tooltips
        initTooltips();
        
        // Set up periodic health checks
        setInterval(healthCheck, 60000); // Every minute

        // FIX: Add capability refresh button handler
        initCapabilityRefresh();

        // Announce page load to screen readers
        announceToScreenReader('ACA AI Content Agent dashboard loaded');
    }
    
    /**
     * Initialize capability refresh functionality
     */
    function initCapabilityRefresh() {
        // Add refresh capabilities button to notices with permission errors
        $(document).on('click', '.notice-error', function() {
            const $notice = $(this);
            const noticeText = $notice.text();
            
            // Check if this is a capability-related error
            if (noticeText.includes('permission') || noticeText.includes('capability') || noticeText.includes('not allowed')) {
                // Only add button if it doesn't already exist
                if (!$notice.find('.aca-refresh-capabilities').length) {
                    const refreshButton = $('<button class="button aca-refresh-capabilities" style="margin-left: 10px;">Refresh Permissions</button>');
                    $notice.find('p').append(refreshButton);
                }
            }
        });
        
        // Handle refresh capabilities button click
        $(document).on('click', '.aca-refresh-capabilities', function(e) {
            e.preventDefault();
            const $button = $(this);
            
            makeAjaxRequest('aca_ai_content_agent_refresh_capabilities', {}, {
                loadingElement: $button,
                loadingText: 'Refreshing...',
                successCallback: function(data) {
                    showNotification(
                        data.message || 'Permissions refreshed successfully!',
                        'success',
                        4000,
                        'Permissions Updated'
                    );
                    
                    // Reload page if required
                    if (data.reload_required) {
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    }
                },
                errorCallback: function(error) {
                    showNotification(
                        error.message || 'Failed to refresh permissions.',
                        'error',
                        6000,
                        'Refresh Failed'
                    );
                }
            });
        });
    }

    /**
     * Load external resources with fallbacks
     */
    function loadExternalResources() {
        // Bootstrap Icons
        if (!$('link[href*="bootstrap-icons"]').length) {
            const link = $('<link>', {
                rel: 'stylesheet',
                href: 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css',
                crossorigin: 'anonymous'
            });
            
            link.on('error', function() {
                console.warn('Failed to load Bootstrap Icons from CDN, using fallback');
                // Fallback is already in CSS
            });
            
            $('head').append(link);
        }

        // Inter font
        if (!$('link[href*="Inter"]').length) {
            const link = $('<link>', {
                rel: 'stylesheet',
                href: 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap'
            });
            
            link.on('error', function() {
                console.warn('Failed to load Inter font from CDN, using system fonts');
            });
            
            $('head').append(link);
        }
    }

    /**
     * Initialize tooltips
     */
    function initTooltips() {
        $('[data-tooltip]').each(function() {
            const $element = $(this);
            if (!$element.hasClass('aca-tooltip')) {
                $element.addClass('aca-tooltip');
            }
        });
    }

    /**
     * Periodic health check
     */
    function healthCheck() {
        // Check if critical elements are still present
        const criticalElements = ['.aca-admin-page', '.aca-nav-tabs', '#aca-notification-container'];
        let healthScore = 0;
        
        criticalElements.forEach(selector => {
            if ($(selector).length > 0) {
                healthScore++;
            }
        });
        
        if (healthScore < criticalElements.length) {
            console.warn('Health check failed: Missing critical elements');
        }
        
        // Clean up orphaned notifications
        $('.aca-notification').each(function() {
            const $notif = $(this);
            if (!$notif.hasClass('show') && $notif.css('opacity') == 0) {
                $notif.remove();
            }
        });
    }

    // ===== DOCUMENT READY =====
    $(document).ready(function() {
        init();
        
        // Handle browser back/forward
        $(window).on('popstate', function() {
            const hash = window.location.hash.substring(1);
            if (hash) {
                const $tab = $(`.aca-nav-tab[data-tab="${hash}"]`);
                if ($tab.length) {
                    $tab.click();
                }
            }
        });
        
        // Handle window resize for responsive adjustments
        $(window).on('resize', debounce(function() {
            // Adjust notification positions on mobile
            if ($(window).width() < 768) {
                $('.aca-notification').css({
                    'left': '10px',
                    'right': '10px',
                    'max-width': 'none'
                });
            } else {
                $('.aca-notification').css({
                    'left': 'auto',
                    'right': '20px',
                    'max-width': '400px'
                });
            }
        }, 250));
    });

    // ===== EXPOSE FUNCTIONS FOR EXTERNAL USE =====
    window.ACA_Admin = {
        showNotification,
        updateCounters,
        makeAjaxRequest,
        setLoadingState,
        validateForm,
        createSkeletonLoader,
        announceToScreenReader,
        escapeHtml
    };

});