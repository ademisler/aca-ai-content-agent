import React, { useState, useEffect, useCallback } from 'react';
import type { PostWithFreshness, FreshnessSettings, FreshnessAnalysis, ContentUpdate } from '../types';
import { contentFreshnessApi } from '../services/wordpressApi';
import { Spinner, Sparkles, RefreshCw, TrendingUp, AlertTriangle, CheckCircle, Settings, BarChart3 } from './Icons';

interface ContentFreshnessManagerProps {
    onShowToast: (message: string, type: 'success' | 'error' | 'warning' | 'info') => void;
}

interface FreshnessCardProps {
    post: PostWithFreshness;
    onAnalyze: (postId: number) => void;
    onUpdate: (postId: number) => void;
    isAnalyzing: boolean;
    isUpdating: boolean;
}

const getPriorityColor = (priority: number | null): string => {
    if (!priority) return '#666';
    switch (priority) {
        case 5: return '#dc3545'; // Urgent - Red
        case 4: return '#fd7e14'; // High - Orange
        case 3: return '#ffc107'; // Medium - Yellow
        case 2: return '#28a745'; // Low - Green
        case 1: return '#6c757d'; // Very low - Gray
        default: return '#666';
    }
};

const getPriorityLabel = (priority: number | null): string => {
    if (!priority) return 'Unknown';
    switch (priority) {
        case 5: return 'Urgent';
        case 4: return 'High';
        case 3: return 'Medium';
        case 2: return 'Low';
        case 1: return 'Very Low';
        default: return 'Unknown';
    }
};

