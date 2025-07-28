<?php
/**
 * ACA Stock Photo Service
 * Handles stock photo API calls to Pexels, Unsplash, and Pixabay
 */

if (!defined('ABSPATH')) {
    exit;
}

class ACA_Stock_Photo_Service {
    
    private $settings;
    
    public function __construct() {
        $this->settings = get_option('aca_settings', array());
    }
    
    /**
     * Fetch stock photo based on provider and query
     */
    public function fetch_stock_photo($query, $provider = null) {
        if (!$provider) {
            $provider = isset($this->settings['imageSourceProvider']) ? $this->settings['imageSourceProvider'] : 'pexels';
        }
        
        switch ($provider) {
            case 'pexels':
                return $this->fetch_from_pexels($query);
            case 'unsplash':
                return $this->fetch_from_unsplash($query);
            case 'pixabay':
                return $this->fetch_from_pixabay($query);
            default:
                throw new Exception('Unsupported stock photo provider: ' . $provider);
        }
    }
    
    /**
     * Fetch photo from Pexels API
     */
    private function fetch_from_pexels($query) {
        $api_key = isset($this->settings['pexelsApiKey']) ? $this->settings['pexelsApiKey'] : '';
        
        if (empty($api_key)) {
            throw new Exception('Pexels API key not configured');
        }
        
        $url = 'https://api.pexels.com/v1/search?' . http_build_query(array(
            'query' => $query,
            'per_page' => 1,
            'orientation' => 'landscape'
        ));
        
        $args = array(
            'headers' => array(
                'Authorization' => $api_key
            ),
            'timeout' => 30
        );
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to fetch from Pexels: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['photos'][0]['src']['large'])) {
            return array(
                'url' => $data['photos'][0]['src']['large'],
                'photographer' => $data['photos'][0]['photographer'],
                'source' => 'Pexels'
            );
        }
        
        throw new Exception('No photos found on Pexels for query: ' . $query);
    }
    
    /**
     * Fetch photo from Unsplash API
     */
    private function fetch_from_unsplash($query) {
        $api_key = isset($this->settings['unsplashApiKey']) ? $this->settings['unsplashApiKey'] : '';
        
        if (empty($api_key)) {
            throw new Exception('Unsplash API key not configured');
        }
        
        $url = 'https://api.unsplash.com/search/photos?' . http_build_query(array(
            'query' => $query,
            'per_page' => 1,
            'orientation' => 'landscape'
        ));
        
        $args = array(
            'headers' => array(
                'Authorization' => 'Client-ID ' . $api_key
            ),
            'timeout' => 30
        );
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to fetch from Unsplash: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['results'][0]['urls']['regular'])) {
            return array(
                'url' => $data['results'][0]['urls']['regular'],
                'photographer' => $data['results'][0]['user']['name'],
                'source' => 'Unsplash'
            );
        }
        
        throw new Exception('No photos found on Unsplash for query: ' . $query);
    }
    
    /**
     * Fetch photo from Pixabay API
     */
    private function fetch_from_pixabay($query) {
        $api_key = isset($this->settings['pixabayApiKey']) ? $this->settings['pixabayApiKey'] : '';
        
        if (empty($api_key)) {
            throw new Exception('Pixabay API key not configured');
        }
        
        $url = 'https://pixabay.com/api/?' . http_build_query(array(
            'key' => $api_key,
            'q' => $query,
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'per_page' => 3,
            'safesearch' => 'true'
        ));
        
        $args = array(
            'timeout' => 30
        );
        
        $response = wp_remote_get($url, $args);
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to fetch from Pixabay: ' . $response->get_error_message());
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['hits'][0]['largeImageURL'])) {
            return array(
                'url' => $data['hits'][0]['largeImageURL'],
                'photographer' => $data['hits'][0]['user'],
                'source' => 'Pixabay'
            );
        }
        
        throw new Exception('No photos found on Pixabay for query: ' . $query);
    }
    
    /**
     * Download image and upload to WordPress media library
     */
    public function sideload_image($image_url, $filename = null) {
        if (!function_exists('media_handle_sideload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        
        // Download the image
        $response = wp_remote_get($image_url, array('timeout' => 60));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to download image: ' . $response->get_error_message());
        }
        
        $image_data = wp_remote_retrieve_body($response);
        $image_type = wp_remote_retrieve_header($response, 'content-type');
        
        if (empty($image_data)) {
            throw new Exception('Downloaded image is empty');
        }
        
        // Generate filename if not provided
        if (!$filename) {
            $filename = 'aca-image-' . time() . '.jpg';
        }
        
        // Create temporary file
        $temp_file = wp_tempnam($filename);
        file_put_contents($temp_file, $image_data);
        
        // Prepare file array for media_handle_sideload
        $file_array = array(
            'name' => $filename,
            'tmp_name' => $temp_file,
            'type' => $image_type
        );
        
        // Upload to media library
        $attachment_id = media_handle_sideload($file_array, 0);
        
        // Clean up temporary file
        @unlink($temp_file);
        
        if (is_wp_error($attachment_id)) {
            throw new Exception('Failed to upload image to media library: ' . $attachment_id->get_error_message());
        }
        
        return $attachment_id;
    }
    
    /**
     * Get image as base64 for immediate use
     */
    public function get_image_as_base64($image_url) {
        $response = wp_remote_get($image_url, array('timeout' => 60));
        
        if (is_wp_error($response)) {
            throw new Exception('Failed to download image: ' . $response->get_error_message());
        }
        
        $image_data = wp_remote_retrieve_body($response);
        $image_type = wp_remote_retrieve_header($response, 'content-type');
        
        if (empty($image_data)) {
            throw new Exception('Downloaded image is empty');
        }
        
        return 'data:' . $image_type . ';base64,' . base64_encode($image_data);
    }
}