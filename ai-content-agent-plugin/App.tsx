
import React, { useState, useCallback, useEffect } from 'react';
import { settingsApi, styleGuideApi, ideasApi, draftsApi, publishedApi, activityApi } from './services/wordpressApi';
import { setGeminiApiKey } from './services/geminiService';
import type { StyleGuide, ContentIdea, Draft, View, AppSettings, ActivityLog, ActivityLogType, IconName } from './types';
import { Sidebar } from './components/Sidebar';
import { Dashboard } from './components/Dashboard';
import { StyleGuideManager } from './components/StyleGuideManager';
import { IdeaBoard } from './components/IdeaBoard';
import { DraftsList } from './components/DraftsList';
import { Settings } from './components/Settings';
import { DraftModal } from './components/DraftModal';
import { Toast, ToastData } from './components/Toast';
import { PublishedList } from './components/PublishedList';
import { Menu } from './components/Icons';
import { ContentCalendar } from './components/ContentCalendar';


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
        imageSourceProvider: 'ai',
        aiImageStyle: 'photorealistic',
        pexelsApiKey: '',
        unsplashApiKey: '',
        pixabayApiKey: '',
        seoPlugin: 'none',
        geminiApiKey: '',
    });
    const [isLoading, setIsLoading] = useState<{ [key: string]: boolean }>({});
    const [toasts, setToasts] = useState<ToastData[]>([]);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [publishingId, setPublishingId] = useState<number | null>(null);


    const drafts = posts.filter(p => p.status === 'draft');
    const publishedPosts = posts.filter(p => p.status === 'published');

    useEffect(() => {
        setGeminiApiKey(settings.geminiApiKey);
    }, [settings.geminiApiKey]);

    // Load initial data from WordPress
    useEffect(() => {
        const loadInitialData = async () => {
            try {
                const [settingsData, styleGuideData, ideasData, draftsData, publishedData, activityData] = await Promise.all([
                    settingsApi.get(),
                    styleGuideApi.get(),
                    ideasApi.get(),
                    draftsApi.get(),
                    publishedApi.get(),
                    activityApi.get()
                ]);
                
                setSettings(settingsData || {
                    mode: 'manual',
                    autoPublish: false,
                    searchConsoleUser: null,
                    imageSourceProvider: 'ai',
                    aiImageStyle: 'photorealistic',
                    pexelsApiKey: '',
                    unsplashApiKey: '',
                    pixabayApiKey: '',
                    seoPlugin: 'none',
                    geminiApiKey: '',
                });
                
                if (styleGuideData) {
                    setStyleGuide(styleGuideData);
                }
                
                setIdeas(ideasData || []);
                setPosts([...(draftsData || []), ...(publishedData || [])]);
                setActivityLogs(activityData || []);
            } catch (error) {
                console.error('Failed to load initial data:', error);
                addToast({ message: 'Failed to load plugin data', type: 'error' });
            }
        };
        
        loadInitialData();
    }, []);

    const addToast = useCallback((toast: Omit<ToastData, 'id'>) => {
        const id = Date.now() + Math.random();
        setToasts(currentToasts => [...currentToasts, { ...toast, id }]);
    }, []);
    
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
                message: `Draft "${draft.title}" created successfully with ${draft.tags?.length || 0} tags and ${draft.categories?.length || 0} categories!`, 
                type: 'success' 
            });
            addLogEntry('draft_created', `Created draft: "${draft.title}" with full WordPress integration`, 'FileText');
        } catch (error) {
            console.error('Error creating draft:', error);
            const errorMessage = error instanceof Error ? error.message : 'Failed to create draft';
            addToast({ message: `Failed to create draft: ${errorMessage}`, type: 'error' });
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

    const handleScheduleDraft = useCallback(async (draftId: number, scheduledDate: string) => {
        try {
            const scheduledDraft = await draftsApi.schedule(draftId, scheduledDate);
            setPosts(prev => prev.map(p => p.id === draftId ? scheduledDraft : p));
            addToast({ message: 'Draft scheduled successfully!', type: 'success' });
            addLogEntry('draft_scheduled', `Scheduled draft: "${scheduledDraft.title}" for ${new Date(scheduledDate).toLocaleDateString()}`, 'Calendar');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to schedule draft';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [addToast, addLogEntry]);

    const handleArchiveIdea = useCallback(async (ideaId: number) => {
        try {
            const idea = ideas.find(i => i.id === ideaId);
            if (!idea) return;

            const updatedIdea = { ...idea, status: 'archived' as const };
            await ideasApi.update(ideaId, updatedIdea);
            setIdeas(prev => prev.map(i => i.id === ideaId ? updatedIdea : i));
            addToast({ message: 'Idea archived successfully!', type: 'success' });
            addLogEntry('idea_archived', `Archived idea: "${idea.title}"`, 'Archive');
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'Failed to archive idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    }, [ideas, addToast, addLogEntry]);

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
                return <IdeaBoard ideas={ideas} onGenerate={() => handleGenerateIdeas(false, 5)} onCreateDraft={(idea) => handleCreateDraft(idea.id)} onArchive={handleArchiveIdea} isLoading={isLoading['ideas']} isLoadingDraft={isLoading} onUpdateTitle={handleUpdateIdeaTitle} onGenerateSimilar={handleGenerateSimilarIdeas} onAddIdea={handleAddIdea} />;
            case 'drafts':
                return <DraftsList drafts={drafts} onSelectDraft={setSelectedDraft} onPublish={handlePublishPost} publishingId={publishingId} />;
            case 'published':
                return <PublishedList posts={publishedPosts} onSelectPost={setSelectedDraft} />;
            case 'settings':
                return <Settings settings={settings} onSaveSettings={handleSaveSettings} />;
            case 'calendar':
                return <ContentCalendar drafts={drafts} publishedPosts={publishedPosts} onScheduleDraft={handleScheduleDraft} onSelectPost={setSelectedDraft} />;
            case 'dashboard':
            default:
                return <Dashboard
                    stats={{ ideas: ideas.length, drafts: drafts.length, published: publishedPosts.length }}
                    lastAnalyzed={styleGuide?.lastAnalyzed}
                    activityLogs={activityLogs}
                    onNavigate={setView}
                    onGenerateIdeas={() => handleGenerateIdeas(false, 5)}
                    onUpdateStyleGuide={() => handleAnalyzeStyle(false)}
                    isLoadingIdeas={isLoading['ideas'] || false}
                    isLoadingStyle={isLoading['style'] || false}
                />;
        }
    };

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
                        <span className="font-semibold text-white">AI Content Agent</span>
                    </div>
                    
                    {/* Page content */}
                    <div className="aca-fade-in">
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