<?php
/**
 * Hook Manager for AI Content Agent - Optimizes WordPress hook registration
 */
if (!defined("ABSPATH")) exit;

class ACA_Hook_Manager {
    private static $hook_stats = ["total" => 0, "admin" => 0, "frontend" => 0];
    
    public static function add_action($hook, $callback, $priority = 10, $args = 1, $context = "auto") {
        if ($context === "admin" && !is_admin()) return false;
        if ($context === "frontend" && is_admin()) return false;
        self::$hook_stats["total"]++;
        if (is_admin()) self::$hook_stats["admin"]++; else self::$hook_stats["frontend"]++;
        return add_action($hook, $callback, $priority, $args);
    }
    
    public static function add_filter($hook, $callback, $priority = 10, $args = 1, $context = "auto") {
        if ($context === "admin" && !is_admin()) return false;
        if ($context === "frontend" && is_admin()) return false;
        self::$hook_stats["total"]++;
        return add_filter($hook, $callback, $priority, $args);
    }
    
    public static function get_stats() { return self::$hook_stats; }
}
