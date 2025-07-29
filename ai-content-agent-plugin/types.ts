
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
    description?: string;
    status: 'active' | 'archived';
    source: IdeaSource;
    createdAt: string;
    tags: string[];
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
export type SeoPlugin = 'none' | 'rank_math' | 'yoast'; // Kept for backward compatibility


export interface AppSettings {
    mode: AutomationMode;
    autoPublish: boolean;
    searchConsoleUser: { email: string } | null;
    gscClientId: string;
    gscClientSecret: string;
    imageSourceProvider: ImageSourceProvider;
    aiImageStyle: AiImageStyle;
    googleCloudProjectId?: string;
    googleCloudLocation?: string;
    pexelsApiKey: string;
    unsplashApiKey: string;
    pixabayApiKey: string;
    seoPlugin: SeoPlugin;
    geminiApiKey: string;
    // Automation frequency settings
    semiAutoIdeaFrequency?: 'daily' | 'weekly' | 'monthly';
    fullAutoDailyPostCount?: number;
    fullAutoPublishFrequency?: 'hourly' | 'daily' | 'weekly';
    analyzeContentFrequency?: 'manual' | 'daily' | 'weekly' | 'monthly';
}

export type ActivityLogType = 
    | 'style_updated' 
    | 'style_analyzed'
    | 'ideas_generated' 
    | 'similar_ideas_generated'
    | 'draft_created' 
    | 'post_published' 
    | 'settings_updated'
    | 'idea_archived'
    | 'idea_updated'
    | 'draft_updated'
    | 'draft_scheduled'
    | 'idea_added';

export type IconName = 'BookOpen' | 'Lightbulb' | 'FileText' | 'Send' | 'Settings' | 'Archive' | 'Edit' | 'Calendar' | 'Sparkles' | 'PlusCircle' | 'Trash' | 'Pencil';

export interface ActivityLog {
    id: number;
    timestamp: string;
    type: ActivityLogType;
    details: string;
    icon: IconName;
}