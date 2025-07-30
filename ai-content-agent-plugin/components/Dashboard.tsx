
import React, { memo } from 'react';
import type { View, ActivityLog } from '../types';
import { Lightbulb, BookOpen, FileText, Send, CheckCircle, Sparkles, AlertTriangle } from './Icons';
import { ActivityLogList } from './ActivityLog';

interface DashboardProps {
    stats: {
        ideas: number;
        drafts: number;
        published: number;
        contentFreshness?: {
            total: number;
            analyzed: number;
            needsUpdate: number;
            averageScore: number;
        };
    };
    lastAnalyzed?: string | undefined;
    activityLogs: ActivityLog[];
    onNavigate: (view: View) => void;
    onGenerateIdeas: () => void;
    onUpdateStyleGuide: () => void;
    isLoadingIdeas: boolean;
    isLoadingStyle: boolean;
}

const ActionButton: React.FC<{
    icon: React.ReactNode;
    title: string;
    description: string;
    onClick: () => void;
    disabled?: boolean;
    isLoading?: boolean;
    loadingTitle?: string;
}> = memo(({ icon, title, description, onClick, disabled, isLoading, loadingTitle }) => {
    return (
        <button
            onClick={onClick}
            disabled={disabled || isLoading}
            className="aca-action-button"
            aria-label={isLoading ? loadingTitle : title}
        >
            <div className="aca-action-icon">
                {icon}
            </div>
            <h3 className="aca-action-title">
                {isLoading ? (loadingTitle || 'Loading...') : title}
                {isLoading && <span className="aca-spinner" style={{ marginLeft: '8px' }}></span>}
            </h3>
            <p className="aca-action-description">{description}</p>
        </button>
    );
});

const PipelineItem: React.FC<{
    icon: React.ReactNode;
    title: string;
    count: number;
    description: string;
    view: View;
    onNavigate: (view: View) => void;
}> = memo(({ icon, title, count, description, view, onNavigate }) => (
    <div className="aca-stat-item">
        <div className="aca-stat-info">
            <div className="aca-stat-icon">{icon}</div>
            <div>
                <h4 className="aca-stat-title">{title}</h4>
                <p className="aca-stat-count">{count} {description}</p>
            </div>
        </div>
        <button 
            onClick={() => onNavigate(view)} 
            className="aca-button"
            aria-label={`View ${title.toLowerCase()}`}
        >
            View
        </button>
    </div>
));

