
import type { AiImageStyle } from '../types';

export interface AiService {
    analyzeStyle(): Promise<string>;
    generateIdeas(styleGuide: string, existingTitles: string[], count?: number, searchConsoleData?: { topQueries: string[], underperformingPages: string[] }): Promise<string>;
    generateSimilarIdeas(baseIdea: string, existingTitles: string[]): Promise<string>;
    createDraft(idea: string, styleGuide: string, publishedPostContext: Array<{ title: string, url: string, content: string }>): Promise<string>;
    generateImage(title: string, style: AiImageStyle): Promise<string>;
}
