<?php
/**
 * ACA_Error_Manager
 *
 * Sets custom handlers to capture PHP errors and exceptions. Any captured
 * messages are logged via ACA_Log_Service for later inspection.
 *
 * @package ACA_AI_Content_Agent\Error_Management
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class ACA_Error_Manager {

    /**
     * Register handlers for errors and exceptions.
     */
    public static function init() {
        set_error_handler( [ __CLASS__, 'handle_error' ] );
        set_exception_handler( [ __CLASS__, 'handle_exception' ] );
        register_shutdown_function( [ __CLASS__, 'handle_shutdown' ] );
    }

    /**
     * Handle standard PHP errors.
     */
    public static function handle_error( $errno, $errstr, $errfile, $errline ) {
        $message = sprintf( '%s in %s on line %d', $errstr, $errfile, $errline );
        ACA_Log_Service::add( $message, 'error' );
        return false; // Execute PHP internal error handler as well.
    }

    /**
     * Handle uncaught exceptions.
     */
    public static function handle_exception( $exception ) {
        $message = sprintf( 'Uncaught exception %s: %s in %s on line %d',
            get_class( $exception ),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
        ACA_Log_Service::add( $message, 'error' );
    }

    /**
     * Handle shutdown and catch fatal errors.
     */
    public static function handle_shutdown() {
        $error = error_get_last();
        if ( $error && in_array( $error['type'], [ E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR ] ) ) {
            $message = sprintf( '%s in %s on line %d', $error['message'], $error['file'], $error['line'] );
            ACA_Log_Service::add( $message, 'error' );
        }
    }
}
