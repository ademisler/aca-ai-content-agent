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
    function showButtonLoading($button, loadingText = 'İşleniyor...') {
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
            showNotification('Lütfen bekleyin, başka bir işlem devam ediyor...', 'error');
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
                    const errorMessage = response.data?.message || 'Bir hata oluştu.';
                    showNotification(errorMessage, 'error');
                    if (errorCallback) {
                        errorCallback(response.data);
                    }
                }
            },
            error: function(xhr, status, error) {
                ACA.isProcessing = false;
                const errorMessage = 'Sunucu hatası: ' + error;
                showNotification(errorMessage, 'error');
                if (errorCallback) {
                    errorCallback({ message: errorMessage });
                }
            }
        });
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
        showButtonLoading($button, 'Üretiliyor...');

        // Simulate API call
        setTimeout(() => {
            const newIdeas = [
                { 
                    id: Date.now() + 1, 
                    title: 'İçerik Stratejisinde Video Pazarlamanın Yeri', 
                    keywords: 'video pazarlama, içerik stratejisi'
                },
                { 
                    id: Date.now() + 2, 
                    title: 'E-Ticaret Siteleri İçin Kullanıcı Deneyimi (UX) İyileştirmeleri', 
                    keywords: 'e-ticaret, ux, kullanıcı deneyimi'
                }
            ];
            
            newIdeas.forEach(idea => {
                const $li = $(`
                    <li data-id="${idea.id}">
                        <div class="aca-idea-content">
                            <div class="aca-idea-title">${idea.title}</div>
                            <div class="aca-idea-meta">
                                <i class="bi bi-clock"></i> şimdi &nbsp;&nbsp; 
                                <i class="bi bi-tags"></i> ${idea.keywords}
                            </div>
                        </div>
                        <div class="aca-idea-actions">
                            <button class="aca-action-button write-draft-btn">
                                <i class="bi bi-pencil-square"></i> Taslak Yaz
                            </button>
                            <button class="aca-action-button secondary reject-idea-btn">
                                <i class="bi bi-x-circle"></i> Reddet
                            </button>
                            <button class="aca-feedback-btn" title="İyi fikir">👍</button>
                            <button class="aca-feedback-btn" title="Kötü fikir">👎</button>
                        </div>
                    </li>
                `);
                $('#idea-list').prepend($li);
            });
            
            ACA.pendingIdeasCount += newIdeas.length;
            updateCounters();
            checkIdeaListState();
            showNotification(`${newIdeas.length} yeni fikir başarıyla üretildi!`);

            hideButtonLoading($button);
        }, 2000);
    }

    /**
     * Handle update style guide
     */
    function handleUpdateStyleGuide($button) {
        showButtonLoading($button, 'Güncelleniyor...');

        setTimeout(() => {
            showNotification('Stil rehberi başarıyla güncellendi.');
            hideButtonLoading($button);
        }, 1500);
    }

    /**
     * Handle generate cluster
     */
    function handleGenerateCluster($button) {
        const topic = $('#cluster-topic-input').val().trim();
        
        if (!topic) {
            showNotification('Lütfen bir ana konu girin.', 'error');
            return;
        }

        showButtonLoading($button, 'Küme Oluşturuluyor...');

        setTimeout(() => {
            showNotification(`"${topic}" konusu için içerik kümesi oluşturuldu.`);
            $('#cluster-topic-input').val('');
            hideButtonLoading($button);
        }, 2500);
    }

    /**
     * Handle write draft
     */
    function handleWriteDraft($button, $ideaItem) {
        $button.html('<span class="aca-loading-spinner"></span> Yazılıyor...');
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
                showNotification(`"${ideaTitle}" başlıklı taslak oluşturuldu.`);
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
            showNotification('Fikir reddedildi.', 'error');
        }, 500);
    }

    // ===== SETTINGS PAGE =====
    function initSettingsPage() {
        // Test connection button
        $('#test-connection-btn').on('click', function() {
            const $statusEl = $('#connection-test-status');
            $statusEl.html('<span class="aca-status-indicator loading"><span class="aca-loading-spinner"></span> Test ediliyor...</span>');
            
            setTimeout(() => {
                const success = Math.random() > 0.2; // 80% success rate
                if (success) {
                    $statusEl.html('<span class="aca-status-indicator success"><i class="bi bi-check-circle-fill"></i> Bağlantı Başarılı</span>');
                } else {
                    $statusEl.html('<span class="aca-status-indicator error"><i class="bi bi-x-circle-fill"></i> Bağlantı Hatası</span>');
                }
            }, 1500);
        });

        // Settings form
        $('#settings-form').on('submit', function(e) {
            e.preventDefault();
            showNotification('Ayarlar başarıyla kaydedildi.');
        });
    }

    // ===== LICENSE PAGE =====
    function initLicensePage() {
        $('#validate-license-btn').on('click', function() {
            const $keyInput = $('#license-key-input');
            const $button = $(this);
            
            if (!$keyInput.val().trim()) {
                showNotification('Lütfen bir lisans anahtarı girin.', 'error');
                return;
            }

            $button.text('Doğrulanıyor...');
            $button.prop('disabled', true);

            setTimeout(() => {
                const $statusBox = $('#license-status-box');
                const $statusText = $('#license-status-text');
                
                $statusBox.removeClass('free').addClass('pro');
                $statusText.text('PRO LİSANS AKTİF');
                
                $('#license-form-container').hide();
                showNotification('Lisans anahtarı başarıyla doğrulandı. Pro özellikler aktif!');
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
                $button.prop('disabled', true).text('İşlendi');
                showNotification(`"${title}" başlıklı içerik işlendi.`);
            }
        });
    }

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