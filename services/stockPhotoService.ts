
import type { ImageSourceProvider } from '../types';

/**
 * Fetches an image from a URL and converts it to a base64 string.
 * @param url The URL of the image to fetch.
 * @returns A promise that resolves with the base64-encoded image data, without the data URL prefix.
 */
const imageUrlToBase64 = (url: string): Promise<string> => {
    return new Promise(async (resolve, reject) => {
        try {
            // NOTE: In a real-world scenario, fetching images directly from a third-party
            // URL on the client-side may be blocked by CORS policies. A server-side
            // proxy would be required to reliably bypass this. For this example,
            // we assume direct fetching is possible.
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const blob = await response.blob();
            const reader = new FileReader();
            reader.onloadend = () => {
                // reader.result is "data:[<mediatype>];base64,<data>"
                // We only want the <data> part.
                const base64data = reader.result as string;
                resolve(base64data.split(',')[1]);
            };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        } catch (error) {
            reject(error);
        }
    });
}

const fetchFromPexels = async (query: string, apiKey: string): Promise<string | null> => {
    const response = await fetch(`https://api.pexels.com/v1/search?query=${encodeURIComponent(query)}&per_page=1&orientation=landscape`, {
        headers: {
            Authorization: apiKey
        }
    });
    if (!response.ok) throw new Error(`Pexels API error: ${response.statusText}`);
    const data = await response.json();
    return data.photos?.[0]?.src?.large2x ?? null;
};

const fetchFromUnsplash = async (query: string, apiKey: string): Promise<string | null> => {
    const response = await fetch(`https://api.unsplash.com/search/photos?query=${encodeURIComponent(query)}&per_page=1&orientation=landscape`, {
        headers: {
            Authorization: `Client-ID ${apiKey}`
        }
    });
    if (!response.ok) throw new Error(`Unsplash API error: ${response.statusText}`);
    const data = await response.json();
    return data.results?.[0]?.urls?.regular ?? null;
};

const fetchFromPixabay = async (query: string, apiKey: string): Promise<string | null> => {
    const response = await fetch(`https://pixabay.com/api/?key=${apiKey}&q=${encodeURIComponent(query)}&image_type=photo&per_page=3&orientation=horizontal`);
    if (!response.ok) throw new Error(`Pixabay API error: ${response.statusText}`);
    const data = await response.json();
    return data.hits?.[0]?.largeImageURL ?? null;
};

/**
 * Fetches a stock photo from the specified provider, converts it to base64.
 * @param query The search term for the image, typically the article title.
 * @param provider The stock photo service to use.
 * @param apiKey The API key for the selected service.
 * @returns A promise that resolves to a base64 string of the image, or null if failed.
 */
export const fetchStockPhotoAsBase64 = async (
    query: string,
    provider: ImageSourceProvider,
    apiKey: string
): Promise<string | null> => {
    let imageUrl: string | null = null;
    try {
        switch (provider) {
            case 'pexels':
                imageUrl = await fetchFromPexels(query, apiKey);
                break;
            case 'unsplash':
                imageUrl = await fetchFromUnsplash(query, apiKey);
                break;
            case 'pixabay':
                imageUrl = await fetchFromPixabay(query, apiKey);
                break;
            default:
                return null; // Should not happen if called correctly
        }

        if (imageUrl) {
            const base64Data = await imageUrlToBase64(imageUrl);
            return base64Data;
        }
        
        console.warn(`No image found on ${provider} for query: "${query}"`);
        return null;

    } catch (error) {
        console.error(`Error fetching image from ${provider}:`, error);
        throw error; // Re-throw to be caught by the caller
    }
};
