<?php

/**
 * Database operations for the plugin
 */
class ACA_Database {

    /**
     * Get all ideas
     */
    public static function get_ideas($status = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ideas';
        
        if ($status) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC",
                $status
            ));
        } else {
            $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        }
        
        return $results ? $results : array();
    }

    /**
     * Add a new idea
     */
    public static function add_idea($title, $source = 'ai') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ideas';
        
        return $wpdb->insert(
            $table_name,
            array(
                'title' => $title,
                'source' => $source,
                'status' => 'new'
            ),
            array('%s', '%s', '%s')
        );
    }

    /**
     * Update idea status
     */
    public static function update_idea_status($id, $status) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ideas';
        
        return $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }

    /**
     * Delete an idea
     */
    public static function delete_idea($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_ideas';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Get all drafts
     */
    public static function get_drafts($status = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_drafts';
        
        if ($status) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC",
                $status
            ));
        } else {
            $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        }
        
        return $results ? $results : array();
    }

    /**
     * Add a new draft
     */
    public static function add_draft($data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_drafts';
        
        return $wpdb->insert(
            $table_name,
            array(
                'title' => $data['title'],
                'content' => $data['content'],
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'focus_keywords' => is_array($data['focus_keywords']) ? implode(',', $data['focus_keywords']) : $data['focus_keywords'],
                'featured_image_url' => $data['featured_image_url'] ?? '',
                'status' => 'draft'
            ),
            array('%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );
    }

    /**
     * Update a draft
     */
    public static function update_draft($id, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_drafts';
        
        $update_data = array();
        $format = array();
        
        if (isset($data['title'])) {
            $update_data['title'] = $data['title'];
            $format[] = '%s';
        }
        if (isset($data['content'])) {
            $update_data['content'] = $data['content'];
            $format[] = '%s';
        }
        if (isset($data['meta_title'])) {
            $update_data['meta_title'] = $data['meta_title'];
            $format[] = '%s';
        }
        if (isset($data['meta_description'])) {
            $update_data['meta_description'] = $data['meta_description'];
            $format[] = '%s';
        }
        if (isset($data['focus_keywords'])) {
            $update_data['focus_keywords'] = is_array($data['focus_keywords']) ? implode(',', $data['focus_keywords']) : $data['focus_keywords'];
            $format[] = '%s';
        }
        if (isset($data['status'])) {
            $update_data['status'] = $data['status'];
            $format[] = '%s';
        }
        if (isset($data['post_id'])) {
            $update_data['post_id'] = $data['post_id'];
            $format[] = '%d';
        }
        if (isset($data['scheduled_for'])) {
            $update_data['scheduled_for'] = $data['scheduled_for'];
            $format[] = '%s';
        }
        
        return $wpdb->update(
            $table_name,
            $update_data,
            array('id' => $id),
            $format,
            array('%d')
        );
    }

    /**
     * Get a single draft
     */
    public static function get_draft($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_drafts';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }

    /**
     * Delete a draft
     */
    public static function delete_draft($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_drafts';
        
        return $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }

    /**
     * Add activity log entry
     */
    public static function add_activity_log($type, $details, $icon = 'Sparkles') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_activity_log';
        
        return $wpdb->insert(
            $table_name,
            array(
                'type' => $type,
                'details' => $details,
                'icon' => $icon
            ),
            array('%s', '%s', '%s')
        );
    }

    /**
     * Get activity logs
     */
    public static function get_activity_logs($limit = 10) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'aca_activity_log';
        
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY created_at DESC LIMIT %d",
            $limit
        ));
        
        return $results ? $results : array();
    }

    /**
     * Get stats for dashboard
     */
    public static function get_stats() {
        global $wpdb;
        
        $ideas_table = $wpdb->prefix . 'aca_ideas';
        $drafts_table = $wpdb->prefix . 'aca_drafts';
        
        $ideas_count = $wpdb->get_var("SELECT COUNT(*) FROM $ideas_table WHERE status = 'new'");
        $drafts_count = $wpdb->get_var("SELECT COUNT(*) FROM $drafts_table WHERE status = 'draft'");
        $published_count = $wpdb->get_var("SELECT COUNT(*) FROM $drafts_table WHERE status = 'published'");
        
        return array(
            'ideas' => intval($ideas_count),
            'drafts' => intval($drafts_count),
            'published' => intval($published_count)
        );
    }
}