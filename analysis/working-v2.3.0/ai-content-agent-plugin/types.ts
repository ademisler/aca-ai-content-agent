
export type View = 'dashboard' | 'style-guide' | 'ideas' | 'drafts' | 'published' | 'settings' | 'calendar' | 'content-freshness';

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
    // Pro license status
    is_pro?: boolean;
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
    | 'idea_added'
    | 'content_freshness_analysis';

export type IconName = 'BookOpen' | 'Lightbulb' | 'FileText' | 'Send' | 'Settings' | 'Archive' | 'Edit' | 'Calendar' | 'Sparkles' | 'PlusCircle' | 'Trash' | 'Pencil';

export interface ActivityLog {
    id: number;
    timestamp: string;
    type: ActivityLogType;
    details: string;
    icon: IconName;
}

// Content Freshness Types
export interface FreshnessSettings {
    analysisFrequency: 'daily' | 'weekly' | 'monthly' | 'manual';
    autoUpdate: boolean;
    updateThreshold: number;
    enabled: boolean;
}

export interface FreshnessData {
    postId: number;
    freshnessScore: number;
    lastAnalyzed: string;
    needsUpdate: boolean;
    updatePriority: number;
}

export interface FreshnessAnalysis {
    score: number;
    needs_update: boolean;
    priority: number;
    suggestions: string[];
    age_score: number;
    seo_score: number;
    ai_score: number;
    days_old: number;
    outdated_information?: string[];
    seo_improvements?: string[];
    readability_improvements?: string[];
    content_gaps?: string[];
    technical_updates?: string[];
}

export interface PostWithFreshness {
    ID: number;
    post_title: string;
    post_date: string;
    post_modified: string;
    freshness_score: number | null;
    last_analyzed: string | null;
    needs_update: boolean | null;
    update_priority: number | null;
    analysis?: FreshnessAnalysis;
}

export interface ContentUpdate {
    postId: number;
    updates: {
        title?: string;
        content?: string;
        metaDescription?: string;
        focusKeywords?: string[];
    };
}