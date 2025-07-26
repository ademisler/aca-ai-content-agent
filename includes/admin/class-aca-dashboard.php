<?php
/**
 * ACA - AI Content Agent
 *
 * Dashboard Page
 *
 * @package ACA_AI_Content_Agent
 * @version 1.3
 * @since   1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class ACA_Dashboard {

    /**
     * Render the dashboard content.
     */
    public static function render() {
        echo '<div class="wrap aca-admin-page">';
        
        // Page header
        self::render_page_header();
        
        // Navigation tabs
        self::render_navigation_tabs();
        
        // Dashboard content
        self::render_dashboard_content();
        
        // Ideas page content
        self::render_ideas_content();
        
        // Settings page content
        self::render_settings_content();
        
        // License page content
        self::render_license_content();
        
        // Notification container
        echo '<div id="aca-notification-container"></div>';
        
        echo '</div>';
    }

    /**
     * Render page header.
     */
    private static function render_page_header() {
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;
        $pending_ideas = self::get_pending_ideas_count();
        $total_drafts = self::get_total_drafts_count();
        
        echo '<header class="aca-page-header">';
        echo '<div class="aca-header-content">';
        echo '<h1><i class="bi bi-robot"></i> ACA - AI Content Agent</h1>';
        echo '<div class="aca-header-meta">';
        echo '<span>Merhaba, <strong>' . esc_html( $user_name ) . '</strong>! Otomatik içerik üretimine hoş geldin.</span>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-header-stats">';
        echo '<div class="aca-stat-item">';
        echo '<span class="aca-stat-number" id="pending-ideas-count">' . esc_html( $pending_ideas ) . '</span>';
        echo '<span class="aca-stat-label">Onay Bekleyen Fikir</span>';
        echo '</div>';
        echo '<div class="aca-stat-item">';
        echo '<span class="aca-stat-number" id="total-drafts-count">' . esc_html( $total_drafts ) . '</span>';
        echo '<span class="aca-stat-label">Toplam Taslak</span>';
        echo '</div>';
        echo '</div>';
        echo '</header>';
    }

    /**
     * Render navigation tabs.
     */
    private static function render_navigation_tabs() {
        echo '<nav class="aca-nav-tabs">';
        echo '<button class="aca-nav-tab aca-nav-tab-active" data-tab="dashboard"><i class="bi bi-grid-1x2-fill"></i> Kontrol Paneli</button>';
        echo '<button class="aca-nav-tab" data-tab="ideas"><i class="bi bi-lightbulb-fill"></i> İçerik Fikirleri</button>';
        echo '<button class="aca-nav-tab" data-tab="settings"><i class="bi bi-gear-fill"></i> Ayarlar</button>';
        echo '<button class="aca-nav-tab" data-tab="license"><i class="bi bi-patch-check-fill"></i> Lisans</button>';
        echo '</nav>';
    }

    /**
     * Render dashboard content.
     */
    private static function render_dashboard_content() {
        echo '<div id="dashboard-content" class="aca-tab-content active">';
        
        // Overview section
        self::render_overview_section();
        
        // Quick actions and cluster planner
        self::render_quick_actions_section();
        
        // Idea stream
        self::render_idea_stream_section();
        
        echo '</div>';
    }

    /**
     * Render overview section.
     */
    private static function render_overview_section() {
        $pending_ideas = self::get_pending_ideas_count();
        $total_drafts = self::get_total_drafts_count();
        
        echo '<section class="aca-section aca-overview-section">';
        echo '<h2><i class="bi bi-bar-chart-line-fill"></i> İçerik Genel Bakış</h2>';
        echo '<div class="aca-overview-grid">';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">💡</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number" id="overview-pending-ideas">' . esc_html( $pending_ideas ) . '</div>';
        echo '<div class="label">Onay Bekleyen Fikir</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">📝</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number" id="overview-total-drafts">' . esc_html( $total_drafts ) . '</div>';
        echo '<div class="label">Oluşturulan Taslak</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">📊</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number">34</div>';
        echo '<div class="label">Bu Ay Üretilen Fikir</div>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-overview-card">';
        echo '<span class="aca-card-icon">⏱️</span>';
        echo '<div class="aca-card-content">';
        echo '<div class="number">2.1s</div>';
        echo '<div class="label">Ort. Üretim Süresi</div>';
        echo '</div>';
        echo '</div>';
        
        echo '</div>';
        echo '</section>';
    }

    /**
     * Render quick actions section.
     */
    private static function render_quick_actions_section() {
        echo '<div class="dashboard-grid">';
        
        // Quick actions
        echo '<section class="aca-section">';
        echo '<h2><i class="bi bi-lightning-charge-fill"></i> Hızlı Eylemler</h2>';
        echo '<p>En sık kullandığınız işlemleri buradan hızlıca gerçekleştirin.</p>';
        echo '<div style="display: flex; gap: 15px; margin-top: 20px;">';
        echo '<button class="aca-action-button" id="generate-ideas-btn">';
        echo '<span class="button-icon"><i class="bi bi-lightbulb"></i></span>';
        echo '<span class="button-text">Yeni Fikirler Üret</span>';
        echo '<span class="button-loader" style="display: none;"><span class="aca-loading-spinner"></span></span>';
        echo '</button>';
        echo '<button class="aca-action-button secondary" id="update-style-guide-btn">';
        echo '<span class="button-icon"><i class="bi bi-palette-fill"></i></span>';
        echo '<span class="button-text">Stil Rehberini Güncelle</span>';
        echo '<span class="button-loader" style="display: none;"><span class="aca-loading-spinner"></span></span>';
        echo '</button>';
        echo '</div>';
        echo '<div id="quick-actions-status" style="margin-top: 20px;"></div>';
        echo '</section>';
        
        // Cluster planner
        echo '<section class="aca-section">';
        echo '<h2><i class="bi bi-diagram-3-fill"></i> İçerik Kümesi Planlayıcı <span class="pro-badge">PRO</span></h2>';
        echo '<p>SEO stratejinizi güçlendirmek için bir ana konu etrafında içerik kümeleri oluşturun.</p>';
        echo '<div style="display: flex; gap: 15px; margin-top: 20px;">';
        echo '<input type="text" id="cluster-topic-input" class="aca-input" placeholder="Ana konuyu girin (örn. WordPress SEO)">';
        echo '<button class="aca-action-button" id="generate-cluster-btn">';
        echo '<i class="bi bi-magic"></i> Küme Oluştur';
        echo '</button>';
        echo '</div>';
        echo '</section>';
        
        echo '</div>';
    }

    /**
     * Render idea stream section.
     */
    private static function render_idea_stream_section() {
        echo '<section class="aca-section">';
        echo '<h2><i class="bi bi-stream"></i> Fikir Akışı</h2>';
        echo '<div id="idea-stream-container">';
        
        // Empty state
        echo '<div class="aca-empty-state" id="idea-empty-state">';
        echo '<div class="aca-empty-state-icon">💡</div>';
        echo '<h3>Henüz Onay Bekleyen Fikir Yok</h3>';
        echo '<p>AI asistanınızdan yeni içerik fikirleri üretmesini isteyin.</p>';
        echo '<button class="aca-action-button" id="generate-ideas-btn-empty">';
        echo '<i class="bi bi-plus-lg"></i> Fikir Üret';
        echo '</button>';
        echo '</div>';
        
        // Idea list
        echo '<ul class="aca-idea-list" id="idea-list">';
        self::render_sample_ideas();
        echo '</ul>';
        
        echo '</div>';
        echo '</section>';
    }

    /**
     * Render sample ideas.
     */
    private static function render_sample_ideas() {
        $sample_ideas = [
            [
                'id' => 101,
                'title' => 'WordPress Siteler için Performans Optimizasyonu İpuçları',
                'keywords' => 'wordpress, seo, performans',
                'time' => '2 saat önce'
            ],
            [
                'id' => 102,
                'title' => '2025 Yılında Öne Çıkan Dijital Pazarlama Trendleri',
                'keywords' => 'dijital pazarlama, trendler',
                'time' => '5 saat önce'
            ]
        ];
        
        foreach ($sample_ideas as $idea) {
            echo '<li data-id="' . esc_attr($idea['id']) . '">';
            echo '<div class="aca-idea-content">';
            echo '<div class="aca-idea-title">' . esc_html($idea['title']) . '</div>';
            echo '<div class="aca-idea-meta">';
            echo '<i class="bi bi-clock"></i> ' . esc_html($idea['time']) . ' &nbsp;&nbsp; ';
            echo '<i class="bi bi-tags"></i> ' . esc_html($idea['keywords']);
            echo '</div>';
            echo '</div>';
            echo '<div class="aca-idea-actions">';
            echo '<button class="aca-action-button write-draft-btn"><i class="bi bi-pencil-square"></i> Taslak Yaz</button>';
            echo '<button class="aca-action-button secondary reject-idea-btn"><i class="bi bi-x-circle"></i> Reddet</button>';
            echo '<button class="aca-feedback-btn" title="İyi fikir">👍</button>';
            echo '<button class="aca-feedback-btn" title="Kötü fikir">👎</button>';
            echo '</div>';
            echo '</li>';
        }
    }

    /**
     * Render ideas content.
     */
    private static function render_ideas_content() {
        echo '<div id="ideas-content" class="aca-tab-content">';
        echo '<section class="aca-section">';
        
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 40%;">Başlık</th>';
        echo '<th>Durum</th>';
        echo '<th>Anahtar Kelimeler</th>';
        echo '<th>Oluşturulma</th>';
        echo '<th>Eylemler</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $sample_ideas_table = [
            [
                'title' => 'WordPress Siteler için Performans Optimizasyonu İpuçları',
                'status' => 'pending',
                'keywords' => 'wordpress, seo, performans',
                'time' => '2 saat önce'
            ],
            [
                'title' => '2025 Yılında Öne Çıkan Dijital Pazarlama Trendleri',
                'status' => 'pending',
                'keywords' => 'dijital pazarlama, trendler',
                'time' => '5 saat önce'
            ],
            [
                'title' => 'Yeni Başlayanlar için Google Analytics 4 Rehberi',
                'status' => 'drafted',
                'keywords' => 'google analytics, ga4, analiz',
                'time' => '1 gün önce'
            ],
            [
                'title' => 'İçerik Pazarlamasında Yapay Zeka Nasıl Kullanılır?',
                'status' => 'rejected',
                'keywords' => 'yapay zeka, içerik pazarlaması',
                'time' => '2 gün önce'
            ]
        ];
        
        foreach ($sample_ideas_table as $idea) {
            echo '<tr>';
            echo '<td><strong>' . esc_html($idea['title']) . '</strong></td>';
            echo '<td><span class="aca-status-badge aca-status-' . esc_attr($idea['status']) . '">' . self::get_status_text($idea['status']) . '</span></td>';
            echo '<td>' . esc_html($idea['keywords']) . '</td>';
            echo '<td>' . esc_html($idea['time']) . '</td>';
            echo '<td>';
            if ($idea['status'] === 'pending') {
                echo '<button class="aca-action-button" style="padding: 8px 16px;"><i class="bi bi-pencil-square"></i> Taslak Yaz</button>';
            } else {
                echo '<button class="aca-action-button secondary" disabled>İşlendi</button>';
            }
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        
        // Pagination
        echo '<div class="tablenav bottom">';
        echo '<div class="tablenav-pages">';
        echo '<span class="displaying-num">12 öğe</span>';
        echo '<span class="pagination-links">';
        echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>';
        echo '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>';
        echo '<span class="screen-reader-text">Geçerli sayfa</span>';
        echo '<span id="table-paging" class="paging-input">';
        echo '<span class="tablenav-paging-text">1 / <span class="total-pages">3</span></span>';
        echo '</span>';
        echo '<a class="next-page button" href="#"><span class="screen-reader-text">Sonraki sayfa</span><span aria-hidden="true">›</span></a>';
        echo '<a class="last-page button" href="#"><span class="screen-reader-text">Son sayfa</span><span aria-hidden="true">»</span></a>';
        echo '</span>';
        echo '</div>';
        echo '<br class="clear">';
        echo '</div>';
        
        echo '</section>';
        echo '</div>';
    }

    /**
     * Render settings content.
     */
    private static function render_settings_content() {
        echo '<div id="settings-content" class="aca-tab-content">';
        echo '<form id="settings-form">';
        echo '<div class="aca-settings-grid">';
        
        // API Settings
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-key-fill"></i> API ve Bağlantı Ayarları</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="gemini_api_key">Google Gemini API Anahtarı</label>';
        echo '<input type="password" id="gemini_api_key" name="gemini_api_key" placeholder="*****************">';
        echo '<p class="description">Google AI Studio\'dan aldığınız anahtarı buraya girin.</p>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<button type="button" class="aca-action-button secondary" id="test-connection-btn">';
        echo '<i class="bi bi-broadcast-pin"></i> Bağlantıyı Test Et';
        echo '</button>';
        echo '<span id="connection-test-status" style="margin-left: 10px;"></span>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="openai_api_key">OpenAI API Anahtarı (DALL-E 3 için) <span class="pro-badge">PRO</span></label>';
        echo '<input type="password" id="openai_api_key" name="openai_api_key" placeholder="Pro sürümde aktif">';
        echo '</div>';
        echo '</section>';
        
        // Automation Settings
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-robot"></i> Otomasyon Ayarları</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="working_mode">Çalışma Modu</label>';
        echo '<select id="working_mode" name="working_mode">';
        echo '<option value="manual">Manuel (Kontrol Bende)</option>';
        echo '<option value="semi">Yarı Otomatik (Sadece Fikir Üret)</option>';
        echo '<option value="full">Tam Otomatik (Fikir Üret ve Taslak Yaz) - PRO</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="default_author">Varsayılan Yazar</label>';
        echo '<select id="default_author" name="default_author">';
        echo '<option value="1">Adem Isler</option>';
        echo '<option value="2">Editör</option>';
        echo '</select>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="generation_limit">Her Döngüde Üretim Limiti</label>';
        echo '<input type="number" id="generation_limit" name="generation_limit" value="5" min="1" max="20">';
        echo '<p class="description">API maliyetlerini kontrol etmek için her otomasyon döngüsünde üretilecek maksimum fikir/taslak sayısı.</p>';
        echo '</div>';
        echo '</section>';
        
        // Content Analysis
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-book-half"></i> İçerik Analizi ve Öğrenme</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label>Analiz Edilecek İçerik Türleri</label>';
        echo '<div>';
        echo '<label><input type="checkbox" checked> Yazılar</label>';
        echo '<label style="margin-left: 15px;"><input type="checkbox"> Sayfalar</label>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="analysis_depth">Analiz Derinliği</label>';
        echo '<input type="number" id="analysis_depth" name="analysis_depth" value="20" min="10" max="100">';
        echo '<p class="description">Yazı stilini öğrenmek için analiz edilecek son gönderi sayısı.</p>';
        echo '</div>';
        echo '</section>';
        
        // Content Enrichment
        echo '<section class="aca-section aca-settings-section">';
        echo '<h3><i class="bi bi-gem"></i> İçerik Zenginleştirme</h3>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="internal_links">Maksimum Dahili Link</label>';
        echo '<input type="number" id="internal_links" name="internal_links" value="3" min="0" max="10">';
        echo '<p class="description">Her taslağa otomatik eklenecek maksimum dahili link sayısı.</p>';
        echo '</div>';
        
        echo '<div class="aca-form-row">';
        echo '<label for="image_provider">Öne Çıkan Görsel Sağlayıcısı</label>';
        echo '<select id="image_provider" name="image_provider">';
        echo '<option value="none">Hiçbiri</option>';
        echo '<option value="pexels">Pexels</option>';
        echo '<option value="dalle">DALL-E 3 (PRO)</option>';
        echo '</select>';
        echo '</div>';
        echo '</section>';
        
        echo '</div>';
        
        echo '<div class="aca-form-actions">';
        echo '<button type="submit" class="aca-action-button">';
        echo '<i class="bi bi-save-fill"></i> Ayarları Kaydet';
        echo '</button>';
        echo '</div>';
        echo '</form>';
        echo '</div>';
    }

    /**
     * Render license content.
     */
    private static function render_license_content() {
        echo '<div id="license-content" class="aca-tab-content">';
        echo '<section class="aca-section aca-license-section">';
        
        echo '<div class="aca-license-status free" id="license-status-box">';
        echo '<p>Mevcut Durum: <strong><span id="license-status-text">ÜCRETSİZ SÜRÜM</span></strong></p>';
        echo '</div>';
        
        echo '<div id="license-form-container">';
        echo '<h3><i class="bi bi-key-fill"></i> Pro Lisansını Aktif Et</h3>';
        echo '<p>Tüm premium özelliklerin kilidini açmak için lisans anahtarınızı girin.</p>';
        echo '<div style="max-width: 500px; margin: 20px auto; display: flex; gap: 10px;">';
        echo '<input type="text" id="license-key-input" class="aca-input" placeholder="Lisans anahtarınız...">';
        echo '<button class="aca-action-button" id="validate-license-btn">Doğrula</button>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="aca-pro-features">';
        echo '<h3><i class="bi bi-star-fill"></i> ACA Pro ile Gelenler</h3>';
        echo '<ul>';
        echo '<li><i class="bi bi-check-circle-fill"></i> İçerik Kümesi Planlayıcı</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> DALL-E 3 ile Özgün Görsel Üretimi</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Copyscape ile İntihal Kontrolü</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Mevcut İçerikleri Güncelleme Asistanı</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Tam Otomasyon Modu</li>';
        echo '<li><i class="bi bi-check-circle-fill"></i> Sınırsız Fikir ve Taslak Üretimi</li>';
        echo '</ul>';
        echo '<a href="#" class="aca-action-button" style="background: var(--aca-success); box-shadow: 0 4px 12px #22c55e60;">';
        echo '<i class="bi bi-rocket-takeoff-fill"></i> ACA Pro\'yu Satın Al';
        echo '</a>';
        echo '</div>';
        
        echo '</section>';
        echo '</div>';
    }

    /**
     * Get pending ideas count.
     */
    private static function get_pending_ideas_count() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $pending_ideas = get_transient('aca_ai_content_agent_pending_ideas_count');
        if (false === $pending_ideas) {
            $pending_ideas = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'pending' ) );
            set_transient('aca_ai_content_agent_pending_ideas_count', $pending_ideas, 5 * MINUTE_IN_SECONDS);
        }

        return $pending_ideas ?: 5; // Fallback to 5 for demo
    }

    /**
     * Get total drafts count.
     */
    private static function get_total_drafts_count() {
        global $wpdb;
        $ideas_table = $wpdb->prefix . 'aca_ai_content_agent_ideas';

        $total_drafts = get_transient('aca_ai_content_agent_total_drafts_count');
        if (false === $total_drafts) {
            $total_drafts = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(id) FROM {$ideas_table} WHERE status = %s", 'drafted' ) );
            set_transient('aca_ai_content_agent_total_drafts_count', $total_drafts, 5 * MINUTE_IN_SECONDS);
        }

        return $total_drafts ?: 12; // Fallback to 12 for demo
    }

    /**
     * Get status text.
     */
    private static function get_status_text($status) {
        $status_texts = [
            'pending' => 'Bekliyor',
            'drafted' => 'Taslak Oluşturuldu',
            'rejected' => 'Reddedildi'
        ];
        
        return isset($status_texts[$status]) ? $status_texts[$status] : 'Bilinmiyor';
    }
}