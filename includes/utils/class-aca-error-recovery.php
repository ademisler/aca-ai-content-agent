<?php
/**
 * Error recovery service for ACA AI Content Agent.
 *
 * Provides graceful degradation, automatic retry mechanisms,
 * and better error handling for improved user experience.
 *
 * @since 1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Error recovery service class for ACA AI Content Agent.
 *
 * Handles error recovery, retry mechanisms, and graceful degradation
 * for better user experience and system reliability.
 *
 * @since 1.2.0
 */
class ACA_Error_Recovery {

    /**
     * Maximum number of retry attempts.
     *
     * @since 1.2.0
     * @var int
     */
    const MAX_RETRY_ATTEMPTS = 3;

    /**
     * Base delay for exponential backoff in seconds.
     *
     * @since 1.2.0
     * @var int
     */
    const BASE_RETRY_DELAY = 2;

    /**
     * Retry a function with exponential backoff.
     *
     * @since 1.2.0
     * @param callable $callback The function to retry.
     * @param array $args Arguments to pass to the callback.
     * @param int $max_attempts Maximum number of retry attempts.
     * @return mixed The result of the callback or WP_Error on failure.
     */
    public static function retry_with_backoff($callback, $args = [], $max_attempts = null) {
        $max_attempts = $max_attempts ?: self::MAX_RETRY_ATTEMPTS;
        $attempt = 0;
        $last_error = null;

        while ($attempt < $max_attempts) {
            $attempt++;
            
            try {
                $result = call_user_func_array($callback, $args);
                
                // If it's a WP_Error, check if it's retryable
                if (is_wp_error($result)) {
                    if (self::is_retryable_error($result)) {
                        $last_error = $result;
                        
                        if ($attempt < $max_attempts) {
                            $delay = self::calculate_backoff_delay($attempt);
                            ACA_Log_Service::warning(
                                sprintf('Retrying operation (attempt %d/%d) after %d seconds', $attempt, $max_attempts, $delay),
                                ['error' => $result->get_error_message()]
                            );
                            sleep($delay);
                            continue;
                        }
                    }
                }
                
                return $result;
                
            } catch (Exception $e) {
                $last_error = new WP_Error('exception', $e->getMessage());
                
                if ($attempt < $max_attempts) {
                    $delay = self::calculate_backoff_delay($attempt);
                    ACA_Log_Service::warning(
                        sprintf('Retrying operation after exception (attempt %d/%d) after %d seconds', $attempt, $max_attempts, $delay),
                        ['exception' => $e->getMessage()]
                    );
                    sleep($delay);
                    continue;
                }
            }
        }

        ACA_Log_Service::error(
            sprintf('Operation failed after %d attempts', $max_attempts),
            ['last_error' => $last_error ? $last_error->get_error_message() : 'Unknown error']
        );

        return $last_error ?: new WP_Error('max_retries_exceeded', __('Operation failed after maximum retry attempts.', 'aca-ai-content-agent'));
    }

    /**
     * Check if an error is retryable.
     *
     * @since 1.2.0
     * @param WP_Error $error The error to check.
     * @return bool True if retryable, false otherwise.
     */
    private static function is_retryable_error($error) {
        $retryable_codes = [
            'http_request_failed',
            'timeout',
            'connection_failed',
            'api_error',
            'rate_limit_exceeded'
        ];

        return in_array($error->get_error_code(), $retryable_codes, true);
    }

    /**
     * Calculate exponential backoff delay.
     *
     * @since 1.2.0
     * @param int $attempt The current attempt number.
     * @return int Delay in seconds.
     */
    private static function calculate_backoff_delay($attempt) {
        return self::BASE_RETRY_DELAY * pow(2, $attempt - 1);
    }

