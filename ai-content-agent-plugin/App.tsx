
import React, { useState, useCallback, useEffect } from 'react';
import { settingsApi, styleGuideApi, ideasApi, draftsApi, publishedApi, activityApi, contentFreshnessApi } from './services/wordpressApi';
import { setGeminiApiKey } from './services/geminiService';
import type { StyleGuide, ContentIdea, Draft, View, AppSettings, ActivityLog, ActivityLogType, IconName } from './types';
import { GeminiApiWarning } from './components/GeminiApiWarning';
import { Sidebar } from './components/Sidebar';
import { Dashboard } from './components/Dashboard';
import { StyleGuideManager } from './components/StyleGuideManager';
import { IdeaBoard } from './components/IdeaBoard';
import { DraftsList } from './components/DraftsList';
import { Settings } from './components/Settings';
import { SettingsLicense } from './components/SettingsLicense';
import { SettingsAutomation } from './components/SettingsAutomation';
import { SettingsContent } from './components/SettingsContent';
import { SettingsAdvanced } from './components/SettingsAdvanced';
import { DraftModal } from './components/DraftModal';
import { Toast, ToastData } from './components/Toast';
import { PublishedList } from './components/PublishedList';
import { Menu } from './components/Icons';
import { ContentCalendar } from './components/ContentCalendar';
import { ContentFreshnessManager } from './components/ContentFreshnessManager';

declare global {
  interface Window {
    acaData: {
      nonce: string;
      api_url: string;
      admin_url: string;
      plugin_url: string;
    };
  }
}

