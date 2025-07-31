
import type { AiImageStyle } from '../types';

/**
 * Defines the contract for an AI service provider.
 * This allows for easy swapping of AI backends (e.g., Gemini, OpenAI)
 * without changing the application logic.
 */
export interface AiService {
    /**
     * Analyzes website content to create a style guide.
     * @returns A JSON string representing the StyleGuide object.
     */
    analyzeStyle(): Promise<string>;
    
    /**
     * Generates new blog post ideas.
     * @param styleGuideJson - A JSON string of the StyleGuide.
     * @param existingTitles - An array of existing titles to avoid duplicates.
     * @param count - The number of ideas to generate.
     * @param searchConsoleData - Optional data from Google Search Console for more strategic ideas.
     * @returns A JSON string array of new, unique, and SEO-friendly blog post titles.
     */
    generateIdeas(
        styleGuideJson: string, 
        existingTitles: string[], 
        count: number, 
        searchConsoleData?: { topQueries: string[], underperformingPages: string[] }
    ): Promise<string>;
    
    /**
     * Generates ideas similar to a base title.
     * @param baseTitle The title of the existing idea to expand upon.
     * @param existingTitles An array of all current titles to avoid duplication.
     * @returns A JSON string array of new, similar, and unique blog post titles.
     */
    generateSimilarIdeas(
        baseTitle: string, 
        existingTitles: string[]
    ): Promise<string>;

    /**
     * Creates a full blog post draft.
     * @param title - The title of the blog post.
     * @param styleGuideJson - A JSON string of the StyleGuide.
     * @param existingPublishedPosts - An array of objects containing the title, url, and content of existing published posts for internal linking.
     * @returns A JSON string with the draft content and meta info.
     */
    createDraft(
        title: string, 
        styleGuideJson: string, 
        existingPublishedPosts: { title: string; url: string; content: string }[]
    ): Promise<string>;

    /**
     * Generates a featured image for a post.
     * @param prompt - The description for the image, typically the article title.
     * @param style - The desired style of the image.
     * @returns A base64 encoded string of the generated JPEG image bytes.
     */
    generateImage(
        prompt: string, 
        style: AiImageStyle
    ): Promise<string>;

    /**
     * Analyzes content for freshness and provides update recommendations.
     * @param content - The content to analyze for freshness.
     * @param title - The title of the content.
     * @returns A JSON string with freshness analysis and recommendations.
     */
    analyzeContentFreshness(
        content: string,
        title: string
    ): Promise<string>;
}