const FreshnessCard: React.FC<FreshnessCardProps> = ({ 
    post, 
    onAnalyze, 
    onUpdate, 
    isAnalyzing, 
    isUpdating 
}) => {
    const [showDetails, setShowDetails] = useState(false);
    
    const priorityColor = getPriorityColor(post.update_priority);
    const priorityLabel = getPriorityLabel(post.update_priority);
    
    const formatDate = (dateString: string | null) => {
        if (!dateString) return 'Never';
        return new Date(dateString).toLocaleDateString();
    };
    
    const getScoreColor = (score: number | null): string => {
        if (!score) return '#666';
        if (score >= 80) return '#28a745'; // Green
        if (score >= 60) return '#ffc107'; // Yellow
        if (score >= 40) return '#fd7e14'; // Orange
        return '#dc3545'; // Red
    };

    return (
        <div className="aca-card" style={{ margin: 0 }}>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', marginBottom: '15px' }}>
                <div style={{ flex: 1 }}>
                    <h3 style={{ margin: '0 0 8px 0', fontSize: '16px', lineHeight: '1.4' }}>
                        {post.post_title}
                    </h3>
                    <div style={{ display: 'flex', gap: '15px', fontSize: '13px', color: '#666' }}>
                        <span>Published: {formatDate(post.post_date)}</span>
                        <span>Modified: {formatDate(post.post_modified)}</span>
                        {post.last_analyzed && (
                            <span>Analyzed: {formatDate(post.last_analyzed)}</span>
                        )}
                    </div>
                </div>
                
                <div style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
                    {post.freshness_score !== null && (
                        <div style={{ 
                            textAlign: 'center',
                            padding: '8px 12px',
                            borderRadius: '6px',
                            backgroundColor: getScoreColor(post.freshness_score) + '20',
                            border: `1px solid ${getScoreColor(post.freshness_score)}40`
                        }}>
                            <div style={{ 
                                fontSize: '18px', 
                                fontWeight: 'bold', 
                                color: getScoreColor(post.freshness_score) 
                            }}>
                                {Math.round(post.freshness_score)}
                            </div>
                            <div style={{ fontSize: '11px', color: '#666' }}>Score</div>
                        </div>
                    )}
                    
                    {post.update_priority && (
                        <div style={{ 
                            textAlign: 'center',
                            padding: '4px 8px',
                            borderRadius: '4px',
                            backgroundColor: priorityColor + '20',
                            border: `1px solid ${priorityColor}40`,
                            fontSize: '11px',
                            color: priorityColor,
                            fontWeight: '500'
                        }}>
                            {priorityLabel}
                        </div>
                    )}
                </div>
            </div>
            
            {post.needs_update && (
                <div className="aca-alert warning" style={{ margin: '10px 0', fontSize: '13px' }}>
                    <AlertTriangle style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                    This content may need updating to maintain freshness and relevance.
                </div>
            )}
            
            {post.analysis && showDetails && (
                <div 
                    id={`details-${post.ID}`}
                    style={{ 
                        marginTop: '15px', 
                        padding: '15px', 
                    backgroundColor: '#f8f9fa', 
                    borderRadius: '6px',
                    fontSize: '13px'
                }}>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(120px, 1fr))', gap: '10px', marginBottom: '15px' }}>
                        <div>
                            <strong>Age Score:</strong> {Math.round(post.analysis.age_score)}
                        </div>
                        <div>
                            <strong>SEO Score:</strong> {Math.round(post.analysis.seo_score)}
                        </div>
                        <div>
                            <strong>AI Score:</strong> {Math.round(post.analysis.ai_score)}
                        </div>
                        <div>
                            <strong>Days Old:</strong> {post.analysis.days_old}
                        </div>
                    </div>
                    
                    {post.analysis.suggestions.length > 0 && (
                        <div style={{ marginBottom: '10px' }}>
                            <strong>Suggestions:</strong>
                            <ul style={{ margin: '5px 0 0 20px', padding: 0 }}>
                                {post.analysis.suggestions.map((suggestion, index) => (
                                    <li key={index} style={{ marginBottom: '3px' }}>{suggestion}</li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            )}
            
            <div style={{ display: 'flex', gap: '10px', marginTop: '15px' }}>
                <button
                    onClick={() => onAnalyze(post.ID)}
                    disabled={isAnalyzing}
                    className="aca-button secondary"
                    style={{ display: 'flex', alignItems: 'center', gap: '6px' }}
                >
                    {isAnalyzing ? (
                        <Spinner className="h-4 w-4" />
                    ) : (
                        <BarChart3 className="h-4 w-4" />
                    )}
                    {isAnalyzing ? 'Analyzing...' : 'Analyze'}
                </button>
                
                {post.needs_update && (
                    <button
                        onClick={() => onUpdate(post.ID)}
                        disabled={isUpdating}
                        className="aca-button primary"
                        style={{ display: 'flex', alignItems: 'center', gap: '6px' }}
                    >
                        {isUpdating ? (
                            <Spinner className="h-4 w-4" />
                        ) : (
                            <RefreshCw className="h-4 w-4" />
                        )}
                        {isUpdating ? 'Updating...' : 'Queue Update'}
                    </button>
                )}
                
                <button
                    onClick={() => setShowDetails(!showDetails)}
                    className="aca-button secondary"
                    style={{ marginLeft: 'auto' }}
                    aria-expanded={showDetails}
                    aria-controls={`details-${post.ID}`}
                >
                    {showDetails ? 'Hide Details' : 'Show Details'}
                </button>
            </div>
        </div>
    );
};

export const ContentFreshnessManager: React.FC<ContentFreshnessManagerProps> = ({ onShowToast }) => {
    const [posts, setPosts] = useState<PostWithFreshness[]>([]);
    const [settings, setSettings] = useState<FreshnessSettings>({
        analysisFrequency: 'weekly',
        autoUpdate: false,
        updateThreshold: 70,
        enabled: true
    });
    const [isLoading, setIsLoading] = useState(false);
    const [isAnalyzing, setIsAnalyzing] = useState<{ [key: number]: boolean }>({});
    const [isUpdating, setIsUpdating] = useState<{ [key: number]: boolean }>({});
    const [filter, setFilter] = useState<'all' | 'needs_update' | 'fresh'>('all');
    const [showSettings, setShowSettings] = useState(false);

    const loadPosts = useCallback(async () => {
        setIsLoading(true);
        try {
            const response = await contentFreshnessApi.getPosts(20, filter === 'all' ? undefined : filter);
            if (response && response.success) {
                setPosts(response.posts || []);
            } else {
                const errorMessage = response?.message || 'Failed to load posts';
                onShowToast(errorMessage, 'error');
                setPosts([]);
            }
        } catch (error) {
            console.error('Error loading posts:', error);
            const errorMessage = error instanceof Error ? error.message : 'Error loading posts';
            onShowToast(errorMessage, 'error');
            setPosts([]);
        } finally {
            setIsLoading(false);
        }
    }, [filter, onShowToast]);

    const loadSettings = useCallback(async () => {
        try {
            const response = await contentFreshnessApi.getSettings();
            if (response && response.success) {
                setSettings(response.settings);
            } else {
                console.warn('Failed to load freshness settings, using defaults');
            }
        } catch (error) {
            console.error('Error loading settings:', error);
            // Keep default settings on error
        }
    }, []);

    useEffect(() => {
        loadPosts();
        loadSettings();
    }, [loadPosts, loadSettings]);

    const handleAnalyzeAll = async () => {
        setIsLoading(true);
        try {
            const response = await contentFreshnessApi.analyzeAll(10);
            if (response.success) {
                onShowToast(`Analyzed ${response.analyzed_count} posts successfully`, 'success');
                loadPosts(); // Reload posts to get updated data
            } else {
                onShowToast('Unable to analyze posts. Please check your Pro license and try again.', 'error');
            }
        } catch (error) {
            console.error('Error analyzing posts:', error);
            onShowToast('Analysis failed due to a connection error. Please try again.', 'error');
        } finally {
            setIsLoading(false);
        }
    };

    const handleAnalyzeSingle = async (postId: number) => {
        setIsAnalyzing(prev => ({ ...prev, [postId]: true }));
        try {
            const response = await contentFreshnessApi.analyzeSingle(postId);
            if (response.success) {
                onShowToast(`Analysis completed for "${response.post_title}"`, 'success');
                // Update the specific post in the list
                setPosts(prev => prev.map(post => 
                    post.ID === postId 
                        ? { 
                            ...post, 
                            freshness_score: response.analysis.score,
                            needs_update: response.analysis.needs_update,
                            update_priority: response.analysis.priority,
                            last_analyzed: new Date().toISOString(),
                            analysis: response.analysis
                        }
                        : post
                ));
            } else {
                onShowToast('Unable to analyze this post. Please check your Pro license.', 'error');
            }
        } catch (error) {
            console.error('Error analyzing post:', error);
            onShowToast('Post analysis failed. Please check your connection and try again.', 'error');
        } finally {
            setIsAnalyzing(prev => ({ ...prev, [postId]: false }));
        }
    };

    const handleUpdateContent = async (postId: number) => {
        setIsUpdating(prev => ({ ...prev, [postId]: true }));
        try {
            const response = await contentFreshnessApi.updateContent(postId);
            if (response.success) {
                onShowToast('Content update queued successfully', 'success');
            } else {
                onShowToast('Unable to queue content update. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Error updating content:', error);
            onShowToast('Content update failed. Please check your connection and try again.', 'error');
        } finally {
            setIsUpdating(prev => ({ ...prev, [postId]: false }));
        }
    };

    const handleSaveSettings = async (newSettings: FreshnessSettings) => {
        try {
            const response = await contentFreshnessApi.saveSettings(newSettings);
            if (response.success) {
                setSettings(newSettings);
                onShowToast('Settings saved successfully', 'success');
                setShowSettings(false);
            } else {
                onShowToast('Unable to save settings. Please try again.', 'error');
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            onShowToast('Settings save failed. Please check your connection and try again.', 'error');
        }
    };

    const needsUpdateCount = posts.filter(post => post.needs_update).length;
    const analyzedCount = posts.filter(post => post.freshness_score !== null).length;
    const averageScore = posts.length > 0 
        ? Math.round(posts.reduce((sum, post) => sum + (post.freshness_score || 0), 0) / posts.length)
        : 0;

    return (
        <div className="aca-fade-in">
            {/* Modern Content Freshness Header */}
            <div style={{
                background: 'linear-gradient(135deg, #06b6d4 0%, #0891b2 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: '12px', marginBottom: '12px' }}>
                        <div style={{
                            width: '48px',
                            height: '48px',
                            background: 'rgba(255,255,255,0.2)',
                            borderRadius: '12px',
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            backdropFilter: 'blur(10px)'
                        }}>
                            <Sparkles style={{ width: '24px', height: '24px' }} />
                        </div>
                        <div>
                            <h1 style={{ 
                                fontSize: '28px', 
                                fontWeight: '700', 
                                margin: 0,
                                textShadow: '0 2px 4px rgba(0,0,0,0.1)',
                                color: 'white'
                            }}>
                                Content Freshness Manager
                            </h1>
                            <div style={{ fontSize: '16px', opacity: 0.9, marginTop: '4px' }}>
                                AI-powered content analysis and updates
                            </div>
                        </div>
                    </div>
                    <p style={{ 
                        fontSize: '14px', 
                        opacity: 0.85,
                        margin: 0,
                        maxWidth: '600px',
                        lineHeight: '1.5'
                    }}>
                        Keep your content up-to-date with AI-powered analysis and update recommendations
                    </p>
                    
                    {/* Stats */}
                    <div style={{ display: 'flex', gap: '20px', marginTop: '20px', flexWrap: 'wrap' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#4ade80', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>Pro Feature</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#fbbf24', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>Content Analysis</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#60a5fa', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>Update Recommendations</span>
                        </div>
                    </div>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-30px',
                    right: '-30px',
                    width: '120px',
                    height: '120px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
                <div style={{
                    position: 'absolute',
                    bottom: '-20px',
                    left: '-20px',
                    width: '80px',
                    height: '80px',
                    background: 'rgba(255,255,255,0.05)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>

            {/* Statistics Cards */}
            <div className="aca-grid aca-grid-4" style={{ marginBottom: '30px' }}>
                <div className="aca-card" style={{ textAlign: 'center', margin: 0 }}>
                    <div style={{ fontSize: '32px', fontWeight: 'bold', color: '#0073aa', marginBottom: '5px' }}>
                        {posts.length}
                    </div>
                    <div style={{ fontSize: '14px', color: '#666' }}>Total Posts</div>
                </div>
                
                <div className="aca-card" style={{ textAlign: 'center', margin: 0 }}>
                    <div style={{ fontSize: '32px', fontWeight: 'bold', color: '#28a745', marginBottom: '5px' }}>
                        {analyzedCount}
                    </div>
                    <div style={{ fontSize: '14px', color: '#666' }}>Analyzed</div>
                </div>
                
                <div className="aca-card" style={{ textAlign: 'center', margin: 0 }}>
                    <div style={{ fontSize: '32px', fontWeight: 'bold', color: '#dc3545', marginBottom: '5px' }}>
                        {needsUpdateCount}
                    </div>
                    <div style={{ fontSize: '14px', color: '#666' }}>Need Updates</div>
                </div>
                
                <div className="aca-card" style={{ textAlign: 'center', margin: 0 }}>
                    <div style={{ fontSize: '32px', fontWeight: 'bold', color: '#ffc107', marginBottom: '5px' }}>
                        {averageScore}
                    </div>
                    <div style={{ fontSize: '14px', color: '#666' }}>Avg. Score</div>
                </div>
            </div>

            {/* Controls */}
            <div className="aca-card" style={{ marginBottom: '30px' }}>
                <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', flexWrap: 'wrap', gap: '15px' }}>
                    <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                        <button
                            onClick={handleAnalyzeAll}
                            disabled={isLoading}
                            className="aca-button primary"
                            style={{ display: 'flex', alignItems: 'center', gap: '6px' }}
                        >
                            {isLoading ? (
                                <Spinner className="h-4 w-4" />
                            ) : (
                                <TrendingUp className="h-4 w-4" />
                            )}
                            {isLoading ? 'Analyzing...' : 'Analyze All Posts'}
                        </button>
                        
                        <button
                            onClick={() => setShowSettings(!showSettings)}
                            className="aca-button secondary"
                            style={{ display: 'flex', alignItems: 'center', gap: '6px' }}
                        >
                            <Settings className="h-4 w-4" />
                            Settings
                        </button>
                    </div>
                    
                    <div style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                        <span style={{ fontSize: '14px', color: '#666' }}>Filter:</span>
                        <select
                            value={filter}
                            onChange={(e) => setFilter(e.target.value as 'all' | 'needs_update' | 'fresh')}
                            className="aca-input"
                            style={{ width: 'auto', minWidth: '120px' }}
                            aria-label="Filter posts by status"
                        >
                            <option value="all">All Posts</option>
                            <option value="needs_update">Needs Update</option>
                            <option value="fresh">Fresh Content</option>
                        </select>
                    </div>
                </div>
            </div>

            {/* Settings Panel */}
            {showSettings && (
                <div className="aca-card" style={{ marginBottom: '30px' }}>
                    <h3 style={{ marginTop: 0 }}>Content Freshness Settings</h3>
                    
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(250px, 1fr))', gap: '20px' }}>
                        <div className="aca-form-group">
                            <label>Analysis Frequency</label>
                            <select
                                value={settings.analysisFrequency}
                                onChange={(e) => setSettings({...settings, analysisFrequency: e.target.value as any})}
                                className="aca-input"
                            >
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="monthly">Monthly</option>
                                <option value="manual">Manual Only</option>
                            </select>
                        </div>
                        
                        <div className="aca-form-group">
                            <label>Update Threshold ({settings.updateThreshold}%)</label>
                            <input
                                type="range"
                                min="30"
                                max="90"
                                value={settings.updateThreshold}
                                onChange={(e) => setSettings({...settings, updateThreshold: parseInt(e.target.value)})}
                                className="aca-input"
                                style={{ width: '100%' }}
                                aria-label={`Update threshold: ${settings.updateThreshold} percent`}
                            />
                            <div style={{ display: 'flex', justifyContent: 'space-between', fontSize: '12px', color: '#666', marginTop: '5px' }}>
                                <span>More Updates (30%)</span>
                                <span>Fewer Updates (90%)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div className="aca-form-group">
                        <label>
                            <input
                                type="checkbox"
                                checked={settings.autoUpdate}
                                onChange={(e) => setSettings({...settings, autoUpdate: e.target.checked})}
                                style={{ marginRight: '8px' }}
                            />
                            Enable automatic content updates (high-confidence suggestions only)
                        </label>
                    </div>
                    
                    <div style={{ display: 'flex', gap: '10px', marginTop: '20px' }}>
                        <button
                            onClick={() => handleSaveSettings(settings)}
                            className="aca-button primary"
                        >
                            Save Settings
                        </button>
                        <button
                            onClick={() => setShowSettings(false)}
                            className="aca-button secondary"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            )}

            {/* Posts List */}
            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                {isLoading && posts.length === 0 ? (
                    <div style={{ textAlign: 'center', padding: '40px' }}>
                        <Spinner className="h-8 w-8" style={{ marginBottom: '10px' }} />
                        <p>Loading posts...</p>
                    </div>
                ) : posts.length === 0 ? (
                    <div className="aca-card" style={{ textAlign: 'center', padding: '40px' }}>
                        <p style={{ margin: 0, color: '#666' }}>No posts found matching the current filter.</p>
                    </div>
                ) : (
                    posts.map(post => (
                        <FreshnessCard
                            key={post.ID}
                            post={post}
                            onAnalyze={handleAnalyzeSingle}
                            onUpdate={handleUpdateContent}
                            isAnalyzing={isAnalyzing[post.ID] || false}
                            isUpdating={isUpdating[post.ID] || false}
                        />
                    ))
                )}
            </div>
        </div>
    );
};