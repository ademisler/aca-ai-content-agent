import React, { useState, useCallback, useEffect } from 'react';
import { wordpressApiService } from './services/wordpressApiService';
import type { StyleGuide, ContentIdea, Draft, View, AppSettings, ActivityLog, ActivityLogType, IconName, IdeaSource } from './types';
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
        seoPlugin: 'none'
    });
    const [isLoading, setIsLoading] = useState<{ [key: string]: boolean }>({});
    const [toasts, setToasts] = useState<ToastData[]>([]);
    const [isSidebarOpen, setIsSidebarOpen] = useState(false);
    const [publishingId, setPublishingId] = useState<number | null>(null);

    const drafts = posts.filter(p => p.status === 'draft');
    const publishedPosts = posts.filter(p => p.status === 'published');

    const addToast = useCallback((toast: Omit<ToastData, 'id'>) => {
        const id = Date.now() + Math.random();
        setToasts(currentToasts => [...currentToasts, { ...toast, id }]);
    }, []);
    
    const removeToast = (id: number) => {
        setToasts(currentToasts => currentToasts.filter(t => t.id !== id));
    };

    // Load initial data on mount
    useEffect(() => {
        const loadInitialData = async () => {
            try {
                setIsLoading(prev => ({ ...prev, initial: true }));
                
                // Load all data in parallel
                const [
                    settingsData,
                    styleGuideData,
                    ideasData,
                    postsData,
                    logsData
                ] = await Promise.all([
                    wordpressApiService.getSettings(),
                    wordpressApiService.getStyleGuide(),
                    wordpressApiService.getIdeas(),
                    wordpressApiService.getPosts(),
                    wordpressApiService.getActivityLogs()
                ]);

                setSettings(settingsData);
                setStyleGuide(styleGuideData);
                setIdeas(ideasData);
                setPosts(postsData);
                setActivityLogs(logsData);
                
            } catch (error) {
                console.error('Failed to load initial data:', error);
                addToast({ message: 'Failed to load data. Please refresh the page.', type: 'error' });
            } finally {
                setIsLoading(prev => ({ ...prev, initial: false }));
            }
        };

        loadInitialData();
    }, [addToast]);

    const handleAnalyzeStyle = useCallback(async (isAuto: boolean = false) => {
        setIsLoading(prev => ({ ...prev, style: true }));
        try {
            const analysis = await wordpressApiService.analyzeStyle();
            setStyleGuide(analysis);
            
            if (!isAuto) {
                addToast({ message: 'Style Guide successfully updated!', type: 'success' });
                setView('style-guide');
            } else {
                addToast({ message: 'Style Guide automatically refreshed.', type: 'info' });
            }
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to analyze style. Check console for details.', type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, style: false }));
        }
    }, [addToast, setView]);

    const handleGenerateIdeas = async (isAuto: boolean = false, count: number = 5) => {
        if (!styleGuide) {
            addToast({ message: 'Please generate a Style Guide first.', type: 'warning' });
            return;
        }
        
        setIsLoading(prev => ({ ...prev, ideas: true }));
        try {
            const newIdeas = await wordpressApiService.generateIdeas(count, isAuto);
            
            if (newIdeas.length > 0) {
                setIdeas(prev => [...newIdeas, ...prev]);
                const message = isAuto ? `Semi-auto: ${newIdeas.length} new ideas generated!` : `${newIdeas.length} new ideas generated!`;
                addToast({ message, type: isAuto ? 'info' : 'success' });
                
                if (!isAuto) {
                    setView('ideas');
                }
            } else if (!isAuto) {
                addToast({ message: 'No new unique ideas could be generated. Try again later.', type: 'warning' });
            }
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to generate ideas. Check console for details.', type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, ideas: false }));
        }
    };
    
    const handleGenerateSimilarIdeas = async (baseIdea: ContentIdea) => {
        setIsLoading(prev => ({ ...prev, [`similar-${baseIdea.id}`]: true }));
        try {
            const similarIdeas = await wordpressApiService.generateSimilarIdeas(baseIdea.title);
            
            if (similarIdeas.length > 0) {
                setIdeas(prev => [...similarIdeas, ...prev]);
                addToast({ message: `Generated ${similarIdeas.length} ideas similar to "${baseIdea.title}"`, type: 'success' });
            } else {
                addToast({ message: 'Could not generate similar ideas.', type: 'warning' });
            }

            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);

        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to generate similar ideas.', type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, [`similar-${baseIdea.id}`]: false }));
        }
    };

    const handleCreateDraft = async (idea: ContentIdea): Promise<Draft | null> => {
        if (!styleGuide) {
            addToast({ message: 'Cannot create draft without a Style Guide.', type: 'warning' });
            return null;
        }
        
        setIsLoading(prev => ({ ...prev, [`draft-${idea.id}`]: true }));
        try {
            const newDraft = await wordpressApiService.createDraft(idea.title);
            
            // Update local state
            setPosts(prev => [newDraft, ...prev]);
            setIdeas(prev => prev.filter(i => i.id !== idea.id));
            
            addToast({ message: `Draft "${idea.title}" created successfully!`, type: 'success' });
            
            if (settings.mode !== 'full-automatic') {
                setView('drafts');
            }
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
            return newDraft;
            
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : "An unknown error occurred";
            addToast({ message: `Failed to create draft: ${errorMessage}`, type: 'error' });
            console.error(error);
            return null;
        } finally {
            setIsLoading(prev => ({ ...prev, [`draft-${idea.id}`]: false }));
        }
    };

    const handlePublishPost = async (id: number) => {
        setPublishingId(id);
        
        try {
            await wordpressApiService.publishPost(id);
            
            // Update local state
            setPosts(currentPosts =>
                currentPosts.map(p => {
                    if (p.id === id) {
                        return { ...p, status: 'published', publishedAt: new Date().toISOString() };
                    }
                    return p;
                })
            );

            const post = posts.find(p => p.id === id);
            if (post) {
                addToast({ message: `Post "${post.title}" published!`, type: 'success' });
            }
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to publish post.', type: 'error' });
        } finally {
            setPublishingId(null);
        }
    };

    const handleUpdateDraft = async (id: number, updates: Partial<Draft>) => {
        try {
            await wordpressApiService.updatePost(id, updates);
            
            // Update local state
            setPosts(currentPosts =>
                currentPosts.map(p => (p.id === id ? { ...p, ...updates } : p))
            );
            setSelectedDraft(prev => prev ? { ...prev, ...updates } : null);
            
            addToast({ message: `Draft "${updates.title || 'post'}" has been updated.`, type: 'success' });
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to update draft.', type: 'error' });
        }
    };
    
    const handleScheduleDraft = async (id: number, date: string) => {
        try {
            await wordpressApiService.schedulePost(id, date);
            
            // Update local state
            setPosts(currentPosts =>
                currentPosts.map(p => {
                    if (p.id === id) {
                        addToast({ message: `Draft "${p.title}" scheduled for ${new Date(date).toLocaleDateString()}.`, type: 'info' });
                        return { ...p, scheduledFor: date };
                    }
                    return p;
                })
            );
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to schedule draft.', type: 'error' });
        }
    };

    const handleSaveSettings = async (newSettings: AppSettings) => {
        try {
            await wordpressApiService.updateSettings(newSettings);
            setSettings(newSettings);
            addToast({ message: 'Settings saved successfully!', type: 'success' });
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to save settings.', type: 'error' });
        }
    };

    const handleSaveStyleGuide = async (newGuide: StyleGuide) => {
        try {
            await wordpressApiService.updateStyleGuide(newGuide);
            setStyleGuide(newGuide);
            addToast({ message: 'Style Guide saved successfully!', type: 'success' });
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to save style guide.', type: 'error' });
        }
    };

    const handleArchiveIdea = async (id: number) => {
        try {
            await wordpressApiService.deleteIdea(id);
            
            const idea = ideas.find(i => i.id === id);
            setIdeas(prev => prev.filter(i => i.id !== id));
            addToast({ message: 'Idea archived.', type: 'info' });
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to archive idea.', type: 'error' });
        }
    };

    const handleUpdateIdeaTitle = async (id: number, newTitle: string) => {
        try {
            await wordpressApiService.updateIdea(id, { title: newTitle });
            
            setIdeas(currentIdeas =>
                currentIdeas.map(idea =>
                    idea.id === id ? { ...idea, title: newTitle } : idea
                )
            );
            addToast({ message: 'Idea title updated.', type: 'info' });
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            addToast({ message: 'Failed to update idea title.', type: 'error' });
        }
    };

    const handleAddIdea = async (title: string) => {
        if (!title.trim()) {
            addToast({ message: 'Idea title cannot be empty.', type: 'warning' });
            return;
        }

        try {
            const newIdea = await wordpressApiService.addManualIdea(title.trim());
            setIdeas(prev => [newIdea, ...prev]);
            addToast({ message: 'Idea added successfully!', type: 'success' });
            
            // Refresh activity logs
            const logs = await wordpressApiService.getActivityLogs();
            setActivityLogs(logs);
            
        } catch (error) {
            console.error(error);
            const errorMessage = error instanceof Error ? error.message : 'Failed to add idea';
            addToast({ message: errorMessage, type: 'error' });
        }
    };

    const renderView = () => {
        switch (view) {
            case 'style-guide':
                return <StyleGuideManager styleGuide={styleGuide} onAnalyze={() => handleAnalyzeStyle(false)} onSaveStyleGuide={handleSaveStyleGuide} isLoading={isLoading['style']} />;
            case 'ideas':
                return <IdeaBoard ideas={ideas} onGenerate={() => handleGenerateIdeas(false, 5)} onCreateDraft={handleCreateDraft} onArchive={handleArchiveIdea} isLoading={isLoading['ideas']} isLoadingDraft={isLoading} onUpdateTitle={handleUpdateIdeaTitle} onGenerateSimilar={handleGenerateSimilarIdeas} onAddIdea={handleAddIdea} />;
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
            <div className="flex h-screen bg-transparent text-slate-300 font-sans">
                {isSidebarOpen && (
                    <div onClick={() => setIsSidebarOpen(false)} className="fixed inset-0 bg-black/60 z-10 sm:hidden" aria-hidden="true"></div>
                )}
                <Sidebar currentView={view} setView={setView} isOpen={isSidebarOpen} closeSidebar={() => setIsSidebarOpen(false)} />
                
                <div className="flex-1 flex flex-col overflow-hidden">
                    <header className="sm:hidden bg-slate-800/80 backdrop-blur-sm border-b border-slate-700 p-2 flex items-center">
                        <button onClick={() => setIsSidebarOpen(true)} className="p-2 text-slate-300 hover:text-white">
                            <Menu className="h-6 w-6" />
                        </button>
                        <span className="ml-2 font-semibold text-white">AI Content Agent</span>
                    </header>
                    <main className="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                        {renderView()}
                    </main>
                </div>
            </div>
            {selectedDraft && (
                <DraftModal
                    draft={selectedDraft}
                    onClose={() => setSelectedDraft(null)}
                    onSave={handleUpdateDraft}
                    settings={settings}
                />
            )}
            <div className="fixed bottom-5 right-5 z-50 w-full max-w-sm space-y-3">
                {toasts.map(toast => (
                    <Toast key={toast.id} {...toast} onDismiss={removeToast} />
                ))}
            </div>
        </>
    );
};

export default App;