export type View = 'dashboard' | 'style-guide' | 'ideas' | 'drafts' | 'published' | 'settings' | 'calendar';

export interface StyleGuide {
    tone: string;
    sentenceStructure: string;
    paragraphLength: string;
    formattingStyle: string;
    customInstructions?: string;
    lastAnalyzed?: string;
}

export type IdeaSource = 'ai' | 'search-console' | 'similar' | 'manual';

export interface ContentIdea {
    id: number;
    title: string;
    status: 'new' | 'archived';
    source: IdeaSource;
}

export interface Draft {
    id: number;
    title: string;
    content: string;
    metaTitle: string;
    metaDescription: string;
    focusKeywords: string[];
    featuredImage: string;
    createdAt: string;
    status: 'draft' | 'published';
    publishedAt?: string;
    url?: string;
    scheduledFor?: string;
}

export type AutomationMode = 'manual' | 'semi-automatic' | 'full-automatic';
export type ImageSourceProvider = 'ai' | 'pexels' | 'unsplash' | 'pixabay';
export type AiImageStyle = 'digital_art' | 'photorealistic';
export type SeoPlugin = 'none' | 'rank_math' | 'yoast';


export interface AppSettings {
    mode: AutomationMode;
    autoPublish: boolean;
    searchConsoleUser: { email: string } | null;
    imageSourceProvider: ImageSourceProvider;
    aiImageStyle: AiImageStyle;
    pexelsApiKey: string;
    unsplashApiKey: string;
    pixabayApiKey: string;
    seoPlugin: SeoPlugin;
    geminiApiKey?: string;
}

export type ActivityLogType = 
    | 'style_updated' 
    | 'ideas_generated' 
    | 'draft_created' 
    | 'post_published' 
    | 'settings_saved'
    | 'idea_archived'
    | 'idea_title_updated'
    | 'draft_updated'
    | 'draft_scheduled'
    | 'idea_added';

export type IconName = 'BookOpen' | 'Lightbulb' | 'FileText' | 'Send' | 'Settings' | 'Trash' | 'Pencil' | 'Calendar' | 'Sparkles' | 'PlusCircle';

export interface ActivityLog {
    id: number;
    timestamp: string;
    type: ActivityLogType;
    details: string;
    icon: IconName;
}

// WordPress-specific types
declare global {
    interface Window {
        acaData: {
            restUrl: string;
            nonce: string;
            adminUrl: string;
            pluginUrl: string;
        };
    }
}