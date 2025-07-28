
import React, { useState, useCallback, useEffect } from 'react';
import { geminiService, setGeminiApiKey } from './services/geminiService';
import { fetchStockPhotoAsBase64 } from './services/stockPhotoService';
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

    const addToast = useCallback((toast: Omit<ToastData, 'id'>) => {
        const id = Date.now() + Math.random();
        setToasts(currentToasts => [...currentToasts, { ...toast, id }]);
    }, []);
    
    const addLogEntry = useCallback((type: ActivityLogType, details: string, icon: IconName) => {
        const newLog: ActivityLog = {
            id: Date.now(),
            timestamp: new Date().toISOString(),
            type,
            details,
            icon,
        };
        setActivityLogs(prevLogs => [newLog, ...prevLogs]);
    }, []);

    const removeToast = (id: number) => {
        setToasts(currentToasts => currentToasts.filter(t => t.id !== id));
    };

    const handleAnalyzeStyle = useCallback(async (isAuto: boolean = false) => {
        if (!settings.geminiApiKey) {
            addToast({ message: 'Please set your Google AI API Key in Settings first.', type: 'warning' });
            if (!isAuto) {
                setView('settings');
            }
            return;
        }
        setIsLoading(prev => ({ ...prev, style: true }));
        try {
            const analysis = await geminiService.analyzeStyle();
            const parsedAnalysis = JSON.parse(analysis);
            setStyleGuide({ ...parsedAnalysis, lastAnalyzed: new Date().toISOString() });
            addLogEntry('style_updated', 'Style Guide was successfully updated.', 'BookOpen');
            if (!isAuto) {
                addToast({ message: 'Style Guide successfully updated!', type: 'success' });
                setView('style-guide');
            } else {
                 addToast({ message: 'Style Guide automatically refreshed.', type: 'info' });
            }
        } catch (error) {
            console.error(error);
            const errorMessage = error instanceof Error ? error.message : 'Failed to analyze style.';
            addToast({ message: errorMessage, type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, style: false }));
        }
    }, [addToast, setView, addLogEntry, settings.geminiApiKey]);

    const handleGenerateIdeas = async (isAuto: boolean = false, count: number = 5) => {
        if (!settings.geminiApiKey) {
            addToast({ message: 'Please set your Google AI API Key in Settings first.', type: 'warning' });
            if (!isAuto) {
                setView('settings');
            }
            return;
        }
        if (!styleGuide) {
            addToast({ message: 'Please generate a Style Guide first.', type: 'warning' });
            return;
        }
        setIsLoading(prev => ({ ...prev, ideas: true }));
        try {
            const existingTitles = [...ideas.map(i => i.title), ...posts.map(p => p.title)];
            const searchConsoleData = settings.searchConsoleUser
                ? {
                    topQueries: ['AI for content marketing', 'how to write blog posts faster', 'wordpress automation tools'],
                    underperformingPages: ['/blog/old-seo-tips', '/blog/2022-social-media-trends']
                  }
                : undefined;
            
            const newIdeasResult = await geminiService.generateIdeas(JSON.stringify(styleGuide), existingTitles, count, searchConsoleData);
            const parsedIdeas = JSON.parse(newIdeasResult);

            if (parsedIdeas.length > 0) {
                const ideaSource: IdeaSource = searchConsoleData ? 'search-console' : 'ai';
                const newIdeaObjects: ContentIdea[] = parsedIdeas.map((title: string) => ({ id: Date.now() + Math.random(), title, status: 'new', source: ideaSource }));
                setIdeas(prev => [...newIdeaObjects, ...prev]);
                const message = isAuto ? `Semi-auto: ${parsedIdeas.length} new ideas generated!` : `${parsedIdeas.length} new ideas generated!`;
                addToast({ message, type: isAuto ? 'info' : 'success' });
                addLogEntry('ideas_generated', `Generated ${parsedIdeas.length} new content ideas.`, 'Lightbulb');
                if (!isAuto) {
                    setView('ideas');
                }
            } else if (!isAuto) {
                 addToast({ message: 'No new unique ideas could be generated. Try again later.', type: 'warning' });
            }
        } catch (error) {
            console.error(error);
            const errorMessage = error instanceof Error ? error.message : 'Failed to generate ideas.';
            addToast({ message: errorMessage, type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, ideas: false }));
        }
    };
    
    const handleGenerateSimilarIdeas = async (baseIdea: ContentIdea) => {
        if (!settings.geminiApiKey) {
            addToast({ message: 'Please set your Google AI API Key in Settings first.', type: 'warning' });
            setView('settings');
            return;
        }
        setIsLoading(prev => ({ ...prev, [`similar-${baseIdea.id}`]: true }));
        try {
            const existingTitles = [...ideas.map(i => i.title), ...posts.map(p => p.title)];
            const similarIdeasResult = await geminiService.generateSimilarIdeas(baseIdea.title, existingTitles);
            const parsedIdeas = JSON.parse(similarIdeasResult);
            
            if (parsedIdeas.length > 0) {
                const newIdeaObjects: ContentIdea[] = parsedIdeas.map((title: string) => ({ id: Date.now() + Math.random(), title, status: 'new', source: 'similar' }));
                setIdeas(prev => [...newIdeaObjects, ...prev]);
                addToast({ message: `Generated ${parsedIdeas.length} ideas similar to "${baseIdea.title}"`, type: 'success' });
                addLogEntry('ideas_generated', `Generated ${parsedIdeas.length} similar ideas.`, 'Sparkles');
            } else {
                addToast({ message: 'Could not generate similar ideas.', type: 'warning' });
            }

        } catch (error) {
            console.error(error);
            const errorMessage = error instanceof Error ? error.message : 'Failed to generate similar ideas.';
            addToast({ message: errorMessage, type: 'error' });
        } finally {
            setIsLoading(prev => ({ ...prev, [`similar-${baseIdea.id}`]: false }));
        }
    };

    const handleCreateDraft = async (idea: ContentIdea): Promise<Draft | null> => {
        if (!settings.geminiApiKey) {
            addToast({ message: 'Please set your Google AI API Key in Settings first.', type: 'warning' });
            setView('settings');
            return null;
        }
        if (!styleGuide) {
            addToast({ message: 'Cannot create draft without a Style Guide.', type: 'warning' });
            return null;
        }
        setIsLoading(prev => ({ ...prev, [`draft-${idea.id}`]: true }));
        try {
            const publishedPostContext = posts
                .filter(p => p.status === 'published' && p.url && p.content)
                .map(p => ({
                    title: p.title,
                    url: p.url!,
                    content: p.content,
                }));
            
            const draftContentResult = await geminiService.createDraft(idea.title, JSON.stringify(styleGuide), publishedPostContext);
            const draftData = JSON.parse(draftContentResult);
            
            let imageResult: string | null = null;
            const { imageSourceProvider, aiImageStyle, pexelsApiKey, unsplashApiKey, pixabayApiKey } = settings;

            if (imageSourceProvider === 'ai') {
                imageResult = await geminiService.generateImage(idea.title, aiImageStyle);
            } else {
                const apiKeys = { pexels: pexelsApiKey, unsplash: unsplashApiKey, pixabay: pixabayApiKey };
                const apiKey = apiKeys[imageSourceProvider];

                if (!apiKey) {
                    addToast({ message: `API key for ${imageSourceProvider} is not set. Please add it in Settings.`, type: 'warning' });
                    setIsLoading(prev => ({ ...prev, [`draft-${idea.id}`]: false }));
                    return null;
                }
                imageResult = await fetchStockPhotoAsBase64(idea.title, imageSourceProvider, apiKey);
            }
            
            if (!imageResult) {
                throw new Error("Failed to generate or fetch a featured image.");
            }

            const newPost: Draft = {
                id: Date.now(),
                title: idea.title,
                content: draftData.content,
                metaTitle: draftData.metaTitle,
                metaDescription: draftData.metaDescription,
                focusKeywords: draftData.focusKeywords,
                featuredImage: `data:image/jpeg;base64,${imageResult}`,
                createdAt: new Date().toISOString(),
                status: 'draft',
            };
            setPosts(prev => [newPost, ...prev]);
            setIdeas(prev => prev.filter(i => i.id !== idea.id));
            addToast({ message: `Draft "${idea.title}" created successfully!`, type: 'success' });
            addLogEntry('draft_created', `Created draft: "${idea.title}"`, 'FileText');
            if (settings.mode !== 'full-automatic') {
                setView('drafts');
            }
            return newPost;
        } catch (error) {
            const errorMessage = error instanceof Error ? error.message : "An unknown error occurred";
            addToast({ message: `Failed to create draft: ${errorMessage}`, type: 'error' });
            console.error(error);
            return null;
        } finally {
            setIsLoading(prev => ({ ...prev, [`draft-${idea.id}`]: false }));
        }
    };
    
    // Auto Style Guide Analysis (runs on mount and periodically)
    useEffect(() => {
        if(settings.geminiApiKey) {
            handleAnalyzeStyle(true); // Initial analysis
            const styleInterval = setInterval(() => handleAnalyzeStyle(true), 30 * 60 * 1000); // every 30 mins
            return () => clearInterval(styleInterval);
        }
    }, [handleAnalyzeStyle, settings.geminiApiKey]);
    
    // Semi-Automatic Mode: Generate Ideas periodically
    useEffect(() => {
        let intervalId: number | undefined;
        if (settings.mode === 'semi-automatic' && styleGuide && settings.geminiApiKey) {
            intervalId = window.setInterval(() => handleGenerateIdeas(true, 5), 15 * 60 * 1000); // every 15 mins
            addToast({ message: "Semi-Automatic mode activated. Will generate ideas periodically.", type: "info" });
        }
        return () => {
            if (intervalId) {
                clearInterval(intervalId);
                addToast({ message: "Semi-Automatic mode deactivated.", type: "info" });
            }
        }
    }, [settings.mode, styleGuide, settings.geminiApiKey, addToast, handleGenerateIdeas]);

    // Full-Automatic Mode: Full content cycle periodically
    useEffect(() => {
        let intervalId: number | undefined;
        const runAutoProcess = async () => {
          if (!settings.geminiApiKey) {
            addToast({ message: "Auto-pilot paused: Please set your Google AI API Key in Settings.", type: "warning" });
            return;
          }
          if (!styleGuide) {
            console.warn("Auto-pilot paused: Style Guide must be set.");
            return;
          }
          addToast({ message: "Auto-pilot: Starting content cycle...", type: "info" });
          setIsLoading(prev => ({ ...prev, auto: true }));
          try {
            const existingTitles = [...posts.map(p => p.title), ...ideas.map(i => i.title)];
             const searchConsoleData = settings.searchConsoleUser
                ? {
                    topQueries: ['AI for content marketing', 'how to write blog posts faster', 'wordpress automation tools'],
                    underperformingPages: ['/blog/old-seo-tips', '/blog/2022-social-media-trends']
                  }
                : undefined;
            
            const ideasResult = await geminiService.generateIdeas(JSON.stringify(styleGuide), existingTitles, 1, searchConsoleData);
            const parsedIdeas = JSON.parse(ideasResult);
            const newTitle = parsedIdeas[0];
            if (!newTitle) throw new Error("Failed to generate a unique idea.");
    
            const ideaObject: ContentIdea = { id: Date.now(), title: newTitle, status: "new", source: 'ai' };
            addToast({ message: `Auto-pilot: Generated idea "${newTitle}"`, type: "info" });
    
            const newDraft = await handleCreateDraft(ideaObject);
            if (newDraft && settings.autoPublish) {
              addToast({ message: `Auto-pilot: Publishing post...`, type: "info" });
              setTimeout(() => handlePublishPost(newDraft.id), 1500);
            }
          } catch (error) {
            const errorMessage = error instanceof Error ? error.message : 'An unknown error occurred';
            console.error("Auto-pilot process failed:", errorMessage);
            addToast({ message: `Auto-pilot error: ${errorMessage}`, type: "error" });
          } finally {
            setIsLoading(prev => ({ ...prev, auto: false }));
          }
        };
    
        if (settings.mode === 'full-automatic') {
          intervalId = window.setInterval(runAutoProcess, 30 * 60 * 1000); // every 30 mins
          addToast({ message: "Full-Automatic mode activated.", type: "info" });
        }
    
        return () => {
          if (intervalId) {
            clearInterval(intervalId);
            if (settings.mode === 'full-automatic') {
                addToast({ message: "Full-Automatic mode deactivated.", type: "info" });
            }
          }
        };
      }, [settings, styleGuide, addToast, handleCreateDraft, ideas, posts]);

    const handlePublishPost = (id: number) => {
        setPublishingId(id);
        let postTitle = '';

        // Simulate network latency for publishing
        setTimeout(() => {
            setPosts(currentPosts =>
                currentPosts.map(p => {
                    if (p.id === id) {
                        postTitle = p.title;
                        const slug = p.title.toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
                        const url = `/blog/${slug}-${p.id}`; // Add ID for uniqueness
                        return { ...p, status: 'published', publishedAt: new Date().toISOString(), url };
                    }
                    return p;
                })
            );

            if (postTitle) {
                addToast({ message: `Post "${postTitle}" published!`, type: 'success' });
                addLogEntry('post_published', `Published post: "${postTitle}"`, 'Send');
            }
            
            setPublishingId(null);
        }, 1500); // 1.5 second delay to make the loading state visible
    };

    const handleUpdateDraft = (id: number, updates: Partial<Draft>) => {
        setPosts(currentPosts =>
            currentPosts.map(p => (p.id === id ? { ...p, ...updates } : p))
        );
        setSelectedDraft(prev => prev ? { ...prev, ...updates } : null);
        addToast({ message: `Draft "${updates.title || 'post'}" has been updated.`, type: 'success' });
        addLogEntry('draft_updated', `Updated draft: "${updates.title}"`, 'Pencil');
    };
    
    const handleScheduleDraft = (id: number, date: string) => {
        setPosts(currentPosts =>
            currentPosts.map(p => {
                if (p.id === id) {
                    addToast({ message: `Draft "${p.title}" scheduled for ${new Date(date).toLocaleDateString()}.`, type: 'info' });
                    addLogEntry('draft_scheduled', `Scheduled draft: "${p.title}"`, 'Calendar');
                    return { ...p, scheduledFor: date };
                }
                return p;
            })
        );
    };

    const handleSaveSettings = (newSettings: AppSettings) => {
        setSettings(newSettings);
        addToast({ message: 'Settings saved successfully!', type: 'success' });
        addLogEntry('settings_saved', 'Application settings were updated.', 'Settings');
    };

    const handleSaveStyleGuide = (newGuide: StyleGuide) => {
        setStyleGuide(newGuide);
        addToast({ message: 'Style Guide saved successfully!', type: 'success' });
        addLogEntry('style_updated', 'Style Guide was manually edited and saved.', 'BookOpen');
    };

    const handleArchiveIdea = (id: number) => {
        const idea = ideas.find(i => i.id === id);
        if (idea) {
            addLogEntry('idea_archived', `Archived idea: "${idea.title}"`, 'Trash');
        }
        setIdeas(prev => prev.filter(i => i.id !== id));
        addToast({ message: 'Idea archived.', type: 'info' });
    };

    const handleUpdateIdeaTitle = (id: number, newTitle: string) => {
        setIdeas(currentIdeas =>
            currentIdeas.map(idea =>
                idea.id === id ? { ...idea, title: newTitle } : idea
            )
        );
        addToast({ message: 'Idea title updated.', type: 'info' });
        addLogEntry('idea_title_updated', `Updated idea title to "${newTitle}"`, 'Pencil');
    };

    const handleAddIdea = (title: string) => {
        if (!title.trim()) {
            addToast({ message: 'Idea title cannot be empty.', type: 'warning' });
            return;
        }

        const existingTitles = [...ideas.map(i => i.title), ...posts.map(p => p.title)];
        if (existingTitles.some(t => t.toLowerCase() === title.trim().toLowerCase())) {
            addToast({ message: 'This idea title already exists.', type: 'warning' });
            return;
        }
        
        const newIdea: ContentIdea = {
            id: Date.now(),
            title: title.trim(),
            status: 'new',
            source: 'manual'
        };
        
        setIdeas(prev => [newIdea, ...prev]);
        addToast({ message: 'Idea added successfully!', type: 'success' });
        addLogEntry('idea_added', `Manually added idea: "${title.trim()}"`, 'PlusCircle');
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