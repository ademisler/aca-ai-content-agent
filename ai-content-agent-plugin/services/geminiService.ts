
import { GoogleGenerativeAI, SchemaType } from "@google/generative-ai";
import type { AiImageStyle } from '../types';
import type { AiService } from './aiService';

let genAI: GoogleGenerativeAI | null = null;

export const setGeminiApiKey = (key: string) => {
    if (key && key.trim() !== '') {
        try {
            genAI = new GoogleGenerativeAI(key);
        } catch (error) {
            console.error("Failed to initialize GoogleGenerativeAI:", error);
            genAI = null;
        }
    } else {
        genAI = null;
    }
};

// Model configuration with fallbacks
const modelConfig = {
    primary: "gemini-2.0-flash",
    fallback: "gemini-1.5-pro",
    maxRetries: 3,
    retryDelay: 2000, // 2 seconds
};

const checkAi = () => {
    if (!genAI) {
        throw new Error("Google AI API key is not set or is invalid. Please configure it in the Settings page.");
    }
    return genAI;
}

/**
 * Make API call with retry logic and model fallback
 */
const makeApiCallWithRetry = async (
    ai: GoogleGenerativeAI, 
    modelName: string, 
    prompt: any, 
    config: any,
    retryCount = 0
): Promise<string> => {
    try {
        const model = ai.getGenerativeModel({ model: modelName });
        const result = await model.generateContent({
            contents: [{ role: "user", parts: [{ text: prompt }] }],
            generationConfig: config,
        });
        
        return result.response.text() || '';
    } catch (error: any) {
        console.error(`ACA: API call failed with ${modelName}, attempt ${retryCount + 1}:`, error);
        
        // Check if it's a 503 or overload error
        const isOverloadError = (
            error.message?.includes('503') || 
            error.message?.includes('overloaded') ||
            error.message?.includes('UNAVAILABLE') ||
            error.status === 503
        );
        
        // If it's overload error and we haven't exhausted retries
        if (isOverloadError && retryCount < modelConfig.maxRetries) {
            // Try fallback model on first retry
            if (retryCount === 0 && modelName === modelConfig.primary) {
                console.log(`ACA: Trying fallback model ${modelConfig.fallback}`);
                await new Promise(resolve => setTimeout(resolve, modelConfig.retryDelay));
                return makeApiCallWithRetry(ai, modelConfig.fallback, prompt, config, retryCount + 1);
            }
            
            // Retry with exponential backoff
            const delay = modelConfig.retryDelay * Math.pow(2, retryCount);
            console.log(`ACA: Retrying in ${delay}ms...`);
            await new Promise(resolve => setTimeout(resolve, delay));
            return makeApiCallWithRetry(ai, modelName, prompt, config, retryCount + 1);
        }
        
        // If all retries failed, throw the error with more context
        throw new Error(`AI service unavailable after ${retryCount + 1} attempts. ${error.message || 'Unknown error'}`);
    }
};

/**
 * Analyzes the style of a website's content to create a style guide.
 * This simulates reading the 20 most recent posts to determine the style.
 * @returns A JSON string representing the StyleGuide object.
 */
const analyzeStyle = async (): Promise<string> => {
    const ai = checkAi();
    
    const prompt = `
        Analyze the common writing style of a professional tech and marketing blog and generate a JSON object that describes it. 
        Imagine you have read the 20 most recent articles from the blog to gather this information.
        This JSON object will be used as a "Style Guide" for generating new content.
        The JSON object must strictly follow this schema:
        {
          "tone": "string (e.g., 'Friendly and conversational', 'Formal and professional', 'Technical and informative', 'Witty and humorous')",
          "sentenceStructure": "string (e.g., 'Mix of short, punchy sentences and longer, more descriptive ones', 'Primarily short and direct sentences', 'Complex sentences with multiple clauses')",
          "paragraphLength": "string (e.g., 'Short, 2-3 sentences per paragraph', 'Medium, 4-6 sentences per paragraph')",
          "formattingStyle": "string (e.g., 'Uses bullet points, bold text for emphasis, and subheadings (H2, H3)', 'Minimal formatting, relies on plain text paragraphs')"
        }
    `;

    const config = {
        responseMimeType: "application/json",
        responseSchema: {
            type: SchemaType.OBJECT,
            properties: {
                tone: { type: SchemaType.STRING },
                sentenceStructure: { type: SchemaType.STRING },
                paragraphLength: { type: SchemaType.STRING },
                formattingStyle: { type: SchemaType.STRING },
            },
            required: ["tone", "sentenceStructure", "paragraphLength", "formattingStyle"],
        },
    };

    return makeApiCallWithRetry(ai, modelConfig.primary, prompt, config);
};

/**
 * Generates new blog post ideas based on a style guide and optional search data.
 * @param styleGuideJson - A JSON string of the StyleGuide.
 * @param existingTitles - An array of existing titles to avoid duplicates.
 * @param count - The number of ideas to generate.
 * @param searchConsoleData - Optional data from Google Search Console for more strategic ideas.
 * @returns A JSON string array of new, unique, and SEO-friendly blog post titles.
 */
