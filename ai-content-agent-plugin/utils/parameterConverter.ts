/**
 * Parameter Converter Utility for AI Content Agent
 * 
 * Handles automatic conversion between camelCase (frontend) and snake_case (backend)
 * to resolve parameter naming inconsistencies across the application.
 * 
 * @package AI_Content_Agent
 * @version 2.3.8
 * @since 2.3.8
 */

/**
 * Convert camelCase to snake_case
 */
export function camelToSnake(str: string): string {
    return str.replace(/[A-Z]/g, letter => `_${letter.toLowerCase()}`);
}

/**
 * Convert snake_case to camelCase
 */
export function snakeToCamel(str: string): string {
    return str.replace(/_([a-z])/g, (match, letter) => letter.toUpperCase());
}

/**
 * Convert object keys from camelCase to snake_case
 */
export function convertObjectToSnakeCase(obj: any): any {
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }
    
    if (Array.isArray(obj)) {
        return obj.map(convertObjectToSnakeCase);
    }
    
    const converted: any = {};
    for (const [key, value] of Object.entries(obj)) {
        const snakeKey = camelToSnake(key);
        converted[snakeKey] = convertObjectToSnakeCase(value);
    }
    
    return converted;
}

/**
 * Convert object keys from snake_case to camelCase
 */
export function convertObjectToCamelCase(obj: any): any {
    if (obj === null || typeof obj !== 'object') {
        return obj;
    }
    
    if (Array.isArray(obj)) {
        return obj.map(convertObjectToCamelCase);
    }
    
    const converted: any = {};
    for (const [key, value] of Object.entries(obj)) {
        const camelKey = snakeToCamel(key);
        converted[camelKey] = convertObjectToCamelCase(value);
    }
    
    return converted;
}

/**
 * Enhanced API call wrapper with automatic parameter conversion
 */
export function makeApiCallWithConversion(
    originalMakeApiCall: (path: string, options?: RequestInit) => Promise<any>
) {
    return async (path: string, options: RequestInit = {}) => {
        // Convert request body from camelCase to snake_case
        if (options.body && typeof options.body === 'string') {
            try {
                const parsedBody = JSON.parse(options.body);
                const convertedBody = convertObjectToSnakeCase(parsedBody);
                options.body = JSON.stringify(convertedBody);
            } catch (error) {
                // If parsing fails, keep original body
                console.warn('Failed to convert request body:', error);
            }
        }
        
        // Make the API call
        const response = await originalMakeApiCall(path, options);
        
        // Convert response from snake_case to camelCase
        return convertObjectToCamelCase(response);
    };
}

/**
 * Specific parameter mappings for common inconsistencies
 */
export const PARAMETER_MAPPINGS = {
    // License related
    'licenseKey': 'license_key',
    'isActive': 'is_active',
    'expiresAt': 'expires_at',
    
    // Content related
    'updateType': 'update_type',
    'contentType': 'content_type',
    'postId': 'post_id',
    'userId': 'user_id',
    
    // API keys
    'apiKey': 'api_key',
    'geminiApiKey': 'gemini_api_key',
    'pexelsApiKey': 'pexels_api_key',
    'unsplashApiKey': 'unsplash_api_key',
    'pixabayApiKey': 'pixabay_api_key',
    
    // Google Cloud
    'googleCloudProjectId': 'google_cloud_project_id',
    'googleCloudLocation': 'google_cloud_location',
    
    // GSC related
    'gscClientId': 'gsc_client_id',
    'gscClientSecret': 'gsc_client_secret',
    'siteUrl': 'site_url',
    'startDate': 'start_date',
    'endDate': 'end_date',
    'rowLimit': 'row_limit',
    
    // Settings
    'autoPublish': 'auto_publish',
    'imageSourceProvider': 'image_source_provider',
    'aiImageStyle': 'ai_image_style',
    'seoPlugin': 'seo_plugin',
    'isPro': 'is_pro'
};

/**
 * Convert specific parameter using mappings
 */
export function convertParameter(key: string, direction: 'toSnake' | 'toCamel'): string {
    if (direction === 'toSnake') {
        return PARAMETER_MAPPINGS[key as keyof typeof PARAMETER_MAPPINGS] || camelToSnake(key);
    } else {
        // Find reverse mapping
        const reverseMapping = Object.entries(PARAMETER_MAPPINGS).find(([, snake]) => snake === key);
        return reverseMapping ? reverseMapping[0] : snakeToCamel(key);
    }
}

/**
 * Validation function to ensure parameter consistency
 */
export function validateParameterConsistency(frontendParams: string[], backendParams: string[]): {
    isConsistent: boolean;
    mismatches: Array<{ frontend: string; expectedBackend: string; actualBackend?: string }>;
} {
    const mismatches: Array<{ frontend: string; expectedBackend: string; actualBackend?: string }> = [];
    
    frontendParams.forEach(frontendParam => {
        const expectedBackend = convertParameter(frontendParam, 'toSnake');
        const actualBackend = backendParams.find(bp => bp === expectedBackend);
        
        if (!actualBackend) {
            mismatches.push({
                frontend: frontendParam,
                expectedBackend,
                actualBackend: backendParams.find(bp => bp.includes(frontendParam.toLowerCase()))
            });
        }
    });
    
    return {
        isConsistent: mismatches.length === 0,
        mismatches
    };
}