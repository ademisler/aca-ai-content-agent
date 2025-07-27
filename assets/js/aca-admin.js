/**
 * ACA AI Content Agent Admin JavaScript
 */

(function($) {
    'use strict';

    // Initialize when document is ready
    $(document).ready(function() {
        ACA_Admin.init();
    });

    // Main admin object
    window.ACA_Admin = {
        
        // Initialize admin functionality
        init: function() {
            this.bindEvents();
            this.initTooltips();
            this.initModals();
        },

        // Bind global events
        bindEvents: function() {
            // Global AJAX error handler
            $(document).ajaxError(function(event, xhr, settings, error) {
                if (xhr.status === 403) {
                    ACA_Admin.showNotice('Session expired. Please refresh the page.', 'error');
                } else if (xhr.status >= 500) {
                    ACA_Admin.showNotice('Server error occurred. Please try again.', 'error');
                }
            });

            // Dismiss notices
            $(document).on('click', '.notice-dismiss', function() {
                $(this).closest('.notice').fadeOut();
            });

            // Auto-dismiss success notices after 5 seconds
            setTimeout(function() {
                $('.notice-success').fadeOut();
            }, 5000);
        },

        // Initialize tooltips
        initTooltips: function() {
            $('[data-tooltip]').each(function() {
                const $element = $(this);
                const tooltip = $element.data('tooltip');
                
                $element.on('mouseenter', function() {
                    ACA_Admin.showTooltip($element, tooltip);
                }).on('mouseleave', function() {
                    ACA_Admin.hideTooltip();
                });
            });
        },

        // Initialize modals
        initModals: function() {
            // Close modal on outside click
            $(document).on('click', '.aca-modal', function(e) {
                if (e.target === this) {
                    ACA_Admin.closeModal($(this));
                }
            });

            // Close modal on escape key
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) { // Escape key
                    ACA_Admin.closeModal($('.aca-modal:visible'));
                }
            });
        },

        // Show notification
        showNotice: function(message, type = 'info', duration = 5000) {
            const noticeClass = `notice-${type}`;
            const $notice = $(`
                <div class="notice ${noticeClass} is-dismissible aca-notice">
                    <p>${message}</p>
                    <button type="button" class="notice-dismiss">
                        <span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            `);

            // Remove existing notices of the same type
            $(`.notice-${type}`).remove();

            // Add to page
            if ($('#aca-messages').length) {
                $('#aca-messages').html($notice).removeClass('hidden');
            } else {
                $('.wrap').prepend($notice);
            }

            // Auto-dismiss after duration
            if (duration > 0) {
                setTimeout(function() {
                    $notice.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, duration);
            }

            return $notice;
        },

        // Show loading state
        showLoading: function($element, text = 'Loading...') {
            $element.prop('disabled', true).addClass('aca-loading');
            
            const $spinner = $('<span class="aca-spinner"></span>');
            const originalText = $element.text();
            
            $element.data('original-text', originalText)
                   .html(`${text} `).append($spinner);
            
            return originalText;
        },

        // Hide loading state
        hideLoading: function($element, originalText = null) {
            $element.prop('disabled', false).removeClass('aca-loading');
            
            if (originalText) {
                $element.text(originalText);
            } else {
                const storedText = $element.data('original-text');
                if (storedText) {
                    $element.text(storedText);
                }
            }
        },

        // Show tooltip
        showTooltip: function($element, text) {
            const $tooltip = $(`<div class="aca-tooltip">${text}</div>`);
            $('body').append($tooltip);
            
            const elementOffset = $element.offset();
            const elementHeight = $element.outerHeight();
            
            $tooltip.css({
                position: 'absolute',
                top: elementOffset.top + elementHeight + 5,
                left: elementOffset.left,
                background: '#333',
                color: 'white',
                padding: '8px 12px',
                borderRadius: '4px',
                fontSize: '12px',
                zIndex: 9999,
                whiteSpace: 'nowrap'
            });
        },

        // Hide tooltip
        hideTooltip: function() {
            $('.aca-tooltip').remove();
        },

        // Open modal
        openModal: function($modal) {
            $modal.removeClass('hidden').addClass('flex');
            $('body').addClass('modal-open');
        },

        // Close modal
        closeModal: function($modal) {
            $modal.addClass('hidden').removeClass('flex');
            $('body').removeClass('modal-open');
        },

        // Confirm dialog
        confirm: function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        },

        // Format date
        formatDate: function(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        },

        // Truncate text
        truncateText: function(text, maxLength) {
            if (text.length <= maxLength) {
                return text;
            }
            return text.substr(0, maxLength) + '...';
        },

        // Debounce function
        debounce: function(func, wait, immediate) {
            let timeout;
            return function executedFunction() {
                const context = this;
                const args = arguments;
                const later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        },

        // Make AJAX request with error handling
        ajaxRequest: function(options) {
            const defaults = {
                type: 'POST',
                dataType: 'json',
                data: {
                    nonce: aca_ajax.nonce
                },
                beforeSend: function() {
                    if (options.$button) {
                        ACA_Admin.showLoading(options.$button, options.loadingText);
                    }
                },
                complete: function() {
                    if (options.$button) {
                        ACA_Admin.hideLoading(options.$button);
                    }
                },
                error: function(xhr, status, error) {
                    let message = 'An error occurred. Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.data) {
                        message = xhr.responseJSON.data;
                    } else if (xhr.status === 0) {
                        message = 'Network error. Please check your connection.';
                    }
                    
                    ACA_Admin.showNotice(message, 'error');
                    
                    if (options.error) {
                        options.error(xhr, status, error);
                    }
                }
            };

            const settings = $.extend(true, {}, defaults, options);
            settings.url = settings.url || aca_ajax.ajax_url;
            
            return $.ajax(settings);
        },

        // Copy text to clipboard
        copyToClipboard: function(text) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    ACA_Admin.showNotice('Copied to clipboard!', 'success', 2000);
                });
            } else {
                // Fallback for older browsers
                const $temp = $('<textarea>');
                $('body').append($temp);
                $temp.val(text).select();
                document.execCommand('copy');
                $temp.remove();
                ACA_Admin.showNotice('Copied to clipboard!', 'success', 2000);
            }
        },

        // Validate form
        validateForm: function($form) {
            let isValid = true;
            
            $form.find('[required]').each(function() {
                const $field = $(this);
                const value = $field.val().trim();
                
                if (!value) {
                    $field.addClass('error');
                    isValid = false;
                } else {
                    $field.removeClass('error');
                }
            });
            
            return isValid;
        },

        // Auto-save functionality
        initAutoSave: function($form, action, interval = 30000) {
            let autoSaveTimer;
            
            const saveData = function() {
                const formData = $form.serialize();
                
                ACA_Admin.ajaxRequest({
                    data: {
                        action: action,
                        ...formData
                    },
                    success: function(response) {
                        if (response.success) {
                            ACA_Admin.showNotice('Auto-saved', 'info', 2000);
                        }
                    }
                });
            };
            
            // Save on form change
            $form.on('change input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(saveData, interval);
            });
            
            // Save before page unload
            $(window).on('beforeunload', function() {
                saveData();
            });
        }
    };

    // Add CSS for modal-open class
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            body.modal-open {
                overflow: hidden;
            }
            .aca-tooltip {
                pointer-events: none;
            }
            .error {
                border-color: #ef4444 !important;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
            }
        `)
        .appendTo('head');

})(jQuery);