export const Dashboard: React.FC<DashboardProps> = ({ 
    stats, 
    lastAnalyzed, 
    activityLogs, 
    onNavigate, 
    onGenerateIdeas, 
    onUpdateStyleGuide, 
    isLoadingIdeas, 
    isLoadingStyle 
}) => {
    const isStyleGuideReady = !!lastAnalyzed;

    return (
        <div className="aca-fade-in">
            {/* Modern Welcome Section */}
            <div className="aca-welcome-section" style={{
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                borderRadius: '12px',
                padding: '30px',
                marginBottom: '30px',
                color: 'white',
                position: 'relative',
                overflow: 'hidden'
            }}>
                <div style={{ position: 'relative', zIndex: 2 }}>
                    <h1 style={{ 
                        fontSize: '28px', 
                        fontWeight: '700', 
                        marginBottom: '8px',
                        textShadow: '0 2px 4px rgba(0,0,0,0.1)',
                        color: 'white'
                    }}>
                        Welcome to AI Content Agent
                    </h1>
                    <p style={{ 
                        fontSize: '16px', 
                        opacity: 0.9,
                        marginBottom: '20px',
                        maxWidth: '600px'
                    }}>
                        Your intelligent content creation companion powered by Google Gemini AI
                    </p>
                    <div style={{ display: 'flex', gap: '15px', flexWrap: 'wrap' }}>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#4ade80', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>AI-Powered</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#60a5fa', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>Automated Workflow</span>
                        </div>
                        <div style={{ display: 'flex', alignItems: 'center', gap: '6px' }}>
                            <div style={{ width: '8px', height: '8px', backgroundColor: '#f59e0b', borderRadius: '50%' }}></div>
                            <span style={{ fontSize: '14px', opacity: 0.9 }}>SEO Optimized</span>
                        </div>
                    </div>
                </div>
                {/* Decorative elements */}
                <div style={{
                    position: 'absolute',
                    top: '-50px',
                    right: '-50px',
                    width: '150px',
                    height: '150px',
                    background: 'rgba(255,255,255,0.1)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
                <div style={{
                    position: 'absolute',
                    bottom: '-30px',
                    left: '-30px',
                    width: '100px',
                    height: '100px',
                    background: 'rgba(255,255,255,0.05)',
                    borderRadius: '50%',
                    zIndex: 1
                }}></div>
            </div>

            {!isStyleGuideReady && (
                <div className="aca-alert info">
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: '15px' }}>
                        <div style={{ display: 'flex', alignItems: 'center' }}>
                            <Lightbulb style={{ width: '24px', height: '24px', marginRight: '12px', flexShrink: 0 }} />
                            <div>
                                <h4 style={{ margin: '0 0 5px 0', fontWeight: '600', color: '#0073aa' }}>Get Started with AI Content Agent (ACA)</h4>
                                <p style={{ margin: 0, fontSize: '13px' }}>Create your Style Guide first to enable all features and generate on-brand content.</p>
                            </div>
                        </div>
                        <button 
                            onClick={() => onNavigate('style-guide')} 
                            className="aca-button large"
                            style={{ flexShrink: 0 }}
                        >
                            Create Style Guide
                        </button>
                    </div>
                </div>
            )}

            <div className="aca-grid aca-grid-2" style={{ marginBottom: '30px' }}>
                <div className="aca-card" style={{ 
                    background: 'linear-gradient(145deg, #f8fafc 0%, #f1f5f9 100%)',
                    border: '1px solid #e2e8f0',
                    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
                }}>
                    <div className="aca-card-header" style={{ borderBottom: '1px solid #e2e8f0', paddingBottom: '15px' }}>
                        <h2 className="aca-card-title" style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            gap: '8px',
                            color: '#1e293b'
                        }}>
                            <div style={{ 
                                width: '32px', 
                                height: '32px', 
                                background: 'linear-gradient(135deg, #3b82f6, #1d4ed8)',
                                borderRadius: '8px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }}>
                                <span style={{ color: 'white', fontSize: '16px' }}>⚡</span>
                            </div>
                            Quick Actions
                        </h2>
                        <p style={{ margin: '8px 0 0 0', color: '#64748b', fontSize: '14px' }}>
                            Get started with AI-powered content creation
                        </p>
                    </div>
                    <div className="aca-grid aca-grid-2" style={{ marginTop: '20px' }}>
                        <ActionButton
                            icon={<Lightbulb />}
                            title="Generate Ideas"
                            description="Create fresh content ideas based on your style guide"
                            onClick={onGenerateIdeas}
                            disabled={!isStyleGuideReady}
                            isLoading={isLoadingIdeas}
                            loadingTitle="Generating Ideas..."
                        />
                        <ActionButton
                            icon={<BookOpen />}
                            title="Update Style Guide"
                            description="Analyze your site content to refresh your style guide"
                            onClick={onUpdateStyleGuide}
                            isLoading={isLoadingStyle}
                            loadingTitle="Analyzing Style..."
                        />
                    </div>
                </div>

                <div className="aca-card" style={{ 
                    background: 'linear-gradient(145deg, #fefefe 0%, #f8f9fa 100%)',
                    border: '1px solid #e2e8f0',
                    boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)'
                }}>
                    <div className="aca-card-header" style={{ borderBottom: '1px solid #e2e8f0', paddingBottom: '15px' }}>
                        <h2 className="aca-card-title" style={{ 
                            display: 'flex', 
                            alignItems: 'center', 
                            gap: '8px',
                            color: '#1e293b'
                        }}>
                            <div style={{ 
                                width: '32px', 
                                height: '32px', 
                                background: 'linear-gradient(135deg, #10b981, #059669)',
                                borderRadius: '8px',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }}>
                                <span style={{ color: 'white', fontSize: '16px' }}>📊</span>
                            </div>
                            Content Pipeline
                        </h2>
                        <p style={{ margin: '8px 0 0 0', color: '#64748b', fontSize: '14px' }}>
                            Track your content creation progress
                        </p>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '10px', marginTop: '20px' }}>
                        <PipelineItem
                            icon={<Lightbulb />}
                            title="Ideas"
                            count={stats.ideas}
                            description="content ideas"
                            view="ideas"
                            onNavigate={onNavigate}
                        />
                        <PipelineItem
                            icon={<FileText />}
                            title="Drafts"
                            count={stats.drafts}
                            description="draft posts"
                            view="drafts"
                            onNavigate={onNavigate}
                        />
                        <PipelineItem
                            icon={<Send />}
                            title="Published"
                            count={stats.published}
                            description="published posts"
                            view="published"
                            onNavigate={onNavigate}
                        />
                    </div>
                </div>
            </div>

            {isStyleGuideReady && (
                <div className="aca-card">
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            <CheckCircle style={{ width: '20px', height: '20px', marginRight: '8px', fill: '#00a32a' }} />
                            Style Guide Active
                        </h2>
                    </div>
                    <p style={{ margin: '0 0 15px 0', color: '#646970' }}>
                        Last analyzed: {new Date(lastAnalyzed).toLocaleDateString()} at {new Date(lastAnalyzed).toLocaleTimeString()}
                    </p>
                    <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                        <button 
                            onClick={() => onNavigate('style-guide')} 
                            className="aca-button secondary"
                        >
                            View Style Guide
                        </button>
                        <button 
                            onClick={onUpdateStyleGuide} 
                            className="aca-button secondary"
                            disabled={isLoadingStyle}
                        >
                            {isLoadingStyle ? 'Updating...' : 'Refresh Analysis'}
                        </button>
                    </div>
                </div>
            )}

            {/* Content Freshness Widget (Pro Feature) */}
            {stats.contentFreshness && (
                <div className="aca-card" style={{ marginBottom: '30px' }}>
                    <div className="aca-card-header">
                        <h2 className="aca-card-title">
                            <Sparkles style={{ marginRight: '8px' }} />
                            Content Freshness (Pro)
                        </h2>
                    </div>
                    <div className="aca-grid aca-grid-4">
                        <div style={{ textAlign: 'center', padding: '15px' }}>
                            <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#0073aa', marginBottom: '5px' }}>
                                {stats.contentFreshness.total}
                            </div>
                            <div style={{ fontSize: '13px', color: '#666' }}>Total Posts</div>
                        </div>
                        <div style={{ textAlign: 'center', padding: '15px' }}>
                            <div style={{ fontSize: '24px', fontWeight: 'bold', color: '#28a745', marginBottom: '5px' }}>
                                {stats.contentFreshness.analyzed}
                            </div>
                            <div style={{ fontSize: '13px', color: '#666' }}>Analyzed</div>
                        </div>
                        <div style={{ textAlign: 'center', padding: '15px' }}>
                            <div style={{ fontSize: '24px', fontWeight: 'bold', color: stats.contentFreshness.needsUpdate > 0 ? '#dc3545' : '#28a745', marginBottom: '5px' }}>
                                {stats.contentFreshness.needsUpdate}
                            </div>
                            <div style={{ fontSize: '13px', color: '#666', display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '4px' }}>
                                {stats.contentFreshness.needsUpdate > 0 && <AlertTriangle style={{ width: '14px', height: '14px' }} />}
                                Need Updates
                            </div>
                        </div>
                        <div style={{ textAlign: 'center', padding: '15px' }}>
                            <div style={{ 
                                fontSize: '24px', 
                                fontWeight: 'bold', 
                                color: stats.contentFreshness.averageScore >= 80 ? '#28a745' : 
                                       stats.contentFreshness.averageScore >= 60 ? '#ffc107' : '#dc3545',
                                marginBottom: '5px' 
                            }}>
                                {stats.contentFreshness.averageScore}
                            </div>
                            <div style={{ fontSize: '13px', color: '#666' }}>Avg. Score</div>
                        </div>
                    </div>
                    <div style={{ marginTop: '15px', textAlign: 'center' }}>
                        <button 
                            onClick={() => onNavigate('content-freshness')} 
                            className="aca-button primary"
                            style={{ display: 'inline-flex', alignItems: 'center', gap: '6px' }}
                        >
                            <Sparkles style={{ width: '16px', height: '16px' }} />
                            Manage Content Freshness
                        </button>
                    </div>
                </div>
            )}

            <div className="aca-card">
                <div className="aca-card-header">
                    <h2 className="aca-card-title">Recent Activity</h2>
                </div>
                <ActivityLogList logs={activityLogs.slice(0, 10)} />
            </div>
        </div>
    );
};