const App: React.FC = () => {
    const [view, setView] = useState<View>('dashboard');
    const [styleGuide, setStyleGuide] = useState<StyleGuide | null>(null);
    const [ideas, setIdeas] = useState<ContentIdea[]>([]);
    const [posts, setPosts] = useState<Draft[]>([]);
    const [selectedDraft, setSelectedDraft] = useState<Draft | null>(null);
    const [activityLogs, setActivityLogs] = useState<ActivityLog[]>([]);
    const [settings, setSettings] = useState<AppSettings>({
        mode: 'manual',
        autoPublish: false,
        searchConsoleUser: null,
        gscClientId: '',
        gscClientSecret: '',
        imageSourceProvider: 'pexels', // Changed default to simplest option
        aiImageStyle: 'photorealistic',
        googleCloudProjectId: '',
        googleCloudLocation: 'us-central1',
        pexelsApiKey: '',
        unsplashApiKey: '',
        pixabayApiKey: '',
        seoPlugin: 'none', // Auto-detected, kept for backward compatibility
        geminiApiKey: '',
        // Automation frequency settings with defaults
        semiAutoIdeaFrequency: 'weekly',
        fullAutoDailyPostCount: 1,
        fullAutoPublishFrequency: 'daily',
        analyzeContentFrequency: 'manual',
    });
    const [isLoading, setIsLoading] = useState<{ [key: string]: boolean }>({});
    const [toasts, setToasts] = useState<ToastData[]>([]);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [publishingId, setPublishingId] = useState<number | null>(null);
    const [contentFreshness, setContentFreshness] = useState<{
        total: number;
        needsUpdate: number;
        averageScore: number;
    } | null>(null);
    const [settingsOpenSection, setSettingsOpenSection] = useState<string | undefined>(undefined);

    const drafts = posts.filter(p => p.status === 'draft');
    const publishedPosts = posts.filter(p => p.status === 'published');
    
    // Check if Gemini API key is configured
    const isGeminiApiConfigured = !!(settings.geminiApiKey && settings.geminiApiKey.trim());

    // Clear openSection when view changes away from settings
    useEffect(() => {
        if (view !== 'settings') {
            setSettingsOpenSection(undefined);
        }
    }, [view]);

    const addToast = useCallback((toast: Omit<ToastData, 'id'>) => {
        const id = Date.now();
        setToasts(prev => [...prev, { ...toast, id }]);
    }, []);

    const showToast = useCallback((message: string, type: 'success' | 'error' | 'warning' | 'info') => {
        addToast({ message, type });
    }, [addToast]);
    
    const addLogEntry = useCallback((type: ActivityLogType, details: string, icon: IconName) => {
        const newLog: ActivityLog = {
            id: Date.now(),
            type,
            details,
            timestamp: new Date().toISOString(),
            icon
        };
        setActivityLogs(prev => [newLog, ...prev.slice(0, 49)]);
        
        // Save to WordPress with correct parameter names
        activityApi.create({
            type,
            message: details,
            icon
        }).catch(console.error);
    }, []);

    const removeToast = useCallback((id: number) => {
        setToasts(currentToasts => currentToasts.filter(toast => toast.id !== id));
    }, []);

    const handleAnalyzeStyle = useCallback(async (showToast = true) => {
        setIsLoading(prev => ({ ...prev, style: true }));
        try {
            const updatedStyleGuide = await styleGuideApi.analyze();
            setStyleGuide(updatedStyleGuide);
            if (showToast) {
                addToast({ message: 'Style guide updated successfully!', type: 'success' });
            }
            addLogEntry('style_analyzed', 'Style guide analyzed and updated', 'BookOpen');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to analyze style guide';
            if (showToast) {
                addToast({ message: errorMessage, type: 'error' });
            }
        } finally {
            setIsLoading(prev => ({ ...prev, style: false }));
        }
    }, [addToast, addLogEntry]);

    const handleSaveStyleGuide = useCallback(async (updatedGuide: Partial<StyleGuide>) => {
        try {
            const savedGuide = await styleGuideApi.update(updatedGuide);
            setStyleGuide(savedGuide);
            addToast({ message: 'Style guide saved successfully!', type: 'success' });
            addLogEntry('style_updated', 'Style guide manually updated', 'BookOpen');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to save style guide';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [addToast, addLogEntry]);

    const handleGenerateIdeas = useCallback(async (showToast = true, count = 5) => {
        if (!styleGuide) {
            addToast({ message: 'Please create a style guide first', type: 'warning' });
            return;
        }

        setIsLoading(prev => ({ ...prev, ideas: true }));
        try {
            const newIdeas = await ideasApi.generate(count);
            setIdeas(prev => [...newIdeas, ...prev]);
            if (showToast) {
                addToast({ message: `Generated ${newIdeas.length} new ideas!`, type: 'success' });
            }
            addLogEntry('ideas_generated', `Generated ${newIdeas.length} new content ideas`, 'Lightbulb');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to generate ideas';
            if (showToast) {
                addToast({ message: errorMessage, type: 'error' });
            }
        } finally {
            setIsLoading(prev => ({ ...prev, ideas: false }));
        }
    }, [styleGuide, addToast, addLogEntry]);

    const handleGenerateSimilarIdeas = useCallback(async (baseIdea: ContentIdea) => {
        if (!styleGuide) {
            addToast({ message: 'Please create a style guide first', type: 'warning' });
            return;
        }

        setIsLoading(prev => ({ ...prev, [`similar_${baseIdea.id}`]: true }));
        try {
            const similarIdeas = await ideasApi.generateSimilar(baseIdea.id);
            setIdeas(prev => [...similarIdeas, ...prev]);
            addToast({ message: `Generated ${similarIdeas.length} similar ideas!`, type: 'success' });
            addLogEntry('similar_ideas_generated', `Generated ${similarIdeas.length} ideas similar to "${baseIdea.title}"`, 'Lightbulb');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to generate similar ideas';
            addToast({ message: errorMessage, type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, [`similar_${baseIdea.id}`]: false }));
        }
    }, [styleGuide, addToast, addLogEntry]);

    const handleCreateDraft = useCallback(async (ideaId: number) => {
        if (!styleGuide) {
            addToast({ message: 'Please create a style guide first', type: 'warning' });
            return;
        }

        const idea = ideas.find(i => i.id === ideaId);
        if (!idea) {
            addToast({ message: 'Idea not found', type: 'error' });
            return;
        }

        console.log('Creating draft for idea:', idea);
        setIsLoading(prev => ({ ...prev, [`draft_${ideaId}`]: true }));
        
        // Show loading toast
        addToast({ 
            message: `🤖 AI is generating draft for "${idea.title}"... This may take a moment.`, 
            type: 'info' 
        });
        
        try {
            console.log('Calling draftsApi.createFromIdea with ideaId:', ideaId);
            const draft = await draftsApi.createFromIdea(ideaId);
            console.log('Draft created successfully:', draft);
            
            setPosts(prev => [draft, ...prev]);
            
            // Archive the idea
            const updatedIdea = { ...idea, status: 'archived' as const };
            await ideasApi.update(ideaId, updatedIdea);
            setIdeas(prev => prev.map(i => i.id === ideaId ? updatedIdea : i));
            
            addToast({ 
                message: `Draft "${draft.title}" created successfully! Check WordPress admin for the post.`, 
                type: 'success' 
            });
            addLogEntry('draft_created', `Created draft: "${draft.title}" with full WordPress integration`, 'FileText');
        } catch (error) {
            console.error('Error creating draft:', error);
            
            // Parse error message for better user feedback
            let errorMessage = 'Failed to create draft. Please try again.';
            
            if (error instanceof Error) {
                const message = error.message.toLowerCase();
                
                if (message.includes('503') || message.includes('overloaded') || message.includes('unavailable')) {
                    errorMessage = '🤖 AI service is temporarily overloaded. Please wait a moment and try again.';
                } else if (message.includes('timeout')) {
                    errorMessage = '⏱️ Request timed out. The AI service might be busy. Please try again.';
                } else if (message.includes('api key')) {
                    errorMessage = '🔑 AI API key is not configured. Please check your settings.';
                } else if (message.includes('style guide')) {
                    errorMessage = '📋 Style guide is required. Please create one first.';
                } else if (message.includes('after') && message.includes('attempts')) {
                    errorMessage = '🔄 AI service is currently unavailable after multiple attempts. Please try again in a few minutes.';
                } else if (message.includes('ai content generation failed')) {
                    errorMessage = '🤖 AI content generation failed. The service might be temporarily unavailable. Please try again.';
                }
            }
            
            addToast({ message: errorMessage, type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, [`draft_${ideaId}`]: false }));
        }
    }, [ideas, styleGuide, addToast, addLogEntry]);

    const handleUpdateDraft = useCallback(async (draftId: number, updates: Partial<Draft>) => {
        try {
            const updatedDraft = await draftsApi.update(draftId, updates);
            setPosts(prev => prev.map(p => p.id === draftId ? updatedDraft : p));
            addToast({ message: 'Draft updated successfully!', type: 'success' });
            addLogEntry('draft_updated', `Updated draft: "${updatedDraft.title}"`, 'FileText');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to update draft';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [addToast, addLogEntry]);

    const handlePublishPost = useCallback(async (draftId: number) => {
        setPublishingId(draftId);
        try {
            const publishedPost = await publishedApi.publish(draftId);
            setPosts(prev => prev.map(p => p.id === draftId ? publishedPost : p));
            addToast({ message: 'Post published successfully!', type: 'success' });
            addLogEntry('post_published', `Published post: "${publishedPost.title}"`, 'Send');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to publish post';
            addToast({ message: errorMessage, type: 'error' });
        } finally {
            setPublishingId(null);
        }
    }, [addToast, addLogEntry]);

    const handleUpdatePostDate = useCallback(async (postId: number, newDate: string, shouldConvertToDraft: boolean = false) => {
        try {
            const updatedPost = await publishedApi.updateDate(postId, newDate, shouldConvertToDraft);
            
            // Update the posts state
            setPosts(prev => prev.map(p => p.id === postId ? updatedPost : p));
            
            const action = shouldConvertToDraft ? 'converted to scheduled draft' : 'publish date updated';
            addToast({ message: `Post ${action} successfully!`, type: 'success' });
            addLogEntry('draft_updated', `Post "${updatedPost.title}" ${action}`, 'Calendar');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to update post date';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [addToast, addLogEntry]);

    const handleScheduleDraft = useCallback(async (draftId: number, scheduledDate: string) => {
        try {
            const updatedDraft = await draftsApi.schedule(draftId, scheduledDate);
            
            // Update the posts array with the properly scheduled draft
            setPosts(prev => prev.map(p => p.id === draftId ? updatedDraft : p));
            
            const formattedDate = new Date(scheduledDate).toLocaleDateString();
            const draftTitle = updatedDraft.title || `Draft ${draftId}`;
            
            addToast({ message: `Draft "${draftTitle}" scheduled for ${formattedDate}!`, type: 'success' });
            addLogEntry('draft_scheduled', `Scheduled draft: "${draftTitle}" for ${formattedDate}`, 'Calendar');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to schedule draft';
            addToast({ message: errorMessage, type: 'error' });
            console.error('Error scheduling draft:', error);
        }
    }, [addToast, addLogEntry]);

    const handleArchiveIdea = useCallback(async (ideaId: number) => {
        try {
            const idea = ideas.find(i => i.id === ideaId);
            if (!idea) return;

            // Use delete API which now archives the idea
            await ideasApi.delete(ideaId);
            const updatedIdea = { ...idea, status: 'archived' as const };
            setIdeas(prev => prev.map(i => i.id === ideaId ? updatedIdea : i));
            addToast({ message: 'Idea archived successfully!', type: 'success' });
            addLogEntry('idea_archived', `Archived idea: "${idea.title}"`, 'Archive');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to archive idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [ideas, addToast, addLogEntry]);

    const handleDeleteIdea = useCallback(async (ideaId: number) => {
        try {
            const idea = ideas.find(i => i.id === ideaId);
            if (!idea) return;

            // Use permanent delete API
            await ideasApi.permanentDelete(ideaId);
            setIdeas(prev => prev.filter(i => i.id !== ideaId));
            addToast({ message: 'Idea deleted permanently!', type: 'success' });
            addLogEntry('idea_updated', `Permanently deleted idea: "${idea.title}"`, 'Trash');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to delete idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [ideas, addToast, addLogEntry]);

    const handleRestoreIdea = useCallback(async (ideaId: number) => {
        try {
            const idea = ideas.find(i => i.id === ideaId);
            if (!idea) return;

            // Use restore API
            await ideasApi.restore(ideaId);
            const updatedIdea = { ...idea, status: 'active' as const };
            setIdeas(prev => prev.map(i => i.id === ideaId ? updatedIdea : i));
            addToast({ message: 'Idea restored successfully!', type: 'success' });
            addLogEntry('idea_restored', `Restored idea: "${idea.title}"`, 'Edit');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to restore idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [ideas, addToast, addLogEntry]);

    const handleGenerateIdeasAndNavigate = useCallback(async () => {
        await handleGenerateIdeas(false, 5);
        // Navigate to ideas view after successful generation
        setView('ideas');
    }, [handleGenerateIdeas]);

    const handleUpdateIdeaTitle = useCallback(async (ideaId: number, newTitle: string) => {
        try {
            const idea = ideas.find(i => i.id === ideaId);
            if (!idea) return;

            const updatedIdea = { ...idea, title: newTitle };
            await ideasApi.update(ideaId, updatedIdea);
            setIdeas(prev => prev.map(i => i.id === ideaId ? updatedIdea : i));
            addToast({ message: 'Idea updated successfully!', type: 'success' });
            addLogEntry('idea_updated', `Updated idea title to: "${newTitle}"`, 'Edit');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to update idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [ideas, addToast, addLogEntry]);

    const handleSaveSettings = useCallback(async (newSettings: AppSettings) => {
        try {
            await settingsApi.update(newSettings);
            setSettings(newSettings);
            addToast({ message: 'Settings saved successfully!', type: 'success' });
            addLogEntry('settings_updated', 'Plugin settings updated', 'Settings');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to save settings';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [addToast, addLogEntry]);

    const handleRefreshApp = useCallback(async () => {
        // Reload app data individually to avoid Promise.all failure cascade
        let refreshErrors = [];
        
        // Refresh settings (most important for license changes)
        try {
            const settingsData = await settingsApi.get();
            setSettings(settingsData || {
                mode: 'manual',
                autoPublish: false,
                searchConsoleUser: null,
                gscClientId: '',
                gscClientSecret: '',
                gscSiteUrl: '',
                geminiApiKey: '',
                is_pro: false,
                seoPlugin: 'none',
                imageSource: 'none',
                aiImageStyle: 'photographic'
            });
        } catch (error) {
            console.error('Failed to refresh settings:', error);
            refreshErrors.push('settings');
        }
        
        // Refresh other data (less critical)
        try {
            const ideasData = await ideasApi.get();
            setIdeas(ideasData || []);
        } catch (error) {
            console.error('Failed to refresh ideas:', error);
            refreshErrors.push('ideas');
        }
        
        // Refresh drafts and published posts
        let draftsData = [];
        let publishedData = [];
        
        try {
            draftsData = await draftsApi.get();
        } catch (error) {
            console.error('Failed to refresh drafts:', error);
            refreshErrors.push('drafts');
        }
        
        try {
            publishedData = await publishedApi.get();
        } catch (error) {
            console.error('Failed to refresh published posts:', error);
            refreshErrors.push('published');
        }
        
        // Combine drafts and published posts like in initial load
        setPosts([...(draftsData || []), ...(publishedData || [])]);
        
        try {
            const activityData = await activityApi.get();
            setActivityLogs(activityData || []);
        } catch (error) {
            console.error('Failed to refresh activity logs:', error);
            refreshErrors.push('activity');
        }
        
        // Only show error if critical data (settings) failed
        if (refreshErrors.includes('settings')) {
            addToast({ message: 'Failed to refresh app settings', type: 'error' });
        } else if (refreshErrors.length > 0) {
            // Non-critical errors, just log them
            console.log('Some non-critical data failed to refresh:', refreshErrors);
        }
        
        // Don't show success toast to reduce notification spam
        // The license success toast is already shown
    }, [addToast]);

    const handleAddIdea = useCallback(async (title: string, description?: string) => {
        try {
            const newIdea: Omit<ContentIdea, 'id'> = {
                title: title.trim(),
                description: description?.trim() || '',
                status: 'active',
                createdAt: new Date().toISOString(),
                tags: []
            };
            
            const createdIdea = await ideasApi.create(newIdea);
            setIdeas(prev => [createdIdea, ...prev]);
            addToast({ message: 'Idea added successfully!', type: 'success' });
            addLogEntry('idea_added', `Manually added idea: "${title.trim()}"`, 'PlusCircle');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to add idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [addToast, addLogEntry]);


    const renderView = () => {
        switch (view) {
            case 'style-guide':
                return <StyleGuideManager styleGuide={styleGuide} onAnalyze={() => handleAnalyzeStyle(false)} onSaveStyleGuide={handleSaveStyleGuide} isLoading={isLoading['style']} />;
            case 'ideas':
                return <IdeaBoard 
                    ideas={ideas} 
                    onGenerate={() => handleGenerateIdeas(false, 5)} 
                    onCreateDraft={(idea) => handleCreateDraft(idea.id)} 
                    onArchive={handleArchiveIdea}
                    onDeleteIdea={handleDeleteIdea}
                    onRestoreIdea={handleRestoreIdea}
                    isLoading={isLoading['ideas']} 
                    isLoadingDraft={isLoading} 
                    onUpdateTitle={handleUpdateIdeaTitle} 
                    onGenerateSimilar={handleGenerateSimilarIdeas} 
                    onAddIdea={handleAddIdea} 
                />;
            case 'drafts':
                return <DraftsList 
                    drafts={drafts} 
                    onSelectDraft={setSelectedDraft} 
                    onPublish={handlePublishPost} 
                    publishingId={publishingId}
                    onNavigateToIdeas={() => setView('ideas')}
                />;
            case 'published':
                return <PublishedList 
                    posts={publishedPosts} 
                    onSelectPost={setSelectedDraft}
                    onNavigateToDrafts={() => setView('drafts')}
                />;
            case 'settings':
                // Redirect to license page as default
                return <SettingsLicense 
                    settings={settings} 
                    onSaveSettings={handleSaveSettings} 
                    onShowToast={showToast} 
                />;
            case 'settings_license':
                return <SettingsLicense 
                    settings={settings} 
                    onSaveSettings={handleSaveSettings} 
                    onShowToast={showToast} 
                />;
                         case 'settings_automation':
                 return <SettingsAutomation 
                     settings={settings} 
                     onSaveSettings={handleSaveSettings} 
                     onShowToast={showToast} 
                     isProActive={settings.is_pro}
                 />;
            case 'settings_integrations':
                return <Settings 
                    settings={settings} 
                    onSaveSettings={handleSaveSettings} 
                    onRefreshApp={handleRefreshApp} 
                    onShowToast={showToast} 
                    openSection="integrations"
                />;
                         case 'settings_content':
                 return <SettingsContent 
                     settings={settings} 
                     onSaveSettings={handleSaveSettings} 
                     onShowToast={showToast} 
                 />;
             case 'settings_advanced':
                 return <SettingsAdvanced 
                     onShowToast={showToast} 
                 />;
            case 'calendar':
                return <ContentCalendar 
                    drafts={drafts} 
                    publishedPosts={publishedPosts} 
                    onScheduleDraft={handleScheduleDraft} 
                    onSelectPost={setSelectedDraft}
                    onPublishDraft={handlePublishPost}
                    onUpdatePostDate={handleUpdatePostDate}
                />;
            case 'content-freshness':
                return <ContentFreshnessManager 
                    onShowToast={showToast}
                    settings={settings}
                />;
            case 'dashboard':
            default:
                return <Dashboard
                    stats={{ 
                        ideas: ideas.filter(idea => idea.status === 'active').length, 
                        drafts: drafts.length, 
                        published: publishedPosts.length,
                        contentFreshness: contentFreshness
                    }}
                    lastAnalyzed={styleGuide?.lastAnalyzed}
                    activityLogs={activityLogs}
                    onNavigate={setView}
                    onGenerateIdeas={handleGenerateIdeasAndNavigate}
                    onUpdateStyleGuide={() => handleAnalyzeStyle(false)}
                    isLoadingIdeas={isLoading['ideas'] || false}
                    isLoadingStyle={isLoading['style'] || false}
                />;
        }
    };

    useEffect(() => {
        // Check if WordPress localized data is available
        if (!window.acaData) {
            console.error('ACA Error: WordPress localized data not available');
            showToast('Plugin not properly loaded. Please refresh the page.', 'error');
            return;
        }
        
        setGeminiApiKey(settings.geminiApiKey);
    }, [settings.geminiApiKey, showToast]);

    // Load initial data from WordPress
    useEffect(() => {
        const loadInitialData = async () => {
            const results = await Promise.allSettled([
                settingsApi.get(),
                styleGuideApi.get(),
                ideasApi.get(),
                draftsApi.get(),
                publishedApi.get(),
                activityApi.get()
            ]);
            
            // Extract successful results and log failed ones
            const [settingsResult, styleGuideResult, ideasResult, draftsResult, publishedResult, activityResult] = results;
            
            let settingsData = null;
            let styleGuideData = null;
            let ideasData = null;
            let draftsData = null;
            let publishedData = null;
            let activityData = null;
            
            const failedLoads: string[] = [];
            
            if (settingsResult.status === 'fulfilled') {
                settingsData = settingsResult.value;
            } else {
                console.error('Failed to load settings:', settingsResult.reason);
                failedLoads.push('Settings');
            }
            
            if (styleGuideResult.status === 'fulfilled') {
                styleGuideData = styleGuideResult.value;
            } else {
                console.error('Failed to load style guide:', styleGuideResult.reason);
                failedLoads.push('Style Guide');
            }
            
            if (ideasResult.status === 'fulfilled') {
                ideasData = ideasResult.value;
            } else {
                console.error('Failed to load ideas:', ideasResult.reason);
                failedLoads.push('Ideas');
            }
            
            if (draftsResult.status === 'fulfilled') {
                draftsData = draftsResult.value;
            } else {
                console.error('Failed to load drafts:', draftsResult.reason);
                failedLoads.push('Drafts');
            }
            
            if (publishedResult.status === 'fulfilled') {
                publishedData = publishedResult.value;
            } else {
                console.error('Failed to load published posts:', publishedResult.reason);
                failedLoads.push('Published Posts');
            }
            
            if (activityResult.status === 'fulfilled') {
                activityData = activityResult.value;
            } else {
                console.error('Failed to load activity logs:', activityResult.reason);
                failedLoads.push('Activity Logs');
            }
            
            // Load content freshness data if Pro is active
            try {
                const freshnessResponse = await contentFreshnessApi.getPosts(50, 'all');
                if (freshnessResponse && freshnessResponse.success && freshnessResponse.posts) {
                    const posts = freshnessResponse.posts;
                    const needsUpdate = posts.filter(post => post.needs_update).length;
                    const postsWithScores = posts.filter(post => post.freshness_score !== null);
                    const averageScore = postsWithScores.length > 0 
                        ? Math.round(postsWithScores.reduce((sum, post) => sum + (post.freshness_score || 0), 0) / postsWithScores.length)
                        : 0;
                    
                    const analyzed = postsWithScores.filter(post => post.freshness_score !== null).length;
                    
                    setContentFreshness({
                        total: posts.length,
                        analyzed,
                        needsUpdate,
                        averageScore
                    });
                }
            } catch (error) {
                // Content freshness is a Pro feature, don't show error if not available
                console.log('Content freshness not available (Pro feature)');
            }
            
            // Set data with fallbacks for failed loads
            setSettings(settingsData || {
                mode: 'manual',
                autoPublish: false,
                searchConsoleUser: null,
                gscClientId: '',
                gscClientSecret: '',
                imageSourceProvider: 'pexels',
                aiImageStyle: 'photorealistic',
                pexelsApiKey: '',
                unsplashApiKey: '',
                pixabayApiKey: '',
                seoPlugin: 'none', // Auto-detected, kept for backward compatibility
                geminiApiKey: '',
                // Automation frequency settings with defaults
                semiAutoIdeaFrequency: 'weekly',
                fullAutoDailyPostCount: 1,
                fullAutoPublishFrequency: 'daily',
                analyzeContentFrequency: 'manual',
            });
            
            if (styleGuideData) {
                setStyleGuide(styleGuideData);
            }
            
            setIdeas(ideasData || []);
            setPosts([...(draftsData || []), ...(publishedData || [])]);
            setActivityLogs(activityData || []);
            
            // Show warning if some data failed to load
            if (failedLoads.length > 0) {
                const failedItems = failedLoads.join(', ');
                addToast({ 
                    message: `Some data could not be loaded: ${failedItems}. Plugin will work with available data.`, 
                    type: 'warning' 
                });
            }
        };
        
        loadInitialData();
    }, []);

    return (
        <>
            <div className="aca-container">
                {/* Mobile overlay */}
                <div 
                    className={`aca-overlay ${isSidebarOpen ? 'show' : ''}`}
                    onClick={() => setIsSidebarOpen(false)}
                />
                
                {/* Sidebar */}
                <Sidebar 
                    currentView={view} 
                    setView={setView} 
                    isOpen={isSidebarOpen} 
                    closeSidebar={() => setIsSidebarOpen(false)} 
                />
                
                {/* Main content */}
                <div className="aca-main">
                    {/* Mobile header */}
                    <div className="aca-mobile-header">
                        <button 
                            onClick={() => setIsSidebarOpen(true)} 
                            className="aca-menu-toggle"
                            aria-label="Open menu"
                        >
                            <Menu className="h-6 w-6" />
                        </button>
                        <span className="font-semibold text-white">AI Content Agent (ACA)</span>
                    </div>
                    
                    {/* Page content */}
                    <div className="aca-fade-in">
                        {/* Gemini API Warning - Show on all pages except Settings if API key is missing */}
                        {!isGeminiApiConfigured && view !== 'settings' && (
                            <GeminiApiWarning onNavigateToSettings={() => {
                                setSettingsOpenSection('integrations');
                                setView('settings');
                            }} />
                        )}
                        {renderView()}
                    </div>
                </div>
            </div>
            
            {/* Draft modal */}
            {selectedDraft && (
                <DraftModal
                    draft={selectedDraft}
                    onClose={() => setSelectedDraft(null)}
                    onSave={handleUpdateDraft}
                    settings={settings}
                />
            )}
            
            {/* Toast notifications */}
            <div className="aca-toast-container">
                {toasts.map(toast => (
                    <Toast key={toast.id} {...toast} onDismiss={removeToast} />
                ))}
            </div>
        </>
    );
};

export default App;