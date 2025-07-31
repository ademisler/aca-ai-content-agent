/**
 * Stock photo service for fetching images from various providers
 */

export type StockPhotoProvider = 'pexels' | 'unsplash' | 'pixabay';

export interface StockPhotoResult {
    base64: string;
    attribution: {
        photographer: string;
        photographerUrl: string;
        source: string;
        sourceUrl: string;
        license: string;
        licenseUrl: string;
    };
}

interface PexelsResponse {
    photos: Array<{
        id: number;
        src: {
            original: string;
            large2x: string;
            large: string;
            medium: string;
        };
    }>;
}

interface UnsplashResponse {
    results: Array<{
        id: string;
        urls: {
            raw: string;
            full: string;
            regular: string;
            small: string;
        };
    }>;
}

interface PixabayResponse {
    hits: Array<{
        id: number;
        webformatURL: string;
        largeImageURL: string;
        fullHDURL: string;
    }>;
}

/**
 * Converts an image URL to base64 string
 */
const urlToBase64 = async (url: string): Promise<string> => {
    try {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Failed to fetch image: ${response.statusText}`);
        }
        
        const blob = await response.blob();
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => {
                const base64 = reader.result as string;
                // Remove the data:image/jpeg;base64, prefix
                const base64Data = base64.split(',')[1];
                resolve(base64Data);
            };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    } catch (error) {
        console.error('Error converting image to base64:', error);
        throw new Error('Failed to process image');
    }
};

/**
 * Fetch image from Pexels
 */
const fetchFromPexels = async (query: string, apiKey: string): Promise<string> => {
    const url = `https://api.pexels.com/v1/search?query=${encodeURIComponent(query)}&per_page=1&orientation=landscape`;
    
    const response = await fetch(url, {
        headers: {
            'Authorization': apiKey
        }
    });

    if (!response.ok) {
        throw new Error(`Pexels API error: ${response.statusText}`);
    }

    const data: PexelsResponse = await response.json();
    
    if (!data.photos || data.photos.length === 0) {
        throw new Error('No images found on Pexels for this query');
    }

    const imageUrl = data.photos[0].src.large;
    return urlToBase64(imageUrl);
};

/**
 * Fetch image from Unsplash
 */
const fetchFromUnsplash = async (query: string, apiKey: string): Promise<string> => {
    const url = `https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=1&orientation=landscape`;
    
    const response = await fetch(url, {
        headers: {
            'Authorization': `Client-ID ${apiKey}`
        }
    });

    if (!response.ok) {
        throw new Error(`Unsplash API error: ${response.statusText}`);
    }

    const data: UnsplashResponse = await response.json();
    
    if (!data.results || data.results.length === 0) {
        throw new Error('No images found on Unsplash for this query');
    }

    const imageUrl = data.results[0].urls.regular;
    return urlToBase64(imageUrl);
};

/**
 * Fetch image from Pixabay
 */
const fetchFromPixabay = async (query: string, apiKey: string): Promise<string> => {
    const url = `https://pixabay.com/api/?key=${apiKey}&q=${encodeURIComponent(query)}&image_type=photo&orientation=horizontal&per_page=3&safesearch=true`;
    
    const response = await fetch(url);

    if (!response.ok) {
        throw new Error(`Pixabay API error: ${response.statusText}`);
    }

    const data: PixabayResponse = await response.json();
    
    if (!data.hits || data.hits.length === 0) {
        throw new Error('No images found on Pixabay for this query');
    }

    const imageUrl = data.hits[0].webformatURL;
    return urlToBase64(imageUrl);
};

/**
 * Main function to fetch stock photo as base64 string
 */
export const fetchStockPhotoAsBase64 = async (
    query: string, 
    provider: StockPhotoProvider, 
    apiKey: string
): Promise<string> => {
    if (!apiKey || !apiKey.trim()) {
        throw new Error(`API key is required for ${provider}`);
    }

    try {
        switch (provider) {
            case 'pexels':
                return await fetchFromPexels(query, apiKey);
            case 'unsplash':
                return await fetchFromUnsplash(query, apiKey);
            case 'pixabay':
                return await fetchFromPixabay(query, apiKey);
            default:
                throw new Error(`Unsupported provider: ${provider}`);
        }
    } catch (error) {
        console.error(`Stock photo fetch failed for ${provider}:`, error);
        throw error;
    }
};