    /**
     * Handle API errors gracefully.
     *
     * @since 1.2.0
     * @param WP_Error $error The API error.
     * @param string $operation The operation that failed.
     * @return WP_Error Enhanced error with user-friendly message.
     */
    public static function handle_api_error($error, $operation = '') {
        $error_code = $error->get_error_code();
        $error_message = $error->get_error_message();

        // Log the error with context
        ACA_Log_Service::error(
            sprintf('API error during %s: %s', $operation, $error_message),
            [
                'error_code' => $error_code,
                'operation' => $operation,
                'user_id' => get_current_user_id()
            ]
        );

        // Provide user-friendly error messages
        switch ($error_code) {
            case 'api_key_missing':
                return new WP_Error($error_code, __('API key is missing. Please check your settings.', 'aca-ai-content-agent'));
            
            case 'rate_limit_exceeded':
                return new WP_Error($error_code, __('API rate limit exceeded. Please try again later.', 'aca-ai-content-agent'));
            
            case 'monthly_limit_exceeded':
                return new WP_Error($error_code, __('Monthly API limit reached. Please upgrade your plan.', 'aca-ai-content-agent'));
            
            case 'timeout':
                return new WP_Error($error_code, __('Request timed out. Please try again.', 'aca-ai-content-agent'));
            
            case 'connection_failed':
                return new WP_Error($error_code, __('Connection failed. Please check your internet connection.', 'aca-ai-content-agent'));
            
            default:
                return new WP_Error($error_code, __('An unexpected error occurred. Please try again.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Provide fallback functionality when primary features fail.
     *
     * @since 1.2.0
     * @param string $feature The feature that failed.
     * @param array $context Additional context.
     * @return mixed Fallback result or WP_Error.
     */
    public static function provide_fallback($feature, $context = []) {
        switch ($feature) {
            case 'idea_generation':
                return self::fallback_idea_generation($context);
            
            case 'content_generation':
                return self::fallback_content_generation($context);
            
            case 'style_guide':
                return self::fallback_style_guide($context);
            
            default:
                return new WP_Error('no_fallback', __('No fallback available for this feature.', 'aca-ai-content-agent'));
        }
    }

    /**
     * Fallback idea generation using local templates.
     *
     * @since 1.2.0
     * @param array $context Additional context.
     * @return array Array of fallback ideas.
     */
    private static function fallback_idea_generation($context = []) {
        $fallback_ideas = [
            'How to Improve Your Website Performance',
            'Top 10 Tips for Better SEO',
            'Understanding Content Marketing',
            'Social Media Best Practices',
            'Email Marketing Strategies'
        ];

        ACA_Log_Service::info('Using fallback idea generation', $context);
        
        return array_slice($fallback_ideas, 0, 3);
    }

    /**
     * Fallback content generation using templates.
     *
     * @since 1.2.0
     * @param array $context Additional context.
     * @return string Fallback content.
     */
    private static function fallback_content_generation($context = []) {
        $title = $context['title'] ?? 'Untitled';
        
        $fallback_content = sprintf(
            '<h2>%s</h2><p>This is a placeholder content for "%s". Please edit this content to add your own unique insights and information.</p>',
            esc_html($title),
            esc_html($title)
        );

        ACA_Log_Service::info('Using fallback content generation', $context);
        
        return $fallback_content;
    }

    /**
     * Fallback style guide using default templates.
     *
     * @since 1.2.0
     * @param array $context Additional context.
     * @return string Fallback style guide.
     */
    private static function fallback_style_guide($context = []) {
        $fallback_guide = "Default Style Guide:\n\n" .
            "1. Use clear, concise language\n" .
            "2. Write in active voice\n" .
            "3. Keep paragraphs short (2-3 sentences)\n" .
            "4. Use headings to organize content\n" .
            "5. Include relevant examples\n" .
            "6. Proofread before publishing";

        ACA_Log_Service::info('Using fallback style guide', $context);
        
        return $fallback_guide;
    }

    /**
     * Notify administrators of critical errors.
     *
     * @since 1.2.0
     * @param string $error_message The error message.
     * @param array $context Additional context.
     */
    public static function notify_admin($error_message, $context = []) {
        $admin_email = get_option('admin_email');
        
        if (empty($admin_email)) {
            return;
        }

        $subject = sprintf('[%s] ACA AI Content Agent - Critical Error', get_bloginfo('name'));
        
        $message = sprintf(
            "A critical error occurred in the ACA AI Content Agent plugin:\n\n" .
            "Error: %s\n\n" .
            "Context: %s\n\n" .
            "Time: %s\n" .
            "User: %s\n" .
            "URL: %s\n\n" .
            "Please check the plugin logs for more details.",
            $error_message,
            wp_json_encode($context),
            current_time('mysql'),
            wp_get_current_user()->user_login,
            home_url()
        );

        wp_mail($admin_email, $subject, $message);
    }
} 