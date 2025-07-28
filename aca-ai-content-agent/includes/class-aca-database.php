<?php
/**
 * ACA Database Handler
 * Handles custom table creation and database operations
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Database {
    
    /**
     * Create custom database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        try {
            $charset_collate = $wpdb->get_charset_collate();
            
            // Create aca_ideas table
            $ideas_table = $wpdb->prefix . 'aca_ideas';
            $ideas_sql = "CREATE TABLE IF NOT EXISTS $ideas_table (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                title TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'new' NOT NULL,
                source VARCHAR(20) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
            
            // Create aca_activity_logs table
            $logs_table = $wpdb->prefix . 'aca_activity_logs';
            $logs_sql = "CREATE TABLE IF NOT EXISTS $logs_table (
                id BIGINT(20) NOT NULL AUTO_INCREMENT,
                timestamp DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
                type VARCHAR(50) NOT NULL,
                details TEXT NOT NULL,
                icon VARCHAR(50) NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";
            
            // Include upgrade.php if not already included
            if (!function_exists('dbDelta')) {
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            }
            
            // Execute table creation
            $result1 = dbDelta($ideas_sql);
            $result2 = dbDelta($logs_sql);
            
            // Log any errors
            if ($wpdb->last_error) {
                error_log('ACA Plugin DB Error: ' . $wpdb->last_error);
            }
            
        } catch (Exception $e) {
            error_log('ACA Plugin Exception in create_tables: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all ideas
     */
    public static function get_ideas() {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_ideas';
        
        $results = $wpdb->get_results(
            "SELECT * FROM $table ORDER BY created_at DESC",
            ARRAY_A
        );
        
        return array_map(function($idea) {
            return array(
                'id' => (int) $idea['id'],
                'title' => $idea['title'],
                'status' => $idea['status'],
                'source' => $idea['source']
            );
        }, $results);
    }
    
    /**
     * Create a new idea
     */
    public static function create_idea($title, $source) {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_ideas';
        
        $result = $wpdb->insert(
            $table,
            array(
                'title' => sanitize_text_field($title),
                'source' => sanitize_text_field($source),
                'status' => 'new'
            ),
            array('%s', '%s', '%s')
        );
        
        if ($result === false) {
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update an idea
     */
    public static function update_idea($id, $data) {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_ideas';
        
        $update_data = array();
        $format = array();
        
        if (isset($data['title'])) {
            $update_data['title'] = sanitize_text_field($data['title']);
            $format[] = '%s';
        }
        
        if (isset($data['status'])) {
            $update_data['status'] = sanitize_text_field($data['status']);
            $format[] = '%s';
        }
        
        if (empty($update_data)) {
            return false;
        }
        
        return $wpdb->update(
            $table,
            $update_data,
            array('id' => $id),
            $format,
            array('%d')
        );
    }
    
    /**
     * Delete an idea
     */
    public static function delete_idea($id) {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_ideas';
        
        return $wpdb->delete(
            $table,
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Get activity logs
     */
    public static function get_activity_logs($limit = 50) {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_activity_logs';
        
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table ORDER BY timestamp DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        );
        
        return array_map(function($log) {
            return array(
                'id' => (int) $log['id'],
                'timestamp' => $log['timestamp'],
                'type' => $log['type'],
                'details' => $log['details'],
                'icon' => $log['icon']
            );
        }, $results);
    }
    
    /**
     * Add activity log entry
     */
    public static function add_activity_log($type, $details, $icon) {
        global $wpdb;
        $table = $wpdb->prefix . 'aca_activity_logs';
        
        return $wpdb->insert(
            $table,
            array(
                'type' => sanitize_text_field($type),
                'details' => sanitize_text_field($details),
                'icon' => sanitize_text_field($icon)
            ),
            array('%s', '%s', '%s')
        );
    }
    
    /**
     * Get posts by status (draft or published)
     */
    public static function get_posts_by_status($status = 'draft') {
        $args = array(
            'post_type' => 'post',
            'post_status' => $status === 'draft' ? 'draft' : 'publish',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_aca_meta_title',
                    'compare' => 'EXISTS'
                )
            )
        );
        
        $posts = get_posts($args);
        $formatted_posts = array();
        
        foreach ($posts as $post) {
            $meta_title = get_post_meta($post->ID, '_aca_meta_title', true);
            $meta_description = get_post_meta($post->ID, '_aca_meta_description', true);
            $focus_keywords = get_post_meta($post->ID, '_aca_focus_keywords', true);
            $scheduled_for = get_post_meta($post->ID, '_aca_scheduled_for', true);
            $featured_image = get_the_post_thumbnail_url($post->ID, 'full');
            
            $formatted_posts[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'content' => $post->post_content,
                'status' => $status,
                'createdAt' => $post->post_date,
                'publishedAt' => $status === 'publish' ? $post->post_date : null,
                'url' => $status === 'publish' ? get_permalink($post->ID) : null,
                'metaTitle' => $meta_title ?: '',
                'metaDescription' => $meta_description ?: '',
                'focusKeywords' => $focus_keywords ? json_decode($focus_keywords, true) : array(),
                'featuredImage' => $featured_image ?: '',
                'scheduledFor' => $scheduled_for ?: null
            );
        }
        
        return $formatted_posts;
    }
    
    /**
     * Create a new draft post
     */
    public static function create_draft($title, $content, $meta_data, $featured_image_id = null) {
        $post_data = array(
            'post_title' => sanitize_text_field($title),
            'post_content' => wp_kses_post($content),
            'post_status' => 'draft',
            'post_type' => 'post',
            'post_author' => get_current_user_id()
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return false;
        }
        
        // Set featured image if provided
        if ($featured_image_id) {
            set_post_thumbnail($post_id, $featured_image_id);
        }
        
        // Save meta data
        if (isset($meta_data['metaTitle'])) {
            update_post_meta($post_id, '_aca_meta_title', sanitize_text_field($meta_data['metaTitle']));
        }
        
        if (isset($meta_data['metaDescription'])) {
            update_post_meta($post_id, '_aca_meta_description', sanitize_text_field($meta_data['metaDescription']));
        }
        
        if (isset($meta_data['focusKeywords']) && is_array($meta_data['focusKeywords'])) {
            update_post_meta($post_id, '_aca_focus_keywords', json_encode($meta_data['focusKeywords']));
        }
        
        return $post_id;
    }
    
    /**
     * Update post meta data
     */
    public static function update_post_meta_data($post_id, $meta_data) {
        if (isset($meta_data['metaTitle'])) {
            update_post_meta($post_id, '_aca_meta_title', sanitize_text_field($meta_data['metaTitle']));
        }
        
        if (isset($meta_data['metaDescription'])) {
            update_post_meta($post_id, '_aca_meta_description', sanitize_text_field($meta_data['metaDescription']));
        }
        
        if (isset($meta_data['focusKeywords']) && is_array($meta_data['focusKeywords'])) {
            update_post_meta($post_id, '_aca_focus_keywords', json_encode($meta_data['focusKeywords']));
        }
        
        if (isset($meta_data['scheduledFor'])) {
            update_post_meta($post_id, '_aca_scheduled_for', sanitize_text_field($meta_data['scheduledFor']));
        }
        
        return true;
    }
    
    /**
     * Publish a draft post
     */
    public static function publish_post($post_id) {
        $post_data = array(
            'ID' => $post_id,
            'post_status' => 'publish'
        );
        
        return wp_update_post($post_data);
    }
    
    /**
     * Schedule a post
     */
    public static function schedule_post($post_id, $scheduled_date) {
        update_post_meta($post_id, '_aca_scheduled_for', sanitize_text_field($scheduled_date));
        return true;
    }
}