const generateIdeas = async (styleGuideJson: string, existingTitles: string[], count: number = 5, searchConsoleData?: { topQueries: string[], underperformingPages: string[] }): Promise<string> => {
    const ai = checkAi();
    
    let searchConsolePrompt = "";
    if (searchConsoleData) {
        searchConsolePrompt = `
    **Leverage Search Console Data:**
    Use the following Google Search Console data to generate highly relevant and strategic ideas. Focus on answering popular user questions or improving underperforming content.
    - Top Performing Queries (create content on related topics): ${searchConsoleData.topQueries.join(', ')}
    - Underperforming Pages (consider creating improved/updated versions or related topics): ${searchConsoleData.underperformingPages.join(', ')}
    `;
    }
    
    const prompt = `
        Based on the following Style Guide, generate a JSON array of ${count} new, interesting, and SEO-friendly blog post titles.
        ${searchConsolePrompt}
        The titles should be unique and not present in the "existing titles" list.
        The output must be a valid JSON array of strings.

        **Style Guide:**
        ${styleGuideJson}

        **Existing Titles to avoid:**
        ${existingTitles.join(', ')}
    `;

    const config = {
        responseMimeType: "application/json",
        responseSchema: {
            type: SchemaType.ARRAY,
            items: { type: SchemaType.STRING }
        }
    };

    return makeApiCallWithRetry(ai, modelConfig.primary, prompt, config);
};

/**
 * Generates new blog post ideas based on an existing idea.
 * @param baseTitle The title of the existing idea to expand upon.
 * @param existingTitles An array of all current titles to avoid duplication.
 * @returns A JSON string array of 3 new, similar, and unique blog post titles.
 */
const generateSimilarIdeas = async (baseTitle: string, existingTitles: string[]): Promise<string> => {
    const ai = checkAi();
    
    const prompt = `
        Based on the blog post title "${baseTitle}", generate a JSON array of 3 unique and creative new blog post titles that are similar in topic or angle.
        The new titles must be distinct from the original title and from each other.
        These titles must also not be in the following list of already existing titles: ${existingTitles.join(', ')}.
        The output must be a valid JSON array of strings. For example: ["New Title 1", "New Title 2", "New Title 3"]
    `;

    const config = {
        responseMimeType: "application/json",
        responseSchema: {
            type: SchemaType.ARRAY,
            items: { type: SchemaType.STRING }
        }
    };

    return makeApiCallWithRetry(ai, modelConfig.primary, prompt, config);
};

/**
 * Creates a full blog post draft from a title and style guide.
 * @param title - The title of the blog post.
 * @param styleGuideJson - A JSON string of the StyleGuide.
 * @param existingPublishedPosts - An array of objects containing the title, url, and content of existing published posts for internal linking.
 * @returns A JSON string with the draft content and meta info.
 */
const createDraft = async (
    title: string,
    styleGuideJson: string,
    existingPublishedPosts: { title: string; url: string; content: string }[]
): Promise<string> => {
    const ai = checkAi();
    
    const internalLinkPrompt = existingPublishedPosts.length > 0 ?
    `5.  **Internal Links:** Directly embed at least 3 relevant internal links within the article content. The links MUST be in markdown format: [anchor text](URL). Choose the most appropriate anchor text from the content you write. You must link to the provided existing published posts. Analyze their content to ensure the links are contextually appropriate. Do NOT add a list of links at the end; they must be embedded in the text.
        
        **Existing Posts Available for Linking:**
        ${existingPublishedPosts.map(p => `
---
Title: ${p.title}
URL: ${p.url}
Content:
${p.content}
---`).join('\n\n')}
    ` :
    `5.  **Internal Links:** No existing posts were provided, so do not add any internal links.`;
    
    const prompt = `
        **Task:** Write a blog post draft based on the provided title and style guide.

        **Requirements:**
        1.  **Title:** ${title}
        2.  **Style Guide:** Adhere strictly to the following style: ${styleGuideJson}
        3.  **Length:** The article should be approximately 800 words.
        4.  **Structure:** The article must have a clear structure:
            - An engaging introduction.
            - A body with multiple sections using H2 and H3 subheadings.
            - A concluding summary.
        ${internalLinkPrompt}
        6.  **SEO:** Generate a concise, SEO-friendly meta title (under 60 characters) and meta description (under 160 characters).
        7.  **Focus Keywords:** Generate exactly 5 relevant SEO focus keywords for the article. The keywords should be a mix of short-tail and long-tail and be highly relevant to the main topic.

        **Output Format:**
        Your entire response MUST be a single, valid JSON object following this exact schema:
        {
          "content": "The full blog post content in markdown format, WITH THE INTERNAL LINKS EMBEDDED as requested.",
          "metaTitle": "The generated SEO meta title.",
          "metaDescription": "The generated SEO meta description.",
          "focusKeywords": ["An array of 5 relevant SEO focus keywords."]
        }
    `;

    const config = {
        responseMimeType: "application/json",
        responseSchema: {
            type: SchemaType.OBJECT,
            properties: {
                content: { type: SchemaType.STRING },
                metaTitle: { type: SchemaType.STRING },
                metaDescription: { type: SchemaType.STRING },
                focusKeywords: {
                    type: SchemaType.ARRAY,
                    items: { type: SchemaType.STRING }
                },
            },
            required: ["content", "metaTitle", "metaDescription", "focusKeywords"]
        }
    };

    return makeApiCallWithRetry(ai, modelConfig.primary, prompt, config);
};

/**
 * Generates an image using AI based on a text prompt and a specified style.
 * @param prompt - The description for the image, typically the article title.
 * @param style - The desired style of the image.
 * @returns A base64 encoded string of the generated JPEG image bytes.
 */
const generateImage = async (prompt: string, style: AiImageStyle = 'digital_art'): Promise<string> => {
    // Note: Gemini doesn't have image generation in the standard API
    // This is a placeholder implementation
    throw new Error("Image generation is not available with Gemini API. Please use a different AI service for image generation.");
};

export const geminiService: AiService = {
  analyzeStyle,
  generateIdeas,
  generateSimilarIdeas,
  createDraft,
  generateImage,
};