
import { GoogleGenAI, Type as GeminiType, GenerateContentResponse } from "@google/genai";
import type { AiImageStyle } from '../types';
import type { AiService } from './aiService';

let ai: GoogleGenAI | null = null;

export const setGeminiApiKey = (key: string) => {
    if (key && key.trim() !== '') {
        try {
            ai = new GoogleGenAI({ apiKey: key });
        } catch (error) {
            console.error("Failed to initialize GoogleGenAI:", error);
            ai = null;
        }
    } else {
        ai = null;
    }
};

const textModel = "gemini-2.5-flash";
const imageModel = "imagen-3.0-generate-002";

const checkAi = () => {
    if (!ai) {
        throw new Error("Google AI API key is not set or is invalid. Please configure it in the Settings page.");
    }
    return ai;
}

/**
 * Analyzes the style of a website's content to create a style guide.
 * This simulates reading the 20 most recent posts to determine the style.
 * @returns A JSON string representing the StyleGuide object.
 */
const analyzeStyle = async (): Promise<string> => {
    const genAI = checkAi();
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

    try {
        const model = genAI.getGenerativeModel({ model: textModel });
        const result = await model.generateContent(prompt);
        const response = await result.response;
        return response.text();
    } catch (error) {
        console.error("Style analysis failed:", error);
        throw new Error("Failed to analyze style guide. Please check your API key and try again.");
    }
};

/**
 * Generates content ideas based on a style guide.
 * @param styleGuide JSON string of the style guide
 * @param existingTitles Array of existing post titles to avoid duplicates
 * @param count Number of ideas to generate
 * @param searchConsoleData Optional search console data for SEO optimization
 * @returns A JSON string array of idea titles
 */
const generateIdeas = async (styleGuide: string, existingTitles: string[], count: number = 5, searchConsoleData?: { topQueries: string[], underperformingPages: string[] }): Promise<string> => {
    const genAI = checkAi();
    let prompt = `
        Based on this style guide: ${styleGuide}
        
        Generate ${count} unique, engaging blog post titles that match the style and tone described in the guide.
        
        Avoid these existing titles: ${JSON.stringify(existingTitles)}
        
        Return ONLY a JSON array of strings (the titles), nothing else.
        Example format: ["Title 1", "Title 2", "Title 3"]
    `;

    if (searchConsoleData) {
        prompt += `
        
        Also consider these SEO insights:
        - Top performing queries: ${JSON.stringify(searchConsoleData.topQueries)}
        - Underperforming pages that could be improved: ${JSON.stringify(searchConsoleData.underperformingPages)}
        
        Incorporate relevant keywords from the top queries and create content that could improve upon the underperforming topics.
        `;
    }

    try {
        const model = genAI.getGenerativeModel({ model: textModel });
        const result = await model.generateContent(prompt);
        const response = await result.response;
        return response.text();
    } catch (error) {
        console.error("Idea generation failed:", error);
        throw new Error("Failed to generate ideas. Please check your API key and try again.");
    }
};

/**
 * Generates similar ideas based on a base idea.
 * @param baseIdea The base idea to generate similar ideas from
 * @param existingTitles Array of existing titles to avoid duplicates
 * @returns A JSON string array of similar idea titles
 */
const generateSimilarIdeas = async (baseIdea: string, existingTitles: string[]): Promise<string> => {
    const genAI = checkAi();
    const prompt = `
        Generate 3-5 blog post titles that are similar to this idea: "${baseIdea}"
        
        The similar titles should:
        - Cover the same general topic but from different angles
        - Be unique and engaging
        - Not duplicate any of these existing titles: ${JSON.stringify(existingTitles)}
        
        Return ONLY a JSON array of strings (the titles), nothing else.
        Example format: ["Similar Title 1", "Similar Title 2", "Similar Title 3"]
    `;

    try {
        const model = genAI.getGenerativeModel({ model: textModel });
        const result = await model.generateContent(prompt);
        const response = await result.response;
        return response.text();
    } catch (error) {
        console.error("Similar idea generation failed:", error);
        throw new Error("Failed to generate similar ideas. Please check your API key and try again.");
    }
};

/**
 * Creates a full blog post draft based on an idea and style guide.
 * @param idea The content idea/title
 * @param styleGuide JSON string of the style guide
 * @param publishedPostContext Array of published posts for context
 * @returns A JSON string with the draft content
 */
const createDraft = async (idea: string, styleGuide: string, publishedPostContext: Array<{ title: string, url: string, content: string }>): Promise<string> => {
    const genAI = checkAi();
    const contextString = publishedPostContext.length > 0 
        ? `Here are some recently published posts for context:\n${publishedPostContext.map(p => `Title: ${p.title}\nURL: ${p.url}\nContent snippet: ${p.content.substring(0, 500)}...`).join('\n\n')}`
        : '';

    const prompt = `
        Create a comprehensive blog post based on this idea: "${idea}"
        
        Use this style guide: ${styleGuide}
        
        ${contextString}
        
        The blog post should be:
        - Well-structured with clear headings and subheadings
        - Engaging and informative
        - 800-1500 words in length
        - SEO-optimized
        - Match the tone and style described in the style guide
        
        Return a JSON object with this exact structure:
        {
          "content": "The full HTML content of the blog post with proper headings (H2, H3), paragraphs, and formatting",
          "metaTitle": "SEO-optimized title (50-60 characters)",
          "metaDescription": "Compelling meta description (150-160 characters)",
          "focusKeywords": ["keyword1", "keyword2", "keyword3"]
        }
    `;

    try {
        const model = genAI.getGenerativeModel({ model: textModel });
        const result = await model.generateContent(prompt);
        const response = await result.response;
        return response.text();
    } catch (error) {
        console.error("Draft creation failed:", error);
        throw new Error("Failed to create draft. Please check your API key and try again.");
    }
};

/**
 * Generates an image based on a title and style preference.
 * @param title The blog post title
 * @param style The preferred image style
 * @returns Base64 encoded image string
 */
const generateImage = async (title: string, style: AiImageStyle): Promise<string> => {
    const genAI = checkAi();
    
    const stylePrompts = {
        photorealistic: "photorealistic, high quality, professional photography",
        digital_art: "digital art, illustration, creative, artistic"
    };

    const prompt = `Create a ${stylePrompts[style]} image for a blog post titled: "${title}". The image should be relevant to the topic, visually appealing, and suitable for use as a featured image on a professional blog.`;

    try {
        const model = genAI.getGenerativeModel({ model: imageModel });
        const result = await model.generateContent(prompt);
        const response = await result.response;
        
        // Note: This is a simplified implementation. In reality, you'd need to handle
        // the image generation response properly and convert it to base64.
        // For now, we'll return a placeholder that indicates the image generation process.
        const imageData = response.text();
        
        // In a real implementation, you would process the actual image data here
        // and return the base64 encoded string
        return imageData;
    } catch (error) {
        console.error("Image generation failed:", error);
        throw new Error("Failed to generate image. Please check your API key and try again.");
    }
};

export const geminiService: AiService = {
    analyzeStyle,
    generateIdeas,
    generateSimilarIdeas,
    createDraft,
    generateImage
};