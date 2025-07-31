<?php
/**
 * Service Container for AI Content Agent
 * 
 * Implements dependency injection container pattern for better
 * architecture, testability, and maintainability.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Service Container Class
 */
class ACA_Service_Container {
    
    /**
     * Service instances
     */
    private static $instances = [];
    
    /**
     * Service definitions
     */
    private static $definitions = [];
    
    /**
     * Service aliases
     */
    private static $aliases = [];
    
    /**
     * Singleton instance
     */
    private static $container_instance = null;
    
    /**
     * Get container instance
     * 
     * @return ACA_Service_Container
     */
    public static function get_instance() {
        if (self::$container_instance === null) {
            self::$container_instance = new self();
            self::$container_instance->register_default_services();
        }
        
        return self::$container_instance;
    }
    
    /**
     * Register a service
     * 
     * @param string $name Service name
     * @param callable|string $definition Service definition
     * @param bool $singleton Whether service should be singleton
     * @return void
     */
    public static function register($name, $definition, $singleton = true) {
        self::$definitions[$name] = [
            'definition' => $definition,
            'singleton' => $singleton,
            'initialized' => false
        ];
    }
    
    /**
     * Register service alias
     * 
     * @param string $alias
     * @param string $service_name
     * @return void
     */
    public static function alias($alias, $service_name) {
        self::$aliases[$alias] = $service_name;
    }
    
    /**
     * Get service instance
     * 
     * @param string $name Service name or alias
     * @return mixed Service instance
     * @throws Exception If service not found
     */
    public static function get($name) {
        // Resolve alias
        if (isset(self::$aliases[$name])) {
            $name = self::$aliases[$name];
        }
        
        // Return existing singleton instance
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }
        
        // Check if service is registered
        if (!isset(self::$definitions[$name])) {
            throw new Exception("Service '{$name}' not found in container");
        }
        
        $definition = self::$definitions[$name];
        
        // Create instance
        $instance = self::create_instance($definition['definition']);
        
        // Store singleton instance
        if ($definition['singleton']) {
            self::$instances[$name] = $instance;
        }
        
        // Initialize if it's a service
        if ($instance instanceof ACA_Service_Interface && !$definition['initialized']) {
            $instance->initialize();
            self::$definitions[$name]['initialized'] = true;
        }
        
        return $instance;
    }
    
    /**
     * Check if service exists
     * 
     * @param string $name
     * @return bool
     */
    public static function has($name) {
        // Check alias
        if (isset(self::$aliases[$name])) {
            $name = self::$aliases[$name];
        }
        
        return isset(self::$definitions[$name]) || isset(self::$instances[$name]);
    }
    
    /**
     * Remove service
     * 
     * @param string $name
     * @return void
     */
    public static function remove($name) {
        unset(self::$instances[$name]);
        unset(self::$definitions[$name]);
    }
    
    /**
     * Create service instance
     * 
     * @param callable|string $definition
     * @return mixed
     */
    private static function create_instance($definition) {
        if (is_callable($definition)) {
            return call_user_func($definition);
        }
        
        if (is_string($definition) && class_exists($definition)) {
            return new $definition();
        }
        
        if (is_object($definition)) {
            return $definition;
        }
        
        throw new Exception("Invalid service definition");
    }
    
    /**
     * Register default services
     * 
     * @return void
     */
    private function register_default_services() {
        // Cache Service
        self::register('cache', function() {
            return new ACA_Cache_Service();
        });
        
        // Database Service
        self::register('database', function() {
            return new ACA_Database_Service();
        });
        
        // Logger Service
        self::register('logger', function() {
            return new ACA_Logger_Service();
        });
        
        // Validation Service
        self::register('validation', function() {
            return new ACA_Validation_Service();
        });
        
        // AI Service (Gemini)
        self::register('ai', function() {
            return new ACA_Gemini_Service();
        });
        
        // Performance Monitor
        self::register('performance', function() {
            return ACA_Performance_Monitor::class;
        });
        
        // Rate Limiter
        self::register('rate_limiter', function() {
            return ACA_Rate_Limiter::class;
        });
        
        // Register aliases
        self::alias('db', 'database');
        self::alias('log', 'logger');
        self::alias('perf', 'performance');
        self::alias('rate', 'rate_limiter');
    }
    
    /**
     * Get all registered services
     * 
     * @return array
     */
    public static function get_registered_services() {
        return array_keys(self::$definitions);
    }
    
    /**
     * Get all service instances
     * 
     * @return array
     */
    public static function get_instances() {
        return self::$instances;
    }
    
    /**
     * Clear all services (for testing)
     * 
     * @return void
     */
    public static function clear() {
        self::$instances = [];
        self::$definitions = [];
        self::$aliases = [];
        self::$container_instance = null;
    }
    
    /**
     * Magic method to get service
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        return self::get($name);
    }
    
    /**
     * Magic method to check if service exists
     * 
     * @param string $name
     * @return bool
     */
    public function __isset($name) {
        return self::has($name);
    }
}

/**
 * Service Factory
 */
class ACA_Service_Factory {
    
    /**
     * Create AI service based on provider
     * 
     * @param string $provider
     * @return ACA_AI_Service_Interface
     * @throws Exception
     */
    public static function create_ai_service($provider = 'gemini') {
        switch (strtolower($provider)) {
            case 'gemini':
                return new ACA_Gemini_Service();
            
            case 'openai':
                return new ACA_OpenAI_Service();
            
            case 'claude':
                return new ACA_Claude_Service();
            
            default:
                throw new Exception("Unsupported AI provider: {$provider}");
        }
    }
    
    /**
     * Create cache service based on type
     * 
     * @param string $type
     * @return ACA_Cache_Service_Interface
     * @throws Exception
     */
    public static function create_cache_service($type = 'wordpress') {
        switch (strtolower($type)) {
            case 'wordpress':
                return new ACA_WordPress_Cache_Service();
            
            case 'redis':
                return new ACA_Redis_Cache_Service();
            
            case 'memcached':
                return new ACA_Memcached_Cache_Service();
            
            default:
                throw new Exception("Unsupported cache type: {$type}");
        }
    }
    
    /**
     * Create database service
     * 
     * @param string $type
     * @return ACA_Database_Service_Interface
     * @throws Exception
     */
    public static function create_database_service($type = 'wordpress') {
        switch (strtolower($type)) {
            case 'wordpress':
                return new ACA_WordPress_Database_Service();
            
            default:
                throw new Exception("Unsupported database type: {$type}");
        }
    }
}

/**
 * Helper functions for service container
 */

/**
 * Get service from container
 * 
 * @param string $name
 * @return mixed
 */
function aca_service($name) {
    return ACA_Service_Container::get($name);
}

/**
 * Get container instance
 * 
 * @return ACA_Service_Container
 */
function aca_container() {
    return ACA_Service_Container::get_instance();
}

/**
 * Register service in container
 * 
 * @param string $name
 * @param callable|string $definition
 * @param bool $singleton
 * @return void
 */
function aca_register_service($name, $definition, $singleton = true) {
    ACA_Service_Container::register($name, $definition, $singleton);
}