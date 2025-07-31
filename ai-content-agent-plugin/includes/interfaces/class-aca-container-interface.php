<?php
/**
 * Container Interface for AI Content Agent
 * 
 * @package AI_Content_Agent
 * @version 2.3.14
 * @since 2.3.14
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Dependency Injection Container Interface
 */
interface ACA_Container_Interface {
    
    /**
     * Register a service
     * 
     * @param string $name
     * @param mixed $definition
     * @param bool $singleton
     * @return void
     */
    public static function register($name, $definition, $singleton = true);
    
    /**
     * Get service instance
     * 
     * @param string $name
     * @return mixed
     */
    public static function get($name);
    
    /**
     * Check if service exists
     * 
     * @param string $name
     * @return bool
     */
    public static function has($name);
    
    /**
     * Inject dependencies into class constructor
     * 
     * @param string $class_name
     * @param array $dependencies
     * @return object
     */
    public static function make($class_name, $dependencies = []);
}
